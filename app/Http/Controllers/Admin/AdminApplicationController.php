<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Support\UploadStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function edit(Application $application): View
    {
        $application->load([
            'rank:id,name_kh,name_en',
            'course:id,name',
            'culturalLevel:id,name',
            'applicationDocuments.documentRequirement:id,name_kh,name_en,slug,is_protected',
        ]);

        return view('admin.application-edit', [
            'application' => $application,
            ...$this->formOptions(),
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

    public function destroy(Request $request, Application $application): \Illuminate\Http\Response|JsonResponse|RedirectResponse
    {
        $application->loadMissing('applicationDocuments');

        $paths = array_values(array_filter([
            $application->id_card_path,
            $application->family_book_path,
            $application->certificate_path,
            $application->other_document_path,
            ...$application->applicationDocuments
                ->pluck('file_path')
                ->filter()
                ->all(),
        ]));

        DB::transaction(function () use ($application) {
            $application->applicationDocuments()->delete();
            $application->delete();
        });

        UploadStorage::delete($paths);

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('admin.home', ['section' => 'applications'])
            ->with('status', 'បានលុបពាក្យស្នើសុំដោយជោគជ័យ។');
    }

    public function replace(Request $request, Application $application): RedirectResponse
    {
        $validated = $request->validate([
            'khmer_name' => ['required', 'string', 'max:255'],
            'latin_name' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:100'],
            'rank_id' => ['required', Rule::exists('ranks', 'id')],
            'gender' => ['nullable', Rule::in(config('military-registration.genders'))],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'date_of_enlistment' => ['required', 'date', 'before_or_equal:today'],
            'position' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'course_id' => ['required', Rule::exists('courses', 'id')],
            'cultural_level_id' => ['required', Rule::exists('cultural_levels', 'id')],
            'place_of_birth' => ['required', Rule::in(config('military-registration.provinces'))],
            'current_address' => ['required', 'string', 'max:1000'],
            'family_situation' => ['required', Rule::in(config('military-registration.family_situations'))],
            'phone_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'status' => ['required', Rule::in(config('military-registration.application_statuses'))],
            'admin_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $application->update($validated);

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('status', 'បានកែប្រែព័ត៌មានពាក្យស្នើសុំដោយជោគជ័យ។');
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

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'ranks' => \App\Models\Rank::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'courses' => \App\Models\Course::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'culturalLevels' => \App\Models\CulturalLevel::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'statuses' => config('military-registration.application_statuses'),
            'provinces' => config('military-registration.provinces'),
            'provinceLabels' => config('military-registration.province_labels'),
            'familySituations' => config('military-registration.family_situations'),
            'familySituationLabels' => config('military-registration.family_situation_labels'),
            'genders' => config('military-registration.genders'),
            'genderLabels' => config('military-registration.gender_labels'),
            'documentRequirements' => \App\Models\DocumentRequirement::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
        ];
    }
}
