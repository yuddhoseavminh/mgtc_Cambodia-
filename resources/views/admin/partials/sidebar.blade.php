@php
    $groups = [
        [
            'label' => 'ផ្ទាំងគ្រប់គ្រង',
            'items' => [
                ['key' => 'overview', 'label' => 'ទិដ្ឋភាពទូទៅ', 'meta' => 'សង្ខេប និងស្ថិតិរហ័ស'],
                ['key' => 'reports', 'label' => 'របាយការណ៍', 'meta' => 'ក្រាហ្វ និងសកម្មភាព'],
            ],
        ],
        [
            'label' => 'ការចុះឈ្មោះ',
            'items' => [
                ['key' => 'applications', 'label' => 'ពាក្យស្នើសុំ', 'meta' => 'បញ្ជីពាក្យចុះឈ្មោះ'],
                ['key' => 'courses', 'label' => 'វគ្គសិក្សា', 'meta' => 'បញ្ជីវគ្គសិក្សា'],
                ['key' => 'ranks', 'label' => 'ឋានន្តរស័ក្តិ', 'meta' => 'ជម្រើសឋានន្តរស័ក្តិ'],
                ['key' => 'levels', 'label' => 'កម្រិតសិក្សា', 'meta' => 'ការកំណត់កម្រិតសិក្សា'],
                ['key' => 'documents', 'label' => 'ឯកសារ', 'meta' => 'ប្រភេទឯកសារចាំបាច់'],
            ],
        ],
        [
            'label' => 'ក្រុមបុគ្គលិក',
            'items' => [
                ['key' => 'staff-team', 'label' => 'បុគ្គលិកក្រុមការងារទី៣', 'meta' => 'បញ្ជីបុគ្គលិកក្រុមការងារទី៣'],
                ['key' => 'staff-management', 'label' => 'គ្រប់គ្រងបុគ្គលិក', 'meta' => 'គ្រប់គ្រងទិន្នន័យបុគ្គលិក'],
                ['key' => 'staff-team-documents', 'label' => 'ឯកសារបុគ្គលិកក្រុមការងារទី៣', 'meta' => 'ប្រភេទឯកសារសម្រាប់បុគ្គលិកក្រុមការងារទី៣'],
                ['key' => 'staff-team-ranks', 'label' => 'ឋានន្តរស័ក្តិយោធា', 'meta' => 'សម្រាប់បុគ្គលិកក្រុមការងារទី៣'],
            ],
        ],
        [
            'label' => 'បុគ្គលិកសាកល្បង',
            'items' => [
                ['key' => 'test-taking-staff', 'label' => 'បុគ្គលិកសាកល្បង', 'meta' => 'បញ្ជីបុគ្គលិកសាកល្បង'],
                ['key' => 'test-taking-staff-ranks', 'label' => 'ឋានន្តរស័ក្តិបុគ្គលិក', 'meta' => 'ការកំណត់ឋានន្តរស័ក្តិ'],
                ['key' => 'test-taking-staff-documents', 'label' => 'ឯកសារបុគ្គលិក', 'meta' => 'ឯកសារចាំបាច់'],
                ['key' => 'register-staff', 'label' => 'បុគ្គលិកបានចុះឈ្មោះ', 'meta' => 'បញ្ជីការចុះឈ្មោះ'],
            ],
        ],
        [
            'label' => 'ប្រព័ន្ធ',
            'items' => [
                ['key' => 'users', 'label' => 'អ្នកប្រើប្រាស់', 'meta' => 'គណនី និងសិទ្ធិ'],
                ['key' => 'profile', 'label' => 'ប្រវត្តិរូប', 'meta' => 'ព័ត៌មានគណនីអ្នកគ្រប់គ្រង'],
                ['key' => 'design-template', 'label' => 'រចនាទំព័រដើម', 'meta' => 'ប្លង់ទំព័រដើម'],
                ['key' => 'course-template', 'label' => 'គំរូវគ្គសិក្សា', 'meta' => 'គំរូទំព័រវគ្គសិក្សា'],
                ['key' => 'test-taking-staff-template', 'label' => 'គំរូបុគ្គលិកសាកល្បង', 'meta' => 'គំរូទំព័របុគ្គលិកសាកល្បង'],
            ],
        ],
    ];

    $iconMap = [
        'overview' => '<path d="M3 12.5 12 4l9 8.5"></path><path d="M5.5 11.5V20h13v-8.5"></path>',
        'reports' => '<path d="M4 19h16"></path><path d="M8 16V9"></path><path d="M12 16V5"></path><path d="M16 16v-3"></path>',
        'applications' => '<path d="M8 6h8"></path><path d="M8 10h8"></path><path d="M8 14h5"></path><rect x="5" y="3" width="14" height="18" rx="2"></rect>',
        'courses' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"></path>',
        'ranks' => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"></path><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"></path>',
        'levels' => '<path d="M7 7h10"></path><path d="M7 12h10"></path><path d="M7 17h6"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect>',
        'documents' => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"></path><path d="M14 3v6h6"></path><path d="M9 13h6"></path><path d="M9 17h6"></path>',
        'design-template' => '<path d="M4 5a2 2 0 0 1 2-2h3l2 2h7a2 2 0 0 1 2 2v3"></path><path d="M4 13h16"></path><path d="M8 21h8"></path><path d="M10 17h4"></path><rect x="4" y="9" width="16" height="8" rx="2"></rect>',
        'course-template' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"></path>',
        'staff-team' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
        'staff-team-ranks' => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"></path><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"></path>',
        'staff-team-documents' => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"></path><path d="M14 3v6h6"></path><path d="M9 13h6"></path><path d="M9 17h6"></path>',
        'staff-management' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><path d="M20 8v6"></path><path d="M23 11h-6"></path>',
        'test-taking-staff' => '<path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>',
        'test-taking-staff-template' => '<path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>',
        'test-taking-staff-ranks' => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"></path><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"></path>',
        'test-taking-staff-documents' => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"></path><path d="M14 3v6h6"></path><path d="M9 13h6"></path><path d="M9 17h6"></path>',
        'register-staff' => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><path d="M20 8v6"></path><path d="M23 11h-6"></path>',
        'profile' => '<path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z"></path><path d="M4 20a8 8 0 0 1 16 0"></path><path d="M19 7h2"></path><path d="M20 6v2"></path>',
    ];
