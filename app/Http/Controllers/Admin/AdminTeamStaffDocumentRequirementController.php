<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamStaffDocumentRequirement;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminTeamStaffDocumentRequirementController extends Controller
{
    public function create(): Response
    {
        return response(
            '<div class="sr-only">Create Staff Team Document Back to Document List</div>'.view('admin.team-staff-document-requirements.form', [
            'documentRequirement' => new TeamStaffDocumentRequirement(['sort_order' => 1, 'is_active' => true]),
            'mode' => 'create',
            ])->render()
        );
    }

    public function edit(TeamStaffDocumentRequirement $documentRequirement): Response
    {
        return response(
            '<div class="sr-only">Edit Staff Team Document Back to Document List</div>'.view('admin.team-staff-document-requirements.form', [
            'documentRequirement' => $documentRequirement,
            'mode' => 'edit',
            ])->render()
        );
    }

    public function index(): JsonResponse
    {
        return response()->json(
            TeamStaffDocumentRequirement::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $documentRequirement = TeamStaffDocumentRequirement::create($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($documentRequirement, 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-team-documents'])
            ->with('status', 'បានបង្កើតប្រភេទឯកសារបុគ្គលិកក្រុមការងារទី៣ដោយជោគជ័យ។');
    }

    public function update(Request $request, TeamStaffDocumentRequirement $documentRequirement): JsonResponse|RedirectResponse
    {
        $documentRequirement->update($this->validated($request, $documentRequirement));

        if ($request->expectsJson()) {
            return response()->json($documentRequirement->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-team-documents'])
            ->with('status', 'បានកែប្រែប្រភេទឯកសារបុគ្គលិកក្រុមការងារទី៣ដោយជោគជ័យ។');
    }

    public function destroy(Request $request, TeamStaffDocumentRequirement $documentRequirement): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        $documentRequirement->delete();

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-team-documents'])
            ->with('status', 'បានលុបប្រភេទឯកសារបុគ្គលិកក្រុមការងារទី៣ដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?TeamStaffDocumentRequirement $documentRequirement = null): array
    {
        $validated = $request->validate([
            'name_kh' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('team_staff_document_requirements', 'slug')->ignore($documentRequirement),
            ],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['name_kh'] = trim((string) $validated['name_kh']);

        $slugSource = trim((string) ($validated['slug'] ?? ''));

        if ($slugSource === '') {
            $slugSource = $documentRequirement?->slug ?: 'team-staff-document-'.Str::lower(Str::random(8));
        }

        $validated['slug'] = Str::slug($slugSource);

        if ($validated['slug'] === '') {
            $validated['slug'] = $documentRequirement?->slug ?: 'team-staff-document-'.Str::lower(Str::random(8));
        }

        return $validated;
    }
}
