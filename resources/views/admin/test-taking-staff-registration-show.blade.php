@extends('app')

@section('body')
    <style>
        .admin-title-font { font-family: 'Kantumruy Pro', sans-serif; }
        .khmer-font { font-family: 'Kantumruy Pro', sans-serif; }
        .registration-hero {
            background: linear-gradient(135deg, #0f172a 0%, #111827 56%, #075985 100%);
        }
        .registration-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 42px 42px;
            opacity: 0.14;
            pointer-events: none;
        }
    </style>

    @php
        $submittedAt = ($registration->submitted_at ?? $registration->created_at)
            ? ($registration->submitted_at ?? $registration->created_at)->timezone('Asia/Phnom_Penh')->khFormat('d/m/Y h:i A')
            : '-';

        $documentGroups = collect($registration->documents)
            ->groupBy(fn ($doc) => $doc->documentRequirement?->name_kh ?? $doc->documentRequirement?->name_en ?? 'ឯកសារភ្ជាប់ផ្សេងៗ')
            ->map(fn ($documents, $label) => [
                'label' => $label,
                'files' => $documents->values(),
            ])
            ->values();

        $documentCount = collect($registration->documents)->count();
        $documentGroupCount = $documentGroups->count();
        $previewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        $imagePreviewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $avatarInitial = strtoupper(substr($registration->name_latin ?: $registration->name_kh ?: 'T', 0, 1));
        $avatarUrl = route('test-taking-staff-registrations.avatar', [
            'testTakingStaffRegistration' => $registration,
            'v' => md5((string) $registration->avatar_path.'|'.optional($registration->updated_at)->timestamp),
        ]);

        $identityFields = [
            ['label' => 'គោត្តនាម-នាម', 'value' => $registration->name_kh, 'icon' => 'user', 'tone' => 'bg-sky-50 text-sky-700'],
            ['label' => 'ឈ្មោះឡាតាំង', 'value' => $registration->name_latin, 'icon' => 'signature', 'tone' => 'bg-indigo-50 text-indigo-700'],
            ['label' => 'អត្តលេខ', 'value' => $registration->id_number, 'icon' => 'id', 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'ឋានន្តរស័ក្តិ', 'value' => $registration->rank?->name_kh ?: $registration->rank?->name_en, 'icon' => 'rank', 'tone' => 'bg-amber-50 text-amber-700'],
            ['label' => 'ថ្ងៃខែឆ្នាំកំណើត', 'value' => optional($registration->date_of_birth)?->khFormat('d/m/Y'), 'icon' => 'calendar', 'tone' => 'bg-violet-50 text-violet-700'],
            ['label' => 'ថ្ងៃចូលបម្រើកងទ័ព', 'value' => optional($registration->military_service_day)?->khFormat('d/m/Y'), 'icon' => 'calendar-check', 'tone' => 'bg-rose-50 text-rose-700'],
            ['label' => 'លេខទូរស័ព្ទ', 'value' => $registration->phone_number, 'icon' => 'phone', 'tone' => 'bg-blue-50 text-blue-700'],
            ['label' => 'កាលបរិច្ឆេទដាក់ស្នើ', 'value' => $submittedAt, 'icon' => 'clock', 'tone' => 'bg-slate-50 text-slate-700'],
        ];

        $heroStats = [
            ['label' => 'លេខសម្គាល់កំណត់ត្រា', 'value' => '#'.$registration->id],
            ['label' => 'ឯកសារភ្ជាប់សរុប', 'value' => $documentCount],
            ['label' => 'ប្រភេទឯកសារក្នុងប្រព័ន្ធ', 'value' => $documentGroupCount],
            ['label' => 'ស្ថានភាពដាក់ស្នើចុងក្រោយ', 'value' => $submittedAt],
        ];

        $iconPaths = [
            'user' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'signature' => '<path d="m12 19 9 2"/><path d="M9 20c-1.5-1.5-2.5-3.5-2.5-5.5a9 9 0 0 1 18 0c0 2-1 4-2.5 5.5"/><path d="m15 10-4 4 2 2 4-4-2-2Z"/>',
            'id' => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 8h10"/><path d="M7 12h10"/><path d="M7 16h6"/>',
            'rank' => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"/><path d="M6 10.5V15c0 2 2.7 3.5 6 3.5s6-1.5 6-3.5v-4.5"/>',
            'calendar' => '<rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
            'calendar-check' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
            'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        ];

        $currentSection = 'register-staff';
    @endphp

    <div class="w-full bg-slate-50/50">
        <div class="dashboard-shell">
            <div class="grid min-h-screen lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => $currentSection])

                <main class="flex min-h-full flex-col">
                    @include('admin.partials.topbar', [
                        'title' => 'ព័ត៌មានបេក្ខជន',
                        'subtitle' => 'ការចុះឈ្មោះបុគ្គលិកសាកល្បង',
                        'currentSection' => $currentSection,
                    ])

                    <div class="flex-1 p-4 sm:p-6 lg:p-8">
                        <div class="mx-auto w-full max-w-[1180px] space-y-7">

                            {{-- Breadcrumbs --}}
                            <nav class="flex flex-wrap items-center gap-2 rounded-[1.25rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm text-slate-500 shadow-sm backdrop-blur">
                                <a href="{{ route('admin.home', ['section' => 'test-taking-staff']) }}" class="font-medium transition hover:text-slate-900">បុគ្គលិកសាកល្បង</a>
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                <a href="{{ route('admin.home', ['section' => 'register-staff']) }}" class="font-medium transition hover:text-slate-900">បញ្ជីដែលបានចុះឈ្មោះ</a>
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                <span class="font-bold text-slate-900 khmer-font">{{ $registration->name_kh }}</span>
                            </nav>

                            {{-- Premium Hero Card --}}
                            <div class="registration-hero relative overflow-hidden rounded-[2rem] p-6 text-white shadow-[0_24px_60px_rgba(15,23,42,0.18)] sm:p-8 lg:p-10">

                                <div class="relative z-10 flex flex-col gap-8 xl:flex-row xl:items-start xl:justify-between">
                                    <div class="flex flex-col gap-6 md:flex-row md:items-center">
                                        <div class="relative shrink-0">
                                            <div class="h-28 w-28 overflow-hidden rounded-[1.5rem] ring-1 ring-white/25 shadow-2xl sm:h-32 sm:w-32">
                                                @if ($registration->hasStoredAvatar())
                                                    <img src="{{ $avatarUrl }}" alt="{{ $registration->name_kh }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center bg-slate-800 text-4xl font-bold text-white khmer-font">
                                                        {{ $avatarInitial }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="absolute -bottom-2 -right-2 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500 text-white ring-4 ring-slate-900 shadow-lg shadow-emerald-950/25">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><path d="M20 6 9 17l-5-5"/></svg>
                                            </div>
                                        </div>

                                        <div class="min-w-0 space-y-4">
                                            <div>
                                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-200/90">Test-Taking Staff Identity</p>
                                                <h1 class="mt-2 break-words text-3xl font-bold leading-tight tracking-tight text-white sm:text-[2.65rem] khmer-font">{{ $registration->name_kh }}</h1>
                                                <p class="mt-2 text-lg font-semibold text-sky-100/80">{{ $registration->name_latin ?: '-' }}</p>
                                            </div>

                                            <div class="flex flex-wrap gap-2">
                                                <span class="rounded-full bg-white/10 px-4 py-2 text-xs font-bold text-white ring-1 ring-white/15 backdrop-blur-md">
                                                    {{ $registration->rank?->name_kh ?: 'គ្មានឋានន្តរស័ក្តិ' }}
                                                </span>
                                                <span class="rounded-full bg-emerald-400/15 px-4 py-2 text-xs font-bold text-emerald-200 ring-1 ring-emerald-300/25 backdrop-blur-md">
                                                    Verified Record
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row xl:justify-end">
                                        <a href="{{ route('admin.home', ['section' => 'register-staff']) }}" class="inline-flex h-12 min-w-[8.25rem] items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-5 text-sm font-bold text-white transition hover:bg-white/15 active:scale-95">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                                            <span>ត្រឡប់</span>
                                        </a>
                                        <a href="{{ route('admin.test-taking-staff-registrations.edit', $registration) }}" class="inline-flex h-12 min-w-[10rem] items-center justify-center gap-2 rounded-xl bg-white px-6 text-sm font-bold text-slate-900 shadow-xl shadow-slate-950/10 transition hover:bg-slate-100 active:scale-95">
                                            <svg class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            <span>កែប្រែព័ត៌មាន</span>
                                        </a>
                                    </div>
                                </div>

                                {{-- Quick Stats Row --}}
                                <div class="relative z-10 mt-8 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                                    @foreach ($heroStats as $stat)
                                        <div class="min-h-[6rem] rounded-[1.25rem] border border-white/10 bg-white/[0.08] p-4 shadow-inner shadow-white/5 transition hover:border-white/20 hover:bg-white/[0.12]">
                                            <p class="text-[11px] font-semibold tracking-[0.08em] text-sky-100/80 khmer-font">{{ $stat['label'] }}</p>
                                            <p class="mt-3 break-words text-xl font-bold leading-tight text-white">{{ $stat['value'] ?: '-' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
                                <div class="space-y-8">
                                    {{-- Information Dossier --}}
                                    <section class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm sm:p-10">
                                        <div class="mb-10 flex items-center justify-between">
                                            <div>
                                                <h2 class="text-2xl font-bold text-slate-900 khmer-font">ព័ត៌មានអត្តសញ្ញាណ</h2>
                                                <p class="mt-1 text-sm text-slate-500 uppercase tracking-wider font-semibold">Primary Personnel Dossier</p>
                                            </div>
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-400">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            </div>
                                        </div>

                                        <div class="grid gap-x-10 gap-y-8 md:grid-cols-2">
                                            @foreach ($identityFields as $field)
                                                <div class="group">
                                                    <p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-200 group-hover:bg-blue-500 transition-colors"></span>
                                                        {{ $field['label'] }}
                                                    </p>
                                                    <div class="mt-3 flex items-center gap-4">
                                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $field['tone'] }} ring-1 ring-black/5 shadow-sm">
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                                {!! $iconPaths[$field['icon']] ?? '' !!}
                                                            </svg>
                                                        </div>
                                                        <p class="text-lg font-bold text-slate-900 khmer-font leading-none">{{ $field['value'] ?: '-' }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>

                                    {{-- Document Vault --}}
                                    <section class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm sm:p-10">
                                        <div class="mb-10 flex items-center justify-between">
                                            <div>
                                                <h2 class="text-2xl font-bold text-slate-900 khmer-font">ប័ណ្ណសារឯកសារ</h2>
                                                <p class="mt-1 text-sm text-slate-500 uppercase tracking-wider font-semibold">Supporting Documents Vault</p>
                                            </div>
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                            </div>
                                        </div>

                                        <div class="grid gap-6">
                                            @foreach ($requirements as $requirement)
                                                @php
                                                    $groupDocs = collect($registration->documents)->where('test_taking_staff_document_requirement_id', $requirement->id);
                                                    $hasDocs = $groupDocs->isNotEmpty();
                                                @endphp
                                                <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-6 transition hover:bg-slate-50 hover:border-slate-200">
                                                    <div class="flex items-center justify-between mb-6">
                                                        <h3 class="font-bold text-slate-900 khmer-font">{{ $requirement->name_kh }}</h3>
                                                        <span class="rounded-full px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest {{ $hasDocs ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                                            {{ $hasDocs ? $groupDocs->count() . ' Files' : 'Missing' }}
                                                        </span>
                                                    </div>

                                                    <div class="space-y-3">
                                                        @forelse ($groupDocs as $document)
                                                            @php
                                                                $extension = strtolower(pathinfo($document->original_name ?? '', PATHINFO_EXTENSION));
                                                                $canPreviewInline = in_array($extension, $previewableExtensions, true);
                                                                $previewKind = in_array($extension, $imagePreviewableExtensions, true) ? 'image' : ($extension === 'pdf' ? 'pdf' : 'other');
                                                            @endphp
                                                            <div class="group relative flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:shadow-md">
                                                                <div class="flex min-w-0 items-center gap-4">
                                                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-[10px] font-black text-white shadow-lg">
                                                                        {{ $extension ?: 'DOC' }}
                                                                    </div>
                                                                    <div class="min-w-0">
                                                                        <p class="truncate text-sm font-bold text-slate-900 khmer-font">{{ $document->original_name ?? basename($document->file_path) }}</p>
                                                                        <p class="mt-1 text-[10px] font-semibold text-slate-400 uppercase tracking-tighter">{{ optional($document->created_at)->khFormat('d/m/Y H:i') }}</p>
                                                                    </div>
                                                                </div>

                                                                <div class="flex items-center gap-2">
                                                                    <button type="button"
                                                                        class="inline-flex h-10 px-4 items-center gap-2 rounded-xl bg-slate-50 text-xs font-bold text-slate-600 transition hover:bg-blue-600 hover:text-white"
                                                                        data-document-preview-trigger
                                                                        data-preview-url="{{ route('test-taking-staff-registrations.documents.show', [$registration, $document]) }}"
                                                                        data-download-url="{{ route('test-taking-staff-registrations.documents.download', [$registration, $document]) }}"
                                                                        data-document-name="{{ $document->original_name ?? basename($document->file_path) }}"
                                                                        data-preview-supported="{{ $canPreviewInline ? 'true' : 'false' }}"
                                                                        data-preview-kind="{{ $previewKind }}">
                                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                                        <span>មើល</span>
                                                                    </button>
                                                                    <a href="{{ route('test-taking-staff-registrations.documents.download', [$registration, $document]) }}"
                                                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-600 transition hover:bg-emerald-600 hover:text-white">
                                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white/50 py-8">
                                                                <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                                                <p class="mt-3 text-xs font-medium text-slate-400 khmer-font">មិនទាន់មានឯកសារ</p>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                </div>

                                <aside class="space-y-8 lg:sticky lg:top-8 lg:self-start">
                                    {{-- Record Status Card --}}
                                    <!-- <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                                        <h3 class="text-lg font-bold text-slate-900 khmer-font mb-6">ស្ថានភាពកំណត់ត្រា</h3>

                                        <div class="space-y-4">
                                            <div class="flex items-center gap-4 rounded-2xl bg-emerald-50 p-4 ring-1 ring-emerald-100">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-200">
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Registration Status</p>
                                                    <p class="text-sm font-bold text-emerald-900 khmer-font">បានចុះឈ្មោះជោគជ័យ</p>
                                                </div>
                                            </div>

                                            <div class="rounded-2xl bg-slate-50 p-6 space-y-4">
                                                <div>
                                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Record ID</p>
                                                    <p class="mt-1 text-lg font-bold text-slate-900 tracking-tight">#{{ $registration->id }}</p>
                                                </div>
                                                <div class="h-px bg-slate-200"></div>
                                                <div>
                                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Submission Date</p>
                                                    <p class="mt-1 text-sm font-bold text-slate-700 khmer-font">{{ $submittedAt }}</p>
                                                </div>
                                            </div>

                                            <button type="button" class="w-full flex h-14 items-center justify-center rounded-2xl bg-slate-900 text-sm font-bold text-white transition hover:bg-slate-800 active:scale-[0.98]">
                                                បោះពុម្ពប័ណ្ណសម្គាល់
                                            </button>
                                        </div>
                                    </div> -->

                                    {{-- Insight/Help Card --}}
                                    <div class="rounded-[2.5rem] border border-blue-100 bg-blue-50/50 p-8 shadow-sm">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-200 mb-6">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-blue-900 khmer-font mb-2">ការណែនាំរហ័ស</h3>
                                        <p class="text-sm leading-relaxed text-blue-700/80">
                                            សូមពិនិត្យឱ្យបានច្បាស់រាល់ឯកសារភ្ជាប់ និងព័ត៌មានអត្តសញ្ញាណ មុននឹងបន្តទៅវគ្គបន្ទាប់។ ប្រសិនបើមានកំហុស សូមចុច "កែប្រែព័ត៌មាន" ភ្លាមៗ។
                                        </p>
                                    </div>
                                </aside>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    {{-- Enhanced Preview Modal --}}
    <div class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-md" data-document-preview-modal aria-hidden="true">
        <div class="absolute inset-0" data-document-preview-close></div>
        <div class="relative z-10 flex h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-[3rem] bg-white shadow-2xl animate-in zoom-in-95 duration-300" data-document-preview-panel>
            <div class="flex items-center justify-between gap-6 border-b border-slate-100 px-8 py-6">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-blue-600 mb-1">Document Inspector</p>
                    <p class="truncate text-xl font-bold text-slate-900 khmer-font" data-document-preview-name>-</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="#" class="inline-flex h-12 items-center rounded-2xl bg-blue-600 px-8 text-sm font-bold text-white transition hover:bg-blue-700 shadow-xl shadow-blue-100" data-document-preview-download>ទាញយក</a>
                    <button type="button" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-900" data-document-preview-close>
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex min-h-0 flex-1 flex-col bg-slate-50 p-8">
                <div class="hidden rounded-3xl border border-amber-200 bg-white p-6 shadow-sm mb-6" data-document-preview-note>
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                        <p class="text-sm font-bold text-amber-900 khmer-font">
                            ប្រភេទឯកសារនេះមិនអាចបង្ហាញផ្ទាល់បានទេ។ សូមប្រើប៊ូតុង "ទាញយក" ដើម្បីពិនិត្យ។
                        </p>
                    </div>
                </div>

                <div class="hidden min-h-0 flex-1 items-center justify-center overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-inner" data-document-preview-image-wrapper>
                    <img src="" alt="Preview" class="max-h-full max-w-full object-contain" data-document-preview-image>
                </div>

                <iframe src="about:blank" class="hidden min-h-0 w-full flex-1 rounded-[2.5rem] border border-slate-200 bg-white shadow-inner" data-document-preview-frame title="Preview"></iframe>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.querySelector('[data-document-preview-modal]');
            if (!modal) return;

            const frame = modal.querySelector('[data-document-preview-frame]');
            const imageWrapper = modal.querySelector('[data-document-preview-image-wrapper]');
            const image = modal.querySelector('[data-document-preview-image]');
            const name = modal.querySelector('[data-document-preview-name]');
            const downloadLink = modal.querySelector('[data-document-preview-download]');
            const note = modal.querySelector('[data-document-preview-note]');
            const closeButton = modal.querySelector('button[data-document-preview-close]');

            let previousFocusedElement = null;

            const setPreviewMode = (previewKind, previewUrl) => {
                const isImage = previewKind === 'image';
                if (imageWrapper) {
                    imageWrapper.classList.toggle('hidden', !isImage);
                    imageWrapper.classList.toggle('flex', isImage);
                }
                if (frame) {
                    frame.classList.toggle('hidden', isImage);
                    frame.classList.toggle('block', !isImage);
                }

                if (isImage) {
                    if (image) image.src = previewUrl || '';
                    if (frame) frame.src = 'about:blank';
                } else {
                    if (image) image.src = '';
                    if (frame) frame.src = previewUrl || 'about:blank';
                }
            };

            const openModal = ({ previewUrl, downloadUrl, documentName, previewSupported, previewKind }) => {
                previousFocusedElement = document.activeElement;
                if (name) name.textContent = documentName || 'មើលឯកសារ';
                setPreviewMode(previewKind, previewUrl);

                if (downloadLink) downloadLink.href = downloadUrl || '#';
                if (note) note.classList.toggle('hidden', previewSupported);

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
                closeButton?.focus();
            };

            const closeModal = () => {
                setPreviewMode('other', 'about:blank');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                if (previousFocusedElement) previousFocusedElement.focus();
            };

            const triggers = document.querySelectorAll('[data-document-preview-trigger]');
            triggers.forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    openModal({
                        previewUrl: trigger.dataset.previewUrl,
                        downloadUrl: trigger.dataset.downloadUrl,
                        documentName: trigger.dataset.documentName,
                        previewSupported: trigger.dataset.previewSupported === 'true',
                        previewKind: trigger.dataset.previewKind || 'other',
                    });
                });
            });

            const closeButtons = modal.querySelectorAll('[data-document-preview-close]');
            closeButtons.forEach((element) => {
                element.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