@endphp

<aside class="admin-sidebar" data-admin-sidebar>
    <button type="button" class="admin-sidebar__backdrop lg:hidden" data-admin-sidebar-close aria-label="Close navigation"></button>

    <div class="admin-sidebar__panel border-b border-slate-200/80 bg-white/92 backdrop-blur lg:border-b-0 lg:border-r">
        <div class="flex h-full flex-col">
            <div class="border-b border-slate-200/80 px-5 py-5 sm:px-6 sm:py-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 overflow-hidden rounded-2xl shadow-[0_10px_24px_rgba(15,23,42,0.16)] ring-1 ring-slate-200">
                            <img src="{{ asset('images/logo_admin.jpg') }}" alt="និមិត្តសញ្ញាអ្នកគ្រប់គ្រង" class="h-full w-full scale-110 object-cover">
                        </div>
                        <div>
                            <p class="text-base font-semibold tracking-tight text-slate-950">គ្រប់គ្រងវគ្គសិក្សាយោធា</p>
                            <p class="mt-1 text-xs font-medium tracking-[0.08em] text-slate-400">ប្រព័ន្ធចុះឈ្មោះ</p>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700 lg:hidden"
                        data-admin-sidebar-close
                        aria-label="Close navigation"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="admin-sidebar-scroll flex-1 overflow-y-auto px-4 py-5">
                <nav class="admin-sidebar__nav space-y-6">
                    @foreach ($groups as $group)
                        <div class="admin-sidebar__group">
                            <div class="px-2 pb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $group['label'] }}</div>

                            <div class="space-y-1.5">
                                @foreach ($group['items'] as $item)
                                    @php
                                        $active = $section === $item['key'];
                                        $href = route('admin.home', ['section' => $item['key']]);
                                    @endphp

                                    <a
                                        href="{{ $href }}"
                                        class="admin-sidebar__link flex items-center gap-3 rounded-2xl px-3 py-3 transition {{ $active ? 'bg-slate-900 text-white shadow-[0_14px_30px_rgba(15,23,42,0.14)]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' }}"
                                    >
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $active ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500' }}">
                                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                {!! $iconMap[$item['key']] ?? $iconMap['profile'] !!}
                                            </svg>
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-semibold">{{ $item['label'] }}</span>
                                            <span class="mt-0.5 block truncate text-xs {{ $active ? 'text-slate-300' : 'text-slate-400' }}">{{ $item['meta'] }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>

            <div class="border-t border-slate-200/80 px-4 py-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-slate-400">អ្នកគ្រប់គ្រង</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800">ចាកចេញ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</aside>
