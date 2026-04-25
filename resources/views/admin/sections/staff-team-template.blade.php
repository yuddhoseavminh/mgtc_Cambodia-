<section class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
    <section class="dashboard-surface p-6">
        <div class="border-b border-slate-200 pb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">គំរូបុគ្គលិកក្រុមការងារទី៣</p>
            <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">កែប្រែ UI ទំព័រប្រវត្តិរូបបុគ្គលិក</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">កែប្រែចំណងជើង ឡូហ្គោ និងអត្ថបទក្បាលសម្រាប់ `http://127.0.0.1:8000/staff/profile`។</p>
        </div>

        <div class="mt-6">
            @include('admin.sections.portal-content', [
                'showDesignTabs' => false,
                'forcedTemplateTab' => 'staff',
                'redirectSection' => 'staff-team-template',
            ])
        </div>
    </section>

    <div class="space-y-6">
        <section class="dashboard-surface p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">មើលជាមុន</p>
            <h3 class="mt-2 text-[1.7rem] font-semibold tracking-tight text-slate-950">បើកទំព័របុគ្គលិក</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">បើកផ្ទាំងចូលរបស់បុគ្គលិក ហើយបន្តទៅ `/staff/profile` ដើម្បីពិនិត្យលទ្ធផលជាក់ស្តែង។</p>
            <a href="{{ route('staff.login') }}" class="mt-5 inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                បើកផ្ទាំងចូលបុគ្គលិក
            </a>
        </section>

        <section class="dashboard-surface p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">អ្វីដែលអាចកែ</p>
            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                <p>1. ឡូហ្គោក្បាលទំព័រ</p>
                <p>2. ចំណងជើងសំខាន់</p>
                <p>3. ចំណងជើងរងក្រោមចំណងជើងមេ</p>
            </div>
        </section>
    </div>
</section>
