@extends('app')

@section('body')
    @php
        $bannerKhmerTitle = $portalContent?->test_taking_staff_page_title ?: 'សាលាហ្វឹកហ្វឺនយោធា';
        $bannerKhmerSubtitle = $portalContent?->test_taking_staff_page_subtitle ?: 'ទម្រង់ចុះឈ្មោះបុគ្គលិកសាកល្បង';
        $bannerKhmerDescription = $portalContent?->test_taking_staff_page_description ?: 'សូមបំពេញព័ត៌មានរបស់បុគ្គលិកសាកល្បងឲ្យបានត្រឹមត្រូវ ដើម្បីឲ្យក្រុមការងារត្រួតពិនិត្យបានងាយស្រួល។';
        $currentRankId = old('test_taking_staff_rank_id');
        $currentRankName = old('test_taking_staff_rank_name');
        $usesCustomRank = old('test_taking_staff_rank_id') === '__custom__' || trim((string) old('test_taking_staff_rank_name', '')) !== '';
    @endphp

    @if (session('status'))
        <div class="public-success-page">
            <div class="public-success-shell">
                <div class="success-flash-icon public-success-icon" aria-hidden="true">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M20 6 9 17l-5-5"></path>
                    </svg>
                </div>
                <p class="public-success-eyebrow">{{ session('status_title', 'ការចុះឈ្មោះជោគជ័យ') }}</p>
                <h1 class="public-success-title">ការបញ្ជូនព័ត៌មានបានរួចរាល់</h1>
                <p class="public-success-message">{{ session('status') }}</p>
            </div>
        </div>
    @else
        <div class="public-page">
            @include('public.partials.submit-loading-overlay')

            @if ($portalContent?->test_taking_staff_page_banner_image_path)
                <section class="public-banner-card mb-4 sm:mb-6">
                    <img src="{{ route('portal.test-taking-staff-banner-image') }}" alt="Test-taking staff banner" class="h-auto w-full object-contain">
                </section>
            @else
                <section class="public-banner-card relative mb-4 sm:mb-6">
                    <div class="absolute left-0 top-0 h-14 w-14 rounded-br-full bg-amber-400/90 sm:h-20 sm:w-20"></div>
                    <div class="absolute bottom-0 right-0 h-16 w-16 rounded-tl-full bg-amber-400/90 sm:h-24 sm:w-24"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(15,23,42,0.05),transparent_46%),linear-gradient(180deg,rgba(255,255,255,0.98),rgba(248,250,252,0.96))]"></div>
                    <div class="relative grid gap-3 px-4 py-4 sm:px-7 sm:py-5 lg:grid-cols-[130px_1fr] lg:items-center">
                        <div class="flex justify-center lg:justify-start">
                            <div class="flex h-[82px] w-[82px] items-center justify-center rounded-full border-4 border-yellow-300 bg-[radial-gradient(circle_at_30%_30%,#2563eb,#1d4ed8_38%,#dc2626_39%,#dc2626_70%,#facc15_71%,#facc15_100%)] shadow-[0_12px_28px_rgba(15,23,42,0.18)] sm:h-[110px] sm:w-[110px] sm:border-[5px]">
                                <div class="flex h-[60px] w-[60px] items-center justify-center rounded-full border-[3px] border-sky-200/90 bg-white/10 text-center text-[10px] font-bold uppercase tracking-[0.14em] text-white backdrop-blur sm:h-[82px] sm:w-[82px] sm:border-4 sm:text-[11px] sm:tracking-[0.18em]">
                                    RCAF
                                </div>
                            </div>
                        </div>
                        <div class="text-center lg:text-left">
                            <p class="text-lg font-semibold leading-snug text-slate-900 sm:text-[34px]">{{ $bannerKhmerTitle }}</p>
                            <p class="mt-1 text-base font-semibold leading-snug text-slate-800 sm:mt-2 sm:text-[30px]">{{ $bannerKhmerSubtitle }}</p>
                            <p class="mt-2 text-xs text-slate-500 sm:text-sm">{{ $bannerKhmerDescription }}</p>
                        </div>
                    </div>
                </section>
            @endif

            <section class="public-form-shell">
                @if ($ranks->isEmpty())
                    <div class="public-inline-alert">
                        មិនទាន់មានទិន្នន័យឋានន្តរសក្តិសម្រាប់ជ្រើសរើសទេ។ សូមទាក់ទងអ្នកគ្រប់គ្រងប្រព័ន្ធ។
                    </div>
                @endif

                <form method="POST" action="{{ route('test-taking-staff.store') }}" enctype="multipart/form-data" class="space-y-5 sm:space-y-6" data-registration-form data-max-upload-total="52428800" data-max-upload-total-mobile="52428800" data-max-upload-per-file="15728640" data-submit-loading-text="សូមចាំបន្តិច....">
                    @csrf

                    <div class="public-section-card space-y-4">
                        <div class="public-section-heading">
                            <h2 class="text-xl font-semibold text-slate-950">ព័ត៌មានបុគ្គលិកសាកល្បង</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">បំពេញឈ្មោះ ឋានន្តរសក្តិ និងព័ត៌មានទំនាក់ទំនងរបស់អ្នកឲ្យបានត្រឹមត្រូវ។</p>
                        </div>

                        <div class="grid gap-4 sm:gap-5 md:grid-cols-2">
                            <div>
                                <label class="form-label">* ឈ្មោះជាភាសាខ្មែរ</label>
                                <input type="text" name="name_kh" value="{{ old('name_kh') }}" placeholder="សូមបញ្ចូលឈ្មោះជាភាសាខ្មែរ" class="form-input">
                                @include('partials.field-error', ['name' => 'name_kh'])
                            </div>

                            <div>
                                <label class="form-label">* ឈ្មោះជាអក្សរឡាតាំង</label>
                                <input type="text" name="name_latin" value="{{ old('name_latin') }}" placeholder="សូមបញ្ចូលឈ្មោះជាអក្សរឡាតាំង" class="form-input">
                                @include('partials.field-error', ['name' => 'name_latin'])
                            </div>

                            <div>
                                <label class="form-label">* អត្តលេខ</label>
                                <input type="text" name="id_number" value="{{ old('id_number') }}" placeholder="សូមបញ្ចូលអត្តលេខ" class="form-input">
                                @include('partials.field-error', ['name' => 'id_number'])
                            </div>

                            <div>
                                <label class="form-label">* ឋានន្តរសក្តិ</label>
                                <div class="relative">
                                    <select
                                        id="test_taking_staff_rank_select"
                                        name="{{ $usesCustomRank ? '' : 'test_taking_staff_rank_id' }}"
                                        class="form-input h-12 w-full min-w-0 bg-white {{ $usesCustomRank ? 'hidden' : '' }}"
                                        onchange="if(this.value === '__custom__') { this.classList.add('hidden'); this.name = ''; document.getElementById('test_taking_staff_rank_input').classList.remove('hidden'); document.getElementById('test_taking_staff_rank_input').name = 'test_taking_staff_rank_name'; document.getElementById('test_taking_staff_rank_input').focus(); document.getElementById('test_taking_staff_rank_cancel_btn').classList.remove('hidden'); }"
                                    >
                                        <option value="">សូមជ្រើសរើសឋានន្តរសក្តិ</option>
                                        @foreach ($ranks as $rank)
                                            <option value="{{ $rank->id }}" @selected(! $usesCustomRank && (string) $currentRankId === (string) $rank->id)>{{ $rank->name_kh }}</option>
                                        @endforeach
                                        <option value="__custom__" class="font-semibold text-[#356AE6]" @selected($usesCustomRank)>+បញ្ចូលថ្មី...</option>
                                    </select>
                                    <input
                                        type="text"
                                        id="test_taking_staff_rank_input"
                                        name="{{ $usesCustomRank ? 'test_taking_staff_rank_name' : '' }}"
                                        value="{{ $usesCustomRank ? $currentRankName : '' }}"
                                        class="form-input h-12 w-full min-w-0 bg-white pr-10 {{ $usesCustomRank ? '' : 'hidden' }}"
                                        placeholder="បញ្ចូលឋានន្តរសក្តិថ្មី..."
                                    >
                                    <button
                                        type="button"
                                        id="test_taking_staff_rank_cancel_btn"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600 {{ $usesCustomRank ? '' : 'hidden' }}"
                                        onclick="document.getElementById('test_taking_staff_rank_input').classList.add('hidden'); document.getElementById('test_taking_staff_rank_input').name = ''; document.getElementById('test_taking_staff_rank_input').value = ''; document.getElementById('test_taking_staff_rank_select').classList.remove('hidden'); document.getElementById('test_taking_staff_rank_select').name = 'test_taking_staff_rank_id'; document.getElementById('test_taking_staff_rank_select').value = ''; this.classList.add('hidden');"
                                        title="បោះបង់ការបញ្ចូលថ្មី និងជ្រើសរើសពីបញ្ជី"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <p class="mt-2 text-sm text-slate-500">ប្រសិនបើមិនមានក្នុងបញ្ជី អ្នកអាចបញ្ចូលឋានន្តរសក្តិថ្មីបាន។</p>
                                @include('partials.field-error', ['name' => 'test_taking_staff_rank_id'])
                                @include('partials.field-error', ['name' => 'test_taking_staff_rank_name'])
                            </div>

                            <div>
                                <label class="form-label">* លេខទូរស័ព្ទ</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="សូមបញ្ចូលលេខទូរស័ព្ទ" class="form-input">
                                @include('partials.field-error', ['name' => 'phone_number'])
                            </div>

                            <div>
                                <label class="form-label">* ថ្ងៃ ខែ ឆ្នាំកំណើត</label>
                                <div class="date-picker" data-date-picker data-placeholder="សូមជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត">
                                    <input type="hidden" name="date_of_birth" value="{{ old('date_of_birth') }}" data-date-value data-max="{{ now()->subDay()->toDateString() }}">
                                    <button type="button" class="date-picker-trigger" data-date-toggle aria-expanded="false">
                                        <span class="date-picker-text" data-date-display>{{ old('date_of_birth') ? \Illuminate\Support\Carbon::parse(old('date_of_birth'))->locale('km')->translatedFormat('d M Y') : 'សូមជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត' }}</span>
                                        <span class="date-picker-icon">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                                <path d="M16 2v4M8 2v4M3 10h18"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    <div class="date-picker-panel hidden" data-date-panel>
                                        <div class="date-picker-header">
                                            <button type="button" class="date-picker-nav" data-date-prev aria-label="Previous month">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="m15 18-6-6 6-6"></path>
                                                </svg>
                                            </button>
                                            <div class="date-picker-month" data-date-month-label></div>
                                            <button type="button" class="date-picker-nav" data-date-next aria-label="Next month">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="m9 18 6-6-6-6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="date-picker-stage" data-date-stage>ជ្រើសរើសឆ្នាំ</div>
                                        <div class="date-picker-steps" data-date-steps>
                                            <span class="date-picker-step" data-step="year">១. ឆ្នាំ</span>
                                            <span class="date-picker-step" data-step="month">២. ខែ</span>
                                            <span class="date-picker-step" data-step="day">៣. ថ្ងៃ</span>
                                        </div>
                                        <div class="date-picker-weekdays">
                                            <span>អា</span>
                                            <span>ច</span>
                                            <span>អ</span>
                                            <span>ពុ</span>
                                            <span>ព្រ</span>
                                            <span>សុ</span>
                                            <span>ស</span>
                                        </div>
                                        <div class="date-picker-grid" data-date-grid></div>
                                        <div class="date-picker-actions">
                                            <button type="button" class="date-picker-link" data-date-clear>សម្អាត</button>
                                            <button type="button" class="date-picker-link" data-date-today>ថ្ងៃនេះ</button>
                                        </div>
                                    </div>
                                </div>
                                @include('partials.field-error', ['name' => 'date_of_birth'])
                            </div>

                            <div>
                                <label class="form-label">* ថ្ងៃចូលបម្រើកងទ័ព</label>
                                <div class="date-picker" data-date-picker data-placeholder="សូមជ្រើសរើសថ្ងៃចូលបម្រើកងទ័ព">
                                    <input type="hidden" name="military_service_day" value="{{ old('military_service_day') }}" data-date-value data-max="{{ now()->toDateString() }}">
                                    <button type="button" class="date-picker-trigger" data-date-toggle aria-expanded="false">
                                        <span class="date-picker-text" data-date-display>{{ old('military_service_day') ? \Illuminate\Support\Carbon::parse(old('military_service_day'))->locale('km')->translatedFormat('d M Y') : 'សូមជ្រើសរើសថ្ងៃចូលបម្រើកងទ័ព' }}</span>
                                        <span class="date-picker-icon">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                                <path d="M16 2v4M8 2v4M3 10h18"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    <div class="date-picker-panel hidden" data-date-panel>
                                        <div class="date-picker-header">
                                            <button type="button" class="date-picker-nav" data-date-prev aria-label="Previous month">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="m15 18-6-6 6-6"></path>
                                                </svg>
                                            </button>
                                            <div class="date-picker-month" data-date-month-label></div>
                                            <button type="button" class="date-picker-nav" data-date-next aria-label="Next month">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="m9 18 6-6-6-6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="date-picker-stage" data-date-stage>ជ្រើសរើសឆ្នាំ</div>
                                        <div class="date-picker-steps" data-date-steps>
                                            <span class="date-picker-step" data-step="year">១. ឆ្នាំ</span>
                                            <span class="date-picker-step" data-step="month">២. ខែ</span>
                                            <span class="date-picker-step" data-step="day">៣. ថ្ងៃ</span>
                                        </div>
                                        <div class="date-picker-weekdays">
                                            <span>អា</span>
                                            <span>ច</span>
                                            <span>អ</span>
                                            <span>ពុ</span>
                                            <span>ព្រ</span>
                                            <span>សុ</span>
                                            <span>ស</span>
                                        </div>
                                        <div class="date-picker-grid" data-date-grid></div>
                                        <div class="date-picker-actions">
                                            <button type="button" class="date-picker-link" data-date-clear>សម្អាត</button>
                                            <button type="button" class="date-picker-link" data-date-today>ថ្ងៃនេះ</button>
                                        </div>
                                    </div>
                                </div>
                                @include('partials.field-error', ['name' => 'military_service_day'])
                            </div>
                        </div>
                    </div>

                    <div class="public-section-card space-y-4">
                        <div class="public-section-heading">
                            <h2 class="text-xl font-semibold text-slate-950">រូបថត និងឯកសារភ្ជាប់</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">សូមភ្ជាប់រូបថត និងឯកសារដែលត្រូវការតាមបញ្ជីខាងក្រោម។ ទំហំសរុបមិនគួរលើស 50 MB ហើយឯកសារនីមួយៗមិនលើស 15 MB។</p>
                        </div>

                        <div class="{{ $errors->has('upload_total') || $errors->has('submission') ? '' : 'hidden' }} rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700" data-upload-total-alert>{{ $errors->first('upload_total') ?: $errors->first('submission') }}</div>

                        <div>
                            <label class="form-label">* រូបថតយោធា</label>
                            <input type="file" name="avatar_image" class="public-file-input block w-full" accept=".jpg,.jpeg,.png,.webp">
                            <p class="mt-2 text-sm text-slate-500">ប្រភេទឯកសារ JPG, PNG, WEBP និងទំហំមិនលើស 5 MB។</p>
                            @include('partials.field-error', ['name' => 'avatar_image'])
                        </div>

                        @if ($documentRequirements->isNotEmpty())
                            <div class="grid gap-3 sm:gap-4">
                                @foreach ($documentRequirements as $documentRequirement)
                                    @php
                                        $selectedStatus = old("document_statuses.{$documentRequirement->id}", 'dont_have');
                                    @endphp
                                    <div class="public-upload-card" data-document-card>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="form-label !mb-2">{{ $documentRequirement->name_kh }}</label>
                                                <p class="text-sm text-slate-500">{{ $documentRequirement->name_en }}</p>
                                            </div>

                                            <div class="space-y-4">
                                                <div>
                                                    <label class="form-label">* ស្ថានភាពឯកសារ</label>
                                                    <select name="document_statuses[{{ $documentRequirement->id }}]" class="form-input" data-document-status>
                                                        <option value="have" @selected($selectedStatus === 'have')>មាន</option>
                                                        <option value="dont_have" @selected($selectedStatus === 'dont_have')>មិនមាន</option>
                                                    </select>
                                                    @include('partials.field-error', ['name' => "document_statuses.{$documentRequirement->id}"])
                                                </div>

                                                <div class="{{ $selectedStatus === 'have' ? '' : 'hidden' }}" data-document-file-wrapper>
                                                    <label class="form-label">Upload File(s)</label>
                                                    <div class="space-y-3" data-document-file-list>
                                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start" data-document-file-row>
                                                            <input type="file" name="document_files[{{ $documentRequirement->id }}][]" class="public-file-input block w-full min-w-0 flex-1" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.webp" multiple data-document-file-input>
                                                            <button type="button" class="hidden rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" data-document-remove-file>Remove</button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="mt-3 inline-flex items-center rounded-xl border border-[#356AE6]/30 bg-[#356AE6]/5 px-4 py-2 text-sm font-semibold text-[#356AE6] transition hover:bg-[#356AE6]/10" data-document-add-file>
                                                        + Add another file
                                                    </button>
                                                    <p class="mt-2 text-sm text-slate-500">អ្នកអាចផ្ទុកឯកសារច្រើនសន្លឹកបាន។ ទំហំមិនត្រូវលើសពី 15 MB ក្នុងមួយហ្វាល់។</p>
                                                    @include('partials.field-error', ['name' => "document_files.{$documentRequirement->id}"])
                                                    @include('partials.field-error', ['name' => "document_files.{$documentRequirement->id}.*"])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="public-inline-alert">
                                មិនមានឯកសារដែលត្រូវភ្ជាប់បន្ថែមពីផ្នែករដ្ឋបាលនៅពេលនេះទេ។
                            </div>
                        @endif
                    </div>

                    <div class="public-actions">
                        <button type="submit" class="public-primary-button" data-submit-button @disabled($ranks->isEmpty())>
                            បញ្ជូនព័ត៌មាន
                        </button>
                        <button type="reset" class="public-secondary-button">
                            សម្អាតទម្រង់
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <script>
            const registrationForm = document.querySelector('[data-registration-form]');
            const uploadAlert = document.querySelector('[data-upload-total-alert]');

            if (registrationForm && uploadAlert) {
                const maxUploadTotal = Number(registrationForm.dataset.maxUploadTotal || 0);
                const maxUploadTotalMobile = Number(registrationForm.dataset.maxUploadTotalMobile || 0);
                const maxUploadPerFile = Number(registrationForm.dataset.maxUploadPerFile || 0);
                const mobileMediaQuery = window.matchMedia ? window.matchMedia('(max-width: 767.98px)') : null;
                const resolveMaxUploadTotal = () => {
                    const isMobileViewport = mobileMediaQuery ? mobileMediaQuery.matches : window.innerWidth < 768;
                    return isMobileViewport && maxUploadTotalMobile > 0 ? maxUploadTotalMobile : maxUploadTotal;
                };

                const formatMegabytes = (bytes) => `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
                const getSelectedFiles = () => Array.from(
                    registrationForm.querySelectorAll('input[type="file"]'),
                ).flatMap((input) => Array.from(input.files || []));

                const syncUploadAlert = () => {
                    const selectedFiles = getSelectedFiles();
                    const totalBytes = selectedFiles.reduce((sum, file) => sum + file.size, 0);
                    const largestFile = selectedFiles.reduce(
                        (largest, file) => (!largest || file.size > largest.size ? file : largest),
                        null,
                    );
                    const resolvedMaxUploadTotal = resolveMaxUploadTotal();
                    const isSingleFileTooLarge = !!largestFile && maxUploadPerFile > 0 && largestFile.size > maxUploadPerFile;
                    const isTotalTooLarge = resolvedMaxUploadTotal > 0 && totalBytes > resolvedMaxUploadTotal;
                    const hasUploadError = isSingleFileTooLarge || isTotalTooLarge;

                    uploadAlert.classList.toggle('hidden', !hasUploadError);

                    if (isSingleFileTooLarge) {
                        uploadAlert.textContent = `Each file must be ${formatMegabytes(maxUploadPerFile)} or smaller. The file "${largestFile.name}" is ${formatMegabytes(largestFile.size)}.`;
                    } else if (isTotalTooLarge) {
                        uploadAlert.textContent = `Total upload is too large. Keep it below ${formatMegabytes(resolvedMaxUploadTotal)}. (Current total: ${formatMegabytes(totalBytes)})`;
                    } else {
                        uploadAlert.textContent = '';
                    }

                    return hasUploadError;
                };

                registrationForm.addEventListener('change', (event) => {
                    const target = event.target;

                    if (target instanceof HTMLInputElement && target.type === 'file') {
                        syncUploadAlert();
                    }
                });

                registrationForm.addEventListener('registration-files-changed', syncUploadAlert);

                registrationForm.addEventListener('submit', (event) => {
                    if (syncUploadAlert()) {
                        event.preventDefault();
                        uploadAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });

                registrationForm.addEventListener('reset', () => {
                    window.setTimeout(syncUploadAlert, 0);
                });

                mobileMediaQuery?.addEventListener?.('change', syncUploadAlert);
            }
        </script>

        <script>
            document.querySelectorAll('[data-document-card]').forEach((card) => {
                const select = card.querySelector('[data-document-status]');
                const fileWrapper = card.querySelector('[data-document-file-wrapper]');
                const fileList = card.querySelector('[data-document-file-list]');
                const addFileButton = card.querySelector('[data-document-add-file]');

                if (!select || !fileWrapper) {
                    return;
                }

                const notifyFilesChanged = () => {
                    registrationForm?.dispatchEvent(new Event('registration-files-changed'));
                };

                const updateRemoveButtons = () => {
                    if (!fileList) {
                        return;
                    }

                    const rows = Array.from(fileList.querySelectorAll('[data-document-file-row]'));

                    rows.forEach((row) => {
                        row.querySelector('[data-document-remove-file]')?.classList.toggle('hidden', rows.length < 2);
                    });
                };

                const syncDocumentField = () => {
                    const hasDocument = select.value === 'have';
                    fileWrapper.classList.toggle('hidden', !hasDocument);

                    if (!hasDocument) {
                        fileWrapper.querySelectorAll('input[type="file"]').forEach((input) => {
                            input.value = '';
                        });

                        fileList?.querySelectorAll('[data-document-file-row]').forEach((row, index) => {
                            if (index > 0) {
                                row.remove();
                            }
                        });

                        updateRemoveButtons();
                        notifyFilesChanged();
                    }
                };

                syncDocumentField();
                select.addEventListener('change', syncDocumentField);

                addFileButton?.addEventListener('click', () => {
                    if (!fileList) {
                        return;
                    }

                    const sourceInput = fileList.querySelector('[data-document-file-input]');

                    if (!sourceInput) {
                        return;
                    }

                    const row = document.createElement('div');
                    row.className = 'flex flex-col gap-2 sm:flex-row sm:items-start';
                    row.setAttribute('data-document-file-row', '');

                    const input = sourceInput.cloneNode(false);
                    input.value = '';

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50';
                    removeButton.setAttribute('data-document-remove-file', '');
                    removeButton.textContent = 'Remove';

                    row.append(input, removeButton);
                    fileList.append(row);
                    updateRemoveButtons();
                    input.click();
                });

                fileList?.addEventListener('click', (event) => {
                    if (!(event.target instanceof Element)) {
                        return;
                    }

                    const removeButton = event.target.closest('[data-document-remove-file]');

                    if (!removeButton) {
                        return;
                    }

                    const rows = Array.from(fileList.querySelectorAll('[data-document-file-row]'));

                    if (rows.length < 2) {
                        return;
                    }

                    removeButton.closest('[data-document-file-row]')?.remove();
                    updateRemoveButtons();
                    notifyFilesChanged();
                });

                registrationForm?.addEventListener('reset', () => {
                    window.setTimeout(() => {
                        fileList?.querySelectorAll('[data-document-file-row]').forEach((row, index) => {
                            if (index > 0) {
                                row.remove();
                            }
                        });

                        updateRemoveButtons();
                    }, 0);
                });

                updateRemoveButtons();
            });
        </script>

        <script>
            (() => {
                const registrationForm = document.querySelector('[data-registration-form]');
                const uploadAlert = document.querySelector('[data-upload-total-alert]');
                const registrationPage = document.querySelector('.public-page');

                if (!registrationForm || !uploadAlert || !window.XMLHttpRequest) {
                    return;
                }

                const submitButton = registrationForm.querySelector('[data-submit-button]');
                const submitButtonText = submitButton?.textContent?.trim() || 'Submit';
                const submitLoadingText = registrationForm.dataset.submitLoadingText || 'Submitting...';
                const loadingOverlay = document.querySelector('[data-submit-loading-overlay]');
                const loadingOverlayMessage = loadingOverlay?.querySelector('[data-submit-loading-message]');
                const loadingProgress = loadingOverlay?.querySelector('[data-submit-upload-progress]');
                const loadingProgressBar = loadingOverlay?.querySelector('[data-submit-upload-progress-bar]');
                const loadingProgressText = loadingOverlay?.querySelector('[data-submit-upload-progress-text]');
                const loadingFileStatus = loadingOverlay?.querySelector('[data-submit-upload-file-status]');
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
                const fieldErrorElements = Array.from(registrationForm.querySelectorAll('[data-field-error]'));

                const formatMegabytes = (bytes) => `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
                const getSelectedUploadBytes = () => Array.from(
                    registrationForm.querySelectorAll('input[type="file"]'),
                ).reduce((total, input) => total + Array.from(input.files || []).reduce((sum, file) => sum + file.size, 0), 0);
                const truncateFileName = (fileName, maxLength = 52) => {
                    if (typeof fileName !== 'string') {
                        return '';
                    }

                    if (fileName.length <= maxLength) {
                        return fileName;
                    }

                    return `${fileName.slice(0, maxLength - 3)}...`;
                };
                const setUploadFileStatus = (text = '') => {
                    if (!loadingFileStatus) {
                        return;
                    }

                    loadingFileStatus.textContent = text;
                    loadingFileStatus.classList.toggle('hidden', !text);
                };
                const uploadTrackingState = {
                    files: [],
                    cumulativeFileBytes: [],
                    totalFileBytes: 0,
                };
                const resetUploadTrackingState = () => {
                    uploadTrackingState.files = [];
                    uploadTrackingState.cumulativeFileBytes = [];
                    uploadTrackingState.totalFileBytes = 0;
                    setUploadFileStatus('');
                };
                const buildUploadTrackingState = (formData) => {
                    const files = [];

                    for (const [, value] of formData.entries()) {
                        if (value instanceof File && value.size > 0) {
                            files.push({
                                name: value.name || 'file',
                                size: value.size,
                            });
                        }
                    }

                    let cumulativeSize = 0;
                    const cumulativeFileBytes = files.map((file) => {
                        cumulativeSize += file.size;
                        return cumulativeSize;
                    });

                    uploadTrackingState.files = files;
                    uploadTrackingState.cumulativeFileBytes = cumulativeFileBytes;
                    uploadTrackingState.totalFileBytes = cumulativeSize;
                };
                const resolveCurrentUploadingFile = (loadedBytes, totalBytes) => {
                    if (uploadTrackingState.files.length === 0 || uploadTrackingState.totalFileBytes <= 0) {
                        return null;
                    }

                    let normalizedLoadedBytes = Number(loadedBytes) || 0;
                    const safeTotalBytes = Number(totalBytes) || 0;

                    if (safeTotalBytes > 0) {
                        const progressRatio = Math.min(1, Math.max(0, normalizedLoadedBytes / safeTotalBytes));
                        normalizedLoadedBytes = progressRatio * uploadTrackingState.totalFileBytes;
                    }

                    const clampedLoadedBytes = Math.min(
                        uploadTrackingState.totalFileBytes,
                        Math.max(0, normalizedLoadedBytes),
                    );

                    let fileIndex = uploadTrackingState.cumulativeFileBytes.findIndex(
                        (cumulativeBytes) => clampedLoadedBytes <= cumulativeBytes,
                    );

                    if (fileIndex < 0) {
                        fileIndex = uploadTrackingState.files.length - 1;
                    }

                    return {
                        index: fileIndex,
                        total: uploadTrackingState.files.length,
                        name: uploadTrackingState.files[fileIndex]?.name || 'file',
                    };
                };
                const canCompressClientImage = !!(window.File && window.FileReader && window.HTMLCanvasElement && window.URL);

                const isCompressibleImage = (file) => file instanceof File
                    && file.size > 1024 * 1024
                    && /^image\/(jpe?g|png|webp)$/i.test(file.type);

                const loadImageFromFile = (file) => new Promise((resolve, reject) => {
                    const objectUrl = URL.createObjectURL(file);
                    const image = new Image();
                    image.onload = () => {
                        URL.revokeObjectURL(objectUrl);
                        resolve(image);
                    };
                    image.onerror = () => {
                        URL.revokeObjectURL(objectUrl);
                        reject(new Error('Unable to load image.'));
                    };
                    image.src = objectUrl;
                });

                const compressImageFile = async (file, options = {}) => {
                    if (!canCompressClientImage || !isCompressibleImage(file)) {
                        return null;
                    }

                    const maxDimension = Number(options.maxDimension || 1800);
                    const quality = Number(options.quality || 0.82);
                    const image = await loadImageFromFile(file);
                    const longestSide = Math.max(image.width || 0, image.height || 0) || 1;
                    const scale = Math.min(1, maxDimension / longestSide);
                    const targetWidth = Math.max(1, Math.round((image.width || 1) * scale));
                    const targetHeight = Math.max(1, Math.round((image.height || 1) * scale));
                    const canvas = document.createElement('canvas');

                    canvas.width = targetWidth;
                    canvas.height = targetHeight;

                    const context = canvas.getContext('2d');
                    if (!context) {
                        return null;
                    }

                    context.drawImage(image, 0, 0, targetWidth, targetHeight);

                    const blob = await new Promise((resolve) => {
                        canvas.toBlob(resolve, 'image/webp', quality);
                    });

                    if (!(blob instanceof Blob) || blob.size >= file.size * 0.95) {
                        return null;
                    }

                    const baseName = file.name.replace(/\.[^/.]+$/, '');
                    return new File([blob], `${baseName}.webp`, {
                        type: 'image/webp',
                        lastModified: file.lastModified,
                    });
                };

                const buildSubmissionFormData = async () => {
                    const rawFormData = new FormData(registrationForm);
                    const optimizedFormData = new FormData();
                    const compressibleImageCount = Array.from(rawFormData.values()).filter(
                        (entry) => isCompressibleImage(entry),
                    ).length;
                    let processedCompressibleImages = 0;

                    for (const [name, value] of rawFormData.entries()) {
                        if (!isCompressibleImage(value)) {
                            optimizedFormData.append(name, value);
                            continue;
                        }

                        if (loadingOverlayMessage) {
                            loadingOverlayMessage.textContent = 'Preparing files...';
                        }

                        setUploadFileStatus(
                            'Optimizing image '
                            + (processedCompressibleImages + 1)
                            + '/'
                            + compressibleImageCount
                            + ': '
                            + truncateFileName(value.name || 'image'),
                        );

                        const compressedImage = await compressImageFile(value, {
                            maxDimension: name === 'avatar_image' ? 1440 : 1800,
                            quality: name === 'avatar_image' ? 0.86 : 0.82,
                        });

                        optimizedFormData.append(name, compressedImage || value);
                        processedCompressibleImages += 1;
                    }

                    setUploadFileStatus('');
                    return optimizedFormData;
                };

                const setUploadProgress = (loaded, total) => {
                    if (!loadingProgress || !loadingProgressBar || !loadingProgressText) {
                        return;
                    }

                    const hasTotal = Number.isFinite(total) && total > 0;
                    const percent = hasTotal ? Math.min(100, Math.round((loaded / total) * 100)) : 0;

                    loadingProgress.classList.remove('hidden');
                    loadingProgressBar.style.width = `${percent}%`;

                    if (hasTotal) {
                        loadingProgressText.textContent = `${percent}% (${formatMegabytes(loaded)} / ${formatMegabytes(total)})`;
                        return;
                    }

                    loadingProgressText.textContent = 'កំពុងផ្ទុកឯកសារ...';
                };

                const resetUploadProgress = () => {
                    if (loadingProgress) {
                        loadingProgress.classList.add('hidden');
                    }

                    if (loadingProgressBar) {
                        loadingProgressBar.style.width = '0%';
                    }

                    if (loadingProgressText) {
                        loadingProgressText.textContent = '0%';
                    }

                    setUploadFileStatus('');
                };

                const showLoadingOverlayNow = (message) => {
                    if (!loadingOverlay) {
                        return;
                    }

                    if (loadingOverlayMessage) {
                        loadingOverlayMessage.textContent = message || submitLoadingText;
                    }

                    setUploadFileStatus('');
                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('overflow-hidden');
                };

                const hideLoadingOverlay = () => {
                    if (!loadingOverlay) {
                        return;
                    }

                    loadingOverlay.classList.add('hidden');
                    loadingOverlay.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('overflow-hidden');
                    resetUploadProgress();
                    resetUploadTrackingState();
                };

                const showSweetAlert = async (icon, title, text) => {
                    if (!window.Swal?.fire) {
                        return;
                    }

                    await window.Swal.fire({
                        icon,
                        title,
                        text,
                        confirmButtonText: 'យល់ព្រម',
                        confirmButtonColor: '#356AE6',
                        customClass: {
                            popup: 'swal2-kh-popup',
                            title: 'swal2-kh-title',
                            htmlContainer: 'swal2-kh-content',
                            confirmButton: 'swal2-kh-confirm',
                        },
                    });
                };

                const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                }[character] || character));

                const clearFieldErrors = () => {
                    fieldErrorElements.forEach((element) => {
                        element.textContent = '';
                        element.classList.add('hidden');
                    });
                };

                const resolveFieldErrorElement = (name) => {
                    const wildcardName = name.replace(/\.\d+$/, '.*');

                    return fieldErrorElements.find((element) => element.dataset.fieldError === name)
                        || fieldErrorElements.find((element) => element.dataset.fieldError === wildcardName)
                        || null;
                };

                const setFieldError = (name, message) => {
                    const element = resolveFieldErrorElement(name);

                    if (!element) {
                        return;
                    }

                    element.textContent = message;
                    element.classList.remove('hidden');
                };

                const showAlert = (message) => {
                    uploadAlert.textContent = message || '';
                    uploadAlert.classList.toggle('hidden', !message);
                };

                const renderSuccessState = (payload) => {
                    const message = payload?.message || 'ការចុះឈ្មោះបុគ្គលិកសាកល្បងបានជោគជ័យ។';

                    if (!registrationPage) {
                        showAlert(message);
                        return;
                    }

                    registrationPage.innerHTML = `
                        <div class="public-success-page">
                            <div class="public-success-shell">
                                <div class="success-flash-icon public-success-icon" aria-hidden="true">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg>
                                </div>
                                <p class="public-success-eyebrow">ការចុះឈ្មោះជោគជ័យ</p>
                                <h1 class="public-success-title">ការចុះឈ្មោះបានជោគជ័យ</h1>
                                <p class="public-success-message">${escapeHtml(message)}</p>
                            </div>
                        </div>
                    `;

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                };

                const submitWithProgress = (formData) => new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();

                    xhr.open('POST', registrationForm.action);
                    xhr.withCredentials = true;
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    if (csrfToken) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    }

                    xhr.upload.addEventListener('progress', (event) => {
                        if (!event.lengthComputable) {
                            setUploadProgress(event.loaded || 0, 0);

                            const indeterminateFile = resolveCurrentUploadingFile(event.loaded || 0, event.total || 0);
                            if (indeterminateFile) {
                                setUploadFileStatus(
                                    'Uploading file '
                                    + (indeterminateFile.index + 1)
                                    + '/'
                                    + indeterminateFile.total
                                    + ': '
                                    + truncateFileName(indeterminateFile.name),
                                );
                            }

                            return;
                        }

                        setUploadProgress(event.loaded, event.total);
                        const currentFile = resolveCurrentUploadingFile(event.loaded, event.total);

                        if (currentFile) {
                            setUploadFileStatus(
                                'Uploading file '
                                + (currentFile.index + 1)
                                + '/'
                                + currentFile.total
                                + ': '
                                + truncateFileName(currentFile.name),
                            );
                        }

                        if (event.loaded >= event.total && loadingOverlayMessage) {
                            loadingOverlayMessage.textContent = 'Saving uploaded files...';
                            setUploadFileStatus('Upload completed. Waiting for server response...');
                        }
                    });

                    xhr.addEventListener('load', () => {
                        let payload = {};

                        try {
                            payload = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                        } catch (error) {
                            payload = {};
                        }

                        resolve({
                            status: xhr.status,
                            payload,
                        });
                    });

                    xhr.addEventListener('error', reject);
                    xhr.addEventListener('abort', reject);
                    xhr.send(formData);
                });

                registrationForm.addEventListener('submit', async (event) => {
                    if (event.defaultPrevented) {
                        return;
                    }

                    event.preventDefault();
                    
                    if (window.Swal) {
                        const confirmResult = await window.Swal.fire({
                            title: 'បញ្ជាក់ការចុះឈ្មោះ',
                            text: "តើអ្នកពិតជាចង់បញ្ជូនទិន្នន័យនេះមែនទេ?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#356AE6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'យល់ព្រម',
                            cancelButtonText: 'បោះបង់',
                            customClass: {
                                popup: 'swal2-kh-popup',
                                title: 'swal2-kh-title',
                                htmlContainer: 'swal2-kh-content',
                                confirmButton: 'swal2-kh-confirm',
                                cancelButton: 'swal2-kh-cancel',
                            }
                        });

                        if (!confirmResult.isConfirmed) {
                            return;
                        }
                    }

                    clearFieldErrors();
                    showAlert('');

                    submitButton?.setAttribute('disabled', 'disabled');

                    if (submitButton) {
                        submitButton.textContent = submitLoadingText;
                    }

                    showLoadingOverlayNow('Uploading files...');
                    setUploadProgress(0, getSelectedUploadBytes());
                    resetUploadTrackingState();

                    try {
                        const formData = await buildSubmissionFormData();
                        buildUploadTrackingState(formData);

                        if (loadingOverlayMessage) {
                            loadingOverlayMessage.textContent = 'Uploading files...';
                        }

                        if (uploadTrackingState.totalFileBytes > 0) {
                            setUploadProgress(0, uploadTrackingState.totalFileBytes);

                            const firstUploadFile = uploadTrackingState.files[0];
                            setUploadFileStatus(
                                'Uploading file 1/'
                                + uploadTrackingState.files.length
                                + ': '
                                + truncateFileName(firstUploadFile?.name || 'file'),
                            );
                        } else {
                            setUploadFileStatus('');
                        }

                        const { status, payload } = await submitWithProgress(formData);

                        if (status === 201) {
                            hideLoadingOverlay();
                            await showSweetAlert('success', 'ជោគជ័យ', payload.message || 'ការចុះឈ្មោះបានជោគជ័យ។');
                            renderSuccessState(payload);
                            return;
                        }

                        if (status === 422) {
                            const errors = payload.errors || {};
                            const generalMessage = errors.upload_total?.[0]
                                || errors.submission?.[0]
                                || payload.message
                                || '';

                            showAlert(generalMessage);

                            Object.entries(errors).forEach(([name, messages]) => {
                                if (Array.isArray(messages) && messages.length > 0) {
                                    setFieldError(name, messages[0]);
                                }
                            });

                            (uploadAlert.textContent ? uploadAlert : registrationForm.querySelector('[data-field-error]:not(.hidden)'))
                                ?.scrollIntoView({ behavior: 'smooth', block: 'center' });

                            hideLoadingOverlay();
                            await showSweetAlert('error', 'សូមពិនិត្យម្តងទៀត', generalMessage || 'សូមពិនិត្យព័ត៌មានដែលបានបំពេញម្តងទៀត។');
                            return;
                        }
                        
                        if (status === 403) {
                             hideLoadingOverlay();
                             await showSweetAlert('error', 'គ្មានសិទ្ធិ', payload.message || 'អ្នកមិនមានសិទ្ធិក្នុងការបញ្ជូនពាក្យសុំនេះទេ។');
                             return;
                        }

                        showAlert(payload.message || 'មិនអាចបញ្ជូនពាក្យសុំបានទេ។ សូមសាកល្បងម្តងទៀត។');
                        uploadAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        hideLoadingOverlay();
                        await showSweetAlert('error', 'មានបញ្ហា', payload.message || 'មិនអាចបញ្ជូនពាក្យសុំបានទេ។ សូមសាកល្បងម្តងទៀត។');
                    } catch (error) {
                        showAlert('មិនអាចបញ្ជូនពាក្យសុំបានទេ។ សូមសាកល្បងម្តងទៀត។');
                        uploadAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        hideLoadingOverlay();
                        await showSweetAlert('error', 'មានបញ្ហា', 'អ្នកមិនមានអ៊ីនធឺណិត ឬម៉ាស៊ីនមេមានបញ្ហា។ សូមសាកល្បងម្តងទៀត។');
                    } finally {
                        hideLoadingOverlay();
                        submitButton?.removeAttribute('disabled');

                        if (submitButton) {
                            submitButton.textContent = submitButtonText;
                        }
                    }
                });

                registrationForm.addEventListener('reset', () => {
                    window.setTimeout(() => {
                        clearFieldErrors();
                        showAlert('');
                    }, 0);
                });
            })();
        </script>
    @endif
@endsection
