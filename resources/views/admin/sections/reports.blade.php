@php
    $reportSummaryCards = [
        [
            'label' => 'ពាក្យស្នើសុំសរុប',
            'value' => $stats['totalApplicants'],
            'meta' => 'ទិន្នន័យគ្រប់ពេលវេលា',
            'badge' => 'All Applications',
            'badgeClass' => 'bg-blue-100 text-blue-700',
        ],
        [
            'label' => 'រង់ចាំអនុម័ត',
            'value' => $stats['pendingApplications'],
            'meta' => 'កំពុងរង់ចាំពិនិត្យ',
            'badge' => 'Pending',
            'badgeClass' => 'bg-amber-100 text-amber-700',
        ],
        [
            'label' => 'អនុម័តរួច',
            'value' => $stats['approvedApplications'],
            'meta' => 'បញ្ចប់ដោយជោគជ័យ',
            'badge' => 'Approved',
            'badgeClass' => 'bg-emerald-100 text-emerald-700',
        ],
        [
            'label' => 'បដិសេធ',
            'value' => $stats['rejectedApplications'],
            'meta' => 'កំណត់ត្រាមិនបានអនុម័ត',
            'badge' => 'Rejected',
            'badgeClass' => 'bg-rose-100 text-rose-700',
        ],
    ];

    $reportStatuses = collect([
        [
            'label' => $statusLabels['Pending'] ?? 'Pending',
            'count' => $stats['pendingApplications'],
            'class' => 'bg-amber-400',
            'textClass' => 'text-amber-700',
        ],
        [
            'label' => $statusLabels['Approved'] ?? 'Approved',
            'count' => $stats['approvedApplications'],
            'class' => 'bg-emerald-500',
            'textClass' => 'text-emerald-700',
        ],
        [
            'label' => $statusLabels['Rejected'] ?? 'Rejected',
            'count' => $stats['rejectedApplications'],
            'class' => 'bg-rose-500',
            'textClass' => 'text-rose-700',
        ],
    ]);

    $reportStatusTotal = max(1, $reportStatuses->sum('count'));
    $peakMonth = $applicationsPerMonth->sortByDesc('applications')->first();
    $latestMonth = $applicationsPerMonth->last();
@endphp

<section class="dashboard-report-grid grid gap-4">
    @foreach ($reportSummaryCards as $card)
        <article class="dashboard-mini-card p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $card['label'] }}</p>
                    <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">{{ $card['value'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">{{ $card['meta'] }}</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $card['badgeClass'] }}">{{ $card['badge'] }}</span>
            </div>
        </article>
    @endforeach
</section>

