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
}
