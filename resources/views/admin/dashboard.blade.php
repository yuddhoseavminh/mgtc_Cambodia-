@extends('app')

@section('body')
    @php
        $maxApplications = max(1, $applicationsPerMonth->max('applications'));
        $statusClasses = [
            'Pending' => 'bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200',
            'Reviewed' => 'bg-sky-100 text-sky-700 ring-1 ring-inset ring-sky-200',
            'Approved' => 'bg-emerald-100 text-emerald-700 ring-1 ring-inset ring-emerald-200',
            'Rejected' => 'bg-rose-100 text-rose-700 ring-1 ring-inset ring-rose-200',
        ];
        $statusLabels = [
            'Pending' => 'រង់ចាំ',
            'Reviewed' => 'បានពិនិត្យ',
            'Approved' => 'អនុម័ត',
            'Rejected' => 'បដិសេធ',
        ];
        $lineWidth = 760;
        $lineHeight = 240;
        $chartPaddingX = 24;
        $chartPaddingTop = 18;
        $chartDrawableHeight = 172;
        $monthCount = max(1, $applicationsPerMonth->count() - 1);
        $linePoints = $applicationsPerMonth
            ->values()
            ->map(function ($month, $index) use ($lineWidth, $chartPaddingX, $chartPaddingTop, $chartDrawableHeight, $monthCount, $maxApplications) {
                $x = $chartPaddingX + (($lineWidth - ($chartPaddingX * 2)) / $monthCount) * $index;
                $y = $chartPaddingTop + $chartDrawableHeight - (($month['applications'] / $maxApplications) * $chartDrawableHeight);

                return ['x' => round($x, 2), 'y' => round($y, 2)];
            });
        $linePointString = $linePoints->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ');
        $firstPoint = $linePoints->first();
        $lastPoint = $linePoints->last();
        $areaPath = 'M '.$firstPoint['x'].' '.$lineHeight.' L '.$firstPoint['x'].' '.$firstPoint['y'].' L '.$linePoints->map(fn ($point) => $point['x'].' '.$point['y'])->implode(' L ').' L '.$lastPoint['x'].' '.$lineHeight.' Z';
        $distributionTotal = max(1, $rankDistribution->sum());
        $distributionColors = ['#356AE6', '#60A5FA', '#34D399', '#F59E0B', '#A78BFA', '#94A3B8'];
        $currentOffset = 0;
        $distributionSegments = $rankDistribution->values()->map(function ($count, $index) use (&$currentOffset, $distributionTotal, $distributionColors) {
            $percentage = round(($count / $distributionTotal) * 100, 1);
            $segment = [
                'color' => $distributionColors[$index % count($distributionColors)],
                'start' => $currentOffset,
                'end' => $currentOffset + $percentage,
                'percentage' => $percentage,
            ];
            $currentOffset += $percentage;

            return $segment;
        });
        $distributionStyle = $distributionSegments->map(fn ($segment) => "{$segment['color']} {$segment['start']}% {$segment['end']}%")->implode(', ');
        $sectionMeta = [
            'overview' => ['eyebrow' => 'ទិដ្ឋភាពទូទៅ', 'title' => 'ផ្ទាំងគ្រប់គ្រង'],
            'reports' => ['eyebrow' => 'វិភាគទិន្នន័យ', 'title' => 'របាយការណ៍'],
            'applications' => ['eyebrow' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'ពាក្យស្នើសុំ'],
            'documents' => ['eyebrow' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'ឯកសារ'],
            'courses' => ['eyebrow' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'វគ្គសិក្សា'],
            'ranks' => ['eyebrow' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'ឋានន្តរស័ក្តិ'],
            'levels' => ['eyebrow' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'កម្រិតសិក្សា'],
            'design-template' => ['eyebrow' => 'ប្រព័ន្ធ', 'title' => 'រចនាទំព័រដើម'],
            'course-template' => ['eyebrow' => 'ប្រព័ន្ធ', 'title' => 'គំរូវគ្គសិក្សា'],
            'staff-team' => ['eyebrow' => 'បុគ្គលិកក្រុមការងារទី៣', 'title' => 'បុគ្គលិកក្រុមការងារទី៣'],
            'staff-team-ranks' => ['eyebrow' => 'បុគ្គលិកក្រុមការងារទី៣', 'title' => 'ឋានន្តរស័ក្តិយោធាបុគ្គលិក'],
            'staff-team-documents' => ['eyebrow' => 'បុគ្គលិកក្រុមការងារទី៣', 'title' => 'ឯកសារបុគ្គលិកក្រុម'],
            'staff-management' => ['eyebrow' => 'បុគ្គលិកក្រុមការងារទី៣', 'title' => 'គ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣'],
            'test-taking-staff' => ['eyebrow' => 'បុគ្គលិកសាកល្បង', 'title' => 'បុគ្គលិកសាកល្បង'],
            'test-taking-staff-template' => ['eyebrow' => 'ប្រព័ន្ធ', 'title' => 'គំរូបុគ្គលិកសាកល្បង'],
            'test-taking-staff-ranks' => ['eyebrow' => 'បុគ្គលិកសាកល្បង', 'title' => 'ឋានន្តរស័ក្តិបុគ្គលិក'],
            'test-taking-staff-documents' => ['eyebrow' => 'បុគ្គលិកសាកល្បង', 'title' => 'ឯកសារបុគ្គលិក'],
            'register-staff' => ['eyebrow' => 'បុគ្គលិកសាកល្បង', 'title' => 'បុគ្គលិកបានចុះឈ្មោះ'],
            'users' => ['eyebrow' => 'ប្រព័ន្ធ', 'title' => 'អ្នកប្រើប្រាស់'],
            'profile' => ['eyebrow' => 'ប្រព័ន្ធ', 'title' => 'ប្រវត្តិរូប'],
        ];
        $currentMeta = $sectionMeta[$section] ?? $sectionMeta['overview'];
    @endphp

    <div class="admin-dashboard w-full">
        <div class="dashboard-shell">
            <div class="admin-dashboard-grid grid lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => $section])

                <main class="admin-main flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => $currentMeta['title'],
                        'subtitle' => $currentMeta['eyebrow'],
                        'filters' => $filters,
                        'pendingNotifications' => $stats['pendingApplications'],
                        'currentSection' => $section,
                    ])

                    <div class="admin-content flex-1 space-y-6 p-4 sm:p-6 lg:p-8">
                        @if ($section === 'overview')
                            @include('admin.sections.overview')
                        @endif

                        @if ($section === 'applications')
                            @include('admin.sections.applications')
                        @endif

                        @if ($section === 'reports')
                            @include('admin.sections.reports')
                        @endif

                        @if ($section === 'documents')
                            @include('admin.sections.documents')
                        @endif

                        @if ($section === 'design-template')
                            @include('admin.sections.design-template')
                        @endif

                        @if ($section === 'course-template')
                            @include('admin.sections.course-template')
                        @endif

                        @if ($section === 'courses')
                            @include('admin.sections.courses')
                        @endif

                        @if ($section === 'ranks')
                            @include('admin.sections.ranks')
                        @endif

                        @if ($section === 'levels')
                            @include('admin.sections.levels')
                        @endif

                        @if ($section === 'staff-team')
                            @include('admin.sections.staff-team')
                        @endif

                        @if ($section === 'staff-team-ranks')
                            @include('admin.sections.staff-team-ranks')
                        @endif

                        @if ($section === 'staff-team-documents')
                            @include('admin.sections.staff-team-documents')
                        @endif

                        @if ($section === 'staff-management')
                            @include('admin.sections.staff-management')
                        @endif

                        @if ($section === 'test-taking-staff')
                            @include('admin.sections.test-taking-staff')
                        @endif

                        @if ($section === 'test-taking-staff-template')
                            @include('admin.sections.test-taking-staff-template')
                        @endif

                        @if ($section === 'test-taking-staff-ranks')
                            @include('admin.sections.test-taking-staff-ranks')
                        @endif

                        @if ($section === 'test-taking-staff-documents')
                            @include('admin.sections.test-taking-staff-documents')
                        @endif

                        @if ($section === 'register-staff')
                            @include('admin.sections.register-staff')
                        @endif

                        @if ($section === 'users')
                            @include('admin.sections.users')
                        @endif

                        @if ($section === 'profile')
                            @include('admin.sections.profile')
                        @endif
                    </div>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <p>&copy; {{ now()->year }} Copyright By Yuddho Seavminh</p>
                        <div class="flex items-center gap-3">
                            {{-- <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">API ដំណើរការ</span> --}}
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">V1.0.0</span>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    </div>
@endsection