<section class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
    <div class="dashboard-surface p-5 sm:p-6">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">និន្នាការចុះឈ្មោះ ១២ ខែ</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Track monthly registration volume with a responsive chart that remains readable across phones, tablets, laptops, and large screens.</p>
            </div>
            <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">{{ $latestMonth['month'] ?? '-' }}</span>
        </div>

        <div class="dashboard-chart-scroll mt-6 overflow-x-auto">
            <div class="dashboard-chart-stage">
                <svg viewBox="0 0 {{ $lineWidth }} {{ $lineHeight }}" class="h-[290px] w-full">
                    @for ($i = 0; $i < 5; $i++)
                        @php $y = $chartPaddingTop + ($chartDrawableHeight / 4) * $i; @endphp
                        <line x1="{{ $chartPaddingX }}" y1="{{ $y }}" x2="{{ $lineWidth - $chartPaddingX }}" y2="{{ $y }}" stroke="#E2E8F0" stroke-dasharray="4 8"></line>
                    @endfor
                    <path d="{{ $areaPath }}" fill="url(#reportsAreaFill)"></path>
                    <polyline points="{{ $linePointString }}" fill="none" stroke="#356AE6" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></polyline>
                    @foreach ($linePoints as $index => $point)
                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                        <text x="{{ $point['x'] }}" y="{{ $lineHeight - 10 }}" text-anchor="middle" fill="#94A3B8" font-size="11" font-weight="600">{{ $applicationsPerMonth[$index]['month'] }}</text>
                    @endforeach
                    <defs>
                        <linearGradient id="reportsAreaFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#356AE6" stop-opacity="0.2"></stop>
                            <stop offset="100%" stop-color="#356AE6" stop-opacity="0.03"></stop>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Peak Month</p>
                <p class="mt-2 text-lg font-semibold text-slate-950">{{ $peakMonth['month'] ?? '-' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $peakMonth['applications'] ?? 0 }} applications</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">This Month</p>
                <p class="mt-2 text-lg font-semibold text-slate-950">{{ $stats['currentMonthApplications'] }}</p>
                <p class="mt-1 text-sm text-slate-500">Current active period</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 sm:col-span-2 xl:col-span-1">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Monthly Trend</p>
                <p class="mt-2 text-lg font-semibold {{ $stats['monthlyTrend'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $stats['monthlyTrend'] >= 0 ? '+' : '' }}{{ $stats['monthlyTrend'] }}
                </p>
                <p class="mt-1 text-sm text-slate-500">Compared with previous month</p>
            </div>
        </div>
    </div>

    <div class="dashboard-surface p-5 sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-200 pb-5">
            <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">Status Breakdown</h3>
            <p class="text-sm leading-6 text-slate-500">A compact approval overview with touch-friendly spacing and clear progress indicators.</p>
        </div>

        <div class="dashboard-report-status mt-5">
            @foreach ($reportStatuses as $status)
                @php
                    $percentage = round(($status['count'] / $reportStatusTotal) * 100);
                @endphp
                <div class="dashboard-report-status-row rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-semibold text-slate-900">{{ $status['label'] }}</span>
                        <span class="text-sm font-semibold {{ $status['textClass'] }}">{{ $status['count'] }}</span>
                    </div>
                    <div class="dashboard-progress-bar h-2.5">
                        <span class="{{ $status['class'] }}" style="width: {{ $percentage }}%"></span>
                    </div>
                    <p class="text-xs font-medium text-slate-500">{{ $percentage }}% of tracked applications</p>
                </div>
            @endforeach
        </div>

        <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-[linear-gradient(135deg,#eff6ff,#ffffff)] p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Operational Snapshot</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl bg-white px-4 py-4">
                    <p class="text-sm font-semibold text-slate-900">Courses</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $stats['totalCourses'] }}</p>
                </div>
                <div class="rounded-2xl bg-white px-4 py-4">
                    <p class="text-sm font-semibold text-slate-900">Ranks</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $stats['totalRanks'] }}</p>
                </div>
                <div class="rounded-2xl bg-white px-4 py-4">
                    <p class="text-sm font-semibold text-slate-900">Team Users</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $stats['adminTeamUsers'] }}</p>
                </div>
                <div class="rounded-2xl bg-white px-4 py-4">
                    <p class="text-sm font-semibold text-slate-900">Register Staff Users</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $stats['registerStaffUsers'] }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
    <div class="dashboard-surface p-5 sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-200 pb-5">
            <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">Rank Distribution</h3>
            <p class="text-sm leading-6 text-slate-500">The chart and legend remain stable in portrait and landscape layouts without causing overflow.</p>
        </div>

        <div class="mt-6 flex justify-center">
            <div class="relative h-52 w-52 max-w-full rounded-full" style="background: conic-gradient({{ $distributionStyle ?: '#356AE6 0% 100%' }});">
                <div class="absolute inset-[24px] rounded-full bg-white"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="px-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Top Ranks</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $rankDistribution->sum() }}</p>
                        <p class="mt-1 text-sm text-slate-500">tracked applications</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-report-list mt-6">
            @forelse ($rankDistribution as $rankName => $count)
                @php $segment = $distributionSegments->values()[$loop->index] ?? null; @endphp
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="h-3 w-3 shrink-0 rounded-full" style="background-color: {{ $segment['color'] ?? '#356AE6' }}"></span>
                        <span class="truncate text-sm font-semibold text-slate-800">{{ $rankName }}</span>
                    </div>
                    <span class="text-sm font-semibold text-slate-900">{{ $count }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No rank distribution data available.</p>
            @endforelse
        </div>
    </div>

    <div class="dashboard-surface p-5 sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-200 pb-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">Recent Activity</h3>
                <p class="text-sm leading-6 text-slate-500">Latest applications in a table that collapses to stacked cards below the tablet breakpoint.</p>
            </div>
            <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="inline-flex w-fit items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Open Applications
            </a>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="admin-data-table min-w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <th class="px-4 py-4">ID</th>
                        <th class="px-4 py-4">Applicant</th>
                        <th class="px-4 py-4">Rank</th>
                        <th class="px-4 py-4">Course</th>
                        <th class="px-4 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentApplications as $application)
                        <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                            <td class="px-4 py-5 font-semibold text-slate-950" data-label="ID" data-table-primary>{{ $application->id_number }}</td>
                            <td class="px-4 py-5" data-label="Applicant">
                                <p class="font-semibold text-slate-900">{{ $application->khmer_name }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $application->latin_name }}</p>
                            </td>
                            <td class="px-4 py-5" data-label="Rank">{{ $application->rank?->name_kh ?? '-' }}</td>
                            <td class="px-4 py-5" data-label="Course">{{ $application->course?->name ?? '-' }}</td>
                            <td class="px-4 py-5" data-label="Status">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$application->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200' }}">
                                    {{ $statusLabels[$application->status] ?? $application->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">No recent applications available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
