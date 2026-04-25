@extends('app')

@section('body')
    @php
        $statusLabels = [
            'Pending' => 'រង់ចាំ',
            'Reviewed' => 'បានពិនិត្យ',
            'Approved' => 'អនុម័ត',
            'Rejected' => 'បដិសេធ',
        ];
        $previewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        $imagePreviewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $availableDocumentRequirements = collect($documentRequirements ?? [])
            ->merge($application->applicationDocuments->pluck('documentRequirement')->filter())
            ->unique('id')
            ->values();
        $uploadedDocumentCount = $application->applicationDocuments
            ->filter(fn ($document) => $document->status === \App\Models\ApplicationDocument::STATUS_HAVE && filled($document->file_path))
            ->count();
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-screen lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'applications'])

                <main class="flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => 'កែប្រែពាក្យស្នើសុំ',
                        'subtitle' => 'បញ្ជី / ពាក្យស្នើសុំ',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => 'applications',
                    ])

                    <div class="flex-1 p-4 sm:p-6 lg:p-8">
                        <div class="mx-auto w-full max-w-[1040px] space-y-6">
                            <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#ffffff,#f8fbff)] p-6 shadow-[0_20px_50px_rgba(15,23,42,0.06)] sm:p-7">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div class="max-w-3xl">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">ព័ត៌មានស្នើសុំ</p>
                                        <h3 class="mt-2 text-[1.85rem] font-semibold tracking-tight text-slate-950">កែប្រែព័ត៌មានអ្នកដាក់ពាក្យ</h3>
                                        <p class="mt-2 text-sm leading-7 text-slate-500">កែប្រែព័ត៌មានមូលដ្ឋាន ស្ថានភាព និងកំណត់ចំណាំរបស់ពាក្យស្នើសុំនេះ។</p>
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('admin.applications.show', $application) }}" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            មើលលម្អិត
                                        </a>
                                        <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            ត្រឡប់ទៅបញ្ជី
                                        </a>
                                    </div>
                                </div>
                            </section>

                            <form method="POST" action="{{ route('admin.applications.replace', $application) }}" class="space-y-6">
                                @csrf
                                @method('PUT')

                                <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                    <div class="mb-6">
                                        <h4 class="text-lg font-semibold text-slate-950">ព័ត៌មានមូលដ្ឋាន</h4>
                                        <p class="mt-1 text-sm text-slate-500">កែប្រែទិន្នន័យចម្បងរបស់អ្នកដាក់ពាក្យ។</p>
                                    </div>

                                    <div class="grid gap-5 md:grid-cols-2">
                                        <div>
                                            <label class="form-label">គោត្តនាម នាម</label>
                                            <input type="text" name="khmer_name" value="{{ old('khmer_name', $application->khmer_name) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'khmer_name'])
                                        </div>
                                        <div>
                                            <label class="form-label">ឈ្មោះឡាតាំង</label>
                                            <input type="text" name="latin_name" value="{{ old('latin_name', $application->latin_name) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'latin_name'])
                                        </div>
                                        <div>
                                            <label class="form-label">អត្តលេខ</label>
                                            <input type="text" name="id_number" value="{{ old('id_number', $application->id_number) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'id_number'])
                                        </div>
                                        <div>
                                            <label class="form-label">ភេទ</label>
                                            <select name="gender" class="form-input bg-slate-50">
                                                <option value="">ជ្រើសរើសភេទ</option>
                                                @foreach ($genders as $gender)
                                                    <option value="{{ $gender }}" @selected(old('gender', $application->gender) === $gender)>{{ $genderLabels[$gender] ?? $gender }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'gender'])
                                        </div>
                                        <div>
                                            <label class="form-label">ឋានន្តរស័ក្តិ</label>
                                            <select name="rank_id" class="form-input bg-slate-50">
                                                <option value="">ជ្រើសរើសឋានន្តរស័ក្តិ</option>
                                                @foreach ($ranks as $rank)
                                                    <option value="{{ $rank->id }}" @selected((string) old('rank_id', $application->rank_id) === (string) $rank->id)>{{ $rank->name_kh }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'rank_id'])
                                        </div>
                                        <div>
                                            <label class="form-label">លេខទូរស័ព្ទ</label>
                                            <input type="text" name="phone_number" value="{{ old('phone_number', $application->phone_number) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'phone_number'])
                                        </div>
                                        <div>
                                            <label class="form-label">ថ្ងៃខែឆ្នាំកំណើត</label>
                                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $application->date_of_birth?->format('Y-m-d')) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'date_of_birth'])
                                        </div>
                                        <div>
                                            <label class="form-label">ថ្ងៃចូលបម្រើកងទ័ព</label>
                                            <input type="date" name="date_of_enlistment" value="{{ old('date_of_enlistment', $application->date_of_enlistment?->format('Y-m-d')) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'date_of_enlistment'])
                                        </div>
                                    </div>
                                </section>

                                <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                    <div class="mb-6">
                                        <h4 class="text-lg font-semibold text-slate-950">ព័ត៌មានបម្រើការងារ</h4>
                                        <p class="mt-1 text-sm text-slate-500">កែប្រែព័ត៌មានអង្គភាព វគ្គសិក្សា និងទីតាំង។</p>
                                    </div>

                                    <div class="grid gap-5 md:grid-cols-2">
                                        <div>
                                            <label class="form-label">មុខតំណែង</label>
                                            <input type="text" name="position" value="{{ old('position', $application->position) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'position'])
                                        </div>
                                        <div>
                                            <label class="form-label">អង្គភាព</label>
                                            <input type="text" name="unit" value="{{ old('unit', $application->unit) }}" class="form-input bg-slate-50">
                                            @include('partials.field-error', ['name' => 'unit'])
                                        </div>
                                        <div>
                                            <label class="form-label">វគ្គសិក្សា</label>
                                            <select name="course_id" class="form-input bg-slate-50">
                                                <option value="">ជ្រើសរើសវគ្គសិក្សា</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}" @selected((string) old('course_id', $application->course_id) === (string) $course->id)>{{ $course->name }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'course_id'])
                                        </div>
                                        <div>
                                            <label class="form-label">កម្រិតវប្បធម៌</label>
                                            <select name="cultural_level_id" class="form-input bg-slate-50">
                                                <option value="">ជ្រើសរើសកម្រិតវប្បធម៌</option>
                                                @foreach ($culturalLevels as $level)
                                                    <option value="{{ $level->id }}" @selected((string) old('cultural_level_id', $application->cultural_level_id) === (string) $level->id)>{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'cultural_level_id'])
                                        </div>
                                        <div>
                                            <label class="form-label">ទីកន្លែងកំណើត</label>
                                            <select name="place_of_birth" class="form-input bg-slate-50">
                                                <option value="">ជ្រើសរើសខេត្ត/រាជធានី</option>
                                                @foreach ($provinces as $province)
                                                    <option value="{{ $province }}" @selected(old('place_of_birth', $application->place_of_birth) === $province)>{{ $provinceLabels[$province] ?? $province }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'place_of_birth'])
                                        </div>
                                        <div>
                                            <label class="form-label">អាសយដ្ឋានបច្ចុប្បន្ន</label>
                                            <select name="current_address" class="form-input bg-slate-50">
                                                <option value="">ជ្រើសរើសខេត្ត/រាជធានី</option>
                                                @foreach ($provinces as $province)
                                                    @php($provinceLabel = $provinceLabels[$province] ?? $province)
                                                    <option value="{{ $provinceLabel }}" @selected(old('current_address', $application->current_address) === $provinceLabel)>{{ $provinceLabel }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'current_address'])
                                        </div>
                                        <div>
                                            <label class="form-label">ស្ថានភាពគ្រួសារ</label>
                                            <select name="family_situation" class="form-input bg-slate-50">
                                                <option value="">ជ្រើសរើសស្ថានភាពគ្រួសារ</option>
                                                @foreach ($familySituations as $familySituation)
                                                    <option value="{{ $familySituation }}" @selected(old('family_situation', $application->family_situation) === $familySituation)>{{ $familySituationLabels[$familySituation] ?? $familySituation }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'family_situation'])
                                        </div>
                                    </div>
                                </section>

                                <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                    <div class="mb-6">
                                        <h4 class="text-lg font-semibold text-slate-950">ស្ថានភាព និងកំណត់ចំណាំ</h4>
                                        <p class="mt-1 text-sm text-slate-500">កែប្រែស្ថានភាព និងមតិយោបល់របស់អ្នកគ្រប់គ្រង។</p>
                                    </div>

                                    <div class="grid gap-5">
                                        <div>
                                            <label class="form-label">ស្ថានភាព</label>
                                            <select name="status" class="form-input bg-slate-50">
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" @selected(old('status', $application->status) === $status)>{{ $statusLabels[$status] ?? $status }}</option>
                                                @endforeach
                                            </select>
                                            @include('partials.field-error', ['name' => 'status'])
                                        </div>
                                        <div>
                                            <label class="form-label">កំណត់ចំណាំ</label>
                                            <textarea name="admin_notes" rows="7" class="form-input bg-slate-50" placeholder="បញ្ចូលកំណត់ចំណាំ">{{ old('admin_notes', $application->admin_notes) }}</textarea>
                                            @include('partials.field-error', ['name' => 'admin_notes'])
                                        </div>
                                    </div>
                                </section>

                                <section class="sticky bottom-3 z-10 rounded-[1.6rem] border border-slate-200 bg-white/95 p-4 shadow-[0_10px_25px_rgba(15,23,42,0.08)] backdrop-blur">
                                    <div class="flex flex-wrap items-center justify-end gap-3">
                                        <a href="{{ route('admin.applications.show', $application) }}" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            បោះបង់
                                        </a>
                                        <button type="submit" class="inline-flex items-center justify-center rounded-[1.35rem] bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                            រក្សាទុកការកែប្រែ
                                        </button>
                                    </div>
                                </section>
                            </form>

                            <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-950">ឯកសារភស្តុតាង</h4>
                                        <p class="mt-1 text-sm text-slate-500">គ្រប់គ្រងឯកសារដែលបេក្ខជនបានបញ្ចូល និងបន្ថែមឯកសារថ្មីតាមប្រភេទ។</p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200">
                                        {{ $uploadedDocumentCount }} ឯកសារ
                                    </span>
                                </div>

                                <div class="space-y-4">
                                    <?php foreach ($availableDocumentRequirements as $requirement): ?>
                                        <?php
                                            $groupDocs = $application->applicationDocuments
                                                ->where('document_requirement_id', $requirement->id)
                                                ->filter(fn ($document) => $document->status === \App\Models\ApplicationDocument::STATUS_HAVE && filled($document->file_path));
                                            $docCount = $groupDocs->count();
                                        ?>
                                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-4 shadow-sm">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900">{{ $requirement->name_kh ?: $requirement->name_en }}</p>
                                                    <p class="mt-1 break-all text-xs text-slate-500">
                                                        {{ $docCount > 0 ? "បានបញ្ចូល $docCount ឯកសារ" : 'មិនទាន់មានឯកសារបញ្ចូលទេ' }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $docCount > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ $docCount > 0 ? "$docCount ឯកសារ" : 'ខ្វះឯកសារ' }}
                                                </span>
                                            </div>

                                            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                                <?php if ($groupDocs->isNotEmpty()): ?>
                                                    <?php foreach ($groupDocs as $document): ?>
                                                    <?php
                                                        $extension = strtolower(pathinfo($document->original_name ?? '', PATHINFO_EXTENSION));
                                                        $canPreviewInline = in_array($extension, $previewableExtensions, true);
                                                        $previewKind = in_array($extension, $imagePreviewableExtensions, true) ? 'image' : ($extension === 'pdf' ? 'pdf' : 'other');
                                                    ?>
                                                    <div class="flex min-h-[10.5rem] flex-col justify-between rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_18px_36px_rgba(15,23,42,0.08)]">
                                                        <div>
                                                            <div class="flex items-start gap-3">
                                                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-[10px] font-black uppercase text-white shadow-lg shadow-slate-200">
                                                                    {{ $extension ?: 'DOC' }}
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p class="line-clamp-2 break-words text-sm font-bold leading-5 text-slate-900">{{ $document->original_name ?? basename($document->file_path) }}</p>
                                                                    <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-slate-500">
                                                                        <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 font-semibold text-blue-700">Uploaded</span>
                                                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">
                                                                            {{ optional($document->updated_at)->khFormat('d/m/Y H:i') }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-4 grid grid-cols-2 gap-2">
                                                            <button type="button"
                                                                class="inline-flex h-9 items-center justify-center rounded-[0.85rem] border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                                                                data-document-preview-trigger
                                                                data-preview-url="{{ route('admin.documents.show', [$application, $document]) }}"
                                                                data-download-url="{{ route('admin.documents.download', [$application, $document]) }}"
                                                                data-document-name="{{ $document->original_name ?? basename($document->file_path) }}"
                                                                data-preview-supported="{{ $canPreviewInline ? 'true' : 'false' }}"
                                                                data-preview-kind="{{ $previewKind }}">
                                                                មើល
                                                            </button>
                                                            <a href="{{ route('admin.documents.download', [$application, $document]) }}"
                                                                class="inline-flex h-9 items-center justify-center rounded-[0.85rem] border border-sky-200 bg-sky-50 px-3 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                                                                ទាញយក
                                                            </a>
                                                            <form method="POST" action="{{ route('admin.documents.destroy', [$application, $document]) }}" data-swal-confirm data-swal-title="បញ្ជាក់ការលុបឯកសារ" data-swal-text="តើអ្នកពិតជាចង់លុបឯកសារនេះមែនទេ?" data-swal-confirm-text="បាទ/ចាស លុប" data-swal-cancel-text="បោះបង់" class="contents">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="inline-flex h-9 w-full items-center justify-center rounded-[0.85rem] border border-rose-200 bg-rose-50 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                                    លុប
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('admin.documents.update', [$application, $document]) }}" enctype="multipart/form-data"
                                                                data-ajax-form
                                                                data-ajax-redirect="{{ route('admin.applications.edit', $application) }}"
                                                                data-ajax-success-title="ជោគជ័យ"
                                                                data-ajax-success-text="បានជំនួសឯកសារដោយជោគជ័យ។"
                                                                class="contents">
                                                                @csrf
                                                                @method('PUT')
                                                                <label class="inline-flex h-9 w-full cursor-pointer items-center justify-center rounded-[0.85rem] border border-amber-200 bg-amber-50 px-3 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                                                    ជំនួស
                                                                    <input type="file" name="document_file" required class="hidden" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" data-application-document-autosubmit data-confirm-title="បញ្ជាក់ការជំនួសឯកសារ" data-confirm-text="តើអ្នកពិតជាចង់ជំនួសឯកសារនេះដោយឯកសារថ្មីមែនទេ?">
                                                                </label>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="rounded-[1rem] border border-dashed border-slate-300 bg-white px-3 py-4 text-center text-xs text-slate-500">
                                                        មិនទាន់មានឯកសារបញ្ចូលទេ
                                                    </div>
                                                <?php endif; ?>

                                                <div class="min-h-[10.5rem] rounded-[1.25rem] border-2 border-dashed border-blue-200 bg-blue-50/40 p-4 transition hover:border-blue-400 hover:bg-blue-50">
                                                    <form action="{{ route('admin.documents.store', $application) }}" method="POST" enctype="multipart/form-data"
                                                        data-ajax-form
                                                        data-ajax-redirect="{{ route('admin.applications.edit', $application) }}"
                                                        data-ajax-success-title="ជោគជ័យ"
                                                        data-ajax-success-text="បានបន្ថែមឯកសារជោគជ័យ។"
                                                        class="contents">
                                                        @csrf
                                                        <input type="hidden" name="document_requirement_id" value="{{ $requirement->id }}">
                                                        <label class="flex h-full min-h-[8.25rem] cursor-pointer flex-col items-center justify-center rounded-[1rem] bg-white/70 px-4 py-5 text-center transition hover:bg-white">
                                                            <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" class="hidden" data-application-document-autosubmit data-confirm-title="បញ្ជាក់ការបន្ថែមឯកសារ" data-confirm-text="តើអ្នកពិតជាចង់បន្ថែមឯកសារនេះមែនទេ?">
                                                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-100">
                                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M12 5v14M5 12h14"/></svg>
                                                            </span>
                                                            <span class="mt-3 text-sm font-bold text-slate-900">បញ្ចូលឯកសារ</span>
                                                            <span class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400">PDF, DOC, JPG, PNG</span>
                                                        </label>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/55 px-4 py-6 backdrop-blur-sm" data-document-preview-modal aria-hidden="true">
        <div class="absolute inset-0" data-document-preview-close></div>
        <div class="relative z-10 flex h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">មើលឯកសារ</p>
                    <p class="mt-1 truncate text-sm font-semibold text-slate-900 sm:text-base" data-document-preview-name>-</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="#" target="_blank" rel="noreferrer" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" data-document-preview-open>បើក</a>
                    <a href="#" class="inline-flex items-center rounded-full bg-[#356AE6] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#204ec7]" data-document-preview-download>ទាញយក</a>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700" data-document-preview-close aria-label="បិទការមើល">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex min-h-0 flex-1 flex-col bg-slate-50 p-4 sm:p-5">
                <p class="hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" data-document-preview-note>
                    ប្រភេទឯកសារនេះអាចមិនអាចមើលក្នុងផ្ទាំងលេចឡើងបានទេ។ សូមប្រើ បើក ឬ ទាញយក ប្រសិនបើផ្ទៃមើលនៅទទេ។
                </p>
                <div class="mt-4 hidden min-h-0 flex-1 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white" data-document-preview-image-wrapper>
                    <img src="" alt="ការមើលឯកសារ" class="max-h-full max-w-full object-contain" data-document-preview-image>
                </div>
                <iframe src="about:blank" class="mt-4 hidden min-h-0 w-full flex-1 rounded-2xl border border-slate-200 bg-white" data-document-preview-frame title="ការមើលឯកសារ"></iframe>
            </div>
        </div>
    </div>

    <script>
        (() => {
            document.querySelectorAll('form[data-ajax-form]').forEach((form) => {
                form.dataset.disableActionFlow = 'true';
            });

            document.querySelectorAll('[data-application-document-autosubmit]').forEach((input) => {
                input.addEventListener('change', async () => {
                    if (!input.files || input.files.length === 0) {
                        return;
                    }

                    const form = input.closest('form');
                    if (!form) {
                        return;
                    }

                    const title = input.dataset.confirmTitle || 'បញ្ជាក់ការបញ្ចូលឯកសារ';
                    const text = input.dataset.confirmText || 'តើអ្នកពិតជាចង់បន្តមែនទេ?';

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
                    });

                    if (result.isConfirmed) {
                        form.requestSubmit();
                        return;
                    }

                    input.value = '';
                });
            });

            const modal = document.querySelector('[data-document-preview-modal]');
            if (!modal) {
                return;
            }

            const frame = modal.querySelector('[data-document-preview-frame]');
            const imageWrapper = modal.querySelector('[data-document-preview-image-wrapper]');
            const image = modal.querySelector('[data-document-preview-image]');
            const name = modal.querySelector('[data-document-preview-name]');
            const openLink = modal.querySelector('[data-document-preview-open]');
            const downloadLink = modal.querySelector('[data-document-preview-download]');
            const note = modal.querySelector('[data-document-preview-note]');

            const setPreviewMode = (previewKind, previewUrl) => {
                const isImage = previewKind === 'image';
                imageWrapper.classList.toggle('hidden', !isImage);
                imageWrapper.classList.toggle('flex', isImage);
                frame.classList.toggle('hidden', isImage);
                frame.classList.toggle('block', !isImage);
                image.src = isImage ? (previewUrl || '') : '';
                frame.src = isImage ? 'about:blank' : (previewUrl || 'about:blank');
            };

            const closeModal = () => {
                setPreviewMode('other', 'about:blank');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
            };

            document.querySelectorAll('[data-document-preview-trigger]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    name.textContent = trigger.dataset.documentName || 'មើលឯកសារ';
                    openLink.href = trigger.dataset.previewUrl || '#';
                    downloadLink.href = trigger.dataset.downloadUrl || '#';
                    note.classList.toggle('hidden', trigger.dataset.previewSupported === 'true');
                    setPreviewMode(trigger.dataset.previewKind || 'other', trigger.dataset.previewUrl);
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('overflow-hidden');
                });
            });

            modal.querySelectorAll('[data-document-preview-close]').forEach((element) => {
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
