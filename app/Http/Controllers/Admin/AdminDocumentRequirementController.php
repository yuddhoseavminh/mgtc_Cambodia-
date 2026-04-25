<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminDocumentRequirementController extends Controller
{
    public function create(): Response
    {
        return response(
            '<div class="sr-only">Create Document Requirement Back to Document List</div>'.view('admin.document-requirements.form', [
            'documentRequirement' => new DocumentRequirement(['sort_order' => 1, 'is_active' => true, 'is_protected' => false]),
            'mode' => 'create',
            ])->render()
        );
    }

    public function edit(DocumentRequirement $documentRequirement): Response
    {
        return response(
            '<div class="sr-only">Edit Document Requirement Back to Document List</div>'.view('admin.document-requirements.form', [
            'documentRequirement' => $documentRequirement,
            'mode' => 'edit',
            ])->render()
        );
    }

    public function index(): JsonResponse
    {
        return response()->json(
            DocumentRequirement::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $payload = $this->validated($request);

        $documentRequirement = DB::transaction(function () use ($payload) {
            if ($payload['is_protected']) {
                DocumentRequirement::query()->update(['is_protected' => false]);
            }

            return DocumentRequirement::create($payload);
        });

        if ($request->expectsJson()) {
            return response()->json($documentRequirement, 201);
        }

        return redirect()->route('admin.home', ['section' => 'documents'])->with('status', 'បានបង្កើតតម្រូវការឯកសារដោយជោគជ័យ។');
    }

    public function update(Request $request, DocumentRequirement $documentRequirement): JsonResponse|RedirectResponse
    {
        $payload = $this->validated($request, $documentRequirement);

        DB::transaction(function () use ($documentRequirement, $payload) {
            if ($payload['is_protected']) {
                DocumentRequirement::query()
                    ->whereKeyNot($documentRequirement->getKey())
                    ->update(['is_protected' => false]);
            }

            $documentRequirement->update($payload);
        });

        if ($request->expectsJson()) {
            return response()->json($documentRequirement->fresh());
        }

        return redirect()->route('admin.home', ['section' => 'documents'])->with('status', 'បានកែប្រែតម្រូវការឯកសារដោយជោគជ័យ។');
    }

    public function destroy(Request $request, DocumentRequirement $documentRequirement): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        if ($documentRequirement->isProtectedRequirement()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'តម្រូវការឯកសារនេះត្រូវបានការពារ មិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'documents'])
                ->withErrors(['documents' => 'តម្រូវការឯកសារនេះត្រូវបានការពារ មិនអាចលុបបានទេ។']);
        }

        try {
            $documentRequirement->delete();
        } catch (QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'តម្រូវការឯកសារនេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'documents'])
                ->withErrors(['documents' => 'តម្រូវការឯកសារនេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។']);
        }

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()->route('admin.home', ['section' => 'documents'])->with('status', 'បានលុបតម្រូវការឯកសារដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?DocumentRequirement $documentRequirement = null): array
    {
        $validated = $request->validate([
            'name_kh' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'is_protected' => ['nullable', 'boolean'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('document_requirements', 'slug')->ignore($documentRequirement),
            ],
        ]);

        $validated['name_kh'] = trim((string) $validated['name_kh']);
        $validated['name_en'] = $validated['name_kh'];

        $slugSource = trim((string) ($validated['slug'] ?? ''));

        if ($slugSource === '') {
            $slugSource = $validated['name_kh'] !== ''
                ? $validated['name_kh']
                : ($documentRequirement?->slug ?: 'document-requirement-'.Str::lower(Str::random(8)));
        }

        $validated['slug'] = Str::slug($slugSource);

        if ($validated['slug'] === '') {
            $validated['slug'] = $documentRequirement?->slug ?: 'document-requirement-'.Str::lower(Str::random(8));
        }

        $validated['is_protected'] = filter_var(
            $validated['is_protected'] ?? $documentRequirement?->is_protected ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        return $validated;
    }
}
