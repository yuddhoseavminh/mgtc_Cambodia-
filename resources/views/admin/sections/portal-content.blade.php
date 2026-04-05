<section class="dashboard-surface p-6">
    <div class="border-b border-slate-200 pb-6">
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">ទំព័រដើមសាធារណៈ</p>
        <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">បង្កើត ឬកែប្រែគម្រប និងចំណងជើង</h3>
        <p class="mt-3 text-sm leading-6 text-slate-500">វាលទាំងនេះគ្រប់គ្រងទំព័រដើមសាធារណៈនៅ `/` រួមមានបដាគម្រប ផ្លាក ចំណងជើង ពិពណ៌នា និងកាតទំព័រដើម។</p>
    </div>

    <form method="POST" action="{{ route('admin.portal-content.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5" data-ajax-form data-ajax-redirect="{{ route('admin.home', ['section' => 'design-template']) }}" data-ajax-success-title="áž‡áŸ„áž‚აჟ‡áŸაჟ™" data-ajax-success-text="បានកែប្រែមាតិកាទំព័រដើមដោយជោគជ័យ។">
        @csrf
        @method('PUT')
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="form-label">ផ្លាក</label>
                <input type="text" name="badge" value="{{ old('badge', $portalContent->badge) }}" class="form-input">
                @include('partials.field-error', ['name' => 'badge'])
            </div>
            <div class="md:col-span-2">
                <label class="form-label">ចំណងជើងទំព័រដើម</label>
                <input type="text" name="title" value="{{ old('title', $portalContent->title) }}" class="form-input">
                @include('partials.field-error', ['name' => 'title'])
            </div>
            <div class="md:col-span-2">
                <label class="form-label">ពិពណ៌នាទំព័រដើម</label>
                <textarea name="description" rows="4" class="form-input">{{ old('description', $portalContent->description) }}</textarea>
                @include('partials.field-error', ['name' => 'description'])
            </div>
            <div class="md:col-span-2">
                <label class="form-label">រូបបដាគម្រប</label>
                <input type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp" class="block w-full rounded-2xl border border-slate-200 bg-[#f8faff] px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-[#eef0ff] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#4b4dff] hover:file:bg-[#e2e7ff]">
                <p class="mt-2 text-sm text-slate-500">ផ្ទុក JPG, PNG ឬ WEBP។ ទំហំអតិបរមា៖ 5 MB។</p>
                @include('partials.field-error', ['name' => 'banner_image'])
            </div>
            @if ($portalContent->banner_image_path)
                <div class="dashboard-soft-surface md:col-span-2 p-5">
                    <p class="text-sm font-semibold text-slate-900">គម្របទំព័រដើមបច្ចុប្បន្ន</p>
                    <div class="mt-4 overflow-hidden rounded-[20px] border border-slate-200 bg-white">
                        <img src="{{ route('portal.banner-image') }}" alt="បដាវេបសាយបច្ចុប្បន្ន" class="h-auto w-full object-contain">
                    </div>
                    <label class="mt-4 inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="remove_banner_image" value="1" class="h-4 w-4 rounded border-slate-300 text-[#4b4dff] focus:ring-[#4b4dff]">
                        លុបរូបបដាបច្ចុប្បន្ន
                    </label>
                </div>
            @endif
            <div>
                <label class="form-label">ចំណងជើងកាតលក្ខណៈពិសេសទី ១</label>
                <input type="text" name="feature_one_title" value="{{ old('feature_one_title', $portalContent->feature_one_title) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">ពិពណ៌នាកាតលក្ខណៈពិសេសទី ១</label>
                <textarea name="feature_one_description" rows="4" class="form-input">{{ old('feature_one_description', $portalContent->feature_one_description) }}</textarea>
            </div>
            <div>
                <label class="form-label">ចំណងជើងកាតលក្ខណៈពិសេសទី ២</label>
                <input type="text" name="feature_two_title" value="{{ old('feature_two_title', $portalContent->feature_two_title) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">ពិពណ៌នាកាតលក្ខណៈពិសេសទី ២</label>
                <textarea name="feature_two_description" rows="4" class="form-input">{{ old('feature_two_description', $portalContent->feature_two_description) }}</textarea>
            </div>
            <div>
                <label class="form-label">ចំណងជើងកាតលក្ខណៈពិសេសទី ៣</label>
                <input type="text" name="feature_three_title" value="{{ old('feature_three_title', $portalContent->feature_three_title) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">ពិពណ៌នាកាតលក្ខណៈពិសេសទី ៣</label>
                <textarea name="feature_three_description" rows="4" class="form-input">{{ old('feature_three_description', $portalContent->feature_three_description) }}</textarea>
            </div>
        </div>
        <button type="submit" class="inline-flex items-center rounded-2xl bg-[linear-gradient(135deg,#6a5cff,#169bff)] px-5 py-3 text-sm font-semibold text-white transition hover:brightness-105">រក្សាទុកមាតិកាគម្រប</button>
    </form>
</section>
