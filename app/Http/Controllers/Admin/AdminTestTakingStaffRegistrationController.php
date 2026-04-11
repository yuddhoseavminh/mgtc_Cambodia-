<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestTakingStaffRegistration;
use App\Models\TestTakingStaffRegistrationDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTestTakingStaffRegistrationController extends Controller
{
    public function avatar(TestTakingStaffRegistration $testTakingStaffRegistration): BinaryFileResponse
    {
        abort_unless($testTakingStaffRegistration->hasStoredAvatar(), 404);

        return response()->file(Storage::disk('local')->path($testTakingStaffRegistration->avatar_path));
    }

    public function downloadDocument(
        TestTakingStaffRegistration $testTakingStaffRegistration,
        TestTakingStaffRegistrationDocument $document,
    ): StreamedResponse {
        abort_unless(
            $document->test_taking_staff_registration_id === $testTakingStaffRegistration->id,
            404
        );

        abort_unless(
            $document->file_path
            && Storage::disk('local')->exists($document->file_path),
            404
        );

        return Storage::disk('local')->download(
            $document->file_path,
            $document->original_name ?? basename($document->file_path)
        );
    }

    public function showDocument(
        TestTakingStaffRegistration $testTakingStaffRegistration,
        TestTakingStaffRegistrationDocument $document,
    ): BinaryFileResponse {
        abort_unless(
            $document->test_taking_staff_registration_id === $testTakingStaffRegistration->id,
            404
        );

        abort_unless(
            $document->file_path
            && Storage::disk('local')->exists($document->file_path),
            404
        );

        return response()->file(Storage::disk('local')->path($document->file_path));
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
            'document_file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        if ($document->file_path) {
            Storage::disk('local')->delete($document->file_path);
        }

        $file = $request->file('document_file');
        
        $document->update([
            'file_path' => $file->store('test-taking-staff/documents', 'local'),
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
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('status', 'បានលុបឯកសារដោយជោគជ័យ។');
    }

    public function show(TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Contracts\View\View
    {
        $testTakingStaffRegistration->load(['rank', 'documents.documentRequirement']);

        return view('admin.test-taking-staff-registration-show', [
            'registration' => $testTakingStaffRegistration,
        ]);
    }

    public function edit(TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Contracts\View\View
    {
        $testTakingStaffRegistration->load(['rank']);

        return view('admin.test-taking-staff-registration-edit', [
            'registration' => $testTakingStaffRegistration,
            'ranks' => \App\Models\TestTakingStaffRank::all(),
        ]);
    }

    public function update(\Illuminate\Http\Request $request, TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'test_taking_staff_rank_id' => ['nullable', 'exists:test_taking_staff_ranks,id'],
            'name_kh' => ['required', 'string', 'max:255'],
            'name_latin' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'military_service_day' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:255'],
        ]);

        $testTakingStaffRegistration->update($validated);

        return redirect()
            ->route('admin.test-taking-staff-registrations.show', $testTakingStaffRegistration)
            ->with('status', 'បានកែប្រែព័ត៌មានដោយជោគជ័យ។');
    }

    public function destroy(TestTakingStaffRegistration $testTakingStaffRegistration): \Illuminate\Http\RedirectResponse
    {
        if ($testTakingStaffRegistration->hasStoredAvatar()) {
            Storage::disk('local')->delete($testTakingStaffRegistration->avatar_path);
        }
        
        foreach ($testTakingStaffRegistration->documents as $doc) {
            if ($doc->file_path) {
                Storage::disk('local')->delete($doc->file_path);
            }
        }

        $testTakingStaffRegistration->delete();

        return redirect()
            ->route('admin.home', ['section' => 'register-staff'])
            ->with('status', 'បានលុបកំណត់ត្រាដោយជោគជ័យ។');
    }
}
