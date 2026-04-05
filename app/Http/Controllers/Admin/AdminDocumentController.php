<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDocumentController extends Controller
{
    public function show(Application $application, int $applicationDocument): StreamedResponse|View
    {
        $applicationDocumentId = $applicationDocument;
        $applicationDocument = $this->resolveApplicationDocument($application, $applicationDocumentId);

        if (! $applicationDocument) {
            return $this->missingDocumentView(
                $application,
                new ApplicationDocument(['id' => $applicationDocumentId]),
                'preview',
            );
        }

        if (! $this->documentIsAvailable($applicationDocument)) {
            return $this->missingDocumentView($application, $applicationDocument, 'preview');
        }

        return Storage::disk('local')->response($applicationDocument->file_path, $applicationDocument->original_name);
    }

    public function download(Application $application, int $applicationDocument): StreamedResponse|View
    {
        $applicationDocumentId = $applicationDocument;
        $applicationDocument = $this->resolveApplicationDocument($application, $applicationDocumentId);

        if (! $applicationDocument) {
            return $this->missingDocumentView(
                $application,
                new ApplicationDocument(['id' => $applicationDocumentId]),
                'download',
            );
        }

        if (! $this->documentIsAvailable($applicationDocument)) {
            return $this->missingDocumentView($application, $applicationDocument, 'download');
        }

        return Storage::disk('local')->download($applicationDocument->file_path, $applicationDocument->original_name);
    }

    private function missingDocumentView(
        Application $application,
        ApplicationDocument $applicationDocument,
        string $mode,
    ): View {
        return view('admin.documents.missing', [
            'application' => $application,
            'applicationDocument' => $applicationDocument,
            'mode' => $mode,
        ]);
    }

    private function documentIsAvailable(ApplicationDocument $applicationDocument): bool
    {
        return filled($applicationDocument->file_path)
            && Storage::disk('local')->exists($applicationDocument->file_path);
    }

    private function resolveApplicationDocument(
        Application $application,
        int $applicationDocumentId,
    ): ?ApplicationDocument {
        $applicationDocument = $application->applicationDocuments()
            ->with('documentRequirement')
            ->find($applicationDocumentId);

        if (! $applicationDocument) {
            Log::warning('Application document lookup failed.', [
                'application_id' => $application->getKey(),
                'application_document_id' => $applicationDocumentId,
            ]);
        }

        return $applicationDocument;
    }
}
