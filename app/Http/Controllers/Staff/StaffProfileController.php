<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\TeamStaff;
use App\Models\TeamStaffDocumentRequirement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffProfileController extends Controller
{
    public function show(Request $request): View
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');
        $documents = collect($staff->documents ?? [])->values();
        $documentRequirements = TeamStaffDocumentRequirement::query()
            ->where('is_active', true)
            ->ordered()
            ->get();

        return view('staff.profile', [
            'staff' => $staff,
            'documents' => $documents,
            'documentRequirements' => $documentRequirements,
            'profileCompletion' => $this->profileCompletion($staff),
        ]);
    }

    public function avatar(Request $request): BinaryFileResponse
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');

        abort_unless($staff->hasStoredAvatar(), 404);

        return response()->file(Storage::disk('local')->path($staff->avatar_path));
    }

    public function storeDocument(Request $request): RedirectResponse
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');

        $validated = $request->validate([
            'document_requirement_id' => [
                'nullable',
                'integer',
                Rule::exists('team_staff_document_requirements', 'id')->where('is_active', true),
            ],
            'document_title' => ['nullable', 'string', 'max:255'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        $documentFile = $validated['document_file'];
        abort_unless($documentFile instanceof UploadedFile, 422);
        $documentRequirement = filled($validated['document_requirement_id'] ?? null)
            ? TeamStaffDocumentRequirement::query()->find($validated['document_requirement_id'])
            : null;
        $documentTitle = trim((string) ($validated['document_title'] ?? ''));

        if (! $documentRequirement && $documentTitle === '') {
            throw ValidationException::withMessages([
                'document_requirement_id' => 'Select a document type or enter a document title.',
            ]);
        }

        $documentPrefix = $documentRequirement?->slug ?: Str::slug($documentTitle);
        $documentPrefix = $documentPrefix !== '' ? $documentPrefix : 'document';

        $path = $documentFile->storeAs(
            'team-staff/'.$staff->id.'/documents',
            $documentPrefix.'-'.Str::uuid().'.'.$documentFile->getClientOriginalExtension(),
            'local',
        );

        $documentPayload = [
            'label' => $documentRequirement?->name_kh ?? $documentTitle,
            'path' => $path,
            'original_name' => $documentFile->getClientOriginalName(),
            'uploaded_by' => 'staff',
            'uploaded_at' => now()->toIso8601String(),
            'status' => 'Pending',
        ];

        if ($documentRequirement) {
            $documentPayload['requirement_id'] = $documentRequirement->id;
            $documentPayload['requirement_slug'] = $documentRequirement->slug;
        }

        $documents = collect($staff->documents ?? [])
            ->push($documentPayload)
            ->values()
            ->all();

        try {
            $staff->update([
                'documents' => $documents,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()->route('staff.profile.show')
            ->with('status', 'Document uploaded successfully.');
    }

    public function downloadDocument(Request $request, int $documentIndex): StreamedResponse
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');
        $document = collect($staff->documents ?? [])->values()->get($documentIndex);

        abort_unless($document && ! empty($document['path']) && Storage::disk('local')->exists($document['path']), 404);

        return Storage::disk('local')->download(
            $document['path'],
            $document['original_name'] ?? basename($document['path']),
        );
    }

    public function showDocument(Request $request, int $documentIndex)
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');
        $document = collect($staff->documents ?? [])->values()->get($documentIndex);

        abort_unless($document && ! empty($document['path']) && Storage::disk('local')->exists($document['path']), 404);

        return Storage::disk('local')->response($document['path']);
    }

    public function destroyDocument(Request $request, int $documentIndex): RedirectResponse
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');
        $documents = collect($staff->documents ?? [])->values();
        $document = $documents->get($documentIndex);

        abort_unless($document, 404);
        abort_unless(($document['uploaded_by'] ?? 'admin') === 'staff', 403);

        if (! empty($document['path'])) {
            Storage::disk('local')->delete($document['path']);
        }

        $staff->update([
            'documents' => $documents
                ->reject(fn (array $entry, int $index) => $index === $documentIndex)
                ->values()
                ->all(),
        ]);

        return redirect()->route('staff.profile.show')
            ->with('status', 'Document deleted successfully.');
    }

    private function profileCompletion(TeamStaff $staff): int
    {
        $fields = [
            $staff->avatar_path,
            $staff->name_kh,
            $staff->name_latin,
            $staff->id_number,
            $staff->gender,
            $staff->position,
            $staff->military_rank,
            $staff->role,
            $staff->phone_number,
        ];

        $completed = collect($fields)->filter(fn ($value) => filled($value))->count();

        return (int) round(($completed / count($fields)) * 100);
    }
}
