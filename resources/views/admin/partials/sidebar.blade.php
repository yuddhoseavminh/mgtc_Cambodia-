@php
    $currentUser = auth()->user();
    $canAccessSection = static fn (string $key): bool => (bool) $currentUser?->canAccessSection($key);
    $groups = [
        [
            'label' => 'ផ្ទាំងគ្រប់គ្រង',
            'items' => [
                ['key' => 'overview',  'label' => 'ទិដ្ឋភាពទូទៅ',  'meta' => 'សង្ខេប និងស្ថិតិ'],
                // ['key' => 'reports',   'label' => 'របាយការណ៍',    'meta' => 'ក្រាហ្វ និងជំមោ'],
            ],
        ],
        [
            'label' => 'ការចុះឈ្មោះ',
            'items' => [
                // [
                //     'key' => 'registration-form',
                //     'accessKey' => 'applications',
                //     'label' => 'សមាជិកចុះឈ្មោះសិក្ខាកាម',
                //     'meta' => 'ទំព័រសាធារណៈ',
                // ],
                ['key' => 'applications', 'label' => 'ពាក្យស្នើសុំការចុះឈ្មោះសិក្ខាកាម', 'meta' => 'បញ្ជីចុះឈ្មោះ'],
                [
                    'key'      => 'create-new',
                    'label'    => 'បង្កើតឯកសារ',
                    'meta'     => 'ការកំណត់',
                    'subItems' => [
                        ['key' => 'courses',   'label' => 'វគ្គសិក្សា'],
                        ['key' => 'ranks',     'label' => 'ឋានន្តរស័ក្តិ'],
                        ['key' => 'levels',    'label' => 'កម្រិតសិក្សា'],
                        ['key' => 'documents', 'label' => 'ឯកសារ'],
                    ],
                ],
            ],
        ],
        [
            'label' => 'បុគ្គលិកក្រុមការងារទី៣',
            'items' => [
                ['key' => 'staff-team',           'label' => 'បុគ្គលិកក្រុមទី៣',   'meta' => 'បញ្ជីបុគ្គលិក'],
                ['key' => 'staff-management',      'label' => 'គ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣', 'meta' => 'ទិន្នន័យបុគ្គលិក'],
                [
                    'key'      => 'create-staff-docs',
                    'label'    => 'បង្កើតឯកសារ',
                    'meta'     => 'ការព្រាងឯកសារ',
                    'subItems' => [
                        ['key' => 'staff-team-documents', 'label' => 'ឯកសារបុគ្គលិក'],
                        ['key' => 'staff-team-ranks',     'label' => 'ឋានន្តរស័ក្តិយោធា'],
                    ],
                ]
            ],
        ],
        [
            'label' => 'បុគ្គលិកសាកល្បង',
            'items' => [
                ['key' => 'test-taking-staff',           'label' => 'បុគ្គលិកសាកល្បង',      'meta' => 'បញ្ជី'],
                ['key' => 'register-staff',              'label' => 'បានចុះឈ្មោះ',           'meta' => 'បញ្ជី'],
                [
                    'key'      => 'create-test-taking-staff-docs',
                    'label'    => 'បង្កើតឯកសារ',
                    'meta'     => 'ឯកសារ',
                    'subItems' => [
                        ['key' => 'test-taking-staff-documents', 'label' => 'ឯកសារបុគ្គលិក'],
                        ['key' => 'test-taking-staff-ranks',     'label' => 'ឋានន្តរស័ក្តិបុគ្គលិក'],
                    ],
                ],
            ],
        ],
        [
            'label' => 'ប្រព័ន្ធ',
            'items' => [
                ['key' => 'users',                      'label' => 'អ្នកប្រើប្រាស់',         'meta' => 'គណនី'],
                ['key' => 'profile',                    'label' => 'ប្រវត្តិរូប',            'meta' => 'ព័ត៌មានគណនី'],
                ['key' => 'design-template',            'label' => 'រចនាទំព័រដើម',           'meta' => 'ប្លង់'],
                ['key' => 'staff-team-template',        'label' => 'គំរូបុគ្គលិកក្រុមការងារទី៣', 'meta' => 'កែ UI'],
                ['key' => 'course-template',            'label' => 'គំរូវគ្គសិក្សា',         'meta' => 'គំរូ'],
                ['key' => 'test-taking-staff-template', 'label' => 'គំរូបុគ្គលិកសាកល្បង',  'meta' => 'គំរូ'],
            ],
        ],
    ];

    $iconMap = [
        'overview'                    => '<path d="M3 12.5 12 4l9 8.5"/><path d="M5.5 11.5V20h13v-8.5"/>',
        'reports'                     => '<path d="M4 19h16"/><path d="M8 16V9"/><path d="M12 16V5"/><path d="M16 16v-3"/>',
        'applications'                => '<path d="M8 6h8"/><path d="M8 10h8"/><path d="M8 14h5"/><rect x="5" y="3" width="14" height="18" rx="2"/>',
        'registration-form'           => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><path d="M12 12v6"/><path d="M9 15h6"/>',
        'courses'                     => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
        'ranks'                       => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"/><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"/>',
        'levels'                      => '<path d="M7 7h10"/><path d="M7 12h10"/><path d="M7 17h6"/><rect x="4" y="4" width="16" height="16" rx="2"/>',
        'documents'                   => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        'create-new'                  => '<circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/>',
        'design-template'             => '<rect x="4" y="9" width="16" height="8" rx="2"/><path d="M4 13h16"/><path d="M4 5a2 2 0 0 1 2-2h3l2 2h7a2 2 0 0 1 2 2v2H4z"/>',
        'staff-team-template'         => '<rect x="4" y="9" width="16" height="8" rx="2"/><path d="M4 13h16"/><path d="M4 5a2 2 0 0 1 2-2h3l2 2h7a2 2 0 0 1 2 2v2H4z"/>',
        'course-template'             => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
        'create-staff-docs'           => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>',
        'staff-team'                  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'staff-team-ranks'            => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"/><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"/>',
        'staff-team-documents'        => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        'staff-management'            => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/>',
        'test-taking-staff'           => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'test-taking-staff-template'  => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'create-test-taking-staff-docs' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>',
        'test-taking-staff-ranks'     => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"/><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"/>',
        'test-taking-staff-documents' => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        'register-staff'              => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'users'                       => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/>',
        'profile'                     => '<path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z"/><path d="M4 20a8 8 0 0 1 16 0"/>',
    ];
