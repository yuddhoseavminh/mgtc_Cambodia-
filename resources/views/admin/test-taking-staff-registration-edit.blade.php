@extends('app')

@section('body')
    <style>
        .admin-title-font { font-family: 'Kantumruy Pro', sans-serif; }
        .khmer-font { font-family: 'Kantumruy Pro', sans-serif; }
        .test-taking-date-grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 16rem), 1fr));
        }
        .test-taking-date-field {
            min-width: 0;
        }
        .test-taking-date-field .form-input,
        .test-taking-date-field .flatpickr-input {
            box-sizing: border-box;
            min-width: 0;
            width: 100%;
            padding-left: 3rem !important;
            padding-right: 0.85rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: clamp(0.78rem, 1.35vw, 0.95rem);
            line-height: 1.45;
        }
    </style>
    @php
        $currentSection = 'register-staff';
        $documentGroups = collect($registration->documents)
            ->groupBy(fn ($doc) => $doc->documentRequirement?->name_kh ?? $doc->documentRequirement?->name_en ?? 'ឯកសារភ្ជាប់ផ្សេងៗ')
            ->map(fn ($documents, $label) => [
                'label' => $label,
                'files' => $documents->values(),
            ])
            ->values();

        $documentCount = collect($registration->documents)->count();
        $previewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        $imagePreviewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $submittedAtInput = $registration->submitted_at
            ? $registration->submitted_at->timezone('Asia/Phnom_Penh')->format('Y-m-d\TH:i')
            : null;
        $avatarUrl = route('test-taking-staff-registrations.avatar', [
            'testTakingStaffRegistration' => $registration,
            'v' => optional($registration->updated_at)->timestamp ?? md5((string) $registration->avatar_path),
        ]);
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-screen lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => $currentSection])

                <main class="flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => 'កែប្រែព័ត៌មានបេក្ខជន',
                        'subtitle' => 'បញ្ជី / បុគ្គលិកសាកល្បង',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => $currentSection,
                    ])

                    <div class="flex-1 p-4 sm:p-6 lg:p-8">
                        <div class="mx-auto w-full max-w-[980px] space-y-6">
                            {{-- Breadcrumbs --}}
                            <nav class="flex items-center gap-2 rounded-[1.4rem] border border-slate-200 bg-white/70 px-5 py-4 text-sm backdrop-blur-md shadow-sm">
                                <a href="{{ route('admin.home', ['section' => 'register-staff']) }}" class="font-medium text-slate-500 transition hover:text-slate-900">បញ្ជីដែលបានចុះឈ្មោះ</a>
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                <a href="{{ route('admin.test-taking-staff-registrations.show', $registration) }}" class="font-medium text-slate-500 transition hover:text-slate-900">{{ $registration->name_kh }}</a>
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                <span class="font-semibold text-slate-950 khmer-font">កែប្រែ</span>
                            </nav>

                            <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#ffffff,#f8fbff)] p-6 shadow-[0_20px_50px_rgba(15,23,42,0.06)] sm:p-7">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div class="max-w-3xl">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">ផ្នែកកែប្រែព័ត៌មាន</p>
                                        <h3 class="mt-2 text-[1.85rem] font-bold tracking-tight text-slate-950 admin-title-font">កែប្រែកំណត់ត្រាបេក្ខជន</h3>
                                        <p class="mt-2 text-sm leading-7 text-slate-500">ធ្វើបច្ចុប្បន្នភាពទិន្នន័យអត្តសញ្ញាណ ព័ត៌មានយោធា និងរាល់ឯកសារភស្តុតាងដែលពាក់ព័ន្ធ។</p>
                                    </div>

                                    <a href="{{ route('admin.home', ['section' => 'register-staff']) }}" class="inline-flex h-12 items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                        ត្រឡប់ទៅបញ្ជីបេក្ខជន
                                    </a>
                                </div>
                            </section>

                                <section class="rounded-[2rem] border border-blue-100 bg-[linear-gradient(135deg,#f8fbff,#ffffff_48%,#eef6ff)] p-5 text-slate-900 shadow-[0_20px_50px_rgba(15,23,42,0.08)] sm:p-6">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="max-w-2xl">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-600">សេចក្តីសង្ខេបនៃការចុះឈ្មោះ</p>
                                            <h4 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950 admin-title-font">ព័ត៌មានស្ថានភាពបច្ចុប្បន្ន</h4>
                                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                                បេក្ខជនបានដាក់ស្នើកាលពីថ្ងៃទី {{ optional($registration->submitted_at ?? $registration->created_at)->khFormat('d/m/Y') }}។
                                            </p>
                                        </div>

                                        <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[24rem]">
                                            <div class="rounded-[1.35rem] border border-blue-100 bg-white/75 px-4 py-4 shadow-sm ring-1 ring-white/70 backdrop-blur-sm">
                                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">លេខសម្គាល់</p>
                                                <p class="mt-2 break-all text-lg font-semibold text-slate-950">#{{ $registration->id }}</p>
                                            </div>
                                            <div class="rounded-[1.35rem] border border-blue-100 bg-white/75 px-4 py-4 shadow-sm ring-1 ring-white/70 backdrop-blur-sm">
                                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">ឯកសារភ្ជាប់</p>
                                                <p class="mt-2 break-all text-lg font-semibold text-slate-950">{{ $documentCount }} ឯកសារ</p>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                                    <div class="space-y-6">
                                        {{-- Main Basic Info Panel --}}
                                        <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <h4 class="text-lg font-bold text-slate-950 khmer-font">ព័ត៌មានមូលដ្ឋាន</h4>
                                                    <p class="mt-1 text-sm text-slate-500">បំពេញព័ត៌មានអត្តសញ្ញាណបេក្ខជន។</p>
                                                </div>
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-600">ផ្នែក ០១</span>
                                            </div>

                                            <form
                                                id="main-edit-form"
                                                method="POST"
                                                action="{{ route('admin.test-taking-staff-registrations.update', $registration) }}"
                                                enctype="multipart/form-data"
                                                class="space-y-6"
                                                data-ajax-form
                                                data-ajax-redirect="{{ route('admin.home', ['section' => 'register-staff']) }}"
                                                data-ajax-success-title="ជោគជ័យ"
                                                data-ajax-success-text="បានកែប្រែព័ត៌មានបេក្ខជនដោយជោគជ័យ។"
                                            >
                                                @csrf @method('PUT')

                                                <div class="grid gap-5 md:grid-cols-2">
                                                    <div class="md:col-span-2">
                                                        <label class="form-label khmer-font">គោត្តនាម-នាម (ខ្មែរ) <span class="text-rose-500">*</span></label>
                                                        <input type="text" name="name_kh" value="{{ old('name_kh', $registration->name_kh) }}" required
                                                            class="form-input bg-slate-50" placeholder="បញ្ចូលឈ្មោះជាភាសាខ្មែរ">
                                                        @error('name_kh') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="form-label">ឈ្មោះឡាតាំង</label>
                                                        <input type="text" name="name_latin" value="{{ old('name_latin', $registration->name_latin) }}"
                                                            class="form-input bg-slate-50" placeholder="NAME IN LATIN">
                                                        @error('name_latin') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="form-label">អត្តលេខយោធា</label>
                                                        <input type="text" name="id_number" value="{{ old('id_number', $registration->id_number) }}"
                                                            class="form-input bg-slate-50" placeholder="បញ្ចូលអត្តលេខ">
                                                        @error('id_number') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="form-label">ឋានន្តរស័ក្តិ</label>
                                                        <select name="test_taking_staff_rank_id" class="form-input bg-slate-50">
                                                            <option value="">ជ្រើសរើសឋានន្តរស័ក្តិ</option>
                                                            @foreach ($ranks as $rank)
                                                                <option value="{{ $rank->id }}" @selected(old('test_taking_staff_rank_id', $registration->test_taking_staff_rank_id) == $rank->id)>
                                                                    {{ $rank->name_kh }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('test_taking_staff_rank_id') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="form-label">លេខទូរស័ព្ទ</label>
                                                        <input type="text" name="phone_number" value="{{ old('phone_number', $registration->phone_number) }}"
                                                            class="form-input bg-slate-50" >
                                                        @error('phone_number') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="form-label">កាលបរិច្ឆេទដាក់ស្នើ</label>
                                                        <input type="datetime-local" name="submitted_at" value="{{ old('submitted_at', $submittedAtInput) }}"
                                                            class="form-input bg-slate-50">
                                                        @error('submitted_at') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div class="grid gap-5 test-taking-date-grid md:col-span-2">
                                                        <div>
                                                            <label class="form-label">ថ្ងៃកំណើត</label>
                                                            <div class="relative test-taking-date-field">
                                                                <input type="text" id="dob_picker" name="date_of_birth" value="{{ old('date_of_birth', optional($registration->date_of_birth)->format('Y-m-d')) }}"
                                                                    class="form-input bg-slate-50 !pl-12" placeholder="ជ្រើសរើសថ្ងៃកំណើត" readonly>
                                                                <div class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-blue-500">
                                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                                </div>
                                                            </div>
                                                            @error('date_of_birth') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                        </div>

                                                        <div>
                                                            <label class="form-label">ថ្ងៃចូលបម្រើ</label>
                                                            <div class="relative test-taking-date-field">
                                                                <input type="text" id="service_day_picker" name="military_service_day" value="{{ old('military_service_day', optional($registration->military_service_day)->format('Y-m-d')) }}"
                                                                    class="form-input bg-slate-50 !pl-12" placeholder="ជ្រើសរើសថ្ងៃចូលបម្រើ" readonly>
                                                                <div class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-blue-500">
                                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                                </div>
                                                            </div>
                                                            @error('military_service_day') <p class="mt-2 text-xs font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </section>
                                    </div>

                                    <aside class="space-y-6">
                                        {{-- Profile Image Section --}}
                                        <section class="sticky top-6 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                            <div class="mb-6">
                                                <h4 class="text-lg font-bold text-slate-950 khmer-font">រូបភាពបេក្ខជន</h4>
                                                <p class="mt-1 text-sm text-slate-500">រូបថតសម្រាប់ប្រើក្នុងប្រព័ន្ធ។</p>
                                            </div>

                                            <div class="grid gap-2set sm:grid-cols-[minmax(13rem,16rem)_minmax(0,1fr)] sm:items-center xl:grid-cols-1">
                                                <div class="flex items-center justify-center rounded-[2.5rem] border border-slate-100 bg-slate-50/50 p-6" data-avatar-preview-container>
                                                    @if ($registration->hasStoredAvatar())
                                                        <img src="{{ $avatarUrl }}" alt="{{ $registration->name_kh }}" class="h-40 w-40 rounded-full object-cover ring-4 ring-white shadow-xl" data-avatar-preview-image>
                                                    @else
                                                        <div class="flex h-40 w-40 items-center justify-center rounded-full bg-slate-900 text-5xl font-bold text-white shadow-xl" data-avatar-preview-placeholder>
                                                            {{ strtoupper(substr($registration->name_latin ?: $registration->name_kh ?: 'T', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="rounded-2xl border border-blue-100 bg-blue-50/40 p-4">
                                                    <label class="block cursor-pointer rounded-[1.25rem] border border-dashed border-blue-200 bg-white/70 px-4 py-4 text-center transition hover:border-blue-400 hover:bg-white">
                                                        <input type="file" name="avatar_image" form="main-edit-form" accept=".jpg,.jpeg,.png,.webp" class="hidden" data-avatar-confirm>
                                                        <span class="block text-sm font-bold text-blue-700 khmer-font" data-avatar-file-name>ជ្រើសរើសរូបថតថ្មី</span>
                                                        <span class="mt-1 block text-[10px] font-semibold uppercase tracking-wider text-slate-400">JPG, PNG, WEBP / Max 5MB</span>
                                                    </label>
                                                    @error('avatar_image') <p class="mt-2 text-xs font-bold text-rose-500 text-center">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </section>
                                    </aside>
                                </div>
                            </div>

                                        {{-- Documents Management Section --}}
                                        <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                                <div>
                                                    <h4 class="text-lg font-bold text-slate-950 khmer-font">ឯកសារភស្តុតាង</h4>
                                                    <p class="mt-1 text-sm text-slate-500">គ្រប់គ្រង និងបន្ថែមឯកសារពាក់ព័ន្ធ។</p>
                                                </div>
                                                <div class="flex flex-wrap gap-3">
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-[10px] font-bold text-slate-500 ring-1 ring-slate-200">
                                                        {{ collect($registration->documents)->count() }} ឯកសារ
                                                    </span>
                                                    <button type="button"
                                                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 text-xs font-bold text-white shadow-lg shadow-slate-200 transition-all hover:bg-black active:scale-95"
                                                        data-document-add-trigger>
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14"/></svg>
                                                        <span>បន្ថែមថ្មី</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="space-y-4">
                                                @foreach ($requirements as $requirement)
                                                    @php
                                                        $groupDocs = collect($registration->documents)->where('test_taking_staff_document_requirement_id', $requirement->id);
                                                        $docCount = $groupDocs->count();
                                                    @endphp
                                                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-4 shadow-sm">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div class="min-w-0">
                                                                <p class="text-sm font-semibold text-slate-900 khmer-font">{{ $requirement->name_kh }}</p>
                                                                <p class="mt-1 break-all text-xs text-slate-500">
                                                                    {{ $docCount > 0 ? "បានបញ្ចូល $docCount ឯកសារ" : "មិនទាន់មានឯកសារបញ្ចូលទេ" }}
                                                                </p>
                                                            </div>
                                                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $docCount > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                                {{ $docCount > 0 ? "$docCount ឯកសារ" : 'ខ្វះឯកសារ' }}
                                                            </span>
                                                        </div>

                                                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                                            @forelse ($groupDocs as $document)
                                                                @php
                                                                    $extension = strtolower(pathinfo($document->original_name ?? '', PATHINFO_EXTENSION));
                                                                    $canPreviewInline = in_array($extension, $previewableExtensions, true);
                                                                    $previewKind = in_array($extension, $imagePreviewableExtensions, true) ? 'image' : ($extension === 'pdf' ? 'pdf' : 'other');
                                                                @endphp
                                                                <div class="group/doc flex min-h-[10.5rem] flex-col justify-between rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_18px_36px_rgba(15,23,42,0.08)]">
                                                                    <div>
                                                                        <div class="flex items-start gap-3">
                                                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-[10px] font-black uppercase text-white shadow-lg shadow-slate-200">
                                                                                {{ $extension ?: 'DOC' }}
                                                                            </div>
                                                                            <div class="min-w-0">
                                                                                <p class="line-clamp-2 break-words text-sm font-bold leading-5 text-slate-900">{{ $document->original_name ?? basename($document->file_path) }}</p>
                                                                                <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-slate-500">
                                                                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 font-semibold text-blue-700">
                                                                                        Admin Upload
                                                                                    </span>
                                                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">
                                                                                        {{ optional($document->created_at)->khFormat('d/m/Y H:i') }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                                                        <button type="button"
                                                                            class="inline-flex h-9 items-center justify-center rounded-[0.85rem] border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                                                                            data-document-preview-trigger
                                                                            data-preview-url="{{ route('test-taking-staff-registrations.documents.show', [$registration, $document]) }}"
                                                                            data-document-name="{{ $document->original_name ?? basename($document->file_path) }}"
                                                                            data-preview-kind="{{ $previewKind }}">
                                                                            មើល
                                                                        </button>
                                                                        <a href="{{ route('test-taking-staff-registrations.documents.download', [$registration, $document]) }}"
                                                                            class="inline-flex h-9 items-center justify-center rounded-[0.85rem] border border-sky-200 bg-sky-50 px-3 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                                                                            ទាញយក
                                                                        </a>
                                                                        <form method="POST" action="{{ route('test-taking-staff-registrations.documents.destroy', [$registration, $document]) }}" data-swal-confirm data-swal-title="បញ្ជាក់ការលុបឯកសារ" data-swal-text="តើអ្នកពិតជាចង់លុបឯកសារនេះមែនទេ?" data-swal-confirm-text="បាទ/ចាស លុប" data-swal-cancel-text="បោះបង់" class="contents">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="inline-flex h-9 w-full items-center justify-center rounded-[0.85rem] border border-rose-200 bg-rose-50 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                                                លុប
                                                                            </button>
                                                                        </form>
                                                                        <form method="POST" action="{{ route('test-taking-staff-registrations.documents.update', [$registration, $document]) }}" enctype="multipart/form-data"
                                                                            data-ajax-form
                                                                            data-ajax-redirect="{{ route('admin.test-taking-staff-registrations.edit', $registration) }}"
                                                                            data-ajax-success-title="ជោគជ័យ"
                                                                            data-ajax-success-text="បានជំនួសឯកសារដោយជោគជ័យ"
                                                                            class="contents">
                                                                            @csrf @method('PUT')
                                                                            <label class="inline-flex h-9 w-full cursor-pointer items-center justify-center rounded-[0.85rem] border border-amber-200 bg-amber-50 px-3 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                                                                ជំនួស
                                                                                <input type="file" name="document_file" required class="hidden" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" data-document-upload-autosubmit data-document-upload-title="បញ្ជាក់ការជំនួសឯកសារ" data-document-upload-text="តើអ្នកពិតជាចង់ជំនួសឯកសារនេះដោយឯកសារថ្មីមែនទេ?">
                                                                            </label>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="rounded-[1rem] border border-dashed border-slate-300 bg-white px-3 py-4 text-center text-xs text-slate-500">
                                                                    មិនទាន់មានឯកសារបញ្ចូលទេ
                                                                </div>
                                                            @endforelse

                                                        {{-- Inline Upload Form --}}
                                                        <div class="group/upload min-h-[10.5rem] rounded-[1.25rem] border-2 border-dashed border-blue-200 bg-blue-50/40 p-4 transition hover:border-blue-400 hover:bg-blue-50">
                                                            <form action="{{ route('test-taking-staff-registrations.documents.store', $registration) }}" method="POST" enctype="multipart/form-data"
                                                                data-ajax-form
                                                                data-ajax-redirect="{{ route('admin.test-taking-staff-registrations.edit', $registration) }}"
                                                                data-ajax-success-title="ជោគជ័យ"
                                                                data-ajax-success-text="បានបន្ថែមឯកសារក្នុងផ្នែក {{ $requirement->name_kh }} ជោគជ័យ។"
                                                                class="contents">
                                                                @csrf
                                                                <input type="hidden" name="test_taking_staff_document_requirement_id" value="{{ $requirement->id }}">
                                                                <label class="flex h-full min-h-[8.25rem] cursor-pointer flex-col items-center justify-center rounded-[1rem] bg-white/70 px-4 py-5 text-center transition group-hover/upload:bg-white">
                                                                    <input type="file" name="document_file" required
                                                                        accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                                                                        class="hidden"
                                                                        data-document-upload-autosubmit
                                                                        data-document-upload-title="បញ្ជាក់ការបន្ថែមឯកសារ"
                                                                        data-document-upload-text="តើអ្នកពិតជាចង់បន្ថែមឯកសារនេះមែនទេ?">
                                                                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-100 transition group-hover/upload:scale-105">
                                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M12 5v14M5 12h14"/></svg>
                                                                    </span>
                                                                    <span class="mt-3 text-sm font-bold text-slate-900 khmer-font">បញ្ចូលឯកសារ</span>
                                                                    <span class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400">PDF, DOC, JPG, PNG</span>
                                                                </label>
                                                            </form>
                                                        </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </section>

                                        <div class="rounded-3xl border border-blue-100 bg-blue-50/30 p-6">
                                            <div class="flex gap-4">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-500 text-white shadow-md shadow-blue-100">
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <div class="space-y-1">
                                                    <h5 class="text-sm font-bold text-slate-900 khmer-font">ការណែនាំសម្រាប់ការកែប្រែ</h5>
                                                    <p class="text-xs leading-relaxed text-slate-600">សូមពិនិត្យព័ត៌មានឲ្យបានច្បាស់លាស់មុននឹងចុចប៊ូតុង "រក្សាទុកការផ្លាស់ប្តូរ"។</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sticky Bottom Actions --}}
                                <section class="sticky bottom-4 z-20 mt-6 rounded-[1.6rem] border border-slate-200 bg-white/95 p-4 shadow-[0_10px_25px_rgba(15,23,42,0.08)] backdrop-blur">
                                    <div class="flex flex-wrap items-center justify-end gap-3">
                                        <button type="reset" form="main-edit-form" class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            កំណត់ឡើងវិញ
                                        </button>
                                        <a href="{{ route('admin.test-taking-staff-registrations.show', $registration) }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            បដិសេធ
                                        </a>
                                        <button type="submit" form="main-edit-form" class="inline-flex h-12 items-center justify-center rounded-xl bg-slate-900 px-8 text-sm font-semibold text-white shadow-lg shadow-slate-200 transition hover:bg-black active:scale-95">
                                            រក្សាទុកការកែប្រែ
                                        </button>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
    {{-- Modals --}}
    {{-- Add Document Modal --}}
    <div class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-md" data-document-add-modal aria-hidden="true">
        <div class="absolute inset-0" data-document-add-close></div>
        <div class="relative z-10 flex w-full max-w-lg flex-col overflow-hidden rounded-[2.5rem] bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-blue-600">New Evidence</p>
                    <h4 class="mt-1 text-xl font-bold text-slate-900 admin-title-font">បន្ថែមឯកសារភស្តុតាង</h4>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition hover:bg-slate-100 hover:text-slate-900" data-document-add-close>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <form
                action="{{ route('test-taking-staff-registrations.documents.store', $registration) }}"
                method="POST"
                enctype="multipart/form-data"
                class="p-8"
                data-ajax-form
                data-ajax-redirect="{{ route('admin.test-taking-staff-registrations.edit', $registration) }}"
                data-ajax-success-title="ជោគជ័យ"
                data-ajax-success-text="បានបន្ថែមឯកសារភស្តុតាងជោគជ័យ។"
            >
                @csrf
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 ml-1">ប្រភេទឯកសារ <span class="text-rose-500">*</span></label>
                        <select name="test_taking_staff_document_requirement_id" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-4 text-sm font-medium transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 khmer-font">
                            <option value="">ជ្រើសរើសប្រភេទឯកសារ...</option>
                            @foreach($requirements as $requirement)
                                <option value="{{ $requirement->id }}">{{ $requirement->name_kh }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 ml-1">ជ្រើសរើសឯកសារ <span class="text-rose-500">*</span></label>
                        <label class="group relative flex h-40 cursor-pointer flex-col items-center justify-center rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50 transition-all hover:border-blue-400 hover:bg-blue-50/30">
                            <input type="file" name="document_file" required class="hidden" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" onchange="this.nextElementSibling.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'ចុចទីនេះដើម្បីបញ្ចូលឯកសារ'">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-blue-600 shadow-sm ring-1 ring-black/5 group-hover:scale-110 transition-transform">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                </div>
                                <span class="text-sm font-bold text-slate-600 khmer-font">ចុចទីនេះដើម្បីបញ្ចូលឯកសារ</span>
                                <p class="text-[10px] uppercase tracking-wider text-slate-400">PDF, DOC, JPG, PNG (Total <= 50MB)</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" class="flex-1 h-12 rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 transition hover:bg-slate-50" data-document-add-close>បោះបង់</button>
                    <button type="submit" class="flex-1 h-12 rounded-2xl bg-blue-600 text-sm font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-700">រក្សាទុក</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-md" data-document-preview-modal aria-hidden="true">
        <div class="absolute inset-0" data-document-preview-close></div>
        <div class="relative z-10 flex h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-[2.5rem] bg-white shadow-2xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5 sm:px-8">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-blue-600">Document Review</p>
                    <p class="mt-1 truncate text-lg font-bold text-slate-900" data-document-preview-name>-</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200" data-document-preview-close>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="flex min-h-0 flex-1 flex-col bg-slate-50 p-6 sm:p-8">
                <div class="mt-4 hidden min-h-0 flex-1 items-center justify-center overflow-hidden rounded-[2rem] border border-slate-200 bg-white" data-document-preview-image-wrapper>
                    <img src="" alt="Preview" class="max-h-full max-w-full object-contain" data-document-preview-image>
                </div>
                <iframe src="about:blank" class="mt-4 hidden min-h-0 w-full flex-1 rounded-[2rem] border border-slate-200 bg-white" data-document-preview-frame></iframe>
            </div>
        </div>
    </div>

    <script>
        (() => {
            // Flatpickr initialization
            if (window.flatpickr) {
                flatpickr('#dob_picker', {
                    locale: 'km',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd F, Y',
                    allowInput: false,
                    disableMobile: true,
                    altInputClass: 'form-input bg-slate-50 !pl-12'
                });

                flatpickr('#service_day_picker', {
                    locale: 'km',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd F, Y',
                    allowInput: false,
                    disableMobile: true,
                    maxDate: 'today',
                    altInputClass: 'form-input bg-slate-40 !pl-12'
                });
            }

            // Document Add Modal
            const addModal = document.querySelector('[data-document-add-modal]');
            const addTriggers = document.querySelectorAll('[data-document-add-trigger]');
            const addCloses = addModal?.querySelectorAll('[data-document-add-close]');

            addTriggers.forEach(t => t.addEventListener('click', () => {
                addModal?.classList.remove('hidden');
                addModal?.classList.add('flex');
            }));

            addCloses?.forEach(c => c.addEventListener('click', () => {
                addModal?.classList.add('hidden');
                addModal?.classList.remove('flex');
            }));

            // Khmer confirmation before auto-uploading a newly selected document.
            const uploadInputs = document.querySelectorAll('[data-document-upload-autosubmit]');
            uploadInputs.forEach(input => {
                input.addEventListener('change', async () => {
                    if (!input.files || input.files.length === 0) {
                        return;
                    }

                    const form = input.closest('form');
                    if (!form) {
                        return;
                    }

                    const title = input.dataset.documentUploadTitle || 'បញ្ជាក់ការបញ្ចូលឯកសារ';
                    const text = input.dataset.documentUploadText || 'តើអ្នកពិតជាចង់បន្តមែនទេ?';

                    if (!window.Swal) {
                        if (window.confirm(`${title}\n${text}`)) {
                            form.requestSubmit();
                            return;
                        }

                        input.value = '';
                        return;
                    }

                    const result = await Swal.fire({
                        icon: 'question',
                        title,
                        text,
                        confirmButtonText: 'បាទ/ចាស បន្ត',
                        cancelButtonText: 'បោះបង់',
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#94a3b8',
                        showCancelButton: true,
                        reverseButtons: true,
                        focusCancel: true,
                        customClass: {
                            popup: 'swal2-kh-popup',
                            title: 'swal2-kh-title',
                            htmlContainer: 'swal2-kh-content',
                            confirmButton: 'swal2-kh-confirm',
                            cancelButton: 'swal2-kh-cancel',
                        },
                    });

                    if (result.isConfirmed) {
                        form.requestSubmit();
                        return;
                    }

                    input.value = '';
                });
            });

            // Khmer confirmation for changing the candidate photo.
            const avatarInput = document.querySelector('[data-avatar-confirm]');
            let avatarPreviewUrl = null;

            const updateAvatarPreview = (file) => {
                const container = document.querySelector('[data-avatar-preview-container]');

                if (!container || !file) {
                    return;
                }

                if (avatarPreviewUrl) {
                    URL.revokeObjectURL(avatarPreviewUrl);
                }

                avatarPreviewUrl = URL.createObjectURL(file);

                let image = container.querySelector('[data-avatar-preview-image]');
                if (!image) {
                    image = document.createElement('img');
                    image.alt = 'Avatar preview';
                    image.dataset.avatarPreviewImage = '';
                    image.className = 'h-40 w-40 rounded-full object-cover ring-4 ring-white shadow-xl';
                    container.replaceChildren(image);
                }

                image.src = avatarPreviewUrl;
            };

            avatarInput?.addEventListener('change', async () => {
                const label = avatarInput.closest('label');
                const selectedFile = avatarInput.files?.[0];
                const fileName = selectedFile?.name;

                if (!fileName) {
                    label?.querySelector('[data-avatar-file-name]')?.replaceChildren(document.createTextNode('ជ្រើសរើសរូបថតថ្មី'));
                    return;
                }

                const resetAvatarInput = () => {
                    avatarInput.value = '';
                    label?.querySelector('[data-avatar-file-name]')?.replaceChildren(document.createTextNode('ជ្រើសរើសរូបថតថ្មី'));
                };

                if (!window.Swal) {
                    if (window.confirm(`បញ្ជាក់ការផ្លាស់ប្តូររូបថត\nតើអ្នកពិតជាចង់ប្រើរូបថតថ្មីនេះមែនទេ?`)) {
                        label?.querySelector('[data-avatar-file-name]')?.replaceChildren(document.createTextNode(fileName));
                        updateAvatarPreview(selectedFile);
                        return;
                    }

                    resetAvatarInput();
                    return;
                }

                const result = await Swal.fire({
                    icon: 'question',
                    title: 'បញ្ជាក់ការផ្លាស់ប្តូររូបថត',
                    text: 'តើអ្នកពិតជាចង់ប្រើរូបថតថ្មីនេះមែនទេ?',
                    confirmButtonText: 'បាទ/ចាស ប្រើរូបនេះ',
                    cancelButtonText: 'បោះបង់',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#94a3b8',
                    showCancelButton: true,
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        popup: 'swal2-kh-popup',
                        title: 'swal2-kh-title',
                        htmlContainer: 'swal2-kh-content',
                        confirmButton: 'swal2-kh-confirm',
                        cancelButton: 'swal2-kh-cancel',
                    },
                });

                if (result.isConfirmed) {
                    label?.querySelector('[data-avatar-file-name]')?.replaceChildren(document.createTextNode(fileName));
                    updateAvatarPreview(selectedFile);
                    return;
                }

                resetAvatarInput();
            });

            // Document Preview Modal
            const previewModal = document.querySelector('[data-document-preview-modal]');
            const previewFrame = previewModal?.querySelector('[data-document-preview-frame]');
            const previewImageWrapper = previewModal?.querySelector('[data-document-preview-image-wrapper]');
            const previewImage = previewModal?.querySelector('[data-document-preview-image]');
            const previewName = previewModal?.querySelector('[data-document-preview-name]');
            const previewTriggers = document.querySelectorAll('[data-document-preview-trigger]');
            const previewCloses = previewModal?.querySelectorAll('[data-document-preview-close]');

            previewTriggers.forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const { previewUrl, documentName, previewKind } = trigger.dataset;
                    if (previewName) previewName.textContent = documentName;

                    if (previewKind === 'image') {
                        if (previewImage) previewImage.src = previewUrl;
                        previewImageWrapper?.classList.remove('hidden');
                        previewFrame?.classList.add('hidden');
                    } else {
                        if (previewFrame) previewFrame.src = previewUrl;
                        previewFrame?.classList.remove('hidden');
                        previewImageWrapper?.classList.add('hidden');
                    }

                    previewModal?.classList.remove('hidden');
                    previewModal?.classList.add('flex');
                });
            });

            previewCloses?.forEach(c => c.addEventListener('click', () => {
                previewModal?.classList.add('hidden');
                previewModal?.classList.remove('flex');
                if (previewFrame) previewFrame.src = 'about:blank';
            }));
        })();
    </script>
@endsection
