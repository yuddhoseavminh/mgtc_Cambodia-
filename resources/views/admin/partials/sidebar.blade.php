@php
    $groups = [
        [
            'label' => 'ផ្ទាំងគ្រប់គ្រង',
            'items' => [
                ['key' => 'overview',  'label' => 'ទិដ្ឋភាពទូទៅ',  'meta' => 'សង្ខេប និងស្ថិតិ'],
                ['key' => 'reports',   'label' => 'របាយការណ៍',    'meta' => 'ក្រាហ្វ និងជំមោ'],
            ],
        ],
        [
            'label' => 'ការចុះឈ្មោះ',
            'items' => [
                ['key' => 'applications', 'label' => 'ពាក្យស្នើសុំ', 'meta' => 'បញ្ជីចុះឈ្មោះ'],
                [
                    'key'      => 'create-new',
                    'label'    => 'ការបង្កើតថ្មី',
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
            'label' => 'ក្រុមបុគ្គលិក',
            'items' => [
                ['key' => 'staff-team',           'label' => 'បុគ្គលិកក្រុមទី៣',   'meta' => 'បញ្ជីបុគ្គលិក'],
                ['key' => 'staff-management',      'label' => 'គ្រប់គ្រងបុគ្គលិក', 'meta' => 'ទិន្នន័យបុគ្គលិក'],
                ['key' => 'staff-team-documents', 'label' => 'ឯកសារបុគ្គលិក',    'meta' => 'ប្រភេទឯកសារ'],
                ['key' => 'staff-team-ranks',     'label' => 'ឋានន្តរស័ក្តិយោធា', 'meta' => 'ការកំណត់'],
            ],
        ],
        [
            'label' => 'បុគ្គលិកសាកល្បង',
            'items' => [
                ['key' => 'test-taking-staff',           'label' => 'បុគ្គលិកសាកល្បង',      'meta' => 'បញ្ជី'],
                ['key' => 'test-taking-staff-ranks',     'label' => 'ឋានន្តរស័ក្តិបុគ្គលិក', 'meta' => 'ការកំណត់'],
                ['key' => 'test-taking-staff-documents', 'label' => 'ឯកសារបុគ្គលិក',       'meta' => 'ឯកសារ'],
                ['key' => 'register-staff',              'label' => 'បានចុះឈ្មោះ',           'meta' => 'បញ្ជី'],
            ],
        ],
        [
            'label' => 'ប្រព័ន្ធ',
            'items' => [
                ['key' => 'users',                      'label' => 'អ្នកប្រើប្រាស់',         'meta' => 'គណនី'],
                ['key' => 'profile',                    'label' => 'ប្រវត្តិរូប',            'meta' => 'ព័ត៌មានគណនី'],
                ['key' => 'design-template',            'label' => 'រចនាទំព័រដើម',           'meta' => 'ប្លង់'],
                ['key' => 'course-template',            'label' => 'គំរូវគ្គសិក្សា',         'meta' => 'គំរូ'],
                ['key' => 'test-taking-staff-template', 'label' => 'គំរូបុគ្គលិកសាកល្បង',  'meta' => 'គំរូ'],
            ],
        ],
    ];

    $iconMap = [
        'overview'                    => '<path d="M3 12.5 12 4l9 8.5"/><path d="M5.5 11.5V20h13v-8.5"/>',
        'reports'                     => '<path d="M4 19h16"/><path d="M8 16V9"/><path d="M12 16V5"/><path d="M16 16v-3"/>',
        'applications'                => '<path d="M8 6h8"/><path d="M8 10h8"/><path d="M8 14h5"/><rect x="5" y="3" width="14" height="18" rx="2"/>',
        'courses'                     => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
        'ranks'                       => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"/><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"/>',
        'levels'                      => '<path d="M7 7h10"/><path d="M7 12h10"/><path d="M7 17h6"/><rect x="4" y="4" width="16" height="16" rx="2"/>',
        'documents'                   => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        'create-new'                  => '<circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/>',
        'design-template'             => '<rect x="4" y="9" width="16" height="8" rx="2"/><path d="M4 13h16"/><path d="M4 5a2 2 0 0 1 2-2h3l2 2h7a2 2 0 0 1 2 2v2H4z"/>',
        'course-template'             => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
        'staff-team'                  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'staff-team-ranks'            => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"/><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"/>',
        'staff-team-documents'        => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        'staff-management'            => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/>',
        'test-taking-staff'           => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'test-taking-staff-template'  => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
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
    display: flex; align-items: center; gap: 8px;
    padding: 7px 8px; border-radius: 8px;
    font-size: 13.5px; font-weight: 500;
    color: #374151; background: transparent;
    text-decoration: none;
    transition: background 120ms, color 120ms;
    cursor: pointer; border: none; width: 100%; text-align: left;
    list-style: none; outline: none;
}
.sb-link:hover { background: #F9FAFB; }
.sb-link.is-active {
    background: #EEF2FF; color: #4F46E5;
    font-weight: 600;
    border-left: 2.5px solid #4F46E5;
    padding-left: 6px;
}
.sb-link.is-active:hover { background: #E0E7FF; }

/* Parent item active (has active sub-item) */
.sb-link.is-parent-active { background: #F5F3FF; color: #4F46E5; font-weight: 600; }
.sb-link.is-parent-active:hover { background: #EDE9FE; }

/* --- Icon box --- */
.sb-icon {
    width: 28px; height: 28px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: 7px; background: #F3F4F6; color: #6B7280;
    transition: background 120ms, color 120ms;
}
.is-active .sb-icon, .is-parent-active .sb-icon {
    background: #E0E7FF; color: #4F46E5;
}

/* --- Link text --- */
.sb-text { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* --- Chevron --- */
.sb-chevron { flex-shrink: 0; color: #CBD5E1; transition: transform 200ms; }
details[open] .sb-chevron { transform: rotate(180deg); }

/* --- Sub-items container --- */
.sb-sub {
    margin: 4px 0 4px 36px;
    padding-left: 12px;
    border-left: 2px solid #E5E7EB;
    display: flex; flex-direction: column; gap: 1px;
}
.sb-sub.has-active { border-color: #C7D2FE; }

/* --- Sub-item link --- */
.sb-sub-link {
    display: flex; align-items: center; gap: 7px;
    padding: 5px 8px; border-radius: 6px;
    font-size: 12.5px; font-weight: 500;
    color: #6B7280; text-decoration: none;
    transition: background 120ms, color 120ms;
}
.sb-sub-link:hover { background: #F9FAFB; color: #374151; }
.sb-sub-link.is-active { background: #EEF2FF; color: #4F46E5; font-weight: 600; }
.sb-sub-link.is-active:hover { background: #E0E7FF; }

/* --- Group label --- */
.sb-group-label {
    padding: 0 8px 6px;
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .12em;
    color: #9CA3AF;
}

/* --- Collapsed sidebar --- */
@media (min-width: 1024px) {
    .admin-dashboard-grid { transition: grid-template-columns 250ms cubic-bezier(.4,0,.2,1); }

    body.sidebar-collapsed .admin-dashboard-grid {
        grid-template-columns: 68px minmax(0,1fr) !important;
    }
    body.sidebar-collapsed .sb-text,
    body.sidebar-collapsed .sb-chevron,
    body.sidebar-collapsed .sb-group-label,
    body.sidebar-collapsed .sb-sub,
    body.sidebar-collapsed .admin-brand-text,
    body.sidebar-collapsed .admin-footer-info {
        display: none !important;
    }
    body.sidebar-collapsed .sb-link {
        justify-content: center;
        padding: 7px 0 !important;
        border-left: none !important;
    }
    body.sidebar-collapsed .admin-sidebar-scroll { padding: 12px 8px !important; }
    body.sidebar-collapsed .admin-footer-avatar-container { justify-content: center; padding: 8px 0; }
    body.sidebar-collapsed #collapseIcon { transform: rotate(180deg); }
    body.sidebar-collapsed .admin-brand-layer { justify-content: center !important; padding: 16px 0 !important; }
}
</style>

<aside class="admin-sidebar" data-admin-sidebar>
    <button type="button" class="admin-sidebar__backdrop lg:hidden" data-admin-sidebar-close aria-label="Close"></button>

    <div class="admin-sidebar__panel" style="background:#fff; border-right:1px solid #F3F4F6; display:flex; flex-direction:column; height:100%;">

        {{-- ── Brand header ── --}}
        <div class="admin-brand-layer" style="position:relative; padding:16px 20px; border-bottom:1px solid #F3F4F6; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:10px; overflow:hidden; flex-shrink:0; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                    <img src="{{ asset('images/logo_admin.jpg') }}" alt="Logo" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="admin-brand-text">
                    <p style="margin:0; font-size:13px; font-weight:700; color:#111827; line-height:1.2;">គ្រប់គ្រងវគ្គសិក្សា</p>
                    <p style="margin:2px 0 0; font-size:10.5px; font-weight:500; color:#9CA3AF; text-transform:uppercase; letter-spacing:.08em;">Admin Panel</p>
                </div>
            </div>

            {{-- Floating collapse button on right border --}}
            <button id="sidebarCollapseBtn"
                class="hidden lg:flex admin-sidebar-collapse-btn"
                type="button" title="Toggle Sidebar"
                style="position:absolute; right:-14px; top:50%; transform:translateY(-50%); width:28px; height:28px; border-radius:50%; border:1px solid #E5E7EB; background:#fff; color:#9CA3AF; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 1px 4px rgba(0,0,0,.08); z-index:10;">
                <svg id="collapseIcon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                </svg>
            </button>

            {{-- Mobile close --}}
            <button type="button" class="lg:hidden" data-admin-sidebar-close aria-label="Close"
                style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #E5E7EB; background:#fff; color:#6B7280; cursor:pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>

        {{-- ── Navigation ── --}}
        <div class="admin-sidebar-scroll" style="flex:1; overflow-y:auto; padding:12px;">
            <nav class="admin-sidebar__nav">
                @foreach ($groups as $group)
                    <div class="admin-sidebar__group" style="margin-bottom:20px;">
                        <div class="sb-group-label">{{ $group['label'] }}</div>
                        <div style="display:flex; flex-direction:column; gap:2px;">

                            @foreach ($group['items'] as $item)
                                @if(isset($item['subItems']))
                                    @php $hasActiveSubItem = collect($item['subItems'])->contains('key', $section); @endphp
                                    <details class="admin-sidebar__details" @if($hasActiveSubItem) open @endif>

                                        <summary class="sb-link {{ $hasActiveSubItem ? 'is-parent-active' : '' }}">
                                            <span class="sb-icon">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    {!! $iconMap[$item['key']] ?? $iconMap['profile'] !!}
                                                </svg>
                                            </span>
                                            <span class="sb-text">{{ $item['label'] }}</span>
                                            <svg class="sb-chevron" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                            </svg>
                                        </summary>

                                        <div class="sb-sub {{ $hasActiveSubItem ? 'has-active' : '' }}">
                                            @foreach ($item['subItems'] as $subItem)
                                                @php
                                                    $active = $section === $subItem['key'];
                                                    $href   = route('admin.home', ['section' => $subItem['key']]);
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
                                        $href   = route('admin.home', ['section' => $item['key']]);
                                    @endphp
                                    <a href="{{ $href }}" class="sb-link {{ $active ? 'is-active' : '' }}">
                                        <span class="sb-icon">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
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
        <div class="admin-footer-band" style="padding:10px 12px; border-top:1px solid #F3F4F6;">
            <div class="admin-footer-avatar-container" style="display:flex; align-items:center; gap:10px; padding:8px; border-radius:10px; background:#F9FAFB;">
                <div style="width:32px; height:32px; border-radius:50%; background:#4F46E5; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="admin-footer-info" style="flex:1; min-width:0; display:flex; align-items:center; justify-content:space-between;">
                    <div style="min-width:0;">
                        <p style="margin:0; font-size:12.5px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                        <p style="margin:1px 0 0; font-size:11px; color:#9CA3AF;">អ្នកគ្រប់គ្រង</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}" style="flex-shrink:0; margin-left:6px;">
                        @csrf
                        <button type="submit" title="Logout"
                            style="width:30px; height:30px; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #E5E7EB; background:#fff; color:#9CA3AF; cursor:pointer;"
                            onmouseover="this.style.background='#FEF2F2';this.style.color='#EF4444';"
                            onmouseout="this.style.background='#fff';this.style.color='#9CA3AF';">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

        // Default to expanded; only collapse if explicitly saved
        var saved = localStorage.getItem('sidebarCollapsed');
        apply(saved === 'true');

        btn.addEventListener('click', function() {
            apply(!document.body.classList.contains('sidebar-collapsed'));
        });
    });
</script>
