<section class="grid gap-4 xl:grid-cols-3">
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ផ្លូវ</p>
        <p class="mt-4 text-2xl font-semibold tracking-tight text-slate-950">/registration/test-taking-staff</p>
        <p class="mt-3 text-sm text-slate-500">គំរូទម្រង់បុគ្គលិកសាកល្បងសាធារណៈ។</p>
    </article>
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ចំណងជើងបដា</p>
        <p class="mt-4 text-xl font-semibold tracking-tight text-slate-950">{{ $portalContent->test_taking_staff_page_title ?: '-' }}</p>
        <p class="mt-3 text-sm text-slate-500">ចំណងជើងខ្មែរដែលបង្ហាញលើទម្រង់។</p>
    </article>
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ចំណងជើងរងបដា</p>
        <p class="mt-4 text-xl font-semibold tracking-tight text-slate-950">{{ $portalContent->test_taking_staff_page_subtitle ?: '-' }}</p>
        <p class="mt-3 text-sm text-slate-500">ចំណងជើងរងដែលបង្ហាញក្រោមចំណងជើងមេ។</p>
    </article>
</section>

<section class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
    <section class="dashboard-surface p-6">
        <div class="border-b border-slate-200 pb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">គំរូបុគ្គលិកសាកល្បង</p>
            <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">បង្កើត ឬកែប្រែរចនាទម្រង់បុគ្គលិក</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">កែប្រែមាតិកាគំរូខាងលើដែលប្រើនៅ `http://127.0.0.1:8000/registration/test-taking-staff`។</p>
        </div>

        <form method="POST" action="{{ route('admin.portal-content.test-taking-staff-template.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5" data-ajax-form data-ajax-redirect="{{ route('admin.home', ['section' => 'test-taking-staff-template']) }}" data-ajax-success-title="áž‡áŸ„აჟ‚ეჟáŸაჟ™" data-ajax-success-text="បានកែប្រែទម្រង់បុគ្គលិកសាកល្បងដោយជោគជ័យ។">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">ចំណងជើង</label>
                <input type="text" name="test_taking_staff_page_title" value="{{ old('test_taking_staff_page_title', $portalContent->test_taking_staff_page_title) }}" class="form-input">
                @include('partials.field-error', ['name' => 'test_taking_staff_page_title'])
            </div>

            <div>
                <label class="form-label">ចំណងជើងរង</label>
                <input type="text" name="test_taking_staff_page_subtitle" value="{{ old('test_taking_staff_page_subtitle', $portalContent->test_taking_staff_page_subtitle) }}" class="form-input">
                @include('partials.field-error', ['name' => 'test_taking_staff_page_subtitle'])
            </div>

            <div>
                <label class="form-label">ពិពណ៌នា</label>
                <textarea name="test_taking_staff_page_description" rows="4" class="form-input">{{ old('test_taking_staff_page_description', $portalContent->test_taking_staff_page_description) }}</textarea>
                @include('partials.field-error', ['name' => 'test_taking_staff_page_description'])
            </div>

            <div>
                <label class="form-label">រូបបដាមុនទម្រង់</label>
                <input type="file" name="test_taking_staff_page_banner_image" accept=".jpg,.jpeg,.png,.webp" class="block w-full rounded-2xl border border-slate-200 bg-[#f8faff] px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-[#eef0ff] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#4b4dff] hover:file:bg-[#e2e7ff]">
                <p class="mt-2 text-sm text-slate-500">ផ្ទុករូបភាពសម្រាប់បង្ហាញមុនទម្រង់បុគ្គលិកសាកល្បង។</p>
                @include('partials.field-error', ['name' => 'test_taking_staff_page_banner_image'])
            </div>

            @if ($portalContent->test_taking_staff_page_banner_image_path)
                <div class="dashboard-soft-surface p-5">
                    <p class="text-sm font-semibold text-slate-900">បដាបុគ្គលិកសាកល្បងបច្ចុប្បន្ន</p>
                    <div class="mt-4 overflow-hidden rounded-[20px] border border-slate-200 bg-white">
                        <img src="{{ route('portal.test-taking-staff-banner-image') }}" alt="បដាទម្រង់បុគ្គលិកសាកល្បងបច្ចុប្បន្ន" class="h-auto w-full object-contain">
                    </div>
                    <label class="mt-4 inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="remove_test_taking_staff_page_banner_image" value="1" class="h-4 w-4 rounded border-slate-300 text-[#4b4dff] focus:ring-[#4b4dff]">
                        លុបបដាបុគ្គលិកសាកល្បងបច្ចុប្បន្ន
                    </label>
                </div>
            @endif

            <button type="submit" class="inline-flex items-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">រក្សាទុកគំរូបុគ្គលិក</button>
        </form>
    </section>

    <div class="space-y-6">
        <section class="dashboard-surface p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">មើលជាមុន</p>
            <h3 class="mt-2 text-[1.7rem] font-semibold tracking-tight text-slate-950">បើកទំព័រសាធារណៈ</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">ពិនិត្យទម្រង់បុគ្គលិកពិតបន្ទាប់ពីរក្សាទុកការកែប្រែ។</p>
            <a href="{{ route('test-taking-staff.form') }}" class="mt-5 inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">បើកទម្រង់បុគ្គលិកសាកល្បង</a>
        </section>

        <section class="dashboard-surface p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">ពាក់ព័ន្ធ</p>
            <h3 class="mt-2 text-[1.7rem] font-semibold tracking-tight text-slate-950">ការរុករកគំរូ</h3>
            <div class="mt-5 space-y-4">
                <a href="{{ route('admin.home', ['section' => 'design-template']) }}" class="block rounded-[24px] border border-slate-200 bg-[#f8fafc] px-5 py-5 transition hover:bg-white">
                    <p class="text-sm font-semibold text-slate-900">ទំព័រដើមវេបសាយ</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">កែប្រែគម្របទំព័រ ចំណងជើង បដា និងកាត។</p>
                </a>
                <a href="{{ route('admin.home', ['section' => 'course-template']) }}" class="block rounded-[24px] border border-slate-200 bg-[#f8fafc] px-5 py-5 transition hover:bg-white">
                    <p class="text-sm font-semibold text-slate-900">គំរូទម្រង់វគ្គសិក្សា</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">កែប្រែមាតិកាទំព័រចុះឈ្មោះវគ្គសិក្សាសាធារណៈ។</p>
                </a>
            </div>
        </section>
    </div>
</section>
