<section class="grid gap-4 xl:grid-cols-3">
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">គម្របទំព័រដើម</p>
        <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950">{{ $portalContent->banner_image_path ? '1' : '0' }}</p>
        <p class="mt-3 text-sm text-slate-500">រូបបដាដែលប្រើលើទំព័រដើមសាធារណៈ `/`។</p>
    </article>
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">មាតិកាទំព័រដើម</p>
        <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950">3</p>
        <p class="mt-3 text-sm text-slate-500">ផ្លាក ចំណងជើង ពិពណ៌នា និងអត្ថបទកាតសម្រាប់ទំព័រសាធារណៈ។</p>
    </article>
    <article class="dashboard-mini-card p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">តំណភ្ជាប់វេបសាយ</p>
        <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950">{{ $stats['totalCourses'] }}</p>
        <p class="mt-3 text-sm text-slate-500">ការចុះឈ្មោះវគ្គសិក្សានៅតែអាចចូលប្រើបានតាមកាតលើទំព័រដើម។</p>
    </article>
</section>

<section class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
    <div>
        @include('admin.sections.portal-content')
    </div>

    <div class="space-y-6">
        <section class="dashboard-surface p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">វេបសាយសាធារណៈ</p>
            <h3 class="mt-2 text-[1.9rem] font-semibold tracking-tight text-slate-950">គ្រប់គ្រងទំព័រដើមវេបសាយ</h3>
            <div class="mt-5 space-y-4">
                <a href="{{ route('portal.home') }}" class="block rounded-[24px] border border-slate-200 bg-[#f8fafc] px-5 py-5 transition hover:bg-white">
                    <p class="text-sm font-semibold text-slate-900">មើលទំព័រដើមសាធារណៈ</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">បើកទំព័រពិតនៅ `/` ហើយពិនិត្យគម្រប ចំណងជើង និងកាតបច្ចុប្បន្ន។</p>
                </a>
                <a href="{{ route('admin.home', ['section' => 'course-template']) }}" class="block rounded-[24px] border border-slate-200 bg-[#f8fafc] px-5 py-5 transition hover:bg-white">
                    <p class="text-sm font-semibold text-slate-900">គំរូទម្រង់វគ្គសិក្សា</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">រចនា ឬកែប្រែគំរូទំព័រចុះឈ្មោះវគ្គសិក្សាសាធារណៈ។</p>
                </a>
                <a href="{{ route('admin.home', ['section' => 'test-taking-staff-template']) }}" class="block rounded-[24px] border border-slate-200 bg-[#f8fafc] px-5 py-5 transition hover:bg-white">
                    <p class="text-sm font-semibold text-slate-900">គំរូបុគ្គលិកសាកល្បង</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">រចនា ឬកែប្រែគំរូទំព័របុគ្គលិកសាកល្បងសាធារណៈ។</p>
                </a>
                <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="block rounded-[24px] border border-slate-200 bg-[#f8fafc] px-5 py-5 transition hover:bg-white">
                    <p class="text-sm font-semibold text-slate-900">ការចុះឈ្មោះសិក្ខាកាម</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">ត្រឡប់ទៅជួរការចុះឈ្មោះ និងដំណើរការពិនិត្យ។</p>
                </a>
                <a href="{{ route('admin.home', ['section' => 'documents']) }}" class="block rounded-[24px] border border-slate-200 bg-[#f8fafc] px-5 py-5 transition hover:bg-white">
                    <p class="text-sm font-semibold text-slate-900">គ្រប់គ្រងឯកសារ</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">រក្សាបញ្ជីត្រួតពិនិត្យទម្រង់សាធារណៈឲ្យស្របតាមរចនា និងដំណើរការរបស់អ្នក។</p>
                </a>
            </div>
        </section>

        <section class="dashboard-surface p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">ប្រព័ន្ធ</p>
            <h3 class="mt-2 text-[1.7rem] font-semibold tracking-tight text-slate-950">ប្រវត្តិរូបអ្នកគ្រប់គ្រង</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">បើអ្នកត្រូវគ្រប់គ្រងគណនីផ្ទាំងអ្នកគ្រប់គ្រងជំនួសគំរូសាធារណៈ។</p>
            <a href="{{ route('admin.home', ['section' => 'profile']) }}" class="mt-5 inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">បើកប្រវត្តិរូប</a>
        </section>
    </div>
</section>
