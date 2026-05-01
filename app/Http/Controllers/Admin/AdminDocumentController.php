<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\DocumentRequirement;
use App\Support\UploadStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDocumentController extends Controller
{
    public function store(Request $request, Application $application): RedirectResponse
    {
        $validated = $request->validate([
            'document_requirement_id' => ['required', 'exists:document_requirements,id'],
            'document_file' => ['required', 'file', 'max:51200', 'mimes:pdf,jpg,jpeg,png,doc,docx,webp'],
        ]);

        $requirement = DocumentRequirement::query()->findOrFail($validated['document_requirement_id']);
        $file = $request->file('document_file');

        $application->applicationDocuments()->create([
            'document_requirement_id' => $requirement->id,
            'status' => ApplicationDocument::STATUS_HAVE,
            'file_path' => UploadStorage::store($file, 'applications/documents'),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return back()->with('status', 'បានបន្ថែមឯកសារដោយជោគជ័យ។');
    }

    public function update(
        Request $request,
        Application $application,
        int $applicationDocument,
    ): RedirectResponse|JsonResponse {
        $applicationDocumentModel = $application->applicationDocuments()->find($applicationDocument);

        if (! $applicationDocumentModel) {
            return $this->documentNotFoundResponse($request, $application, $applicationDocument);
        }

        $request->validate([
            'document_file' => ['required', 'file', 'max:51200', 'mimes:pdf,jpg,jpeg,png,doc,docx,webp'],
        ]);

        if ($applicationDocumentModel->file_path) {
            UploadStorage::delete($applicationDocumentModel->file_path);
        }

        $file = $request->file('document_file');

        $applicationDocumentModel->update([
            'status' => ApplicationDocument::STATUS_HAVE,
            'file_path' => UploadStorage::store($file, 'applications/documents'),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return back()->with('status', 'បានជំនួសឯកសារដោយជោគជ័យ។');
    }

    public function destroy(Request $request, Application $application, int $applicationDocument): RedirectResponse|JsonResponse
    {
        $applicationDocumentModel = $application->applicationDocuments()->find($applicationDocument);

        if (! $applicationDocumentModel) {
            return $this->documentNotFoundResponse($request, $application, $applicationDocument);
        }

        if ($applicationDocumentModel->file_path) {
            UploadStorage::delete($applicationDocumentModel->file_path);
        }

        $applicationDocumentModel->delete();

        return back()->with('status', 'បានលុបឯកសារដោយជោគជ័យ។');
    }

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

        return UploadStorage::readDisk($applicationDocument->file_path)
            ->response($applicationDocument->file_path, $applicationDocument->original_name);
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

        return UploadStorage::readDisk($applicationDocument->file_path)
            ->download($applicationDocument->file_path, $applicationDocument->original_name);
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
            && UploadStorage::exists($applicationDocument->file_path);
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

    private function documentNotFoundResponse(
        Request $request,
        Application $application,
        int $applicationDocumentId,
    ): RedirectResponse|JsonResponse {
        Log::warning('Application document not found for update/delete.', [
            'application_id' => $application->getKey(),
            'application_document_id' => $applicationDocumentId,
        ]);

        $message = 'The selected document was not found for this application.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 404);
        }

        return back()->withErrors(['documents' => $message]);
    }
}
