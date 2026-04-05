<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamStaff;
use App\Models\TeamStaffDocumentRequirement;
use App\Models\TeamStaffRank;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTeamStaffController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.home', ['section' => 'staff-management']);
    }

    public function create(): View
    {
        return view('admin.team-staff.form', [
            'teamStaff' => new TeamStaff(['sequence_no' => $this->nextSequenceNo()]),
            'mode' => 'create',
            'rankSuggestions' => $this->rankSuggestions(),
            'positionSuggestions' => $this->positionSuggestions(),
            'documentTypeSuggestions' => $this->documentTypeSuggestions(),
            'roleOptions' => $this->roleOptions(),
            'genderOptions' => $this->genderOptions(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validated($request);
        $folder = 'team-staff/'.Str::uuid();
        $avatarPath = null;
        $documents = [];

        try {
            $avatarPath = $this->storeAvatar($request->file('avatar_image'), $folder);
            $documents = $this->storeDocuments(
                $request->file('documents', []),
                $folder,
                $request->input('documents_labels', []),
            );

            $teamStaff = DB::transaction(function () use ($validated, $request, $avatarPath, $documents) {
                return TeamStaff::query()->create([
                    ...$validated,
                    'sequence_no' => $this->nextSequenceNo(lock: true),
                    'avatar_path' => $avatarPath,
                    'avatar_original_name' => $request->file('avatar_image')?->getClientOriginalName(),
                    'documents' => $documents,
                ]);
            });
        } catch (\Throwable $exception) {
            if ($avatarPath) {
                Storage::disk('local')->delete($avatarPath);
            }

            $this->deleteStoredDocuments($documents);

            throw $exception;
        }

        if ($request->expectsJson()) {
            return response()->json($teamStaff, 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-management'])
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', "បានបង្កើតបុគ្គលិកក្រុម {$teamStaff->name_latin} ដោយជោគជ័យ។");
    }

    public function show(TeamStaff $teamStaff): View
    {
        return view('admin.team-staff.show', [
            'teamStaff' => $teamStaff,
        ]);
    }

    public function edit(TeamStaff $teamStaff): View
    {
        return view('admin.team-staff.form', [
            'teamStaff' => $teamStaff,
            'mode' => 'edit',
            'rankSuggestions' => $this->rankSuggestions(),
            'positionSuggestions' => $this->positionSuggestions(),
            'documentTypeSuggestions' => $this->documentTypeSuggestions(),
            'roleOptions' => $this->roleOptions(),
            'genderOptions' => $this->genderOptions(),
        ]);
    }

    public function update(Request $request, TeamStaff $teamStaff): JsonResponse|RedirectResponse
    {
        $validated = $this->validated($request, $teamStaff);
        $payload = $validated;
        $newAvatarPath = null;
        $newDocuments = null;
        $oldAvatarPath = $teamStaff->avatar_path;
        $oldDocuments = $teamStaff->documents ?? [];

        if ($request->hasFile('avatar_image')) {
            $folder = 'team-staff/'.Str::uuid();
            $newAvatarPath = $this->storeAvatar($request->file('avatar_image'), $folder);
            $payload['avatar_path'] = $newAvatarPath;
            $payload['avatar_original_name'] = $request->file('avatar_image')?->getClientOriginalName();
        }

        $documents = $request->file('documents', []);

        if (! empty(array_filter($documents))) {
            $folder = 'team-staff/'.Str::uuid();
            $newDocuments = $this->storeDocuments(
                $documents,
                $folder,
                $request->input('documents_labels', []),
            );

            $payload['documents'] = $newDocuments;
        }

        try {
            DB::transaction(function () use ($teamStaff, $payload) {
                $teamStaff->update($payload);
            });
        } catch (\Throwable $exception) {
            if ($newAvatarPath) {
                Storage::disk('local')->delete($newAvatarPath);
            }

            if (is_array($newDocuments)) {
                $this->deleteStoredDocuments($newDocuments);
            }

            throw $exception;
        }

        if ($newAvatarPath && $oldAvatarPath) {
            Storage::disk('local')->delete($oldAvatarPath);
        }

        if (is_array($newDocuments)) {
            $this->deleteStoredDocuments($oldDocuments);
        }

        if ($request->expectsJson()) {
            return response()->json($teamStaff->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-management'])
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', "បានកែប្រែបុគ្គលិកក្រុម {$teamStaff->name_latin} ដោយជោគជ័យ។");
    }

    public function updateMilitaryRank(Request $request, TeamStaff $teamStaff): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'military_rank' => ['required', 'string', 'max:120'],
        ]);

        $teamStaff->update([
            'military_rank' => $validated['military_rank'],
        ]);

        if ($request->expectsJson()) {
            return response()->json($teamStaff->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-management'])
            ->with('status_title', 'Success')
            ->with('status', 'Military rank updated successfully.');
    }

    public function destroy(Request $request, TeamStaff $teamStaff): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        $this->deleteAvatar($teamStaff);
        $this->deleteDocuments($teamStaff);
        $teamStaff->delete();

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-management'])
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', "បានលុបបុគ្គលិកក្រុម {$teamStaff->name_latin} ដោយជោគជ័យ។");
    }

    public function avatar(TeamStaff $teamStaff): BinaryFileResponse
    {
        abort_unless($teamStaff->hasStoredAvatar(), 404);

        return response()->file(Storage::disk('local')->path($teamStaff->avatar_path));
    }

    public function downloadDocument(TeamStaff $teamStaff, int $documentIndex): StreamedResponse
    {
        $documents = collect($teamStaff->documents ?? [])->values();
        $document = $documents->get($documentIndex);

        abort_unless($document && ! empty($document['path']) && Storage::disk('local')->exists($document['path']), 404);

        return Storage::disk('local')->download($document['path'], $document['original_name'] ?? basename($document['path']));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?TeamStaff $teamStaff = null): array
    {
        return $request->validate([
            'military_rank' => ['required', 'string', 'max:120'],
            'name_kh' => ['required', 'string', 'max:255'],
            'name_latin' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:100', Rule::unique('team_staff', 'id_number')->ignore($teamStaff?->id)],
            'avatar_image' => [$teamStaff ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gender' => ['required', Rule::in($this->genderOptions())],
            'position' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:5120'],
            'documents_labels' => ['nullable', 'array'],
            'documents_labels.*' => ['nullable', 'string', 'max:120'],
        ]);
    }

    /**
     * @param  array<int, UploadedFile|null>  $documents
     * @param  array<int, string|null>  $documentLabels
     * @return array<int, array<string, string>>
     */
    private function storeDocuments(array $documents, string $folder, array $documentLabels = []): array
    {
        $labels = collect($documentLabels);

        return collect($documents)
            ->map(function ($document, int $index) use ($folder, $labels) {
                if (! $document instanceof UploadedFile) {
                    return null;
                }

                $path = $document->storeAs(
                    $folder,
                    'document-'.($index + 1).'-'.Str::uuid().'.'.$document->getClientOriginalExtension(),
                    'local',
                );

                $label = trim((string) $labels->get($index, ''));

                return [
                    'label' => $label !== '' ? $label : 'ឯកសារ '.($index + 1),
                    'path' => $path,
                    'original_name' => $document->getClientOriginalName(),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function storeAvatar(?UploadedFile $avatar, string $folder): string
    {
        if (! $avatar) {
            abort(422, 'ត្រូវការរូបភាពប្រវត្តិរូប។');
        }

        return $avatar->storeAs(
            $folder,
            'avatar-'.Str::uuid().'.'.$avatar->getClientOriginalExtension(),
            'local',
        );
    }

    private function deleteAvatar(TeamStaff $teamStaff): void
    {
        if ($teamStaff->avatar_path) {
            Storage::disk('local')->delete($teamStaff->avatar_path);
        }
    }

    private function deleteDocuments(TeamStaff $teamStaff): void
    {
        $this->deleteStoredDocuments($teamStaff->documents ?? []);
    }

    /**
     * @return list<string>
     */
    private function roleOptions(): array
    {
        return ['Admin', 'Manager', 'Staff', 'Viewer'];
    }

    /**
     * @return list<string>
     */
    private function genderOptions(): array
    {
        return ['Male', 'Female', 'Other'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $documents
     */
    private function deleteStoredDocuments(array $documents): void
    {
        collect($documents)
            ->pluck('path')
            ->filter()
            ->each(fn ($path) => Storage::disk('local')->delete($path));
    }

    private function nextSequenceNo(bool $lock = false): int
    {
        $query = TeamStaff::query();

        if ($lock) {
            $query->lockForUpdate();
        }

        return (int) $query->max('sequence_no') + 1;
    }

    private function rankSuggestions()
    {
        return TeamStaffRank::query()
            ->where('is_active', true)
            ->ordered()
            ->pluck('name_kh')
            ->merge(
                TeamStaff::query()
                    ->whereNotNull('military_rank')
                    ->select('military_rank')
                    ->distinct()
                    ->orderBy('military_rank')
                    ->pluck('military_rank')
            )
            ->filter()
            ->unique()
            ->values();
    }

    private function positionSuggestions()
    {
        return TeamStaff::query()
            ->whereNotNull('position')
            ->select('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->filter()
            ->values();
    }

    private function documentTypeSuggestions()
    {
        return TeamStaffDocumentRequirement::query()
            ->where('is_active', true)
            ->ordered()
            ->pluck('name_kh')
            ->filter()
            ->unique()
            ->values();
    }
}
