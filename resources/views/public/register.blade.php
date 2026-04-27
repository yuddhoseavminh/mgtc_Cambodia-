@extends('app')

@section('body')
    @php
        $bannerKhmerTitle = $portalContent?->course_page_title ?: 'សាលាហ្វឹកហ្វឺនយោធា';
        $bannerKhmerSubtitle = $portalContent?->course_page_subtitle ?: 'ប្រព័ន្ធចុះឈ្មោះសិក្សាវគ្គយោធា';
        $bannerKhmerDescription = $portalContent?->course_page_description ?: 'សូមបំពេញព័ត៌មានឱ្យបានត្រឹមត្រូវ និងងាយស្រួលត្រួតពិនិត្យ';
        $selectedCourseId = old('course_id');
        $currentRankId = old('rank_id');
        $currentRankName = old('rank_name');
        $usesCustomRank = old('rank_id') === '__custom__' || trim((string) old('rank_name', '')) !== '';
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
                <h1 class="public-success-title">អ្នកបានចុះឈ្មោះដោយជោគជ័យ</h1>
                <p class="public-success-message">{{ session('status') }}</p>
            </div>
        </div>
    @else
    <div class="public-page">
        @include('public.partials.submit-loading-overlay')

        @if ($portalContent?->course_page_banner_image_path)
            <section class="public-banner-card mb-4 sm:mb-6">
                <img src="{{ route('portal.course-banner-image') }}" alt="Course registration banner" class="h-auto w-full object-contain">
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
            <form method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data" class="space-y-5 sm:space-y-6" data-registration-form data-max-upload-total="41943040" data-max-upload-total-mobile="10485760" data-submit-loading-text="សូមចាំបន្តិច....">
                @csrf

                <div class="public-section-card space-y-4">
                    <div class="public-section-heading">
                        <h2 class="text-xl font-semibold text-slate-950">ព័ត៌មានផ្ទាល់ខ្លួន</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">សូមបំពេញព័ត៌មានអត្តសញ្ញាណរបស់អ្នកឱ្យបានច្បាស់លាស់ ដូចមានក្នុងឯកសារផ្លូវការ។</p>
                    </div>

                    <div class="grid gap-4 sm:gap-5 md:grid-cols-2">
                        <div>
                            <label class="form-label">* គោត្តនាម នាម</label>
                            <input type="text" name="khmer_name" value="{{ old('khmer_name') }}" placeholder="សូមបំពេញឈ្មោះជាភាសាខ្មែរ" class="form-input">
                            @include('partials.field-error', ['name' => 'khmer_name'])
                        </div>
                        <div>
                            <label class="form-label">* ឈ្មោះជាអក្សរឡាតាំង</label>
                            <input type="text" name="latin_name" value="{{ old('latin_name') }}" placeholder="សូមបំពេញឈ្មោះជាអក្សរឡាតាំង" class="form-input">
                            @include('partials.field-error', ['name' => 'latin_name'])
                        </div>
                        <div class="md:col-span-2 grid gap-4 sm:gap-5 xl:grid-cols-3">
                        <div>
                            <label class="form-label">* អត្តលេខ</label>
                            <input type="text" name="id_number" value="{{ old('id_number') }}" placeholder="សូមបំពេញអត្តលេខ" class="form-input">
                            @include('partials.field-error', ['name' => 'id_number'])
                        </div>
                        <div>
                            <label class="form-label">* ឋានន្តរសក្តិ</label>
                            <div class="relative">
                                <select
                                    id="rank_select"
                                    name="{{ $usesCustomRank ? '' : 'rank_id' }}"
                                    class="form-input h-12 w-full min-w-0 bg-white {{ $usesCustomRank ? 'hidden' : '' }}"
                                    onchange="if(this.value === '__custom__') { this.classList.add('hidden'); this.name = ''; document.getElementById('rank_input').classList.remove('hidden'); document.getElementById('rank_input').name = 'rank_name'; document.getElementById('rank_input').focus(); document.getElementById('rank_cancel_btn').classList.remove('hidden'); }"
                                >
                                    <option value="">សូមជ្រើសរើសឋានន្តរសក្តិ</option>
                                    @foreach ($ranks as $rank)
                                        <option value="{{ $rank->id }}" @selected(! $usesCustomRank && $currentRankId == $rank->id)>{{ $rank->name_kh }}@if ($rank->name_en)@endif</option>
                                    @endforeach
                                    <option value="__custom__" class="font-semibold text-[#356AE6]" @selected($usesCustomRank)>+បញ្ចូលថ្មី...</option>
                                </select>
                                <input
                                    type="text"
                                    id="rank_input"
                                    name="{{ $usesCustomRank ? 'rank_name' : '' }}"
                                    value="{{ $usesCustomRank ? $currentRankName : '' }}"
                                    class="form-input h-12 w-full min-w-0 bg-white pr-10 {{ $usesCustomRank ? '' : 'hidden' }}"
                                    placeholder="បញ្ចូលឋានន្តរសក្តិថ្មី..."
                                >
                                <button
                                    type="button"
                                    id="rank_cancel_btn"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600 {{ $usesCustomRank ? '' : 'hidden' }}"
                                    onclick="document.getElementById('rank_input').classList.add('hidden'); document.getElementById('rank_input').name = ''; document.getElementById('rank_input').value = ''; document.getElementById('rank_select').classList.remove('hidden'); document.getElementById('rank_select').name = 'rank_id'; document.getElementById('rank_select').value = ''; this.classList.add('hidden');"
                                    title="បោះបង់ការបញ្ចូលថ្មី និងជ្រើសរើសពីបញ្ជី"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">ប្រសិនបើមិនមានក្នុងបញ្ជី អ្នកអាចបញ្ចូលឋានន្តរសក្តិថ្មីបាន។</p>
                            @include('partials.field-error', ['name' => 'rank_id'])
                            @include('partials.field-error', ['name' => 'rank_name'])
                        </div>
                        <div>
                            <label class="form-label">* ភេទ</label>
                            <select name="gender" class="form-input">
                                <option value="">សូមជ្រើសរើសភេទ</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $genderLabels[$gender] ?? $gender }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'gender'])
                        </div>
                        </div>
                        <div class="md:col-span-2 grid gap-4 sm:gap-5 md:grid-cols-2">
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
                            <label class="form-label">* ថ្ងៃ ខែ ឆ្នាំ ចូលបម្រើទ័ព</label>
                            <div class="date-picker" data-date-picker data-placeholder="សូមជ្រើសរើសថ្ងៃចូលបម្រើកងទ័ពទ័ព">
                                <input type="hidden" name="date_of_enlistment" value="{{ old('date_of_enlistment') }}" data-date-value data-max="{{ now()->toDateString() }}">
                                <button type="button" class="date-picker-trigger" data-date-toggle aria-expanded="false">
                                    <span class="date-picker-text" data-date-display>{{ old('date_of_enlistment') ? \Illuminate\Support\Carbon::parse(old('date_of_enlistment'))->locale('km')->translatedFormat('d M Y') : 'សូមជ្រើសរើសថ្ងៃចូលបម្រើកងទ័ពទ័ព' }}</span>
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
                            @include('partials.field-error', ['name' => 'date_of_enlistment'])
                        </div>
                        </div>
                    </div>
                </div>

                <div class="public-section-card space-y-4">
                 

                    <div class="grid gap-4 sm:gap-5 md:grid-cols-2">
                        <div>
                            <label class="form-label">* មុខតំណែង / មុខងារ</label>
                            <input type="text" name="position" value="{{ old('position') }}" placeholder="សូមបំពេញមុខតំណែង / មុខងារ" class="form-input">
                            @include('partials.field-error', ['name' => 'position'])
                        </div>
                        <div>
                            <label class="form-label">* កងឯកភាព</label>
                            <input type="text" name="unit" value="{{ old('unit') }}" placeholder="សូមបំពេញកងឯកភាព" class="form-input">
                            @include('partials.field-error', ['name' => 'unit'])
                        </div>
                        <div>
                            <label class="form-label">* ស្នើសុំចូលសិក្សាវគ្គ </label>
                            <select name="course_id" class="form-input">
                                <option value="">សូមជ្រើសរើសវគ្គសិក្សា</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected((string) $selectedCourseId === (string) $course->id)>{{ $course->name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'course_id'])
                        </div>
                        <div>
                            <label class="form-label">* កម្រិតវប្បធម៌ទូទៅ</label>
                            <select name="cultural_level_id" class="form-input">
                                <option value="">សូមជ្រើសរើសកម្រិតវប្បធម៌ទូទៅ</option>
                                @foreach ($culturalLevels as $level)
                                    <option value="{{ $level->id }}" @selected(old('cultural_level_id') == $level->id)>{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'cultural_level_id'])
                        </div>
                    </div>
                </div>

                <div class="public-section-card space-y-4">
                

                    <div class="grid gap-4 sm:gap-5 md:grid-cols-2">
                        <div>
                            <label class="form-label">* ទីកន្លែងកំណើត (ខេត្ត / រាជធានី)</label>
                            <select name="place_of_birth" class="form-input">
                                <option value="">សូមជ្រើសរើសខេត្ត / រាជធានី</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province }}" @selected(old('place_of_birth') === $province)>{{ $provinceLabels[$province] ?? $province }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'place_of_birth'])
                        </div>
                        <div>
                            <label class="form-label">ស្ថានភាពគ្រួសារ</label>
                            <select name="family_situation" class="form-input">
                                <option value="">សូមជ្រើសរើសស្ថានភាពគ្រួសារ</option>
                                @foreach ($familySituations as $familySituation)
                                    <option value="{{ $familySituation }}" @selected(old('family_situation') === $familySituation)>{{ $familySituationLabels[$familySituation] ?? $familySituation }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'family_situation'])
                        </div>
                        <div>
                            <label class="form-label">* អាសយដ្ឋានបច្ចុប្បន្ន</label>
                            <select name="current_address" class="form-input">
                                <option value="">សូមជ្រើសរើសខេត្ត / រាជធានី</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $provinceLabels[$province] ?? $province }}" @selected(old('current_address') === ($provinceLabels[$province] ?? $province))>{{ $provinceLabels[$province] ?? $province }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'current_address'])
                        </div>
                        <div>
                            <label class="form-label">លេខទូរស័ព្ទទំនាក់ទំនង</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="សូមបំពេញលេខទូរស័ព្ទ" class="form-input">
                            @include('partials.field-error', ['name' => 'phone_number'])
                        </div>
                    </div>
                </div>

                <div class="public-section-card space-y-4">
                    <div class="public-section-heading">
                        <h2 class="text-xl font-semibold text-slate-950">* ឯកសារភ្ជាប់មកជាមួយ</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">សូមជ្រើសរើសថា មាន ឬ មិនមាន រួចបន្តផ្ទុកឯកសារ ប្រសិនបើមាន។ អ្នកអាចផ្ទុកឯកសារច្រើនសន្លឹកបាន ទំហំសរុបមិនត្រូវលើសពី 100 MB ហើយឯកសារនីមួយៗមិនលើស 50 MB។</p>
                    </div>

                    <div class="{{ $errors->has('upload_total') || $errors->has('submission') ? '' : 'hidden' }} rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700" data-upload-total-alert>{{ $errors->first('upload_total') ?: $errors->first('submission') }}</div>

                    <div class="grid gap-3 sm:gap-4">
                        @foreach ($documentRequirements as $documentRequirement)
                            @php
                                $selectedStatus = old("document_statuses.{$documentRequirement->id}", 'dont_have');
                            @endphp
                            <div class="public-upload-card" data-document-card>
                                <div class="space-y-4">
                                    <div>
                                        <label class="form-label !mb-2">{{ $documentRequirement->name_kh }}</label>
                                        <p class="text-sm text-slate-500">* ជ្រើសរើសថា មាន ឬ មិនមាន រួចបន្តផ្ទុកឯកសារ ប្រសិនបើមាន។</p>
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
                                            <p class="mt-2 text-sm text-slate-500">អ្នកអាចផ្ទុកឯកសារច្រើនសន្លឹកបាន។ ទំហំមិនត្រូវលើសពី 50 MB ក្នុងមួយហ្វាល់។</p>
                                            @include('partials.field-error', ['name' => "document_files.{$documentRequirement->id}"])
                                            @include('partials.field-error', ['name' => "document_files.{$documentRequirement->id}.*"])
                                        </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>

                <div class="public-actions">
                    <button type="submit" class="public-primary-button" data-submit-button>
                       បញ្ជូនពាក្យសុំ
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
            const mobileMediaQuery = window.matchMedia ? window.matchMedia('(max-width: 767.98px)') : null;
            const resolveMaxUploadTotal = () => {
                const isMobileViewport = mobileMediaQuery ? mobileMediaQuery.matches : window.innerWidth < 768;
                return isMobileViewport && maxUploadTotalMobile > 0 ? maxUploadTotalMobile : maxUploadTotal;
            };

            const formatMegabytes = (bytes) => `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
            const getSelectedUploadBytes = () => Array.from(
                registrationForm.querySelectorAll('input[type="file"]'),
            ).reduce((total, input) => total + Array.from(input.files || []).reduce((sum, file) => sum + file.size, 0), 0);

            const syncUploadAlert = () => {
                const totalBytes = getSelectedUploadBytes();
                const resolvedMaxUploadTotal = resolveMaxUploadTotal();
                const isTooLarge = resolvedMaxUploadTotal > 0 && totalBytes > resolvedMaxUploadTotal;

                uploadAlert.classList.toggle('hidden', !isTooLarge);

                if (isTooLarge) {
                    uploadAlert.textContent = `Total upload is too large. Keep it below ${formatMegabytes(resolvedMaxUploadTotal)}. (Current total: ${formatMegabytes(totalBytes)})`;
                } else {
                    uploadAlert.textContent = '';
                }

                return isTooLarge;
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

            if (!registrationForm || !uploadAlert || !window.fetch) {
                return;
            }

            const submitButton = registrationForm.querySelector('[data-submit-button]');
            const submitButtonText = submitButton?.textContent?.trim() || 'Submit';
            const submitLoadingText = registrationForm.dataset.submitLoadingText || 'Submitting...';
            const loadingOverlay = document.querySelector('[data-submit-loading-overlay]');
            const loadingOverlayMessage = loadingOverlay?.querySelector('[data-submit-loading-message]');
            const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
            const fieldErrorElements = Array.from(registrationForm.querySelectorAll('[data-field-error]'));
            let loadingOverlayTimerId = null;

            const showLoadingOverlayNow = (message) => {
                if (!loadingOverlay) {
                    return;
                }

                if (loadingOverlayMessage) {
                    loadingOverlayMessage.textContent = message || submitLoadingText;
                }

                loadingOverlay.classList.remove('hidden');
                loadingOverlay.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
            };

            const queueLoadingOverlay = (message) => {
                if (!loadingOverlay) {
                    return;
                }

                if (loadingOverlayTimerId !== null) {
                    window.clearTimeout(loadingOverlayTimerId);
                }

                loadingOverlayTimerId = window.setTimeout(() => {
                    loadingOverlayTimerId = null;
                    showLoadingOverlayNow(message);
                }, 220);
            };

            const hideLoadingOverlay = () => {
                if (loadingOverlayTimerId !== null) {
                    window.clearTimeout(loadingOverlayTimerId);
                    loadingOverlayTimerId = null;
                }

                if (!loadingOverlay) {
                    return;
                }

                loadingOverlay.classList.add('hidden');
                loadingOverlay.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
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
                const message = payload?.message || 'អ្នកបានចុះឈ្មោះដោយជោគជ័យ';

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
                            <h1 class="public-success-title">អ្នកបានចុះឈ្មោះដោយជោគជ័យ</h1>
                            <p class="public-success-message">${escapeHtml(message)}</p>
                        </div>
                    </div>
                `;

                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

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
                
                if (window.Swal) {
                    window.Swal.fire({
                        title: 'កំពុងដំណើរការ...',
                        text: 'សូមរង់ចាំបន្តិច',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            window.Swal.showLoading();
                        }
                    });
                }

                try {
                    const response = await fetch(registrationForm.action, {
                        method: 'POST',
                        body: new FormData(registrationForm),
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (response.status === 201) {
                        await showSweetAlert('success', 'ជោគជ័យ', payload.message || 'អ្នកបានចុះឈ្មោះជោគជ័យ។');
                        renderSuccessState(payload);
                        return;
                    }

                    if (response.status === 422) {
                        const errors = payload.errors || {};
                        const firstValidationMessage = Object.values(errors)
                            .find((messages) => Array.isArray(messages) && messages.length > 0)?.[0] || '';
                        const generalMessage = errors.upload_total?.[0]
                            || errors.submission?.[0]
                            || firstValidationMessage
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

                        await showSweetAlert('error', 'សូមពិនិត្យម្តងទៀត', generalMessage || 'សូមពិនិត្យព័ត៌មានដែលបានបំពេញម្តងទៀត។');
                        return;
                    }
                    
                    if (response.status === 403) {
                         await showSweetAlert('error', 'គ្មានសិទ្ធិ', payload.message || 'អ្នកមិនមានសិទ្ធិក្នុងការបញ្ជូនពាក្យសុំនេះទេ។');
                         return;
                    }

                    showAlert(payload.message || 'មិនអាចបញ្ជូនពាក្យសុំបានទេ។ សូមសាកល្បងម្តងទៀត។');
                    uploadAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    await showSweetAlert('error', 'មានបញ្ហា', payload.message || 'មិនអាចបញ្ជូនពាក្យសុំបានទេ។ សូមសាកល្បងម្តងទៀត។');
                } catch (error) {
                    showAlert('មិនអាចបញ្ជូនពាក្យសុំបានទេ។ សូមសាកល្បងម្តងទៀត។');
                    uploadAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    await showSweetAlert('error', 'មានបញ្ហា', 'អ្នកមិនមានអ៊ីនធឺណិត ឬម៉ាស៊ីនមេមានបញ្ហា។ សូមសាកល្បងម្តងទៀត។');
                } finally {
                    submitButton?.removeAttribute('disabled');

                    if (submitButton) {
                        submitButton.textContent = submitButtonText;
                    }
                }
            });
        })();
    </script>
    @endif
@endsection
