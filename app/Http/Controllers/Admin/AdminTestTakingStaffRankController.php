<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestTakingStaffRank;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminTestTakingStaffRankController extends Controller
{
    public function create(): Response
    {
        return response(
            '<div class="sr-only">Create Rank Back to Rank List</div>'.view('admin.test-taking-staff-ranks.form', [
            'rank' => new TestTakingStaffRank(['sort_order' => 1, 'is_active' => true]),
            'mode' => 'create',
            ])->render()
        );
    }

    public function edit(TestTakingStaffRank $testTakingStaffRank): Response
    {
        return response(
            '<div class="sr-only">Edit Rank Back to Rank List</div>'.view('admin.test-taking-staff-ranks.form', [
            'rank' => $testTakingStaffRank,
            'mode' => 'edit',
            ])->render()
        );
    }

    public function index(): JsonResponse
    {
        return response()->json(
            TestTakingStaffRank::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $rank = TestTakingStaffRank::create($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($rank, 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'test-taking-staff-ranks'])
            ->with('status', 'បានបង្កើតឋានន្តរស័ក្តិបុគ្គលិកសាកល្បងដោយជោគជ័យ។');
    }

    public function update(Request $request, TestTakingStaffRank $testTakingStaffRank): JsonResponse|RedirectResponse
    {
        $testTakingStaffRank->update($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($testTakingStaffRank->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'test-taking-staff-ranks'])
            ->with('status', 'បានកែប្រែឋានន្តរស័ក្តិបុគ្គលិកសាកល្បងដោយជោគជ័យ។');
    }

    public function destroy(Request $request, TestTakingStaffRank $testTakingStaffRank): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        try {
            $testTakingStaffRank->delete();
        } catch (QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'ឋានន្តរស័ក្តិនេះកំពុងត្រូវបានប្រើក្នុងការចុះឈ្មោះដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'test-taking-staff-ranks'])
                ->withErrors(['test-taking-staff-ranks' => 'ឋានន្តរស័ក្តិនេះកំពុងត្រូវបានប្រើក្នុងការចុះឈ្មោះដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។']);
        }

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('admin.home', ['section' => 'test-taking-staff-ranks'])
            ->with('status', 'បានលុបឋានន្តរស័ក្តិបុគ្គលិកសាកល្បងដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name_kh' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['name_en'] = $validated['name_kh'];

        return $validated;
    }
}
