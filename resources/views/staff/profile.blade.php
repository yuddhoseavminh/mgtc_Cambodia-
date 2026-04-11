@extends('app')

@section('body')
    @php
        $profileFields = [
            ['label' => 'ឈ្មោះខ្មែរ', 'value' => $staff->name_kh],
            ['label' => 'ឈ្មោះឡាតាំង', 'value' => $staff->name_latin],
            ['label' => 'លេខសម្គាល់', 'value' => $staff->id_number],
            ['label' => 'ភេទ', 'value' => match(strtolower((string)$staff->gender)) { 'male' => 'ប្រុស', 'female' => 'ស្រី', 'other' => 'ផ្សេងទៀត', default => $staff->gender }],
            ['label' => 'មុខតំណែង', 'value' => $staff->position],
            ['label' => 'ឋានន្តរស័ក្តិ', 'value' => $staff->military_rank],
            ['label' => 'តួនាទី', 'value' => match(strtolower((string)$staff->role)) { 'admin' => 'អ្នកគ្រប់គ្រង', 'manager' => 'អ្នកចាត់ការ', 'staff' => 'បុគ្គលិក', 'viewer' => 'អ្នកមើល', default => $staff->role }],
            ['label' => 'លេខទូរស័ព្ទ', 'value' => $staff->phone_number],
        ];
        $indexedឯកសារ = $documents
            ->map(fn ($document, $index) => [...$document, 'document_index' => $index])
            ->values();
        $documentsByRequirementSlug = $indexedឯកសារ
            ->filter(fn ($document) => filled($document['requirement_slug'] ?? null))
            ->groupBy(fn ($document) => $document['requirement_slug']);
        $otherឯកសារ = $indexedឯកសារ
            ->filter(fn ($document) => blank($document['requirement_slug'] ?? null))
            ->values();
    @endphp

    <div class="min-h-screen bg-[radial-gradient(circle_at_top,#4b0e14_0%,#17070a_32%,#020304_100%)] px-4 py-4 sm:px-6 sm:py-6">
        <div class="mx-auto w-full max-w-3xl space-y-4">
            @if (session('status'))
                <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-[1.4rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,#111827,#7f1d1d_82%)] text-white shadow-[0_30px_80px_rgba(0,0,0,0.35)]">
                <div class="flex flex-col gap-5 px-5 py-6 sm:px-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-200">ប្រវត្តិរូបបុគ្គលិក</p>
                            <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $staff->displayName() }}</h1>
                            <p class="mt-2 text-sm text-slate-200">{{ $staff->username }} · {{ $staff->position }}</p>
                        </div>

                        <form method="POST" action="{{ route('staff.logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex min-h-[2.85rem] items-center justify-center rounded-[1rem] border border-white/10 bg-white/10 px-4 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">
                                ចាកចេញ
                            </button>
                        </form>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="shrink-0">
                            @if ($staff->hasStoredAvatar())
                                <img src="{{ route('staff.profile.avatar') }}" alt="{{ $staff->displayName() }}" class="h-24 w-24 rounded-[1.8rem] object-cover ring-2 ring-white/20">
                            @else
                                <div class="grid h-24 w-24 place-items-center rounded-[1.8rem] bg-white/10 text-3xl font-semibold text-white ring-2 ring-white/20">
                                    {{ strtoupper(substr($staff->name_latin ?: $staff->name_kh, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="grid flex-1 gap-3 sm:grid-cols-3">
                            <div class="rounded-[1.35rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-100">ប្រវត្តិរូប</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ $profileCompletion }}%</p>
                            </div>
                            <div class="rounded-[1.35rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-100">ស្ថានភាពគណនី</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ $staff->is_active ? 'សកម្ម' : 'អសកម្ម' }}</p>
                            </div>
                            <div class="rounded-[1.35rem] border border-white/10 bg-white/8 px-4 py-4 backdrop-blur-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-100">ឯកសារ</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ $documents->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if ($profileCompletion < 100)
                <section class="rounded-[1.6rem] border border-amber-200 bg-amber-50 px-4 py-4 shadow-sm">
                    <p class="text-sm font-semibold text-amber-900">ប្រវត្តិរូបមិនទាន់ពេញលេញ</p>
                    <p class="mt-1 text-sm leading-6 text-amber-700">
                        ព័ត៌មានបុគ្គលិកមួយចំនួននៅតែបាត់។ សូមទាក់ទងមកក្រុមរដ្ឋបាល ប្រសិនបើមានព័ត៌មានណាមួយត្រូវការកែតម្រូវ។
                    </p>
                </section>
            @endif

            <section class="rounded-[1.9rem] border border-slate-200 bg-white px-4 py-5 shadow-[0_18px_45px_rgba(15,23,42,0.08)] sm:px-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ព័ត៌មានផ្ទាល់ខ្លួន</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">ព័ត៌មានលម្អិតរបស់អ្នក</h2>
                    </div>
                    <a href="{{ route('staff.password.edit') }}" class="inline-flex min-h-[2.85rem] items-center justify-center rounded-[1rem] border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        ផ្លាស់ប្តូរលេខសម្ងាត់
                    </a>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($profileFields as $field)
                        <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $field['label'] }}</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $field['value'] ?: '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-[1.9rem] border border-slate-200 bg-white px-4 py-5 shadow-[0_18px_45px_rgba(15,23,42,0.08)] sm:px-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ឯកសារសុវត្ថិភាព</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">ការបញ្ចូលឯកសារ</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            @if ($documentRequirements->isNotEmpty())
                                បញ្ជីត្រូវបានគ្រប់គ្រងដោយរដ្ឋបាល។ បុគ្គលិកត្រូវបញ្ចូលឯកសារតាមប្រភេទច្បាស់លាស់នៅទីនេះ។
                            @else
                                រដ្ឋបាលមិនទាន់បានបង្កើតប្រភេទបញ្ជីឯកសារនៅឡើយទេ។ អ្នកនៅតែអាចបញ្ចូលឯកសារឯកជនបាន ដោយការដាក់ចំណងជើងជាក់លាក់។
                            @endif
                        </p>
                    </div>

                    <button type="button" class="inline-flex min-h-[3.1rem] items-center justify-center rounded-[1.15rem] bg-[linear-gradient(135deg,#7f1d1d,#dc2626)] px-5 text-sm font-semibold text-white shadow-[0_18px_35px_rgba(127,29,29,0.28)] transition hover:opacity-95" data-upload-open>
                        បញ្ចូលឯកសារ
                    </button>
                </div>

                @if ($documentRequirements->isNotEmpty())
                    <div class="mt-5 grid gap-3 md:grid-cols-2">
                        @foreach ($documentRequirements as $documentRequirement)
                            @php
                                $requirementឯកសារ = $documentsByRequirementSlug->get($documentRequirement->slug, collect())->values();
                            @endphp
                            <article class="rounded-[1.45rem] border border-slate-200 bg-slate-50 px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-base font-semibold text-slate-950">{{ $documentRequirement->name_kh }}</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $requirementឯកសារ->isNotEmpty() ? $requirementឯកសារ->count().' ឯកសារបានបញ្ចូល' : 'មិនទាន់មានឯកសារទេ' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $requirementឯកសារ->isNotEmpty() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $requirementឯកសារ->isNotEmpty() ? 'បានបញ្ជូល' : 'បាត់ឯកសារ' }}
                                    </span>
                                </div>

                                @if ($requirementឯកសារ->isNotEmpty())
                                    <div class="mt-3 space-y-2">
                                        @foreach ($requirementឯកសារ as $document)
                                            @php
                                                $documentSource = $document['uploaded_by'] ?? 'admin';
                                                $canDeleteDocument = $documentSource === 'staff' && isset($document['document_index']);
                                            @endphp
                                            <div class="rounded-[1rem] border border-slate-200 bg-white px-3 py-3">
                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $document['original_name'] ?? '-' }}</p>
                                                        <div class="mt-2 flex flex-wrap gap-2">
                                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200">
                                                                {{ $documentSource === 'staff' ? 'បញ្ចូលដោយអ្នក' : 'បញ្ចូលដោយរដ្ឋបាល' }}
                                                            </span>
                                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200">
                                                                {{ match(ucfirst((string) ($document['status'] ?? ''))) { 'Approved' => 'បានអនុម័តរួចរាល់', 'Pending' => 'រង់ចាំការអនុម័ត', 'Rejected' => 'ត្រូវបានបដិសេធ', default => ($documentSource === 'staff' ? 'រង់ចាំការអនុម័ត' : 'បានអនុម័តរួចរាល់') } }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-wrap gap-2 sm:justify-end">
                                                        <a href="{{ route('staff.profile.documents.show', $document['document_index']) }}" target="_blank" class="inline-flex min-h-[2.8rem] items-center justify-center rounded-[1rem] border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                                            មើលឯកសារ
                                                        </a>
                                                        <a href="{{ route('staff.profile.documents.download', $document['document_index']) }}" class="inline-flex min-h-[2.8rem] items-center justify-center rounded-[1rem] border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                                            ទាញយក
                                                        </a>

                                                        @if ($canDeleteDocument)
                                                            <form method="POST" action="{{ route('staff.profile.documents.destroy', $document['document_index']) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="inline-flex min-h-[2.8rem] items-center justify-center rounded-[1rem] border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                                                                    លុប
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button" class="inline-flex min-h-[2.8rem] items-center justify-center rounded-[1rem] bg-[linear-gradient(135deg,#7f1d1d,#dc2626)] px-4 text-sm font-semibold text-white transition hover:opacity-95" data-upload-open data-upload-requirement="{{ $documentRequirement->id }}">
                                        {{ $requirementឯកសារ->isNotEmpty() ? 'បញ្ចូលបន្ថែម' : 'បញ្ចូល' }}
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-[1.45rem] border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                        <p class="text-base font-semibold text-slate-900">មិនទាន់មានបញ្ជីឯកសារពីរដ្ឋបាលនៅឡើយទេ</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            ឯកសាររាល់ការបញ្ចូលនីមួយៗ នឹងត្រូវរក្សាទុកជាឯកសារឯកជន រហូតដល់រដ្ឋបាលបង្កើតបញ្ជី។
                        </p>
                    </div>
                @endif

                @if ($otherឯកសារ->isNotEmpty())
                    <div class="mt-5 space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ឯកសារផ្សេងៗ</p>
                        @foreach ($otherឯកសារ as $document)
                            @php
                                $documentSource = $document['uploaded_by'] ?? 'admin';
                                $documentStatus = $document['status'] ?? ($documentSource === 'staff' ? 'រង់ចាំការអនុម័ត' : 'ឯកសាររដ្ឋបាល');
                                $canDelete = $documentSource === 'staff';
                                $documentIndex = $documents->search(fn ($entry) => ($entry['path'] ?? null) === ($document['path'] ?? null));
                            @endphp
                            <article class="rounded-[1.45rem] border border-slate-200 bg-slate-50 px-4 py-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="text-base font-semibold text-slate-950">{{ $document['label'] ?? 'ឯកសារ' }}</p>
                                        <p class="mt-1 break-all text-sm text-slate-500">{{ $document['original_name'] ?? '-' }}</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200">
                                                {{ match(ucfirst((string) $documentStatus)) { 'Approved' => 'បានអនុម័តរួចរាល់', 'Pending' => 'រង់ចាំការអនុម័ត', 'Rejected' => 'ត្រូវបានបដិសេធ', default => ucfirst((string) $documentStatus) } }}
                                            </span>
                                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200">
                                                {{ $documentSource === 'staff' ? 'បញ្ចូលដោយអ្នក' : 'ផ្តល់ដោយរដ្ឋបាល' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 sm:justify-end">
                                        @if ($documentIndex !== false)
                                            <a href="{{ route('staff.profile.documents.show', $documentIndex) }}" target="_blank" class="inline-flex min-h-[2.8rem] items-center justify-center rounded-[1rem] border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                                មើលឯកសារ
                                            </a>
                                            <a href="{{ route('staff.profile.documents.download', $documentIndex) }}" class="inline-flex min-h-[2.8rem] items-center justify-center rounded-[1rem] border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                                ទាញយក
                                            </a>
                                        @endif

                                        @if ($canDelete && $documentIndex !== false)
                                            <form method="POST" action="{{ route('staff.profile.documents.destroy', $documentIndex) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex min-h-[2.8rem] items-center justify-center rounded-[1rem] border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                                                    លុប
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/70 px-3 py-4 backdrop-blur-sm sm:items-center sm:px-4" data-upload-modal aria-hidden="true">
        <div class="absolute inset-0" data-upload-close></div>
        <div class="relative z-10 w-full max-w-lg overflow-hidden rounded-[2rem] border border-white/10 bg-white shadow-[0_30px_80px_rgba(0,0,0,0.35)]">
            <div class="bg-[linear-gradient(135deg,#111827,#7f1d1d)] px-5 py-5 text-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.26em] text-rose-200">បន្ថែមឯកសារ</p>
                        <h3 class="mt-2 text-2xl font-semibold tracking-tight">ជ្រើសរើសឯកសារបញ្ជូល</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-200">
                            @if ($documentRequirements->isNotEmpty())
                                ជ្រើសរើសប្រភេទឯកសារណាមួយពីបញ្ជីរដ្ឋបាល។
                            @else
                                បញ្ជូលចំណងជើងច្បាស់លាស់ដើម្បីឲ្យរដ្ឋបាលងាយស្រួលកំណត់អត្តសញ្ញាណ។
                            @endif
                        </p>
                    </div>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white" data-upload-close>
                        ×
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('staff.profile.documents.store') }}" enctype="multipart/form-data" class="space-y-4 px-5 py-5" data-upload-form>
                @csrf

                @if ($documentRequirements->isNotEmpty())
                    <div>
                        <label for="document_requirement_id" class="mb-2 block text-sm font-semibold text-slate-900">ប្រភេទឯកសារ</label>
                        <select id="document_requirement_id" name="document_requirement_id" class="form-input min-h-[3.3rem] bg-slate-50" required data-upload-requirement-select>
                            <option value="">ជ្រើសរើសឯកសារដែលត្រូវការ</option>
                            @foreach ($documentRequirements as $documentRequirement)
                                <option value="{{ $documentRequirement->id }}">{{ $documentRequirement->name_kh }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label for="document_title" class="mb-2 block text-sm font-semibold text-slate-900">ចំណងជើងឯកសារ</label>
                        <input id="document_title" name="document_title" type="text" class="form-input min-h-[3.3rem] bg-slate-50" placeholder="ឧទាហរណ៍៖ លិខិតបញ្ជាក់សេវាកម្ម" required>
                    </div>
                @endif

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-900">ភ្ជាប់ឯកសារ</label>
                    <label class="flex cursor-pointer flex-col items-center justify-center rounded-[1.6rem] border-2 border-dashed border-slate-300 bg-slate-50 px-5 py-8 text-center transition hover:border-rose-300 hover:bg-rose-50/50" data-upload-dropzone>
                        <span class="grid h-16 w-16 place-items-center rounded-full bg-white text-rose-700 shadow-sm">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 16V4"></path>
                                <path d="m7 9 5-5 5 5"></path>
                                <path d="M5 20h14"></path>
                            </svg>
                        </span>
                        <span class="mt-4 text-lg font-semibold text-slate-900">ចុចដើម្បីរើសឯកសារបញ្ជូល</span>
                        <span class="mt-2 text-sm leading-6 text-slate-500">អ្នកក៏អាចអូសឯកសារទម្លាក់នៅទីនេះបានផងដែរ។</span>
                        <span class="mt-4 text-sm font-medium text-rose-700" data-upload-file-name>មិនទាន់មានឯកសារ</span>
                        <input type="file" name="document_file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required data-upload-input>
                    </label>
                </div>

                <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-sm font-semibold text-slate-900">ច្បាប់លក្ខខណ្ឌ</p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        អនុញ្ញាត៖ PDF, JPG, PNG, DOCX (អតិបរមា 10MB)។ មានតែអ្នក និងរដ្ឋបាលប៉ុណ្ណោះដែលអាចមើលឯកសារនេះបាន។
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <button type="button" class="inline-flex min-h-[3.2rem] items-center justify-center rounded-[1.15rem] border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" data-upload-close>
                        បោះបង់
                    </button>
                    <button type="submit" class="inline-flex min-h-[3.2rem] items-center justify-center rounded-[1.15rem] bg-[linear-gradient(135deg,#7f1d1d,#dc2626)] px-5 text-sm font-semibold text-white shadow-[0_18px_35px_rgba(127,29,29,0.28)] transition hover:opacity-95">
                        បញ្ចូលឯកសារ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm" data-upload-loading>
        <div class="w-full max-w-sm rounded-[1.8rem] bg-white px-5 py-6 text-center shadow-[0_30px_80px_rgba(0,0,0,0.35)]">
            <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-rose-700 border-r-transparent"></div>
            <p class="mt-4 text-lg font-semibold text-slate-950">កំពុងបញ្ចូលឯកសារ...</p>
            <p class="mt-2 text-sm text-slate-500">សូមកុំបិទទំព័រនេះរហូតដល់ការបញ្ចូលត្រូវបានបញ្ចប់។</p>
        </div>
    </div>

    <button type="button" class="fixed bottom-4 right-4 z-30 inline-flex min-h-[3.3rem] items-center justify-center rounded-full bg-[linear-gradient(135deg,#7f1d1d,#dc2626)] px-5 text-sm font-semibold text-white shadow-[0_20px_40px_rgba(127,29,29,0.35)] md:hidden" data-upload-open>
        បញ្ចូលឯកសារ
    </button>

    <script>
        (() => {
            const modal = document.querySelector('[data-upload-modal]');
            const fileInput = document.querySelector('[data-upload-input]');
            const fileName = document.querySelector('[data-upload-file-name]');
            const dropzone = document.querySelector('[data-upload-dropzone]');
            const uploadForm = document.querySelector('[data-upload-form]');
            const uploadLoading = document.querySelector('[data-upload-loading]');
            const requirementSelect = document.querySelector('[data-upload-requirement-select]');

            if (!modal || !fileInput || !fileName || !dropzone || !uploadForm) {
                return;
            }

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
            };

            const syncFileName = () => {
                const file = fileInput.files?.[0];
                fileName.textContent = file ? file.name : 'មិនទាន់មានឯកសារ';
            };

            document.querySelectorAll('[data-upload-open]').forEach((button) => {
                button.addEventListener('click', () => {
                    const requirementId = button.dataset.uploadRequirement || '';

                    if (requirementSelect && requirementId !== '') {
                        requirementSelect.value = requirementId;
                    }

                    openModal();
                });
            });

            document.querySelectorAll('[data-upload-close]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                    closeModal();
                }
            });

            ['dragenter', 'dragover'].forEach((eventName) => {
                dropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    dropzone.classList.add('border-rose-400', 'bg-rose-50');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                dropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    dropzone.classList.remove('border-rose-400', 'bg-rose-50');
                });
            });

            dropzone.addEventListener('drop', (event) => {
                const file = event.dataTransfer?.files?.[0];

                if (!file) {
                    return;
                }

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                syncFileName();
            });

            fileInput.addEventListener('change', syncFileName);

            uploadForm.addEventListener('submit', () => {
                if (!uploadLoading) {
                    return;
                }

                uploadLoading.classList.remove('hidden');
                uploadLoading.classList.add('flex');
            });
        })();
    </script>
@endsection
