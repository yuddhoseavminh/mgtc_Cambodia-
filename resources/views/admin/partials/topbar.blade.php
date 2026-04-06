@php
    $now = now()->timezone('Asia/Phnom_Penh');
    $currentSection = $currentSection ?? 'applications';
    $fallbackMeta = [
        'overview'                    => ['subtitle' => 'ទិដ្ឋភាពទូទៅ',             'title' => 'ផ្ទាំងគ្រប់គ្រង'],
        'reports'                     => ['subtitle' => 'វិភាគទិន្នន័យ',             'title' => 'របាយការណ៍'],
        'applications'                => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'ពាក្យស្នើសុំ'],
        'documents'                   => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'ឯកសារ'],
        'courses'                     => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'វគ្គសិក្សា'],
        'ranks'                       => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'ឋានន្តរស័ក្តិ'],
        'levels'                      => ['subtitle' => 'ការចុះឈ្មោះវគ្គសិក្សា',    'title' => 'កម្រិតសិក្សា'],
        'design-template'             => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'រចនាទំព័រដើម'],
        'course-template'             => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'គំរូវគ្គសិក្សា'],
        'staff-team'                  => ['subtitle' => 'ក្រុមបុគ្គលិក',            'title' => 'បុគ្គលិកក្រុមការងារទី៣'],
        'staff-team-ranks'            => ['subtitle' => 'ក្រុមបុគ្គលិក',            'title' => 'ឋានន្តរស័ក្តិយោធាបុគ្គលិក'],
        'staff-team-documents'        => ['subtitle' => 'ក្រុមបុគ្គលិក',            'title' => 'ឯកសារបុគ្គលិកក្រុម'],
        'staff-management'            => ['subtitle' => 'ក្រុមបុគ្គលិក',            'title' => 'គ្រប់គ្រងបុគ្គលិក'],
        'test-taking-staff'           => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'បុគ្គលិកសាកល្បង'],
        'test-taking-staff-template'  => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'គំរូបុគ្គលិកសាកល្បង'],
        'test-taking-staff-ranks'     => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'ឋានន្តរស័ក្តិបុគ្គលិក'],
        'test-taking-staff-documents' => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'ឯកសារបុគ្គលិក'],
        'register-staff'              => ['subtitle' => 'បុគ្គលិកសាកល្បង',         'title' => 'បុគ្គលិកបានចុះឈ្មោះ'],
        'users'                       => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'អ្នកប្រើប្រាស់'],
        'profile'                     => ['subtitle' => 'ប្រព័ន្ធ',                  'title' => 'ប្រវត្តិរូប'],
    ];
    $resolvedSubtitle = $fallbackMeta[$currentSection]['subtitle'] ?? ($subtitle ?? 'អ្នកគ្រប់គ្រង');
    $resolvedTitle    = $fallbackMeta[$currentSection]['title']    ?? ($title    ?? 'ផ្ទាំងគ្រប់គ្រង');
@endphp

<header class="admin-topbar" style="
    position: sticky; top: 0; z-index: 20;
    background: #ffffff;
    border-bottom: 1px solid #F3F4F6;
    height: 60px;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
">

    {{-- ── Left: mobile toggle + page heading ── --}}
    <div style="display:flex; align-items:center; gap:12px; min-width:0; flex:1;">

        {{-- Mobile hamburger --}}
        <button type="button"
            data-admin-sidebar-open
            aria-label="Open navigation"
            class="lg:hidden"
            style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1px solid #E5E7EB;background:#fff;color:#6B7280;cursor:pointer;flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/>
            </svg>
        </button>

        {{-- Title block --}}
        <div style="min-width:0;">
            <p style="margin:0 0 1px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#9CA3AF;">{{ $resolvedSubtitle }}</p>
            <h1 class="admin-title-font" style="margin:0;font-size:clamp(1rem,2vw,1.2rem);font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $resolvedTitle }}</h1>
        </div>
    </div>

    {{-- ── Right: search · bell · avatar ── --}}
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.home') }}"
            class="hidden md:flex"
            style="align-items:center;gap:8px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;padding:7px 12px;">
            <input type="hidden" name="section" value="{{ $currentSection === 'reports' ? 'reports' : 'applications' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" style="flex-shrink:0;">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="ស្វែងរក..."
                style="border:none;background:transparent;outline:none;font-size:13px;color:#374151;width:200px;font-family:inherit;">
        </form>

        {{-- Bell --}}
        <button type="button"
            style="position:relative;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1px solid #E5E7EB;background:#fff;color:#6B7280;cursor:pointer;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                <path d="M10 21a2 2 0 0 0 4 0"/>
            </svg>
            @if(($pendingNotifications ?? 0) > 0)
                <span style="position:absolute;top:-4px;right:-4px;min-width:16px;height:16px;background:#EF4444;border-radius:999px;font-size:9px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;padding:0 3px;">{{ $pendingNotifications }}</span>
            @endif
        </button>

        {{-- Avatar --}}
        <a href="{{ route('admin.home', ['section' => 'profile']) }}"
            style="display:flex;align-items:center;gap:8px;border-radius:8px;border:1px solid #E5E7EB;background:#fff;padding:5px 10px 5px 6px;text-decoration:none;">
            <div style="width:28px;height:28px;border-radius:50%;background:#4F46E5;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <span class="hidden sm:block" style="font-size:13px;font-weight:600;color:#111827;max-width:120px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
            <svg class="hidden sm:block" width="12" height="12" viewBox="0 0 20 20" fill="#9CA3AF" style="flex-shrink:0;">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
            </svg>
        </a>
    </div>

</header>
