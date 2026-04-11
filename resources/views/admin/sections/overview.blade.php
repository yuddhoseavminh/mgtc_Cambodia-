@php
    $catalogCards = [
        [
            'eyebrow' => 'កាតាឡុក ១',
            'title' => 'ការចុះឈ្មោះ',
            'description' => 'គ្រប់គ្រងការទទួលពាក្យ វគ្គសិក្សា ឋានន្តរស័ក្តិ កម្រិតវប្បធម៌ និងតម្រូវការឯកសារ។',
            'href' => route('admin.home', ['section' => 'applications']),
            'cta' => 'បើកកាតាឡុកចុះឈ្មោះ',
            'gradient' => 'from-[#1d4ed8] via-[#356AE6] to-[#67b4ff]',
            'items' => ['ការចុះឈ្មោះសិក្ខាកាម', 'គ្រប់គ្រងវគ្គសិក្សា', 'គ្រប់គ្រងឋានន្តរស័ក្តិ', 'កម្រិតវប្បធម៌', 'គ្រប់គ្រងឯកសារ', 'គំរូរចនា'],
        ],
        [
            'eyebrow' => 'កាតាឡុក ២',
            'title' => 'បុគ្គលិកក្រុមការងារទី៣ទី៣',
            'description' => 'តាមដានក្រុមអ្នកគ្រប់គ្រង ថ្នាក់ដឹកនាំសកម្ម និងកន្លែងធ្វើការគ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣ខាងក្នុង។',
            'href' => route('admin.home', ['section' => 'staff-team']),
            'cta' => 'បើកបុគ្គលិកក្រុមការងារទី៣ទី៣',
            'gradient' => 'from-[#0f766e] via-[#14b8a6] to-[#7dd3fc]',
            'items' => ['បុគ្គលិកក្រុមការងារទី៣ទី៣', 'គ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣'],
        ],
        [
            'eyebrow' => 'កាតាឡុក ៣',
            'title' => 'បុគ្គលិកសាកល្បង',
            'description' => 'រៀបចំប្រតិបត្តិការបុគ្គលិកចុះឈ្មោះ និងជួរងារសម្រាប់គាំទ្រការប្រឡង។',
            'href' => route('admin.home', ['section' => 'test-taking-staff']),
            'cta' => 'បើកផ្នែកបុគ្គលិកសាកល្បង',
            'gradient' => 'from-[#7c3aed] via-[#a855f7] to-[#f472b6]',
            'items' => ['បុគ្គលិកសាកល្បង', 'គ្រប់គ្រងបុគ្គលិកសាកល្បង'],
        ],
        [
            'eyebrow' => 'កាតាឡុក ៤',
            'title' => 'ការគ្រប់គ្រងប្រព័ន្ធ',
            'description' => 'ចូលប្រើអ្នកប្រើប្រាស់ ការកំណត់ និងឧបករណ៍បញ្ជាប្រតិបត្តិការសម្រាប់ផ្ទាំងគ្រប់គ្រងទាំងមូល។',
            'href' => route('admin.home', ['section' => 'profile']),
            'cta' => 'បើកការគ្រប់គ្រងប្រព័ន្ធ',
            'gradient' => 'from-[#111827] via-[#334155] to-[#64748b]',
            'items' => ['អ្នកប្រើប្រាស់', 'ប្រវត្តិរូប', 'ចាកចេញ'],
        ],
    ];

    $summaryCards = [
        ['label' => 'ចំនួនចុះឈ្មោះសរុប', 'value' => $stats['totalApplicants'], 'meta' => 'គ្រប់ពេលវេលា', 'badge' => 'បច្ចុប្បន្ន', 'badgeClass' => 'bg-emerald-100 text-emerald-700'],
        ['label' => 'រង់ចាំអនុម័ត', 'value' => $stats['pendingApplications'], 'meta' => 'ត្រូវពិនិត្យ', 'badge' => 'ជួរ', 'badgeClass' => 'bg-sky-100 text-sky-700'],
        ['label' => 'វគ្គសិក្សាសរុប', 'value' => $stats['totalCourses'], 'meta' => 'កាតាឡុកគ្រប់គ្រង', 'badge' => 'បច្ចុប្បន្ន', 'badgeClass' => 'bg-amber-100 text-amber-700'],
        ['label' => 'អ្នកគ្រប់គ្រង', 'value' => $stats['adminTeamUsers'], 'meta' => 'បុគ្គលិកក្រុមការងារទី៣ទី៣', 'badge' => 'សិទ្ធិ', 'badgeClass' => 'bg-violet-100 text-violet-700'],
        ['label' => 'បុគ្គលិកចុះឈ្មោះ', 'value' => $stats['registerStaffUsers'], 'meta' => 'គណនីប្រតិបត្តិករ', 'badge' => 'ប្រតិបត្តិការ', 'badgeClass' => 'bg-rose-100 text-rose-700'],
        ['label' => 'អ្នកប្រើប្រាស់សរុប', 'value' => $stats['totalUsers'], 'meta' => 'គណនីប្រព័ន្ធ', 'badge' => 'បច្ចុប្បន្ន', 'badgeClass' => 'bg-slate-100 text-slate-700'],
    ];
