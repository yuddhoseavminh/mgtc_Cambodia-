@php
    $now = now()->timezone('Asia/Phnom_Penh');
    $currentSection = $currentSection ?? 'applications';
    $fallbackMeta = [
        'overview'                    => ['subtitle' => 'ទិដ្ឋភាពទូទៅ',             'title' => 'ផ្ទាំងគ្រប់គ្រង'],
        'reports'                     => ['subtitle' => 'វិភាគទិន្នន័យ',             'title' => 'របាយការណ៍'],
        'applications'                => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'ពាក្យស្នើសុំ'],
        'registration-form'           => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'សមាជិកចុះឈ្មោះសិក្ខាកាម'],
        'documents'                   => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'ឯកសារ'],
        'courses'                     => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'វគ្គសិក្សា'],
        'ranks'                       => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'ឋានន្តរស័ក្តិ'],
        'levels'                      => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'កម្រិតសិក្សា'],
        'design-template'             => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'រចនាទំព័រដើម'],
        'staff-team-template'         => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'គំរូបុគ្គលិកក្រុមការងារទី៣'],
        'course-template'             => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'គំរូវគ្គសិក្សា'],
        'staff-team'                  => ['subtitle' => 'បុគ្គលិកក្រុមការងារទី៣',            'title' => 'បុគ្គលិកក្រុមការងារទី៣'],
        'staff-team-ranks'            => ['subtitle' => 'បុគ្គលិកក្រុមការងារទី៣',            'title' => 'ឋានន្តរស័ក្តិយោធាបុគ្គលិក'],
        'staff-team-documents'        => ['subtitle' => 'បុគ្គលិកក្រុមការងារទី៣',            'title' => 'ឯកសារបុគ្គលិកក្រុមការងារទី៣'],
        'staff-management'            => ['subtitle' => 'បុគ្គលិកក្រុមការងារទី៣',            'title' => 'គ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣'],
        'test-taking-staff'           => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'បុគ្គលិកសាកល្បង'],
        'test-taking-staff-template'  => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'គំរូបុគ្គលិកសាកល្បង'],
        'test-taking-staff-ranks'     => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'ឋានន្តរស័ក្តិបុគ្គលិក'],
        'test-taking-staff-documents' => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'ឯកសារបុគ្គលិក'],
        'register-staff'              => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'បុគ្គលិកបានចុះឈ្មោះ'],
        'users'                       => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'អ្នកប្រើប្រាស់'],
        'profile'                     => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'ប្រវត្តិរូប'],
    ];
    $resolvedSubtitle = $subtitle ?? ($fallbackMeta[$currentSection]['subtitle'] ?? 'អ្នកគ្រប់គ្រង');
    $resolvedTitle    = $title ?? ($fallbackMeta[$currentSection]['title'] ?? 'ផ្ទាំងគ្រប់គ្រង');
    $topbarSearchSection = in_array($currentSection, ['applications', 'registration-form', 'reports'], true)
        ? $currentSection
        : 'applications';
@endphp

<header class="admin-topbar">

    {{-- -- Left: mobile toggle + page heading -- --}}
    <div class="admin-topbar__leading">

        {{-- Mobile hamburger --}}
        <button type="button"
            data-admin-sidebar-open
            aria-label="Open navigation"
            title="Open navigation"
            class="admin-topbar__menu-btn lg:hidden">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/>
            </svg>
        </button>

        {{-- Title block --}}
        <div class="admin-topbar__title-block">
            <p class="admin-topbar__eyebrow">{{ $resolvedSubtitle }}</p>
            <h1 class="admin-topbar__title">{{ $resolvedTitle }}</h1>
        </div>
    </div>

    {{-- -- Right: search + bell + avatar -- --}}
    <div class="admin-topbar__action-cluster">

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.home') }}"
            class="admin-topbar__search hidden md:flex">
            <input type="hidden" name="section" value="{{ $topbarSearchSection }}">
            <svg class="admin-topbar__search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="ស្វែងរក..."
                class="admin-topbar__search-input">
        </form>

        {{-- Bell --}}
        <button type="button"
            class="admin-topbar__icon-btn"
            title="Notifications">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                <path d="M10 21a2 2 0 0 0 4 0"/>
            </svg>
            @if(($pendingNotifications ?? 0) > 0)
                <span class="admin-topbar__notification-badge">{{ $pendingNotifications }}</span>
            @endif
        </button>

        {{-- Avatar --}}
        <a href="{{ route('admin.home', ['section' => 'profile']) }}"
            class="admin-topbar__profile">
            <div class="admin-topbar__avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <span class="admin-topbar__profile-name hidden sm:block">{{ auth()->user()->name }}</span>
            <svg class="admin-topbar__profile-chevron hidden sm:block" width="12" height="12" viewBox="0 0 20 20" fill="#9CA3AF">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
            </svg>
        </a>
    </div>

</header>
