<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamStaffRank;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminTeamStaffRankController extends Controller
{
    public function create(): View
    {
        return view('admin.team-staff-ranks.form', [
            'rank' => new TeamStaffRank(['sort_order' => 1, 'is_active' => true]),
            'mode' => 'create',
        ]);
    }

    public function edit(TeamStaffRank $teamStaffRank): View
    {
        return view('admin.team-staff-ranks.form', [
            'rank' => $teamStaffRank,
            'mode' => 'edit',
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json(
            TeamStaffRank::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $rank = TeamStaffRank::create($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($rank, 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-team-ranks'])
            ->with('status', 'បានបង្កើតឋានន្តរស័ក្តិយោធាសម្រាប់បុគ្គលិកដោយជោគជ័យ។');
    }

    public function update(Request $request, TeamStaffRank $teamStaffRank): JsonResponse|RedirectResponse
    {
        $teamStaffRank->update($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($teamStaffRank->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-team-ranks'])
            ->with('status', 'បានកែប្រែឋានន្តរស័ក្តិយោធាសម្រាប់បុគ្គលិកដោយជោគជ័យ។');
    }

    public function destroy(Request $request, TeamStaffRank $teamStaffRank): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        $teamStaffRank->delete();

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('admin.home', ['section' => 'staff-team-ranks'])
            ->with('status', 'បានលុបឋានន្តរស័ក្តិយោធាសម្រាប់បុគ្គលិកដោយជោគជ័យ។');
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

        $validated['name_kh'] = trim((string) $validated['name_kh']);

        return $validated;
    }
}
