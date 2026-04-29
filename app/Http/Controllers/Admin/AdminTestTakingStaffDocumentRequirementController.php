<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestTakingStaffDocumentRequirement;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminTestTakingStaffDocumentRequirementController extends Controller
{
    public function create(): Response
    {
        return response(
            '<div class="sr-only">Create Document Back to Document List</div>'.view('admin.test-taking-staff-document-requirements.form', [
            'documentRequirement' => new TestTakingStaffDocumentRequirement([
                'sort_order' => 1,
                'is_active' => true,
                'send_to_telegram' => true,
            ]),
            'mode' => 'create',
            ])->render()
        );
    }

    public function edit(TestTakingStaffDocumentRequirement $documentRequirement): Response
    {
        return response(
            '<div class="sr-only">Edit Document Back to Document List</div>'.view('admin.test-taking-staff-document-requirements.form', [
            'documentRequirement' => $documentRequirement,
            'mode' => 'edit',
            ])->render()
        );
    }

    public function index(): JsonResponse
    {
        return response()->json(
            TestTakingStaffDocumentRequirement::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $documentRequirement = TestTakingStaffDocumentRequirement::create($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($documentRequirement, 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'test-taking-staff-documents'])
            ->with('status', 'បានបង្កើតតម្រូវការឯកសារបុគ្គលិកសាកល្បងដោយជោគជ័យ។');
    }

    public function update(Request $request, TestTakingStaffDocumentRequirement $documentRequirement): JsonResponse|RedirectResponse
    {
        $documentRequirement->update($this->validated($request, $documentRequirement));

        if ($request->expectsJson()) {
            return response()->json($documentRequirement->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'test-taking-staff-documents'])
            ->with('status', 'បានកែប្រែតម្រូវការឯកសារបុគ្គលិកសាកល្បងដោយជោគជ័យ។');
    }

    public function destroy(Request $request, TestTakingStaffDocumentRequirement $documentRequirement): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        try {
            $documentRequirement->delete();
        } catch (QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'តម្រូវការឯកសារនេះកំពុងត្រូវបានប្រើក្នុងការចុះឈ្មោះដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'test-taking-staff-documents'])
                ->withErrors(['test-taking-staff-documents' => 'តម្រូវការឯកសារនេះកំពុងត្រូវបានប្រើក្នុងការចុះឈ្មោះដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។']);
        }

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('admin.home', ['section' => 'test-taking-staff-documents'])
            ->with('status', 'បានលុបតម្រូវការឯកសារបុគ្គលិកសាកល្បងដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(
        Request $request,
        ?TestTakingStaffDocumentRequirement $testTakingStaffDocumentRequirement = null,
    ): array {
        $validated = $request->validate([
            'name_kh' => ['required', 'string', 'max:255'],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('test_taking_staff_document_requirements', 'slug')->ignore($testTakingStaffDocumentRequirement),
            ],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'send_to_telegram' => ['required', 'boolean'],
        ]);

        $validated['name_en'] = $validated['name_kh'];
        $validated['slug'] = Str::slug(($validated['slug'] ?? null) ?: $validated['name_kh']);
        
        if (empty($validated['slug'])) {
            $validated['slug'] = 'tt-doc-' . Str::lower(Str::random(8));
        }

        return $validated;
    }
}