@endphp

<section class="grid gap-5 xl:grid-cols-4">
    @foreach ($catalogCards as $card)
        <article class="overflow-hidden rounded-[30px] bg-gradient-to-br {{ $card['gradient'] }} p-[1px] shadow-[0_30px_80px_rgba(15,23,42,0.12)]">
            <div class="flex h-full flex-col rounded-[29px] bg-[linear-gradient(180deg,rgba(255,255,255,0.2),rgba(255,255,255,0.08))] p-6 text-white backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/65">{{ $card['eyebrow'] }}</p>
                <h3 class="mt-4 text-[1.7rem] font-semibold tracking-tight">{{ $card['title'] }}</h3>
                <p class="mt-3 text-sm leading-6 text-white/78">{{ $card['description'] }}</p>

                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($card['items'] as $item)
                        <span class="rounded-full border border-white/18 bg-white/10 px-3 py-1 text-xs font-semibold text-white/85">{{ $item }}</span>
                    @endforeach
                </div>

                <a href="{{ $card['href'] }}" class="mt-8 inline-flex w-fit items-center rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                    {{ $card['cta'] }}
                </a>
            </div>
        </article>
    @endforeach
</section>

<section class="grid gap-4 xl:grid-cols-6">
    @foreach ($summaryCards as $card)
        <article class="dashboard-mini-card p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $card['label'] }}</p>
                    <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
                    <p class="mt-3 text-sm text-slate-500">{{ $card['meta'] }}</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $card['badgeClass'] }}">{{ $card['badge'] }}</span>
            </div>
        </article>
    @endforeach
</section>

