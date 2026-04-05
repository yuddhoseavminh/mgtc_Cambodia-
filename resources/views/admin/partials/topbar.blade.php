@php
    $now = now()->timezone('Asia/Phnom_Penh');
    $currentSection = $currentSection ?? 'applications';
    $fallbackMeta = [
        'overview' => ['subtitle' => 'ទិដ្ឋភាពទូទៅ', 'title' => 'ផ្ទាំងគ្រប់គ្រង'],
        'reports' => ['subtitle' => 'វិភាគទិន្នន័យ', 'title' => 'របាយការណ៍'],
        'applications' => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'ពាក្យស្នើសុំ'],
        'documents' => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'ឯកសារ'],
        'courses' => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'វគ្គសិក្សា'],
        'ranks' => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'ឋានន្តរស័ក្តិ'],
        'levels' => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា', 'title' => 'កម្រិតសិក្សា'],
        'design-template' => ['subtitle' => 'ប្រព័ន្ធ', 'title' => 'រចនាទំព័រដើម'],
        'course-template' => ['subtitle' => 'ប្រព័ន្ធ', 'title' => 'គំរូវគ្គសិក្សា'],
        'staff-team' => ['subtitle' => 'ក្រុមបុគ្គលិក', 'title' => 'បុគ្គលិកក្រុមការងារទី៣'],
        'staff-team-ranks' => ['subtitle' => 'ក្រុមបុគ្គលិក', 'title' => 'ឋានន្តរស័ក្តិយោធាបុគ្គលិក'],
        'staff-team-documents' => ['subtitle' => 'ក្រុមបុគ្គលិក', 'title' => 'ឯកសារបុគ្គលិកក្រុម'],
        'staff-management' => ['subtitle' => 'ក្រុមបុគ្គលិក', 'title' => 'គ្រប់គ្រងបុគ្គលិក'],
        'test-taking-staff' => ['subtitle' => 'បុគ្គលិកសាកល្បង', 'title' => 'បុគ្គលិកសាកល្បង'],
        'test-taking-staff-template' => ['subtitle' => 'ប្រព័ន្ធ', 'title' => 'គំរូបុគ្គលិកសាកល្បង'],
        'test-taking-staff-ranks' => ['subtitle' => 'បុគ្គលិកសាកល្បង', 'title' => 'ឋានន្តរស័ក្តិបុគ្គលិក'],
        'test-taking-staff-documents' => ['subtitle' => 'បុគ្គលិកសាកល្បង', 'title' => 'ឯកសារបុគ្គលិក'],
        'register-staff' => ['subtitle' => 'បុគ្គលិកសាកល្បង', 'title' => 'បុគ្គលិកបានចុះឈ្មោះ'],
        'users' => ['subtitle' => 'ប្រព័ន្ធ', 'title' => 'អ្នកប្រើប្រាស់'],
        'profile' => ['subtitle' => 'ប្រព័ន្ធ', 'title' => 'ប្រវត្តិរូប'],
    ];
    $resolvedSubtitle = $fallbackMeta[$currentSection]['subtitle'] ?? ($subtitle ?? 'អ្នកគ្រប់គ្រង');
    $resolvedTitle = $fallbackMeta[$currentSection]['title'] ?? ($title ?? 'ផ្ទាំងគ្រប់គ្រង');
@endphp

<header class="admin-topbar sticky top-0 z-20 border-b border-slate-200/80 bg-white/88 px-4 py-4 backdrop-blur sm:px-6">
    <div class="admin-topbar__inner flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div class="admin-topbar__intro flex items-start gap-3 sm:gap-4">
            <button
                type="button"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 lg:hidden"
                data-admin-sidebar-open
                aria-label="Open navigation"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            <div class="min-w-0">
                <p class="hidden text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 sm:block">{{ $resolvedSubtitle }}</p>
                <h1 class="admin-title-font mt-1 break-words text-[1.2rem] font-semibold tracking-tight text-slate-950 sm:mt-2 sm:text-[2.05rem]">{{ $resolvedTitle }}</h1>
            </div>
        </div>

        <div class="admin-topbar__actions flex w-full flex-col gap-3 xl:max-w-[920px] xl:items-end">
            <div class="flex w-full flex-col gap-3 lg:flex-row lg:items-center lg:justify-end">
                <form method="GET" action="{{ route('admin.home') }}" class="admin-topbar__search flex min-w-0 flex-1 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 lg:max-w-[420px]">
                    <input type="hidden" name="section" value="{{ $currentSection === 'reports' ? 'reports' : 'applications' }}">
                    <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="ស្វែងរកពាក្យស្នើសុំ វគ្គសិក្សា ឬឋានន្តរស័ក្តិ..."
                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:ring-0"
                    >
                </form>

                <div class="admin-topbar__action-cluster flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.home', ['section' => 'users']) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <path d="M20 8v6"></path>
                            <path d="M23 11h-6"></path>
                        </svg>
                        <span>អ្នកប្រើប្រាស់</span>
                    </a>

                    <a href="{{ route('admin.home', ['section' => 'profile']) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z"></path>
                            <path d="M4 20a8 8 0 0 1 16 0"></path>
                        </svg>
                        <span>ប្រវត្តិរូប</span>
                    </a>

                    <button type="button" class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"></path>
                            <path d="M10 21a2 2 0 0 0 4 0"></path>
                        </svg>
                        @if (($pendingNotifications ?? 0) > 0)
                            <span class="absolute right-0 top-0 inline-flex min-h-5 min-w-5 -translate-y-1/3 translate-x-1/3 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">
                                {{ $pendingNotifications }}
                            </span>
                        @endif
                    </button>

                    <div class="admin-topbar__profile flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-400">ចូលប្រើជា</p>
                            <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-topbar__clock flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-500">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                    <path d="M16 3v4"></path>
                    <path d="M8 3v4"></path>
                    <path d="M3 11h18"></path>
                </svg>
                <span data-admin-clock data-admin-clock-timezone="Asia/Phnom_Penh">{{ $now->format('d/m/Y') }} / {{ $now->format('H:i:s') }}</span>
            </div>
        </div>
    </div>
</header>
