<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestTakingStaffRegistration;
use App\Models\TestTakingStaffRegistrationDocument;
use App\Support\UploadStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTestTakingStaffRegistrationController extends Controller
{
    public function avatar(TestTakingStaffRegistration $testTakingStaffRegistration): StreamedResponse
    {
        abort_unless($testTakingStaffRegistration->hasStoredAvatar(), 404);

        $response = UploadStorage::readDisk($testTakingStaffRegistration->avatar_path)
            ->response($testTakingStaffRegistration->avatar_path)
            ->setPrivate()
            ->setMaxAge(0);

        $response->headers->add([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);

        return $response;
    }

    public function downloadDocument(
        TestTakingStaffRegistration $testTakingStaffRegistration,
        int $document,
    ): StreamedResponse|View {
        $documentId = $document;
        $document = $this->resolveRegistrationDocument($testTakingStaffRegistration, $documentId);

        if (! $document || ! $this->documentIsAvailable($document)) {
            return $this->missingDocumentView($testTakingStaffRegistration, $documentId, $document, 'download');
        }

        return UploadStorage::readDisk($document->file_path)
            ->download($document->file_path, $document->original_name ?? basename($document->file_path));
    }

    public function showDocument(
        TestTakingStaffRegistration $testTakingStaffRegistration,
        int $document,
    ): StreamedResponse|View {
        $documentId = $document;
        $document = $this->resolveRegistrationDocument($testTakingStaffRegistration, $documentId);

        if (! $document || ! $this->documentIsAvailable($document)) {
            return $this->missingDocumentView($testTakingStaffRegistration, $documentId, $document, 'preview');
        }

        return UploadStorage::readDisk($document->file_path)
            ->response($document->file_path, $document->original_name ?? basename($document->file_path));
    }

    public function updateDocument(
        \Illuminate\Http\Request $request,
        TestTakingStaffRegistration $testTakingStaffRegistration,
        TestTakingStaffRegistrationDocument $document
    ): \Illuminate\Http\RedirectResponse {
        abort_unless(
            $document->test_taking_staff_registration_id === $testTakingStaffRegistration->id,
            404
        );

        $request->validate([
            'document_file' => ['required', 'file', 'max:51200', 'mimes:pdf,jpg,jpeg,png,doc,docx,webp'],
        ]);

        if ($document->file_path) {
            UploadStorage::delete($document->file_path);
        }

        $file = $request->file('document_file');

        $document->update([
            'file_path' => UploadStorage::store($file, 'test-taking-staff/documents'),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return back()->with('status', 'បានកែប្រែឯកសារដោយជោគជ័យ។');
    }

    public function destroyDocument(
        TestTakingStaffRegistration $testTakingStaffRegistration,
        TestTakingStaffRegistrationDocument $document
    ): \Illuminate\Http\RedirectResponse {
        abort_unless(
            $document->test_taking_staff_registration_id === $testTakingStaffRegistration->id,
            404
        );

        if ($document->file_path) {
            UploadStorage::delete($document->file_path);
        }

        $document->delete();

        return back()->with('status', 'បានលុបឯកសារដោយជោគជ័យ។');
    }

    public function storeDocument(
        \Illuminate\Http\Request $request,
        TestTakingStaffRegistration $testTakingStaffRegistration
    ): \Illuminate\Http\RedirectResponse {
        $request->validate([
            'test_taking_staff_document_requirement_id' => ['required', 'exists:test_taking_staff_document_requirements,id'],
            'document_file' => ['required', 'file', 'max:51200', 'mimes:pdf,jpg,jpeg,png,doc,docx,webp'],
        ]);

        $file = $request->file('document_file');

        $testTakingStaffRegistration->documents()->create([
            'test_taking_staff_document_requirement_id' => $request->test_taking_staff_document_requirement_id,
            'file_path' => UploadStorage::store($file, 'test-taking-staff/documents'),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return back()->with('status', 'បានបន្ថែមឯកសារដោយជោគជ័យ។');
    }

    public function show(TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Contracts\View\View
    {
        $testTakingStaffRegistration->load(['rank', 'documents.documentRequirement']);

        return view('admin.test-taking-staff-registration-show', [
            'registration' => $testTakingStaffRegistration,
            'requirements' => \App\Models\TestTakingStaffDocumentRequirement::all(),
        ]);
    }

    public function edit(TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Contracts\View\View
    {
        $testTakingStaffRegistration->load(['rank', 'documents.documentRequirement']);

        return view('admin.test-taking-staff-registration-edit', [
            'registration' => $testTakingStaffRegistration,
            'ranks' => \App\Models\TestTakingStaffRank::all(),
            'requirements' => \App\Models\TestTakingStaffDocumentRequirement::all(),
        ]);
    }

    public function update(\Illuminate\Http\Request $request, TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'test_taking_staff_rank_id' => ['nullable', 'exists:test_taking_staff_ranks,id'],
            'name_kh' => ['required', 'string', 'max:255'],
            'name_latin' => ['nullable', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:100', Rule::unique('test_taking_staff_registrations', 'id_number')->ignore($testTakingStaffRegistration->id)],
            'date_of_birth' => ['nullable', 'date'],
            'military_service_day' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'submitted_at' => ['nullable', 'date'],
            'avatar_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (array_key_exists('submitted_at', $validated)) {
            $validated['submitted_at'] = filled($validated['submitted_at'])
                ? \Carbon\CarbonImmutable::parse($validated['submitted_at'], 'Asia/Phnom_Penh')->timezone('UTC')
                : null;
        }

        unset($validated['avatar_image']);

        $avatarToDelete = null;

        if ($request->hasFile('avatar_image')) {
            $avatar = $request->file('avatar_image');
            $oldAvatarPath = $testTakingStaffRegistration->avatar_path;

            $validated['avatar_path'] = UploadStorage::store($avatar, 'test-taking-staff/avatars');
            $validated['avatar_original_name'] = $avatar->getClientOriginalName();

            if ($oldAvatarPath && $oldAvatarPath !== $validated['avatar_path']) {
                $avatarToDelete = $oldAvatarPath;
            }
        }

        $testTakingStaffRegistration->update($validated);

        if ($avatarToDelete) {
            UploadStorage::delete($avatarToDelete);
        }

        return redirect()
            ->route('admin.home', ['section' => 'register-staff'])
            ->with('status', 'បានកែប្រែព័ត៌មានដោយជោគជ័យ។');
    }

    public function destroy(TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Http\RedirectResponse
    {
        if ($testTakingStaffRegistration->hasStoredAvatar()) {
            UploadStorage::delete($testTakingStaffRegistration->avatar_path);
        }

        foreach ($testTakingStaffRegistration->documents as $doc) {
            if ($doc->file_path) {
                UploadStorage::delete($doc->file_path);
            }
        }

        $testTakingStaffRegistration->delete();

        return redirect()
            ->route('admin.home', ['section' => 'register-staff'])
            ->with('status', 'បានលុបកំណត់ត្រាដោយជោគជ័យ។');
    }

    private function resolveRegistrationDocument(
        TestTakingStaffRegistration $registration,
        int $documentId,
    ): ?TestTakingStaffRegistrationDocument {
        $document = $registration->documents()
            ->with('documentRequirement')
            ->find($documentId);

        if (! $document) {
            Log::warning('Test-taking staff document lookup failed.', [
                'registration_id' => $registration->getKey(),
                'document_id' => $documentId,
            ]);
        }

        return $document;
    }

    private function documentIsAvailable(TestTakingStaffRegistrationDocument $document): bool
    {
        return filled($document->file_path)
            && UploadStorage::exists($document->file_path);
    }

    private function missingDocumentView(
        TestTakingStaffRegistration $registration,
        int $documentId,
        ?TestTakingStaffRegistrationDocument $document,
        string $mode,
    ): View {
        if ($document) {
            Log::warning('Test-taking staff document file missing.', [
                'registration_id' => $registration->getKey(),
                'document_id' => $document->getKey(),
                'file_path' => $document->file_path,
                'uploads_disk' => UploadStorage::diskName(),
                'legacy_uploads_disk' => UploadStorage::legacyDiskName(),
            ]);
        }

        return view('admin.documents.test-taking-staff-missing', [
            'registration' => $registration,
            'document' => $document ?? new TestTakingStaffRegistrationDocument(['id' => $documentId]),
            'mode' => $mode,
        ]);
    }
}
