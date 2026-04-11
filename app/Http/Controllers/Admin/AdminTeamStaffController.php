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
use Illuminate\Support\Collection;
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
        $sequenceNo = $this->nextSequenceNo();

        return view('admin.team-staff.form', [
            'teamStaff' => new TeamStaff([
                'sequence_no' => $sequenceNo,
                'id_number' => TeamStaff::buildGeneratedIdNumber($sequenceNo),
            ]),
            'mode' => 'create',
            'rankSuggestions' => $this->rankSuggestions(),
            'positionSuggestions' => $this->positionSuggestions(),
            'documentTypeSuggestions' => $this->documentTypeSuggestions(),
            'documentRequirements' => $this->activeDocumentRequirements(),
            'roleOptions' => $this->roleOptions(),
            'genderOptions' => $this->genderOptions(),
            'credentialPreview' => [
                'username' => TeamStaff::usernameBase((string) old('name_latin', '')),
                'password' => (string) old('id_number', TeamStaff::buildGeneratedIdNumber($sequenceNo)),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validated($request);
        $folder = 'team-staff/'.Str::uuid();
        $avatarPath = null;
        $documents = [];
        $storedDocumentPaths = [];

        try {
            $avatarPath = $this->storeAvatar($request->file('avatar_image'), $folder);
            ['documents' => $documents, 'stored_paths' => $storedDocumentPaths] = $this->storeDocuments(
                $request->file('documents', []),
                $folder,
                $this->activeDocumentRequirements()->keyBy('id'),
            );

            $teamStaff = DB::transaction(function () use ($validated, $request, $avatarPath, $documents) {
                $sequenceNo = $this->nextSequenceNo(lock: true);
                $requestedIdNumber = trim((string) ($validated['id_number'] ?? ''));
                $idNumber = $requestedIdNumber !== ''
                    ? $requestedIdNumber
                    : TeamStaff::buildGeneratedIdNumber($sequenceNo);

                return TeamStaff::query()->create([
                    ...$validated,
                    'sequence_no' => $sequenceNo,
                    'id_number' => $idNumber,
                    'username' => TeamStaff::makeUniqueUsername($validated['name_latin']),
                    'password' => $idNumber,
                    'is_active' => true,
                    'must_change_password' => true,
                    'avatar_path' => $avatarPath,
                    'avatar_original_name' => $request->file('avatar_image')?->getClientOriginalName(),
                    'documents' => array_values($documents),
                ]);
            });
        } catch (\Throwable $exception) {
            if ($avatarPath) {
                Storage::disk('local')->delete($avatarPath);
            }

            $this->deletePaths($storedDocumentPaths);

            throw $exception;
        }

        if ($request->expectsJson()) {
            return response()->json($teamStaff, 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-management'])
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', "បានបង្កើតបុគ្គលិក {$teamStaff->name_latin} ដោយជោគជ័យ។");
    }

    public function show(TeamStaff $teamStaff): View
    {
        return view('admin.team-staff.show', [
            'teamStaff' => $teamStaff,
            'documentRequirements' => $this->activeDocumentRequirements(),
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
            'documentRequirements' => $this->activeDocumentRequirements(),
            'roleOptions' => $this->roleOptions(),
            'genderOptions' => $this->genderOptions(),
            'credentialPreview' => [
                'username' => TeamStaff::makeUniqueUsername((string) old('name_latin', $teamStaff->name_latin), $teamStaff->id),
                'password' => $teamStaff->id_number,
            ],
        ]);
    }

    public function update(Request $request, TeamStaff $teamStaff): JsonResponse|RedirectResponse
    {
        $validated = $this->validated($request, $teamStaff);
        $payload = $validated;
        $newAvatarPath = null;
        $newDocumentPaths = [];
        $replacedDocumentPaths = [];
        $oldAvatarPath = $teamStaff->avatar_path;
        $oldDocuments = $teamStaff->documents ?? [];
        $requestedIdNumber = trim((string) ($validated['id_number'] ?? ''));

        if ($requestedIdNumber === '') {
            $payload['id_number'] = $teamStaff->id_number ?: TeamStaff::buildGeneratedIdNumber($teamStaff->sequence_no ?: $teamStaff->id, $teamStaff->id);
        }

        $payload['username'] = TeamStaff::makeUniqueUsername($validated['name_latin'], $teamStaff->id);

        if ($request->hasFile('avatar_image')) {
            $folder = 'team-staff/'.Str::uuid();
            $newAvatarPath = $this->storeAvatar($request->file('avatar_image'), $folder);
            $payload['avatar_path'] = $newAvatarPath;
            $payload['avatar_original_name'] = $request->file('avatar_image')?->getClientOriginalName();
        }

        $documents = $request->file('documents', []);

        if (! empty(array_filter($documents))) {
            $folder = 'team-staff/'.Str::uuid();
            ['documents' => $uploadedDocuments, 'stored_paths' => $newDocumentPaths] = $this->storeDocuments(
                $documents,
                $folder,
                $this->activeDocumentRequirements()->keyBy('id'),
            );

            ['documents' => $mergedDocuments, 'replaced_paths' => $replacedDocumentPaths] = $this->mergeDocuments(
                $oldDocuments,
                $uploadedDocuments,
            );

            $payload['documents'] = $mergedDocuments;
        }

        try {
            DB::transaction(function () use ($teamStaff, $payload) {
                $teamStaff->update($payload);
            });
        } catch (\Throwable $exception) {
            if ($newAvatarPath) {
                Storage::disk('local')->delete($newAvatarPath);
            }

            $this->deletePaths($newDocumentPaths);

            throw $exception;
        }

        if ($newAvatarPath && $oldAvatarPath) {
            Storage::disk('local')->delete($oldAvatarPath);
        }

        $this->deletePaths($replacedDocumentPaths);

        if ($request->expectsJson()) {
            return response()->json($teamStaff->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-management'])
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', "បានកែប្រែព័ត៌មានបុគ្គលិក {$teamStaff->name_latin} ដោយជោគជ័យ។");
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
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', 'បានកែប្រែឋានន្តរស័ក្តិដោយជោគជ័យ។');
    }

    public function updatePassword(Request $request, TeamStaff $teamStaff): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6', 'max:50'],
        ]);

        $teamStaff->update([
            'password' => $validated['new_password'],
            'must_change_password' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Password reset successfully']);
        }

        return back()
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', 'កំណត់លេខសម្ងាត់ថ្មីបានជោគជ័យ។');
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
            ->with('status', "បានលុបបុគ្គលិក {$teamStaff->name_latin} ដោយជោគជ័យ។");
    }

    public function avatar(TeamStaff $teamStaff): BinaryFileResponse
    {
        abort_unless($teamStaff->hasStoredAvatar(), 404);

        return response()->file(Storage::disk('local')->path($teamStaff->avatar_path));
    }

    public function showDocument(TeamStaff $teamStaff, int $documentIndex): StreamedResponse
    {
        $documents = collect($teamStaff->documents ?? [])->values();
        $document = $documents->get($documentIndex);

        abort_unless($document && ! empty($document['path']) && Storage::disk('local')->exists($document['path']), 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        return $disk->response(
            $document['path'],
            $document['original_name'] ?? basename($document['path']),
        );
    }

    public function downloadDocument(TeamStaff $teamStaff, int $documentIndex): StreamedResponse
    {
        $documents = collect($teamStaff->documents ?? [])->values();
        $document = $documents->get($documentIndex);

        abort_unless($document && ! empty($document['path']) && Storage::disk('local')->exists($document['path']), 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        return $disk->download($document['path'], $document['original_name'] ?? basename($document['path']));
    }

    public function destroyDocument(
        Request $request,
        TeamStaff $teamStaff,
        int $documentIndex,
    ): RedirectResponse {
        $documents = collect($teamStaff->documents ?? [])->values();
        $document = $documents->get($documentIndex);

        abort_unless($document, 404);

        if (! empty($document['path'])) {
            Storage::disk('local')->delete($document['path']);
        }

        $teamStaff->update([
            'documents' => $documents
                ->reject(fn (array $entry, int $index) => $index === $documentIndex)
                ->values()
                ->all(),
        ]);

        return redirect()
            ->route('team-staff.show', $teamStaff)
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', 'បានលុបឯកសារដោយជោគជ័យ។');
    }

    public function showDocumentByRequirement(
        TeamStaff $teamStaff,
        TeamStaffDocumentRequirement $documentRequirement,
    ): StreamedResponse {
        $document = $this->documentForRequirement($teamStaff, $documentRequirement);

        abort_unless($document, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        return $disk->response(
            $document['path'],
            $document['original_name'] ?? basename($document['path']),
        );
    }

    public function downloadDocumentByRequirement(
        TeamStaff $teamStaff,
        TeamStaffDocumentRequirement $documentRequirement,
    ): StreamedResponse {
        $document = $this->documentForRequirement($teamStaff, $documentRequirement);

        abort_unless($document, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        return $disk->download(
            $document['path'],
            $document['original_name'] ?? basename($document['path']),
        );
    }

    public function upsertDocumentByRequirement(
        Request $request,
        TeamStaff $teamStaff,
        TeamStaffDocumentRequirement $documentRequirement,
    ): RedirectResponse {
        $validated = $request->validate([
            'document_file' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $documentFile = $validated['document_file'];
        abort_unless($documentFile instanceof UploadedFile, 422);

        $path = $documentFile->storeAs(
            'team-staff/'.$teamStaff->id.'/documents',
            $documentRequirement->slug.'-'.Str::uuid().'.'.$documentFile->getClientOriginalExtension(),
            'local',
        );

        $documents = collect($teamStaff->documents ?? [])->values();

        $documentPayload = [
            'label' => $documentRequirement->name_kh,
            'path' => $path,
            'original_name' => $documentFile->getClientOriginalName(),
            'uploaded_by' => 'admin',
            'uploaded_at' => now()->toIso8601String(),
            'status' => 'Approved',
            'requirement_id' => $documentRequirement->id,
            'requirement_slug' => $documentRequirement->slug,
        ];

        $documents->push($documentPayload);

        try {
            $teamStaff->update([
                'documents' => $documents->values()->all(),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('team-staff.show', $teamStaff)
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', 'បានរក្សាទុកឯកសារដោយជោគជ័យ។');
    }

    public function destroyDocumentByRequirement(
        Request $request,
        TeamStaff $teamStaff,
        TeamStaffDocumentRequirement $documentRequirement,
    ): RedirectResponse {
        $documents = collect($teamStaff->documents ?? [])->values();
        $documentIndex = $documents->search(
            fn (array $document) => ($document['requirement_slug'] ?? null) === $documentRequirement->slug
        );

        abort_unless($documentIndex !== false, 404);

        $document = $documents->get($documentIndex);

        if (! empty($document['path'])) {
            Storage::disk('local')->delete($document['path']);
        }

        $teamStaff->update([
            'documents' => $documents
                ->reject(fn (array $entry, int $index) => $index === $documentIndex)
                ->values()
                ->all(),
        ]);

        return redirect()
            ->route('team-staff.show', $teamStaff)
            ->with('status_title', 'ជោគជ័យ')
            ->with('status', 'បានលុបឯកសារដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?TeamStaff $teamStaff = null): array
    {
        $request->merge([
            'name_kh' => trim((string) $request->input('name_kh')),
            'name_latin' => trim((string) $request->input('name_latin')),
            'id_number' => $this->normalizeIdNumber($request->input('id_number')),
            'position' => trim((string) $request->input('position')),
            'role' => trim((string) $request->input('role')),
            'phone_number' => $this->normalizePhoneNumber($request->input('phone_number')),
        ]);

        return $request->validate([
            'military_rank' => ['required', 'string', 'max:120'],
            'name_kh' => ['required', 'string', 'max:255'],
            'name_latin' => ['required', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:100', Rule::unique('team_staff', 'id_number')->ignore($teamStaff?->id)],
            'avatar_image' => [$teamStaff ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gender' => ['required', Rule::in($this->genderOptions())],
            'position' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:50'],
            'phone_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'avatar_image.required' => 'ត្រូវបញ្ចូលរូបភាពប្រវត្តិរូប។',
            'avatar_image.max' => 'រូបភាពប្រវត្តិរូបត្រូវតែមានទំហំមិនលើស 5MB។',
            'phone_number.regex' => 'លេខទូរស័ព្ទត្រូវមានតែខ្ទង់លេខពី 8 ដល់ 15 ខ្ទង់ប៉ុណ្ណោះ។',
            'role.max' => 'តួនាទីត្រូវមានអតិបរមា 50 តួអក្សរ។',
        ]);
    }

    /**
     * @param  array<int|string, UploadedFile|null>  $documents
     * @param  Collection<int, TeamStaffDocumentRequirement>  $documentRequirements
     * @return array{documents: array<int, array<string, mixed>>, stored_paths: list<string>}
     */
    private function storeDocuments(array $documents, string $folder, Collection $documentRequirements): array
    {
        $storedPaths = [];

        $preparedDocuments = collect($documents)
            ->map(function ($document, int|string $requirementId) use ($folder, $documentRequirements, &$storedPaths) {
                if (! $document instanceof UploadedFile) {
                    return null;
                }

                $documentRequirement = $documentRequirements->get((int) $requirementId);

                if (! $documentRequirement) {
                    return null;
                }

                $path = $document->storeAs(
                    $folder,
                    $documentRequirement->slug.'-'.Str::uuid().'.'.$document->getClientOriginalExtension(),
                    'local',
                );

                $storedPaths[] = $path;

                return [
                    'label' => $documentRequirement->name_kh,
                    'path' => $path,
                    'original_name' => $document->getClientOriginalName(),
                    'uploaded_by' => 'admin',
                    'uploaded_at' => now()->toIso8601String(),
                    'status' => 'Approved',
                    'requirement_id' => $documentRequirement->id,
                    'requirement_slug' => $documentRequirement->slug,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'documents' => $preparedDocuments,
            'stored_paths' => $storedPaths,
        ];
    }

    private function storeAvatar(?UploadedFile $avatar, string $folder): string
    {
        if (! $avatar) {
            abort(422, 'ត្រូវបញ្ចូលរូបភាពប្រវត្តិរូប។');
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
     * @param  array<int, array<string, mixed>>  $currentDocuments
     * @param  array<int, array<string, mixed>>  $uploadedDocuments
     * @return array{documents: array<int, array<string, mixed>>, replaced_paths: list<string>}
     */
    private function mergeDocuments(array $currentDocuments, array $uploadedDocuments): array
    {
        return [
            'documents' => collect($currentDocuments)
                ->concat($uploadedDocuments)
                ->values()
                ->all(),
            'replaced_paths' => [],
        ];
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

    /**
     * @param  list<string>  $paths
     */
    private function deletePaths(array $paths): void
    {
        collect($paths)
            ->filter()
            ->each(fn (string $path) => Storage::disk('local')->delete($path));
    }

    private function nextSequenceNo(bool $lock = false): int
    {
        $query = TeamStaff::query();

        if ($lock) {
            $query->lockForUpdate();
        }

        return (int) $query->max('sequence_no') + 1;
    }

    private function normalizeIdNumber(mixed $value): ?string
    {
        $normalized = trim($this->convertLocalizedDigits((string) $value));

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizePhoneNumber(mixed $value): string
    {
        $normalized = $this->convertLocalizedDigits((string) $value);
        $normalized = preg_replace('/[\s\-()]+/', '', $normalized) ?? '';

        return trim($normalized);
    }

    private function convertLocalizedDigits(string $value): string
    {
        return strtr($value, [
            '០' => '0',
            '១' => '1',
            '២' => '2',
            '៣' => '3',
            '៤' => '4',
            '៥' => '5',
            '៦' => '6',
            '៧' => '7',
            '៨' => '8',
            '៩' => '9',
        ]);
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
        return $this->activeDocumentRequirements()
            ->pluck('name_kh')
            ->filter()
            ->unique()
            ->values();
    }

    private function activeDocumentRequirements(): Collection
    {
        return TeamStaffDocumentRequirement::query()
            ->where('is_active', true)
            ->ordered()
            ->get();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function documentForRequirement(
        TeamStaff $teamStaff,
        TeamStaffDocumentRequirement $documentRequirement,
    ): ?array {
        $document = collect($teamStaff->documents ?? [])
            ->first(fn (array $entry) => ($entry['requirement_slug'] ?? null) === $documentRequirement->slug);

        if (! $document || empty($document['path']) || ! Storage::disk('local')->exists($document['path'])) {
            return null;
        }

        return $document;
    }
}
