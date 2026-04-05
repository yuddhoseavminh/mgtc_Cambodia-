<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Course;
use App\Models\Rank;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
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

        return response()->json([
            'stats' => [
                'total_applicants' => Application::count(),
                'total_courses' => Course::count(),
                'total_ranks' => Rank::count(),
                'pending_applications' => Application::where('status', 'Pending')->count(),
            ],
            'applications_per_month' => $applicationsPerMonth,
            'recent_applications' => Application::query()
                ->with(['rank:id,name_kh,name_en', 'course:id,name'])
                ->latest('submitted_at')
                ->limit(5)
                ->get()
                ->map(fn (Application $application) => [
                    'id' => $application->id,
                    'applicant_name' => $application->khmer_name,
                    'rank' => $application->rank?->name_kh,
                    'course' => $application->course?->name,
                    'status' => $application->status,
                    'submitted_at' => $application->submitted_at?->toIso8601String(),
                ]),
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
