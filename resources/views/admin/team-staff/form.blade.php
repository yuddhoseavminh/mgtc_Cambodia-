@extends('app')

@section('body')
    @php
        $isEdit = $mode === 'edit';
        $roleLabels = [
            'Admin' => 'អ្នកគ្រប់គ្រង',
            'Manager' => 'អ្នកចាត់ការ',
            'Staff' => 'បុគ្គលិក',
            'Viewer' => 'អ្នកមើល',
        ];
        $genderLabels = [
            'Male' => 'ប្រុស',
            'Female' => 'ស្រី',
            'Other' => 'ផ្សេងទៀត',
        ];
        $existingDocuments = collect($teamStaff->documents ?? [])->values();
        $documentRequirements = collect($documentRequirements ?? [])->values();
        $documentsByRequirementSlug = $existingDocuments
            ->filter(fn ($document) => filled($document['requirement_slug'] ?? null))
            ->keyBy(fn ($document) => $document['requirement_slug']);
        $legacyDocuments = $existingDocuments
            ->filter(fn ($document) => blank($document['requirement_slug'] ?? null))
            ->values();
        $previewIdNumber = old('id_number', $credentialPreview['password'] ?? $teamStaff->id_number);
        $previewUsername = $credentialPreview['username']
            ?? \App\Models\TeamStaff::usernameBase((string) old('name_latin', $teamStaff->name_latin));
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-screen lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'staff-management'])

                <main class="flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => $isEdit ? 'កែប្រែព័ត៌មានបុគ្គលិក' : 'បង្កើតបុគ្គលិកថ្មី',
                        'subtitle' => 'បញ្ជី / បុគ្គលិកក្រុម',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => 'staff-management',
                    ])

                    <div class="flex-1 p-4 sm:p-6 lg:p-8">
                        <div class="mx-auto w-full max-w-[980px] space-y-6">
                            <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#ffffff,#f8fbff)] p-6 shadow-[0_20px_50px_rgba(15,23,42,0.06)] sm:p-7">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div class="max-w-3xl">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">បញ្ជីបុគ្គលិក</p>
                                        <h3 class="mt-2 text-[1.85rem] font-semibold tracking-tight text-slate-950">
                                            {{ $isEdit ? 'កែប្រែកំណត់ត្រាបុគ្គលិក' : 'បង្កើតកំណត់ត្រាបុគ្គលិកថ្មី' }}
                                        </h3>
                                        <p class="mt-2 text-sm leading-7 text-slate-500">
                                            ផ្នែកឯកសារនៅទីនេះប្រើតាមបញ្ជីឯកសារដែលអេដមីនបានកំណត់។ ប្រសិនបើមិនទាន់មានឯកសារ វានឹងនៅតែបង្ហាញដើម្បីអាចបញ្ចូលនៅពេលក្រោយ។
                                        </p>
                                    </div>

                                    <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                        ត្រឡប់ទៅបញ្ជីបុគ្គលិក
                                    </a>
                                </div>
                            </section>

                            <form
                                method="POST"
                                action="{{ $isEdit ? route('team-staff.update', $teamStaff) : route('team-staff.store') }}"
                                enctype="multipart/form-data"
                                class="space-y-6"
                                data-ajax-form
                                data-ajax-redirect="{{ route('admin.home', ['section' => 'staff-management']) }}"
                                data-ajax-success-title="ជោគជ័យ"
                                data-ajax-success-text="{{ $isEdit ? 'បានកែប្រែព័ត៌មានបុគ្គលិកដោយជោគជ័យ។' : 'បានបង្កើតបុគ្គលិកដោយជោគជ័យ។' }}"
                            >
                                @csrf
                                @if ($isEdit)
                                    @method('PUT')
                                @endif

                                <section class="rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#0f172a,#111827_52%,#7f1d1d)] p-5 text-white shadow-[0_20px_50px_rgba(15,23,42,0.16)] sm:p-6">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="max-w-2xl">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-200">{{ $isEdit ? 'គណនីចូលប្រព័ន្ធ' : 'ការមើលជាមុននៃគណនីចូល' }}</p>
                                            <h4 class="mt-2 text-2xl font-semibold tracking-tight">{{ $isEdit ? 'ព័ត៌មានចូលប្រើបច្ចុប្បន្ន' : 'គណនីដែលប្រព័ន្ធបង្កើត' }}</h4>
                                            <p class="mt-2 text-sm leading-7 text-slate-200">
                                                ឈ្មោះអ្នកប្រើត្រូវគ្នានឹងឈ្មោះឡាតាំង។ លេខសម្ងាត់ដើមគឺលេខសម្គាល់បុគ្គលិក។
                                            </p>
                                        </div>

                                        <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[24rem]">
                                            <div class="rounded-[1.35rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-100">ឈ្មោះអ្នកប្រើ</p>
                                                <p class="mt-2 break-all text-lg font-semibold text-white" data-credential-username>{{ $previewUsername }}</p>
                                            </div>
                                            <div class="rounded-[1.35rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-100">{{ $isEdit ? 'លេខសម្គាល់យោង' : 'លេខសម្ងាត់ដើម' }}</p>
                                                <p class="mt-2 break-all text-lg font-semibold text-white" data-credential-password>{{ $previewIdNumber }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                                    <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <h4 class="text-lg font-semibold text-slate-950">ព័ត៌មានមូលដ្ឋាន</h4>
                                                <p class="mt-1 text-sm text-slate-500">សូមបំពេញព័ត៌មានបុគ្គលិកជាមុនសិន មុនពេលបញ្ចូលឯកសារ។</p>
                                            </div>
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">ផ្នែក ០១</span>
                                        </div>

                                        <div class="grid gap-5 md:grid-cols-2">
                                            <div>
                                                <label class="form-label">លេខរៀង</label>
                                                <input type="text" value="{{ old('sequence_no', $teamStaff->sequence_no) }}" class="form-input bg-slate-50" readonly>
                                            </div>

                                            <div>
                                                <div class="mb-2 flex items-center justify-between gap-3">
                                                    <label class="form-label !mb-0">ឋានន្តរស័ក្តិ</label>
                                                    <a href="{{ route('admin.home', ['section' => 'staff-team-ranks']) }}" class="text-xs font-semibold text-[#356AE6] hover:underline">គ្រប់គ្រងឋានន្តរស័ក្តិ</a>
                                                </div>
                                                <select name="military_rank" class="form-input bg-slate-50 text-slate-700">
                                                    <option value="">ជ្រើសរើសឋានន្តរស័ក្តិ</option>
                                                    @foreach ($rankSuggestions as $rankSuggestion)
                                                        <option value="{{ $rankSuggestion }}" @selected(old('military_rank', $teamStaff->military_rank) === $rankSuggestion)>{{ $rankSuggestion }}</option>
                                                    @endforeach
                                                </select>
                                                @include('partials.field-error', ['name' => 'military_rank'])
                                            </div>

                                            <div>
                                                <label class="form-label">ឈ្មោះជាភាសាខ្មែរ</label>
                                                <input type="text" name="name_kh" value="{{ old('name_kh', $teamStaff->name_kh) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលឈ្មោះជាភាសាខ្មែរ">
                                                @include('partials.field-error', ['name' => 'name_kh'])
                                            </div>

                                            <div>
                                                <label class="form-label">ឈ្មោះឡាតាំង</label>
                                                <input type="text" name="name_latin" value="{{ old('name_latin', $teamStaff->name_latin) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលឈ្មោះឡាតាំង">
                                                @include('partials.field-error', ['name' => 'name_latin'])
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="form-label">លេខសម្គាល់បុគ្គលិក</label>
                                                <input type="text" name="id_number" value="{{ old('id_number', $teamStaff->id_number) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលលេខសម្គាល់បុគ្គលិក">
                                                @include('partials.field-error', ['name' => 'id_number'])
                                            </div>

                                            <div>
                                                <label class="form-label">ភេទ</label>
                                                <select name="gender" class="form-input bg-slate-50">
                                                    <option value="">ជ្រើសរើសភេទ</option>
                                                    @foreach ($genderOptions as $genderOption)
                                                        <option value="{{ $genderOption }}" @selected(old('gender', $teamStaff->gender) === $genderOption)>{{ $genderLabels[$genderOption] ?? $genderOption }}</option>
                                                    @endforeach
                                                </select>
                                                @include('partials.field-error', ['name' => 'gender'])
                                            </div>

                                            <div>
                                                <label class="form-label">លេខទូរស័ព្ទ</label>
                                                <input type="text" name="phone_number" value="{{ old('phone_number', $teamStaff->phone_number) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលលេខទូរស័ព្ទ">
                                                @include('partials.field-error', ['name' => 'phone_number'])
                                            </div>

                                            <div>
                                                <label class="form-label">មុខតំណែង</label>
                                                @php($currentPosition = old('position', $teamStaff->position))
                                                @php($isCustomPosition = filled($currentPosition) && ! $positionSuggestions->contains($currentPosition))
                                                <div class="relative">
                                                    <select 
                                                        id="position_select" 
                                                        name="{{ $isCustomPosition ? '' : 'position' }}" 
                                                        class="form-input bg-slate-50 {{ $isCustomPosition ? 'hidden' : '' }}" 
                                                        onchange="if(this.value === '__NEW__') { this.classList.add('hidden'); this.name = ''; document.getElementById('position_input').classList.remove('hidden'); document.getElementById('position_input').name = 'position'; document.getElementById('position_input').focus(); document.getElementById('position_cancel_btn').classList.remove('hidden'); }"
                                                    >
                                                        <option value="">ជ្រើសរើសមុខតំណែង</option>
                                                        @foreach ($positionSuggestions as $positionSuggestion)
                                                            <option value="{{ $positionSuggestion }}" @selected($currentPosition === $positionSuggestion)>{{ $positionSuggestion }}</option>
                                                        @endforeach
                                                        <option value="__NEW__" class="font-semibold text-[#356AE6]">+ បញ្ចូលថ្មី...</option>
                                                    </select>
                                                    <input 
                                                        type="text" 
                                                        id="position_input" 
                                                        name="{{ $isCustomPosition ? 'position' : '' }}" 
                                                        value="{{ $isCustomPosition ? $currentPosition : '' }}" 
                                                        class="form-input bg-slate-50 pr-10 {{ $isCustomPosition ? '' : 'hidden' }}" 
                                                        placeholder="បញ្ចូលមុខតំណែងថ្មី..."
                                                    >
                                                    <button 
                                                        type="button" 
                                                        id="position_cancel_btn" 
                                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition {{ $isCustomPosition ? '' : 'hidden' }}" 
                                                        onclick="document.getElementById('position_input').classList.add('hidden'); document.getElementById('position_input').name = ''; document.getElementById('position_input').value = ''; document.getElementById('position_select').classList.remove('hidden'); document.getElementById('position_select').name = 'position'; document.getElementById('position_select').value = ''; this.classList.add('hidden');" 
                                                        title="បោះបង់ការបញ្ចូលថ្មី និងជ្រើសរើសពីបញ្ជី"
                                                    >
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                                @include('partials.field-error', ['name' => 'position'])
                                            </div>

                                            <div>
                                                <label class="form-label">តួនាទី</label>
                                                @php($currentRole = old('role', $teamStaff->role))
                                                <select name="role" class="form-input bg-slate-50">
                                                    <option value="">ជ្រើសរើសតួនាទី</option>
                                                    @foreach ($roleOptions as $roleOption)
                                                        <option value="{{ $roleOption }}" @selected($currentRole === $roleOption)>{{ $roleLabels[$roleOption] ?? $roleOption }}</option>
                                                    @endforeach
                                                    @if (filled($currentRole) && ! in_array($currentRole, $roleOptions, true))
                                                        <option value="{{ $currentRole }}" selected>{{ $currentRole }}</option>
                                                    @endif
                                                </select>
                                                @include('partials.field-error', ['name' => 'role'])
                                            </div>
                                        </div>
                                    </section>

                                    <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7 xl:sticky xl:top-6 xl:self-start">
                                        <div class="mb-6 flex flex-col gap-3">
                                            <div>
                                                <h4 class="text-lg font-semibold text-slate-950">រូបភាពប្រវត្តិរូប</h4>
                                                <p class="mt-1 text-sm text-slate-500">បញ្ចូលរូបភាពសម្រាប់បុគ្គលិកនេះ។</p>
                                            </div>
                                            <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">ផ្នែក ០២</span>
                                        </div>

                                        <div class="space-y-4">
                                            <input type="file" name="avatar_image" accept=".jpg,.jpeg,.png,.webp" class="block w-full rounded-[1.35rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-[1.1rem] file:border-0 file:bg-[#e8efff] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#2c5dd6] hover:file:bg-[#dce7ff]" data-avatar-input>
                                            @include('partials.field-error', ['name' => 'avatar_image'])

                                            <div class="flex items-center justify-center rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                                @if ($isEdit && $teamStaff->hasStoredAvatar())
                                                    <img src="{{ route('team-staff.avatar', $teamStaff) }}" alt="{{ $teamStaff->name_latin }}" class="mx-auto rounded-full object-cover ring-1 ring-slate-200" style="width: 8.5rem; height: 8.5rem;" data-avatar-preview-image data-initial-src="{{ route('team-staff.avatar', $teamStaff) }}">
                                                    <div class="hidden h-[8.5rem] w-[8.5rem] place-items-center rounded-full bg-slate-900 text-3xl font-semibold text-white" data-avatar-preview-empty>
                                                        {{ strtoupper(substr($teamStaff->name_latin ?: $teamStaff->name_kh ?: 'S', 0, 1)) }}
                                                    </div>
                                                @else
                                                    <img src="" alt="ការមើលរូបភាពជាមុន" class="hidden mx-auto rounded-full object-cover ring-1 ring-slate-200" style="width: 8.5rem; height: 8.5rem;" data-avatar-preview-image>
                                                    <div class="grid h-[8.5rem] w-[8.5rem] place-items-center rounded-full bg-slate-900 text-3xl font-semibold text-white" data-avatar-preview-empty>
                                                        {{ strtoupper(substr($teamStaff->name_latin ?: $teamStaff->name_kh ?: 'S', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                </section>

                                <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h4 class="text-lg font-semibold text-slate-950">ឯកសារ</h4>
                                            <p class="mt-1 text-sm text-slate-500">ប្រអប់នីមួយៗខាងក្រោមត្រូវបានយកមកពីបញ្ជីតម្រូវការឯកសាររបស់បុគ្គលិកក្រុម។</p>
                                        </div>
                                        <a href="{{ route('admin.home', ['section' => 'staff-team-documents']) }}" class="inline-flex items-center justify-center rounded-[1.1rem] border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">
                                            គ្រប់គ្រងប្រភេទឯកសារ
                                        </a>
                                    </div>

                                    @if ($documentRequirements->isNotEmpty())
                                        <div class="grid gap-5 md:grid-cols-2">
                                            @foreach ($documentRequirements as $documentRequirement)
                                                @php($linkedDocument = $documentsByRequirementSlug->get($documentRequirement->slug))
                                                <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50 p-4">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-semibold text-slate-900">{{ $documentRequirement->name_kh }}</p>
                                                            <p class="mt-1 break-all text-xs text-slate-500">{{ $linkedDocument['original_name'] ?? 'មិនទាន់មានឯកសារបញ្ចូលទេ' }}</p>
                                                        </div>
                                                        <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $linkedDocument ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                            {{ $linkedDocument ? 'បានបញ្ចូល' : 'ខ្វះឯកសារ' }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-4 space-y-3">
                                                        <input
                                                            type="file"
                                                            name="documents[{{ $documentRequirement->id }}]"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            class="block w-full rounded-[1.35rem] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-[1.1rem] file:border-0 file:bg-[#e8efff] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#2c5dd6] hover:file:bg-[#dce7ff]"
                                                        >
                                                        @include('partials.field-error', ['name' => 'documents.'.$documentRequirement->id])

                                                        @if ($isEdit && $linkedDocument)
                                                            <a href="{{ route('team-staff.documents.download-by-requirement', [$teamStaff, $documentRequirement]) }}" class="inline-flex items-center rounded-[1.1rem] border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                                                ទាញយកឯកសារបច្ចុប្បន្ន
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="rounded-[1.35rem] border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                            មិនទាន់មានការបង្កើតប្រភេទឯកសារសម្រាប់បុគ្គលិកក្រុមនៅឡើយទេ។
                                        </div>
                                    @endif

                                    @include('partials.field-error', ['name' => 'documents'])

                                    @if ($isEdit && $legacyDocuments->isNotEmpty())
                                        <div class="mt-6 rounded-[1.35rem] border border-slate-200 bg-white p-4">
                                            <p class="text-sm font-semibold text-slate-900">ឯកសារចាស់</p>
                                            <p class="mt-1 text-sm text-slate-500">ឯកសារទាំងនេះនៅតែភ្ជាប់ជាមួយកំណត់ត្រានេះ ប៉ុន្តែមិនទាន់ភ្ជាប់ជាមួយតម្រូវការឯកសារដែលកំពុងប្រើទេ។</p>
                                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                @foreach ($legacyDocuments as $document)
                                                    @php($documentIndex = $existingDocuments->search(fn ($entry) => ($entry['path'] ?? null) === ($document['path'] ?? null)))
                                                    @if ($documentIndex !== false)
                                                        <a href="{{ route('team-staff.documents.download', [$teamStaff, $documentIndex]) }}" class="inline-flex items-center rounded-[1.1rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                                            {{ $document['label'] ?? 'ឯកសារ' }}: {{ $document['original_name'] ?? '-' }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </section>

                                <section class="sticky bottom-3 z-10 rounded-[1.6rem] border border-slate-200 bg-white/95 p-4 shadow-[0_10px_25px_rgba(15,23,42,0.08)] backdrop-blur">
                                    <div class="flex flex-wrap items-center justify-end gap-3">
                                        <button type="reset" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            កំណត់ឡើងវិញ
                                        </button>
                                        <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            បោះបង់
                                        </a>
                                        <button type="submit" class="inline-flex items-center justify-center rounded-[1.35rem] bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                            {{ $isEdit ? 'រក្សាទុកការកែប្រែ' : 'បង្កើតបុគ្គលិក' }}
                                        </button>
                                    </div>
                                </section>
                            </form>
                        </div>
                    </div>

                    <datalist id="position-options">
                        @foreach ($positionSuggestions as $positionSuggestion)
                            <option value="{{ $positionSuggestion }}"></option>
                        @endforeach
                    </datalist>

                    {{-- Blade data bridge: stores server-side values for the JS below --}}
                    <div
                        id="credential-data"
                        class="hidden"
                        data-preview-username="{{ $previewUsername }}"
                        data-preview-id-number="{{ $previewIdNumber ?: '000001' }}"
                    ></div>

                    <script>
                        (() => {
                            const credentialNameInput = document.querySelector('input[name="name_latin"]');
                            const credentialIdInput = document.querySelector('input[name="id_number"]');
                            const credentialUsernameOutput = document.querySelector('[data-credential-username]');
                            const credentialPasswordOutput = document.querySelector('[data-credential-password]');
                            const avatarInput = document.querySelector('[data-avatar-input]');
                            const avatarPreviewImage = document.querySelector('[data-avatar-preview-image]');
                            const avatarPreviewEmpty = document.querySelector('[data-avatar-preview-empty]');

                            const usernameBase = (value) => {
                                const normalized = (value || '')
                                    .replace(/\s+/g, ' ')
                                    .trim();

                                return normalized || 'បុគ្គលិក';
                            };

                            const credentialData = document.getElementById('credential-data');
                            const fallbackUsername = credentialData ? credentialData.dataset.previewUsername : '';
                            const fallbackIdNumber = credentialData ? credentialData.dataset.previewIdNumber : '000001';

                            const syncCredentialPreview = () => {
                                if (credentialUsernameOutput) {
                                    credentialUsernameOutput.textContent = usernameBase(credentialNameInput?.value || fallbackUsername);
                                }

                                if (credentialPasswordOutput) {
                                    credentialPasswordOutput.textContent = credentialIdInput?.value?.trim() || fallbackIdNumber;
                                }
                            };

                            credentialNameInput?.addEventListener('input', syncCredentialPreview);
                            credentialIdInput?.addEventListener('input', syncCredentialPreview);
                            syncCredentialPreview();

                            if (avatarInput && avatarPreviewImage && avatarPreviewEmpty) {
                                let previewObjectUrl = null;

                                avatarInput.addEventListener('change', (event) => {
                                    const [file] = event.target.files || [];
                                    const initialSrc = avatarPreviewImage.dataset.initialSrc || '';

                                    if (previewObjectUrl) {
                                        URL.revokeObjectURL(previewObjectUrl);
                                        previewObjectUrl = null;
                                    }

                                    if (!file) {
                                        if (initialSrc !== '') {
                                            avatarPreviewImage.src = initialSrc;
                                            avatarPreviewImage.classList.remove('hidden');
                                            avatarPreviewEmpty.classList.add('hidden');
                                            avatarPreviewEmpty.classList.remove('grid');
                                        } else {
                                            avatarPreviewImage.src = '';
                                            avatarPreviewImage.classList.add('hidden');
                                            avatarPreviewEmpty.classList.remove('hidden');
                                            avatarPreviewEmpty.classList.add('grid');
                                        }

                                        return;
                                    }

                                    previewObjectUrl = URL.createObjectURL(file);
                                    avatarPreviewImage.src = previewObjectUrl;
                                    avatarPreviewImage.classList.remove('hidden');
                                    avatarPreviewEmpty.classList.add('hidden');
                                    avatarPreviewEmpty.classList.remove('grid');
                                });
                            }
                        })();
                    </script>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <p>&copy; {{ now()->year }} ប្រព័ន្ធចុះឈ្មោះយោធា</p>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">API ដំណើរការ</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">V1.0</span>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    </div>
@endsection