@endphp

{{-- ═══════════════════════════════════════════
     Sidebar CSS — all static, no Blade in style
════════════════════════════════════════════ --}}
<style>
/* --- Base sidebar nav item --- */
.sb-link {
    display: flex; align-items: center; gap: 12px;
    padding: 8px 12px; border-radius: 8px;
    font-size: 13.5px; font-weight: 500;
    color: #475467; background: transparent;
    text-decoration: none;
    transition: background 150ms, color 150ms;
    cursor: pointer; border: none; width: 100%; text-align: left;
    list-style: none; outline: none; position: relative;
    margin-bottom: 2px;
}
.sb-link:hover { background: #F3F4F6; color: #111827; }

/* Active state for link */
.sb-link.is-active {
    background: #EEF2FF;
    color: #4338CA;
    font-weight: 600;
}
.sb-link.is-active:hover { background: #E0E7FF; }

/* Parent item active (has active sub-item) */
.sb-link.is-parent-active { background: transparent; color: #111827; font-weight: 600; }
.sb-link.is-parent-active:hover { background: #F3F4F6; }

/* --- Icon box --- */
.sb-icon {
    width: 20px; height: 20px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    color: #667085;
    transition: color 150ms;
}
.sb-link:hover .sb-icon { color: #344054; }
.sb-link.is-active .sb-icon { color: #4338CA; }
.sb-link.is-parent-active .sb-icon { color: #111827; }

/* --- Link text --- */
.sb-text { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* --- Chevron --- */
.sb-chevron { flex-shrink: 0; color: #98A2B3; transition: transform 220ms cubic-bezier(.4,0,.2,1); }
details[open] .sb-chevron { transform: rotate(180deg); color: #667085; }

/* --- Sub-items container --- */
.sb-sub {
    margin: 2px 0 6px 30px;
    padding-left: 14px;
    border-left: 1px solid #EAECF0;
    display: flex; flex-direction: column; gap: 2px;
}

/* --- Sub-item link --- */
.sb-sub-link {
    display: flex; align-items: center; gap: 8px;
    padding: 6px 12px; border-radius: 6px;
    font-size: 13px; font-weight: 500;
    color: #475467; text-decoration: none;
    transition: background 150ms, color 150ms;
}
.sb-sub-link:hover { background: #F3F4F6; color: #111827; }
.sb-sub-link.is-active { background: #EEF2FF; color: #4338CA; font-weight: 600; }
.sb-sub-link.is-active:hover { background: #E0E7FF; }

/* --- Group label --- */
.sb-group-label {
    padding: 12px 12px 6px;
    font-size: 11px; font-weight: 600;
    color: #98A2B3;
}

.admin-brand-copy {
    min-width: 0;
    flex: 1;
}

.admin-brand-copy > p:first-child {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.admin-brand-subtitle {
    white-space: nowrap;
}

.admin-sidebar-mobile-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* --- details summary: hide default marker --- */
summary { list-style: none; }
summary::-webkit-details-marker { display: none; }

/* ══════════════════════════════════════════
   Collapsed sidebar (desktop only)
══════════════════════════════════════════ */
@media (min-width: 1024px) {
    .admin-dashboard-grid { transition: grid-template-columns 280ms cubic-bezier(.4,0,.2,1); }

    body.sidebar-collapsed .admin-dashboard-grid {
        grid-template-columns: 72px minmax(0,1fr) !important;
    }

    /* Hide text/labels when collapsed */
    body.sidebar-collapsed .sb-text,
    body.sidebar-collapsed .sb-chevron,
    body.sidebar-collapsed .sb-group-label,
    body.sidebar-collapsed .sb-sub,
    body.sidebar-collapsed .admin-brand-copy,
    body.sidebar-collapsed .admin-footer-info {
        display: none !important;
    }

    /* Center icons when collapsed */
    body.sidebar-collapsed .sb-link {
        justify-content: center;
        padding: 10px 0 !important;
        margin-inline: 8px;
        width: auto;
    }
    body.sidebar-collapsed .sb-icon {
        width: 22px; height: 22px;
    }

    /* Collapsed navigation padding */
    body.sidebar-collapsed .admin-sidebar-scroll { padding: 12px 0 !important; }
    body.sidebar-collapsed .admin-sidebar__nav { gap: 4px; }
    body.sidebar-collapsed .admin-sidebar__group { margin-bottom: 8px !important; border-bottom: 1px solid #F3F4F6; padding-bottom: 8px; }
    body.sidebar-collapsed .admin-sidebar__group:last-child { border-bottom: none; }

    /* Footer avatar center */
    body.sidebar-collapsed .admin-footer-avatar-container {
        justify-content: center; padding: 8px 0;
        gap: 0; background: transparent !important;
    }
    body.sidebar-collapsed .admin-footer-avatar-container > div:first-child {
        width: 36px; height: 36px; font-size: 13px;
    }

    /* Collapse icon rotation */
    body.sidebar-collapsed #collapseIcon { transform: rotate(180deg); }

    /* Brand area */
    body.sidebar-collapsed .admin-brand-layer {
        justify-content: center !important;
        padding: 16px 0 !important;
    }
    body.sidebar-collapsed .admin-brand-logo-wrap {
        margin: 0 !important;
    }

    /* Tooltip on hover when collapsed */
    body.sidebar-collapsed .sb-link[data-label]:hover::after {
        content: attr(data-label);
        position: absolute;
        left: calc(100% + 8px);
        top: 50%; transform: translateY(-50%);
        background: #111827;
        color: #fff;
        font-size: 12.5px; font-weight: 500;
        padding: 6px 12px;
        border-radius: 6px;
        white-space: nowrap;
        pointer-events: none;
        z-index: 100;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        letter-spacing: 0;
    }

    .admin-sidebar-mobile-close {
        display: none !important;
    }
}

@media (max-width: 639px) {
    .admin-brand-layer {
        padding: 14px 14px !important;
        height: auto !important;
        min-height: 60px;
    }

    .admin-brand-layer > div:first-child {
        min-width: 0;
        gap: 10px !important;
    }

    .admin-brand-logo-wrap {
        width: 32px !important;
        height: 32px !important;
    }

    .admin-brand-copy {
        min-width: 0;
    }

    .admin-brand-copy > p:first-child {
        font-size: 12px !important;
    }

    .admin-brand-subtitle {
        display: none;
    }
}
</style>

<aside class="admin-sidebar" data-admin-sidebar>
    <button type="button" class="admin-sidebar__backdrop lg:hidden" data-admin-sidebar-close aria-label="Close"></button>

    <div class="admin-sidebar__panel" style="background:#FAFBFD; display:flex; flex-direction:column; height:100%;">

        {{-- ── Brand header ── --}}
        <div class="admin-brand-layer" style="position:relative; padding:16px 18px; border-bottom:1px solid #EAECF2; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; height: 61px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="admin-brand-logo-wrap" style="width:30px; height:30px; border-radius:8px; overflow:hidden; flex-shrink:0; box-shadow:0 2px 5px rgba(0,0,0,.08); border: 1px solid #fff;">
                    <img src="{{ asset('images/logo_admin.jpg') }}" alt="Logo" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="admin-brand-copy">
                    <p style="margin:0; font-size:13px; font-weight:700; color:#111827; line-height:1.2; letter-spacing:-.01em;">មជ្ឈមណ្ឌលហ្វឹកហ្វឺនភូមិសាស្រ្តយោធា
</p>
                    <p class="admin-brand-subtitle" style="margin:2px 0 0; font-size:10px; font-weight:500; color:#9CA3AF; text-transform:uppercase; letter-spacing:.1em;">ផ្ទាំងគ្រប់គ្រង</p>
                </div>
            </div>

            {{-- Floating collapse button on right border (desktop only) --}}
            <button id="sidebarCollapseBtn"
                class="hidden lg:flex"
                type="button" title="Toggle Sidebar"
                style="position:absolute; right:-12px; top:50%; transform:translateY(-50%); width:24px; height:24px; border-radius:9999px; border:1px solid #E5E7EB; background:#FFFFFF; align-items:center; justify-content:center; color:#6B7280; box-shadow:0 2px 6px rgba(0,0,0,.08); z-index:50;">
                <svg id="collapseIcon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition:transform 280ms cubic-bezier(.4,0,.2,1);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                </svg>
            </button>

            {{-- Mobile close button --}}
            <button type="button" class="admin-sidebar-mobile-close lg:hidden" data-admin-sidebar-close aria-label="Close"
                style="width:32px; height:32px; border-radius:9999px; border:1px solid #E5E7EB; color:#6B7280; background:#FFFFFF;"
                onmouseover="this.style.background='#F9FAFB'"
                onmouseout="this.style.background='#FFFFFF'"
                onclick="document.body.removeAttribute('data-admin-sidebar-open');">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>

        {{-- ── Navigation ── --}}
        <div class="admin-sidebar-scroll" style="flex:1; overflow-y:auto; padding:10px 8px;">
            <nav class="admin-sidebar__nav" style="display:flex; flex-direction:column; gap:0;">
                @foreach ($groups as $group)
                    @php
                        $visibleItems = collect($group['items'])
                            ->filter(function (array $item) use ($canAccessSection) {
                                if (isset($item['subItems'])) {
                                    return collect($item['subItems'])
                                        ->contains(fn (array $subItem): bool => $canAccessSection($subItem['accessKey'] ?? $subItem['key']));
                                }

                                return $canAccessSection($item['accessKey'] ?? $item['key']);
                            })
                            ->values();
                    @endphp
                    @continue($visibleItems->isEmpty())
                    <div class="admin-sidebar__group" style="margin-bottom:18px;">
                        <div class="sb-group-label">{{ $group['label'] }}</div>
                        <div style="display:flex; flex-direction:column; gap:1px;">

                            @foreach ($visibleItems as $item)
                                @if(isset($item['subItems']))
                                    @php
                                        $visibleSubItems = collect($item['subItems'])
                                            ->filter(fn (array $subItem): bool => $canAccessSection($subItem['key']))
                                            ->values();
                                        $hasActiveSubItem = $visibleSubItems->contains('key', $section);
                                    @endphp
                                    @continue($visibleSubItems->isEmpty())
                                    <details class="admin-sidebar__details" @if($hasActiveSubItem) open @endif>

                                        <summary class="sb-link {{ $hasActiveSubItem ? 'is-parent-active' : '' }}" data-label="{{ $item['label'] }}">
                                            <span class="sb-icon">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    {!! $iconMap[$item['key']] ?? $iconMap['profile'] !!}
                                                </svg>
                                            </span>
                                            <span class="sb-text">{{ $item['label'] }}</span>
                                            <svg class="sb-chevron" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                            </svg>
                                        </summary>

                                        <div class="sb-sub {{ $hasActiveSubItem ? 'has-active' : '' }}">
                                            @foreach ($visibleSubItems as $subItem)
                                                @php
                                                    $active = $section === $subItem['key'];
                                                    $href   = $subItem['href'] ?? route('admin.home', ['section' => $subItem['key']]);
                                                @endphp
                                                <a href="{{ $href }}" class="sb-sub-link {{ $active ? 'is-active' : '' }}">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                                                        {!! $iconMap[$subItem['key']] ?? $iconMap['profile'] !!}
                                                    </svg>
                                                    <span class="sb-text">{{ $subItem['label'] }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>

                                @else
                                    @php
                                        $active = $section === $item['key'];
                                        $href   = $item['href'] ?? route('admin.home', ['section' => $item['key']]);
                                    @endphp
                                    <a href="{{ $href }}" class="sb-link {{ $active ? 'is-active' : '' }}" data-label="{{ $item['label'] }}">
                                        <span class="sb-icon">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                {!! $iconMap[$item['key']] ?? $iconMap['profile'] !!}
                                            </svg>
                                        </span>
                                        <span class="sb-text">{{ $item['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach

                        </div>
                    </div>
                @endforeach
            </nav>
        </div>

        {{-- ── User footer ── --}}
        <div class="admin-sidebar-footer" style="padding:8px 10px; border-top:1px solid #EAECF2; flex-shrink:0;">
            <div class="admin-footer-avatar-container" style="display:flex; align-items:center; gap:9px; padding:8px 6px; border-radius:10px; background:#F3F5FA; transition:background 150ms;">
                <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#6366F1,#4F46E5); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0; box-shadow:0 2px 8px rgba(79,70,229,.3);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="admin-footer-info" style="flex:1; min-width:0; display:flex; align-items:center; justify-content:space-between;">
                    <div style="min-width:0;">
                        <p style="margin:0; font-size:12px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                        <p style="margin:1px 0 0; font-size:10.5px; color:#9CA3AF; font-weight:500;">អ្នកគ្រប់គ្រង</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}" style="flex-shrink:0; margin-left:6px;">
                        @csrf
                        <button type="submit" title="Logout"
                            style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:7px; border:1px solid #E5E7EB; background:#fff; color:#9CA3AF; cursor:pointer; transition:all 150ms;"
                            onmouseover="this.style.background='#FEF2F2';this.style.color='#EF4444';this.style.borderColor='#FECACA';"
                            onmouseout="this.style.background='#fff';this.style.color='#9CA3AF';this.style.borderColor='#E5E7EB';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('sidebarCollapseBtn');
        if (!btn) return;

        function apply(collapsed) {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            localStorage.setItem('sidebarCollapsed', collapsed ? 'true' : 'false');
        }

        // Restore saved state (default: expanded)
        var saved = localStorage.getItem('sidebarCollapsed');
        apply(saved === 'true');

        btn.addEventListener('click', function() {
            apply(!document.body.classList.contains('sidebar-collapsed'));
        });
    });
</script>
