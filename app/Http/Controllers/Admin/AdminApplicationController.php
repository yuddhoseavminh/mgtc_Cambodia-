<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min($request->integer('per_page', 100), 200));
        $applications = Application::query()
            ->with([
                'rank:id,name_kh,name_en',
                'course:id,name',
                'culturalLevel:id,name',
            ])
            ->latest('submitted_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $applications->getCollection()
                ->map(fn (Application $application) => $this->serializeApplication($application))
                ->values(),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
            ],
        ]);
    }

    public function show(Request $request, Application $application): JsonResponse|View
    {
        $application->load([
            'rank:id,name_kh,name_en',
            'course:id,name,description,duration',
            'culturalLevel:id,name',
            'applicationDocuments.documentRequirement:id,name_kh,name_en,slug',
        ]);

        if ($request->expectsJson()) {
            return response()->json(
                $this->serializeApplication($application, true)
            );
        }

        return view('admin.application-show', [
            'application' => $application,
            'statuses' => config('military-registration.application_statuses'),
        ]);
    }

    public function update(Request $request, Application $application): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(config('military-registration.application_statuses'))],
            'admin_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $application->update($validated);
        $application->load([
            'rank:id,name_kh,name_en',
            'course:id,name,description,duration',
            'culturalLevel:id,name',
            'applicationDocuments.documentRequirement:id,name_kh,name_en,slug',
        ]);

        if ($request->expectsJson()) {
            return response()->json(
                $this->serializeApplication($application, true)
            );
        }

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('status', 'បានកែប្រែការពិនិត្យពាក្យស្នើសុំដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeApplication(Application $application, bool $detailed = false): array
    {
        $base = [
            'id' => $application->id,
            'applicant_name' => $application->khmer_name,
            'latin_name' => $application->latin_name,
            'id_number' => $application->id_number,
            'rank' => $application->rank ? [
                'id' => $application->rank->id,
                'name_kh' => $application->rank->name_kh,
                'name_en' => $application->rank->name_en,
            ] : null,
            'gender' => $application->gender,
            'course' => $application->course ? [
                'id' => $application->course->id,
                'name' => $application->course->name,
                'description' => $application->course->description,
                'duration' => $application->course->duration,
            ] : null,
            'cultural_level' => $application->culturalLevel ? [
                'id' => $application->culturalLevel->id,
                'name' => $application->culturalLevel->name,
            ] : null,
            'unit' => $application->unit,
            'phone_number' => $application->phone_number,
            'status' => $application->status,
            'submitted_at' => $application->submitted_at?->toIso8601String(),
        ];

        if (! $detailed) {
            return $base;
        }

        return [
            ...$base,
            'khmer_name' => $application->khmer_name,
            'gender' => $application->gender,
            'date_of_birth' => $application->date_of_birth?->toDateString(),
            'date_of_enlistment' => $application->date_of_enlistment?->toDateString(),
            'position' => $application->position,
            'place_of_birth' => $application->place_of_birth,
            'current_address' => $application->current_address,
            'family_situation' => $application->family_situation,
            'admin_notes' => $application->admin_notes,
            'documents' => collect($application->documents())
                ->map(fn (array $document) => [
                    'id' => $document['id'] ?? null,
                    'type' => $document['type'],
                    'label' => $document['label'],
                    'name' => $document['name'],
                    'view_url' => $document['source'] === 'managed' && isset($document['id'])
                        ? route('admin.documents.show', [$application, $document['id']])
                        : null,
                    'download_url' => $document['source'] === 'managed' && isset($document['id'])
                        ? route('admin.documents.download', [$application, $document['id']])
                        : null,
                ])
                ->values(),
        ];
    }
}
