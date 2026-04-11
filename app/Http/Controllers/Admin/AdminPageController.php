<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Course;
use App\Models\CulturalLevel;
use App\Models\DocumentRequirement;
use App\Models\PortalContent;
use App\Models\Rank;
use App\Models\TestTakingStaffDocumentRequirement;
use App\Models\TestTakingStaffRank;
use App\Models\TestTakingStaffRegistration;
use App\Models\TeamStaff;
use App\Models\TeamStaffDocumentRequirement;
use App\Models\TeamStaffRank;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminPageController extends Controller
{
    public function index(Request $request): View
    {
        $sections = [
            'overview',
            'reports',
            'applications',
            'courses',
            'ranks',
            'levels',
            'documents',
            'design-template',
            'course-template',
            'staff-team',
            'staff-team-ranks',
            'staff-team-documents',
            'staff-management',
            'test-taking-staff',
            'test-taking-staff-template',
            'test-taking-staff-ranks',
            'test-taking-staff-documents',
            'register-staff',
            'users',
            'profile',
            'settings',
            'portal-content',
        ];
        $allowedSections = collect($sections)
            ->filter(fn (string $candidate): bool => (bool) $request->user()?->canAccessSection($candidate))
            ->values()
            ->all();
        $section = $request->string('section')->toString();
        if ($section === '') {
            $section = (string) ($request->route('section') ?? '');
        }

        if (! in_array($section, $sections, true) || ! in_array($section, $allowedSections, true)) {
            $section = $allowedSections[0] ?? 'profile';
        }

        if ($section === 'portal-content') {
            $section = 'design-template';
        }

        if ($section === 'settings') {
            $section = 'profile';
        }

        $monthlyWindowStart = now()->subMonths(11)->startOfMonth();
        $counts = $this->monthlyApplicationCounts($monthlyWindowStart);

        $applicationsPerMonth = collect(CarbonPeriod::create($monthlyWindowStart, '1 month', now()->startOfMonth()))
            ->map(function (Carbon $month) use ($counts) {
                $key = $month->format('Y-m');

                return [
                    'month' => $month->format('M Y'),
                    'applications' => $counts->get($key, 0),
                ];
            })
            ->values();

        $search = trim($request->string('search')->toString());
        $statusFilter = $request->string('status')->toString();
        $rankFilter = $request->string('rank')->toString();
        $courseFilter = $request->string('course')->toString();
        $staffSearch = trim($request->string('staff_search')->toString());
        $staffGenderFilter = $request->string('staff_gender')->toString();
        $staffRoleFilter = $request->string('staff_role')->toString();
        $staffPositionFilter = $request->string('staff_position')->toString();
        $registerStaffSearch = trim($request->string('register_staff_search')->toString());
        $registerStaffRankFilter = $request->string('register_staff_rank')->toString();

        $applicationsQuery = Application::query()
            ->with([
                'rank:id,name_kh,name_en',
                'course:id,name',
                'culturalLevel:id,name',
            ])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $innerQuery) use ($search) {
                    $innerQuery
                        ->where('khmer_name', 'like', "%{$search}%")
                        ->orWhere('latin_name', 'like', "%{$search}%")
                        ->orWhere('id_number', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('unit', 'like', "%{$search}%");
                });
            })
            ->when(in_array($statusFilter, config('military-registration.application_statuses'), true), function (Builder $query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($rankFilter !== '', function (Builder $query) use ($rankFilter) {
                $query->where('rank_id', $rankFilter);
            })
            ->when($courseFilter !== '', function (Builder $query) use ($courseFilter) {
                $query->where('course_id', $courseFilter);
            })
            ->latest('submitted_at');

        $applications = (clone $applicationsQuery)
            ->paginate(10)
            ->withQueryString();

        $recentApplications = Application::query()
            ->with(['rank:id,name_kh,name_en', 'course:id,name'])
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        $rankDistribution = Application::query()
            ->leftJoin('ranks', 'ranks.id', '=', 'applications.rank_id')
            ->selectRaw("COALESCE(ranks.name_kh, '-') as rank_name, COUNT(*) as aggregate")
            ->groupBy('rank_name')
            ->orderByDesc('aggregate')
            ->limit(6)
            ->pluck('aggregate', 'rank_name')
            ->map(fn ($count) => (int) $count);

        $currentMonthStart = now()->startOfMonth();
        $previousMonthStart = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();

        $currentMonthApplications = Application::query()
            ->whereBetween('submitted_at', [$currentMonthStart, now()])
            ->count();

        $previousMonthApplications = Application::query()
            ->whereBetween('submitted_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        $trend = $currentMonthApplications - $previousMonthApplications;
        $userCounts = User::query()
            ->selectRaw('is_admin, COUNT(*) as aggregate')
            ->groupBy('is_admin')
            ->pluck('aggregate', 'is_admin');
        $users = $section === 'users'
            ? User::query()->latest()->get()
            : collect();
        $adminUsers = $section === 'users'
            ? $users->where('is_admin', true)->values()
            : collect();
        $registerStaffUsers = $section === 'users'
            ? $users->where('is_admin', false)->values()
            : collect();
        $testTakingStaffRegistrationsQuery = TestTakingStaffRegistration::query()
            ->with([
                'rank:id,name_kh,name_en',
                'documents.documentRequirement:id,name_kh,name_en',
            ])
            ->when($registerStaffSearch !== '', function (Builder $query) use ($registerStaffSearch) {
                $query->where(function (Builder $innerQuery) use ($registerStaffSearch) {
                    $innerQuery
                        ->where('name_kh', 'like', "%{$registerStaffSearch}%")
                        ->orWhere('name_latin', 'like', "%{$registerStaffSearch}%")
                        ->orWhere('phone_number', 'like', "%{$registerStaffSearch}%");
                });
            })
            ->when($registerStaffRankFilter !== '', function (Builder $query) use ($registerStaffRankFilter) {
                $query->where('test_taking_staff_rank_id', $registerStaffRankFilter);
            })
            ->latest('submitted_at')
            ->latest('created_at');
        $testTakingStaffRegistrations = (clone $testTakingStaffRegistrationsQuery)
            ->paginate(10, ['*'], 'register_staff_page')
            ->withQueryString();
        $teamStaffQuery = TeamStaff::query()
            ->when($staffSearch !== '', function (Builder $query) use ($staffSearch) {
                $query->where(function (Builder $innerQuery) use ($staffSearch) {
                    $innerQuery
                        ->where('name_kh', 'like', "%{$staffSearch}%")
                        ->orWhere('name_latin', 'like', "%{$staffSearch}%")
                        ->orWhere('id_number', 'like', "%{$staffSearch}%");
                });
            })
            ->when($staffGenderFilter !== '', fn (Builder $query) => $query->where('gender', $staffGenderFilter))
            ->when($staffRoleFilter !== '', fn (Builder $query) => $query->where('role', $staffRoleFilter))
            ->when($staffPositionFilter !== '', fn (Builder $query) => $query->where('position', $staffPositionFilter))
            ->ordered();

        $teamStaffMembers = (clone $teamStaffQuery)
            ->paginate(10, ['*'], 'team_staff_page')
            ->withQueryString();
        $staffTeamPreview = TeamStaff::query()
            ->ordered()
            ->paginate(6, ['*'], 'staff_team_page')
            ->withQueryString();
        $totalTeamStaff = TeamStaff::count();
        $biTeamStaff = TeamStaff::query()
            ->whereIn('role', ['Admin', 'Manager'])
            ->count();
        $teamStaffMilitaryRanks = TeamStaffRank::query()
            ->where('is_active', true)
            ->ordered()
            ->pluck('name_kh')
            ->merge(
                TeamStaff::query()
                    ->whereNotNull('military_rank')
                    ->select('military_rank')
                    ->distinct()
                    ->orderBy('military_rank')
                    ->pluck('military_rank')
            )
            ->filter()
            ->unique()
            ->values();

        return view('admin.dashboard', [
            'section' => $section,
            'stats' => [
                'totalApplicants' => Application::count(),
                'pendingApplications' => Application::query()->where('status', 'Pending')->count(),
                'approvedApplications' => Application::query()->where('status', 'Approved')->count(),
                'rejectedApplications' => Application::query()->where('status', 'Rejected')->count(),
                'currentMonthApplications' => $currentMonthApplications,
                'previousMonthApplications' => $previousMonthApplications,
                'monthlyTrend' => $trend,
                'totalCourses' => Course::count(),
                'totalRanks' => Rank::count(),
                'totalUsers' => (int) $userCounts->sum(),
                'adminTeamUsers' => (int) ($userCounts->get(1) ?? 0),
                'registerStaffUsers' => (int) ($userCounts->get(0) ?? 0),
                'totalTeamStaff' => $totalTeamStaff,
                'biTeamStaff' => $biTeamStaff,
                'totalTeamStaffRanks' => TeamStaffRank::count(),
                'totalTestTakingStaffRanks' => TestTakingStaffRank::count(),
                'totalTestTakingStaffDocuments' => TestTakingStaffDocumentRequirement::count(),
                'totalTestTakingStaffRegistrations' => TestTakingStaffRegistration::count(),
                'totalTeamStaffDocuments' => TeamStaffDocumentRequirement::count(),
            ],
            'applicationsPerMonth' => $applicationsPerMonth,
            'recentApplications' => $recentApplications,
            'applications' => $applications,
            'filters' => [
                'search' => $search,
                'status' => $statusFilter,
                'rank' => $rankFilter,
                'course' => $courseFilter,
            ],
            'rankDistribution' => $rankDistribution,
            'courses' => Course::query()->ordered()->get(),
            'ranks' => Rank::query()->ordered()->get(),
            'culturalLevels' => CulturalLevel::query()->ordered()->get(),
            'documentRequirements' => DocumentRequirement::query()->ordered()->get(),
            'portalContent' => PortalContent::query()->firstOrFail(),
            'testTakingStaffRanks' => TestTakingStaffRank::query()->ordered()->get(),
            'testTakingStaffDocumentRequirements' => TestTakingStaffDocumentRequirement::query()->ordered()->get(),
            'statuses' => config('military-registration.application_statuses'),
            'users' => $users,
            'adminUsers' => $adminUsers,
            'registerStaffUsers' => $registerStaffUsers,
            'testTakingStaffRegistrations' => $testTakingStaffRegistrations,
            'registerStaffFilters' => [
                'search' => $registerStaffSearch,
                'rank' => $registerStaffRankFilter,
            ],
            'teamStaffMembers' => $teamStaffMembers,
            'teamStaffRanks' => TeamStaffRank::query()->ordered()->get(),
            'teamStaffDocumentRequirements' => TeamStaffDocumentRequirement::query()->ordered()->get(),
            'staffTeamPreview' => $staffTeamPreview,
            'teamStaffFilters' => [
                'search' => $staffSearch,
                'gender' => $staffGenderFilter,
                'role' => $staffRoleFilter,
                'position' => $staffPositionFilter,
            ],
            'teamStaffPositions' => TeamStaff::query()
                ->select('position')
                ->distinct()
                ->orderBy('position')
                ->pluck('position'),
            'teamStaffGenders' => ['Male', 'Female', 'Other'],
            'teamStaffRoles' => TeamStaff::query()
                ->whereNotNull('role')
                ->select('role')
                ->distinct()
                ->orderBy('role')
                ->pluck('role')
                ->merge(['Admin', 'Manager', 'Staff', 'Viewer'])
                ->filter()
                ->unique()
                ->values(),
            'teamStaffMilitaryRanks' => $teamStaffMilitaryRanks,
        ]);
    }

    /**
     * @return Collection<string, int>
     */
    private function monthlyApplicationCounts(Carbon $monthlyWindowStart): Collection
    {
        $driver = DB::connection()->getDriverName();
        $monthExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m', submitted_at)"
            : "DATE_FORMAT(submitted_at, '%Y-%m')";

        return Application::query()
            ->selectRaw("{$monthExpression} as month_key, COUNT(*) as aggregate")
            ->where('submitted_at', '>=', $monthlyWindowStart)
            ->groupBy('month_key')
            ->pluck('aggregate', 'month_key')
            ->map(fn ($count) => (int) $count);
    }
}