<section class="dashboard-surface p-6">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">ពាក្យស្នើសុំថ្មីៗ</h3>
            <p class="mt-2 text-sm text-slate-500">តាមដានការដាក់ពាក្យថ្មីៗ និងស្ថានភាពពិនិត្យ។</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <form method="GET" action="{{ route('admin.home') }}" class="flex w-full min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-[#f8fafc] px-4 py-3 sm:min-w-[250px] xl:w-auto">
                <input type="hidden" name="section" value="applications">
                <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] }}"
                    placeholder="ស្វែងរកពាក្យស្នើសុំ"
                    class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:ring-0"
                >
            </form>

            <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">បើកតារាង</a>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="admin-data-table min-w-full text-left">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                    <th class="px-4 py-4">អត្តលេខ</th>
                    <th class="px-4 py-4">អ្នកដាក់ពាក្យ</th>
                    <th class="px-4 py-4">ឋានន្តរស័ក្តិ</th>
                    <th class="px-4 py-4">វគ្គសិក្សា</th>
                    <th class="px-4 py-4">ទូរស័ព្ទ</th>
                    <th class="px-4 py-4">ស្ថានភាព</th>
                    <th class="px-4 py-4 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentApplications as $application)
                    <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                        <td class="px-4 py-5 font-semibold text-slate-950" data-label="លេខអត្តសញ្ញាណ" data-table-primary>{{ $application->id_number }}</td>
                        <td class="px-4 py-5" data-label="អ្នកដាក់ពាក្យ">
                            <p class="font-semibold text-slate-900">{{ $application->khmer_name }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $application->latin_name }}</p>
                        </td>
                        <td class="px-4 py-5" data-label="ឋានន្តរស័ក្តិ">{{ $application->rank?->name_kh }}</td>
                        <td class="px-4 py-5" data-label="វគ្គសិក្សា">{{ $application->course?->name }}</td>
                        <td class="px-4 py-5" data-label="លេខទូរស័ព្ទ">{{ $application->phone_number }}</td>
                        <td class="px-4 py-5" data-label="ស្ថានភាព">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$application->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200' }}">
                                {{ $statusLabels[$application->status] ?? $application->status }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-right" data-label="សកម្មភាព" data-table-actions>
                            <a href="{{ route('admin.applications.show', $application) }}" class="text-sm font-semibold text-[#356AE6] transition hover:text-[#204ec7]">មើល</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">មិនមានពាក្យស្នើសុំថ្មីៗទេ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
        <p>បង្ហាញ {{ $recentApplications->count() }} ពាក្យស្នើសុំចុងក្រោយ</p>
        <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="font-semibold text-[#356AE6] transition hover:text-[#204ec7]">មើលទាំងអស់</a>
    </div>
</section>

<section class="grid gap-6 xl:grid-cols-[1.7fr_0.85fr]">
    <div class="dashboard-surface p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">ទិដ្ឋភាពការចុះឈ្មោះ</h3>
                <p class="mt-2 text-sm text-slate-500">លទ្ធផលការចុះឈ្មោះប្រចាំខែក្នុងរយៈពេល ១២ ខែចុងក្រោយ។</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">១២ ខែចុងក្រោយ</span>
        </div>

        <div class="dashboard-chart-scroll mt-8 overflow-x-auto">
            <div class="dashboard-chart-stage min-w-[760px]">
                <svg viewBox="0 0 {{ $lineWidth }} {{ $lineHeight }}" class="h-[290px] w-full">
                    @for ($i = 0; $i < 5; $i++)
                        @php $y = $chartPaddingTop + ($chartDrawableHeight / 4) * $i; @endphp
                        <line x1="{{ $chartPaddingX }}" y1="{{ $y }}" x2="{{ $lineWidth - $chartPaddingX }}" y2="{{ $y }}" stroke="#E2E8F0" stroke-dasharray="4 8"></line>
                    @endfor
                    <path d="{{ $areaPath }}" fill="url(#registrationOverviewArea)"></path>
                    <polyline points="{{ $linePointString }}" fill="none" stroke="#356AE6" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></polyline>
                    @foreach ($linePoints as $index => $point)
                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                        <text x="{{ $point['x'] }}" y="{{ $lineHeight - 10 }}" text-anchor="middle" fill="#94A3B8" font-size="11" font-weight="600">{{ $applicationsPerMonth[$index]['month'] }}</text>
                    @endforeach
                    <defs>
                        <linearGradient id="registrationOverviewArea" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#356AE6" stop-opacity="0.18"></stop>
                            <stop offset="100%" stop-color="#356AE6" stop-opacity="0.03"></stop>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>
    </div>

    <div class="dashboard-surface p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">ការបែងចែកតាមឋានន្តរស័ក្តិ</h3>
                <p class="mt-2 text-sm text-slate-500">ពាក្យស្នើសុំបច្ចុប្បន្នតាមក្រុមឋានន្តរស័ក្តិ។</p>
            </div>
            <a href="{{ route('admin.home', ['section' => 'ranks']) }}" class="text-sm font-semibold text-[#356AE6] transition hover:text-[#204ec7]">គ្រប់គ្រង</a>
        </div>

        <div class="mt-6 flex justify-center">
            <div
                id="rank-donut-chart"
                class="relative h-48 w-48 rounded-full"
                data-gradient="{{ $distributionStyle ?: '#356AE6 0% 100%' }}"
            >
                <div class="absolute inset-[22px] rounded-full bg-white"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">អ្នកដាក់ពាក្យ</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $rankDistribution->sum() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (function () {
                var el = document.getElementById('rank-donut-chart');
                if (el) { el.style.background = 'conic-gradient(' + el.dataset.gradient + ')'; }
            })();
        </script>

        <div class="mt-6 space-y-3">
            @forelse ($rankDistribution as $rankName => $count)
                @php $segment = $distributionSegments->values()->get($loop->index, ['color' => '#94A3B8']); @endphp
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full" data-color="{{ $segment['color'] }}"></span>
                        <span class="text-sm font-semibold text-slate-800">{{ $rankName }}</span>
                    </div>
                    <span class="text-sm font-semibold text-slate-900">{{ $count }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">មិនមានទិន្នន័យការបែងចែកតាមឋានន្តរស័ក្តិទេ។</p>
            @endforelse
        </div>
        <script>
            document.querySelectorAll('[data-color]').forEach(function(el) {
                el.style.backgroundColor = el.dataset.color;
            });
        </script>
    </div>
</section>
