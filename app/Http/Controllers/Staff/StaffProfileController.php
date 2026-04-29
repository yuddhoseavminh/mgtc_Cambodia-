<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PortalContent;
use App\Models\TeamStaff;
use App\Models\TeamStaffDocumentRequirement;
use App\Support\UploadStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
            'portalContent' => PortalContent::query()->first(),
        ]);
    }

    public function avatar(Request $request): StreamedResponse
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');

        abort_unless($staff->hasStoredAvatar(), 404);

        return UploadStorage::readDisk($staff->avatar_path)
            ->response($staff->avatar_path);
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
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'document_files' => ['nullable', 'array', 'min:1'],
            'document_files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        $documentFiles = collect($request->file('document_files', []))
            ->flatten()
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->values();

        if ($documentFiles->isEmpty()) {
            $singleDocumentFile = $validated['document_file'] ?? null;
            if ($singleDocumentFile instanceof UploadedFile) {
                $documentFiles = collect([$singleDocumentFile]);
            }
        }

        if ($documentFiles->isEmpty()) {
            throw ValidationException::withMessages([
                'document_files' => 'Please select at least one document file.',
            ]);
        }

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

        $storedPaths = [];
        $newDocuments = [];

        try {
            foreach ($documentFiles as $documentFile) {
                $path = UploadStorage::storeAs(
                    $documentFile,
                    'team-staff/'.$staff->id.'/documents',
                    $documentPrefix.'-'.Str::uuid().'.'.$documentFile->getClientOriginalExtension(),
                );
                $storedPaths[] = $path;

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

                $newDocuments[] = $documentPayload;
            }

            $documents = collect($staff->documents ?? [])
                ->concat($newDocuments)
                ->values()
                ->all();

            $staff->update([
                'documents' => $documents,
            ]);
        } catch (\Throwable $exception) {
            if ($storedPaths !== []) {
                UploadStorage::delete($storedPaths);
            }

            throw $exception;
        }

        return redirect()->route('staff.profile.show')
            ->with('status', count($newDocuments) > 1 ? 'Documents uploaded successfully.' : 'Document uploaded successfully.');
    }

    public function downloadDocument(Request $request, int $documentIndex): StreamedResponse
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');
        $document = collect($staff->documents ?? [])->values()->get($documentIndex);
        $path = is_array($document) ? $this->documentPath($document) : null;

        abort_unless($document && $path && UploadStorage::exists($path), 404);

        return UploadStorage::readDisk($path)->download(
            $path,
            $this->documentOriginalName($document, $path),
        );
    }

    public function showDocument(Request $request, int $documentIndex)
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');
        $document = collect($staff->documents ?? [])->values()->get($documentIndex);
        $path = is_array($document) ? $this->documentPath($document) : null;

        abort_unless($document && $path && UploadStorage::exists($path), 404);

        return UploadStorage::readDisk($path)->response($path);
    }

    public function destroyDocument(Request $request, int $documentIndex): RedirectResponse
    {
        /** @var TeamStaff $staff */
        $staff = $request->user('staff');
        $documents = collect($staff->documents ?? [])->values();
        $document = $documents->get($documentIndex);

        abort_unless($document, 404);
        abort_unless(($document['uploaded_by'] ?? 'admin') === 'staff', 403);

        $status = strtolower((string) ($document['status'] ?? 'pending'));
        if ($status === 'approved') {
            return redirect()->route('staff.profile.show')
                ->withErrors(['documents' => 'Approved documents cannot be deleted.']);
        }

        $path = is_array($document) ? $this->documentPath($document) : null;
        if ($path) {
            UploadStorage::delete($path);
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
            $staff->dob,
            $staff->date_of_enlistment,
            $staff->pob,
            $staff->training_code,
            $staff->leader_ref,
            $staff->origin_ref,
        ];

        $completed = collect($fields)->filter(fn ($value) => filled($value))->count();

        return (int) round(($completed / count($fields)) * 100);
    }

    private function documentPath(array $document): ?string
    {
        $path = $document['path'] ?? $document['file_path'] ?? null;

        return filled($path) ? (string) $path : null;
    }

    private function documentOriginalName(array $document, string $path): string
    {
        $originalName = $document['original_name'] ?? null;

        return filled($originalName) ? (string) $originalName : basename($path);
    }
}
