<section class="grid gap-4 xl:grid-cols-3">
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">អ្នកប្រើប្រាស់គ្រប់គ្រង</p>
        <p class="mt-4 text-2xl font-semibold tracking-tight text-slate-950">{{ auth()->user()->name }}</p>
        <p class="mt-3 text-sm text-slate-500">{{ auth()->user()->email }}</p>
    </article>
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">តួនាទី</p>
        <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950">អ្នកគ្រប់គ្រង</p>
        <p class="mt-3 text-sm text-slate-500">ប្រវត្តិរូបនេះគ្រប់គ្រងសិទ្ធិចូលប្រើផ្ទាំងអ្នកគ្រប់គ្រង។</p>
    </article>
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ឧបករណ៍សម័យប្រើប្រាស់</p>
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('admin.home', ['section' => 'users']) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">អ្នកប្រើប្រាស់</a>
            <a href="{{ route('admin.home', ['section' => 'design-template']) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">គំរូរចនា</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">ចាកចេញ</button>
            </form>
        </div>
    </article>
</section>

<section class="dashboard-surface p-6">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">ផ្ទាំងប្រវត្តិរូប</h3>
            <p class="mt-2 text-sm text-slate-500">គ្រប់គ្រងព័ត៌មានប្រវត្តិរូប និងធ្វើបច្ចុប្បន្នភាពគណនីរបស់អ្នក។</p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">ការគ្រប់គ្រងប្រព័ន្ធ</span>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-6 grid gap-5 xl:grid-cols-[1.1fr_0.9fr]" data-ajax-form data-ajax-redirect="{{ route('admin.home', ['section' => 'profile']) }}" data-ajax-success-title="áž‡áŸ„áž‚აჟ‡áŸაჟ™" data-ajax-success-text="បានកែប្រែព័ត៌មានគណនីដោយជោគជ័យ។">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div class="dashboard-soft-surface p-5">
                <label class="form-label">ឈ្មោះ</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-input">
                @include('partials.field-error', ['name' => 'name'])
            </div>

            <div class="dashboard-soft-surface p-5">
                <label class="form-label">អ៊ីមែល</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-input">
                @include('partials.field-error', ['name' => 'email'])
            </div>
        </div>

        <div class="space-y-5">
            <div class="dashboard-soft-surface p-5">
                <label class="form-label">ពាក្យសម្ងាត់បច្ចុប្បន្ន</label>
                <input type="password" name="current_password" class="form-input" placeholder="ត្រូវការតែពេលប្តូរពាក្យសម្ងាត់">
                @include('partials.field-error', ['name' => 'current_password'])
            </div>

            <div class="dashboard-soft-surface p-5">
                <label class="form-label">ពាក្យសម្ងាត់ថ្មី</label>
                <input type="password" name="password" class="form-input" placeholder="ទុកទទេដើម្បីរក្សាពាក្យសម្ងាត់ចាស់">
                @include('partials.field-error', ['name' => 'password'])
            </div>

            <div class="dashboard-soft-surface p-5">
                <label class="form-label">បញ្ជាក់ពាក្យសម្ងាត់ថ្មី</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
        </div>

        <div class="xl:col-span-2 flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                រក្សាទុកប្រវត្តិរូប
            </button>
            <a href="{{ route('admin.home', ['section' => 'users']) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                មើលអ្នកប្រើប្រាស់
            </a>
        </div>
    </form>
</section>
