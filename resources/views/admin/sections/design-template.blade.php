@php
    $activeTemplateTab = request()->query('template_tab') === 'staff' ? 'staff' : 'portal';

    $templateMode = $activeTemplateTab === 'staff'
        ? [
            'eyebrow' => 'Staff Profile Branding',
            'title' => 'កែសម្រួលគំរូបុគ្គលិក',
            'description' => 'កំណត់ចំណងជើង និងឡូហ្គោសម្រាប់ទំព័រប្រវត្តិរូបបុគ្គលិក។',
            'preview_href' => route('portal.home'),
            'preview_label' => 'មើលទំព័រសាធារណៈ',
            'headline' => $portalContent->staff_title ?: '-',
            'subline' => $portalContent->staff_subtitle ?: '-',
            'asset_label' => 'Staff Logo',
            'asset_value' => $portalContent->staff_logo_path ? 'មាន' : 'មិនទាន់មាន',
        ]
        : [
            'eyebrow' => 'Public Home Layout',
            'title' => 'កែសម្រួលទំព័រដើមសាធារណៈ',
            'description' => 'គ្រប់គ្រង badge, ចំណងជើង, banner និង feature cards សម្រាប់ទំព័រដើម។',
            'preview_href' => route('portal.home'),
            'preview_label' => 'មើលទំព័រដើម',
            'headline' => $portalContent->title ?: '-',
            'subline' => $portalContent->badge ?: '-',
            'asset_label' => 'Hero Banner',
            'asset_value' => $portalContent->banner_image_path ? 'មាន' : 'មិនទាន់មាន',
        ];

    $designStats = [
        [
            'label' => 'ផ្ទាំងកំពុងកែ',
            'value' => $activeTemplateTab === 'staff' ? 'Staff' : 'Portal',
            'hint' => 'ផ្លាស់ប្តូរតាម sub-tab ខាងលើ។',
        ],
        [
            'label' => 'Asset សកម្ម',
            'value' => $templateMode['asset_value'],
            'hint' => $templateMode['asset_label'].' កំពុងភ្ជាប់ជាមួយទំព័រនេះ។',
        ],
        [
            'label' => 'Feature Cards',
            'value' => '3',
            'hint' => 'កាតបង្ហាញសង្ខេបនៅលើទំព័រដើម។',
        ],
    ];

    $quickLinks = [
        [
            'title' => 'ទំព័រដើមសាធារណៈ',
            'description' => 'ពិនិត្យទម្រង់នៅផ្នែក public home page។',
            'href' => route('portal.home'),
        ],
        [
            'title' => 'ទម្រង់ចុះឈ្មោះវគ្គ',
            'description' => 'ពិនិត្យការតភ្ជាប់រវាង template និង registration page។',
            'href' => route('registration.form'),
        ],
        [
            'title' => 'គំរូបុគ្គលិកសាកល្បង',
            'description' => 'បន្តទៅទំព័រកែសម្រួល template បុគ្គលិកសាកល្បង។',
            'href' => route('admin.home', ['section' => 'test-taking-staff-template']),
        ],
    ];
@endphp

<div class="space-y-6" x-data="{ activeTab: '{{ $activeTemplateTab }}' }">
    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#ffffff,#f8fbff,#eef4ff)] p-6 shadow-[0_18px_45px_rgba(15,23,42,0.06)] sm:p-7">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $templateMode['eyebrow'] }}</p>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-950">{{ $templateMode['title'] }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-500">{{ $templateMode['description'] }}</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[24rem]">
                <a href="{{ $templateMode['preview_href'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-[1.2rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    {{ $templateMode['preview_label'] }}
                </a>
                <a href="{{ route('admin.home', ['section' => 'profile']) }}" class="inline-flex items-center justify-center rounded-[1.2rem] bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    គណនីអេដមីន
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($designStats as $card)
            <article class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.04)]">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $card['hint'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_320px]">
        <div>
            @include('admin.sections.portal-content', ['showDesignTabs' => false])
        </div>

        {{-- <aside class="space-y-4">
            <article class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.04)]">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ស្ថានភាពបច្ចុប្បន្ន</p>
                <div class="mt-4 space-y-4">
                    <div class="rounded-[1.2rem] bg-slate-50 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">ចំណងជើងសកម្ម</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $templateMode['headline'] }}</p>
                    </div>
                    <div class="rounded-[1.2rem] bg-slate-50 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">អត្ថបទរង</p>
                        <p class="mt-2 text-sm font-medium text-slate-700">{{ $templateMode['subline'] }}</p>
                    </div>
                    <div class="rounded-[1.2rem] bg-slate-50 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $templateMode['asset_label'] }}</p>
                        <p class="mt-2 text-sm font-medium text-slate-700">{{ $templateMode['asset_value'] }}</p>
                    </div>
                </div>
            </article>

            <article class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.04)]">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ជំហានមុនបោះផ្សាយ</p>
                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                    <p>1. កែសម្រួលអត្ថបទ និង asset ដែលត្រូវការ។</p>
                    <p>2. ចុចប៊ូតុងរក្សាទុកខាងក្រោម។</p>
                    <p>3. បើក public page ដើម្បីពិនិត្យទម្រង់ជាក់ស្តែង។</p>
                </div>
            </article>

            @foreach ($quickLinks as $link)
                <a href="{{ $link['href'] }}" target="_blank" rel="noopener noreferrer" class="block rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.04)] transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50/70">
                    <p class="text-sm font-semibold text-slate-900">{{ $link['title'] }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $link['description'] }}</p>
                </a>
            @endforeach
        </aside> --}}
    </section>
</div>
