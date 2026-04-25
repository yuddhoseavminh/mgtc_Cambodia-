@php
    $showDesignTabs = $showDesignTabs ?? true;
    $forcedTemplateTab = in_array(($forcedTemplateTab ?? null), ['portal', 'staff'], true) ? $forcedTemplateTab : null;
    $activeTemplateTab = $forcedTemplateTab ?? (request()->query('template_tab') === 'staff' ? 'staff' : 'portal');
    $redirectSection = in_array(($redirectSection ?? null), ['design-template', 'staff-team-template'], true)
        ? $redirectSection
        : 'design-template';
    $designTemplateRedirect = route('admin.home', ['section' => $redirectSection, 'template_tab' => $activeTemplateTab]);
@endphp

<div class="space-y-6" x-data="{ activeTab: '{{ $activeTemplateTab }}' }">
    @if ($showDesignTabs)
        <div class="flex w-fit max-w-full items-center gap-2 overflow-x-auto rounded-2xl bg-slate-100 p-1.5">
            <button type="button"
                @click="activeTab = 'portal'"
                :class="activeTab === 'portal' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="inline-flex h-9 shrink-0 items-center justify-center whitespace-nowrap rounded-xl px-5 text-sm font-bold transition">
                ទំព័រដើមសាធារណៈ
            </button>
            <button type="button"
                @click="activeTab = 'staff'"
                :class="activeTab === 'staff' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="inline-flex h-9 shrink-0 items-center justify-center whitespace-nowrap rounded-xl px-5 text-sm font-bold transition">
                គំរូបុគ្គលិក
            </button>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.portal-content.update') }}"
        enctype="multipart/form-data"
        data-ajax-form
        data-ajax-redirect="{{ $designTemplateRedirect }}"
        data-ajax-success-title="ជោគជ័យ"
        data-ajax-success-text="បានរក្សាទុកការកែប្រែរួចរាល់។"
        class="space-y-6"
    >
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_section" value="{{ $redirectSection }}">
        <input type="hidden" name="template_tab" value="{{ $activeTemplateTab }}" x-bind:value="activeTab">

        <div x-show="activeTab === 'portal'" x-transition.opacity.duration.200ms class="space-y-6">
            <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-[0_14px_30px_rgba(15,23,42,0.04)] sm:p-7">
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Portal Content</p>
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">ព័ត៌មានសម្រាប់ Hero Section</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">កំណត់ badge, ចំណងជើង និងសេចក្តីពិពណ៌នាសម្រាប់ផ្នែកខាងលើនៃទំព័រដើម។</p>
                </div>

                <div class="mt-6 grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
                    <div>
                        <label class="form-label">Badge</label>
                        <input type="text" name="badge" value="{{ old('badge', $portalContent->badge) }}" class="form-input bg-slate-50" placeholder="ឧ. សូមស្វាគមន៍">
                        @include('partials.field-error', ['name' => 'badge'])
                    </div>
                    <div>
                        <label class="form-label">ចំណងជើងធំ</label>
                        <input type="text" name="title" value="{{ old('title', $portalContent->title) }}" class="form-input bg-slate-50">
                        @include('partials.field-error', ['name' => 'title'])
                    </div>
                </div>

                <div class="mt-5">
                    <label class="form-label">សេចក្តីពិពណ៌នា</label>
                    <textarea name="description" rows="4" class="form-input bg-slate-50">{{ old('description', $portalContent->description) }}</textarea>
                    @include('partials.field-error', ['name' => 'description'])
                </div>
            </section>

            <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-[0_14px_30px_rgba(15,23,42,0.04)] sm:p-7">
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Hero Asset</p>
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">រូបភាព Banner</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">បង្ហាញរូបភាពសម្រាប់ផ្ទៃខាងលើរបស់ទំព័រដើម។</p>
                </div>

                <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="space-y-4">
                        <label class="flex cursor-pointer flex-col items-center justify-center rounded-[1.6rem] border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center transition hover:border-slate-300 hover:bg-white">
                            <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <path d="m7 10 5-5 5 5"/>
                                    <path d="M12 5v10"/>
                                </svg>
                            </span>
                            <span class="mt-4 text-sm font-semibold text-slate-900">ជ្រើសរើសរូបភាពថ្មី</span>
                            <span class="mt-2 text-xs text-slate-500">JPG, PNG, WEBP · អតិបរមា 5MB</span>
                            <input type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp" class="sr-only">
                        </label>
                        @include('partials.field-error', ['name' => 'banner_image'])

                        @if ($portalContent->banner_image_path)
                            <label class="inline-flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                                <input type="checkbox" name="remove_banner_image" value="1" class="h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                លុបរូបភាព Banner បច្ចុប្បន្ន
                            </label>
                        @endif
                    </div>

                    <div class="rounded-[1.6rem] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Preview</p>
                        <div class="mt-4 overflow-hidden rounded-[1.2rem] border border-slate-200 bg-white shadow-sm">
                            @if ($portalContent->banner_image_path)
                                <img src="{{ route('portal.banner-image') }}" alt="Current Banner" class="aspect-video h-full w-full object-cover">
                            @else
                                <div class="flex aspect-video items-center justify-center bg-slate-100 text-sm font-medium text-slate-400">
                                    មិនទាន់មានរូបភាព
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-[0_14px_30px_rgba(15,23,42,0.04)] sm:p-7">
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Feature Blocks</p>
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">កាតអត្ថបទសង្ខេប</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">កំណត់ចំណងជើង និងពិពណ៌នាសម្រាប់កាតទាំង ៣ នៅលើទំព័រដើម។</p>
                </div>

                <div class="mt-6 grid gap-5 lg:grid-cols-3">
                    @foreach (['one', 'two', 'three'] as $index)
                        <article class="rounded-[1.4rem] border border-slate-200 bg-slate-50 p-5">
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">Card {{ strtoupper($index) }}</span>
                            <div class="mt-4">
                                <label class="form-label">ចំណងជើង</label>
                                <input type="text" name="feature_{{ $index }}_title" value="{{ old('feature_'.$index.'_title', $portalContent->{'feature_'.$index.'_title'}) }}" class="form-input bg-white">
                            </div>
                            <div class="mt-4">
                                <label class="form-label">ពិពណ៌នា</label>
                                <textarea name="feature_{{ $index }}_description" rows="4" class="form-input bg-white">{{ old('feature_'.$index.'_description', $portalContent->{'feature_'.$index.'_description'}) }}</textarea>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>

        <div x-show="activeTab === 'staff'" x-transition.opacity.duration.200ms class="space-y-6">
            <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-[0_14px_30px_rgba(15,23,42,0.04)] sm:p-7">
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Staff Branding</p>
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">ចំណងជើងទំព័របុគ្គលិក</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">អត្ថបទទាំងនេះនឹងបង្ហាញនៅផ្នែកក្បាលនៃទំព័រប្រវត្តិរូបបុគ្គលិក។</p>
                </div>

                <div class="mt-6 grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="form-label">ចំណងជើងសំខាន់</label>
                        <input type="text" name="staff_title" value="{{ old('staff_title', $portalContent->staff_title) }}" class="form-input bg-slate-50" placeholder="ឧ. កងទ័ពជើងគោក">
                        @include('partials.field-error', ['name' => 'staff_title'])
                    </div>
                    <div>
                        <label class="form-label">ចំណងជើងរង</label>
                        <input type="text" name="staff_subtitle" value="{{ old('staff_subtitle', $portalContent->staff_subtitle) }}" class="form-input bg-slate-50" placeholder="ឧ. ROYAL CAMBODIAN ARMY">
                        @include('partials.field-error', ['name' => 'staff_subtitle'])
                    </div>
                </div>
            </section>

            <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-[0_14px_30px_rgba(15,23,42,0.04)] sm:p-7">
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Logo Asset</p>
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">ឡូហ្គោបុគ្គលិក</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">រូបភាពនេះនឹងបង្ហាញជាមួយចំណងជើងនៅលើទំព័រប្រវត្តិរូបបុគ្គលិក។</p>
                </div>

                <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_280px]">
                    <div class="space-y-4">
                        <label class="flex cursor-pointer flex-col items-center justify-center rounded-[1.6rem] border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center transition hover:border-slate-300 hover:bg-white">
                            <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14"/>
                                    <path d="m5 12 7-7 7 7"/>
                                </svg>
                            </span>
                            <span class="mt-4 text-sm font-semibold text-slate-900">បញ្ចូលឡូហ្គោថ្មី</span>
                            <span class="mt-2 text-xs text-slate-500">JPG, PNG, WEBP · អតិបរមា 2MB</span>
                            <input type="file" name="staff_logo" accept=".jpg,.jpeg,.png,.webp" class="sr-only">
                        </label>
                        @include('partials.field-error', ['name' => 'staff_logo'])

                        @if ($portalContent->staff_logo_path)
                            <label class="inline-flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                                <input type="checkbox" name="remove_staff_logo" value="1" class="h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                លុបឡូហ្គោបច្ចុប្បន្ន
                            </label>
                        @endif
                    </div>

                    <div class="rounded-[1.6rem] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Preview</p>
                        <div class="mt-4 flex items-center justify-center rounded-[1.2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="h-40 w-40 overflow-hidden rounded-full border border-slate-200 bg-slate-50">
                                @if ($portalContent->staff_logo_path)
                                    <img src="{{ route('portal.staff-logo-image') }}" alt="Staff Logo" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-sm font-medium text-slate-400">
                                        មិនទាន់មានឡូហ្គោ
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="sticky bottom-4 z-10 flex items-center justify-end">
            <div class="rounded-[1.4rem] border border-slate-200 bg-white/95 p-3 shadow-[0_20px_45px_rgba(15,23,42,0.08)] backdrop-blur">
                <button type="submit" class="inline-flex min-h-[3.1rem] items-center justify-center rounded-[1rem] bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800">
                    រក្សាទុកការកែប្រែ
                </button>
            </div>
        </div>
    </form>
</div>
