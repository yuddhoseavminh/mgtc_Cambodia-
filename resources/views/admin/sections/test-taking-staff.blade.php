@php
    $opsCards = [
        ['label' => 'ឋានន្តរស័ក្តិសាធារណៈ', 'value' => $stats['totalTestTakingStaffRanks'], 'meta' => 'ជម្រើសខ្មែរ និងអង់គ្លេស'],
        ['label' => 'ឯកសារសាធារណៈ', 'value' => $stats['totalTestTakingStaffDocuments'], 'meta' => 'ធាតុបញ្ជីត្រួតពិនិត្យរបស់អ្នកដាក់ពាក្យ'],
        ['label' => 'ទម្រង់ដែលបានដាក់', 'value' => $stats['totalTestTakingStaffRegistrations'], 'meta' => 'ការចុះឈ្មោះសាធារណៈដែលទទួលបាន'],
    ];
@endphp

<section class="grid gap-4 xl:grid-cols-[1.35fr_1fr]">
    <article class="overflow-hidden rounded-[30px] bg-[linear-gradient(135deg,#3b0764,#9333ea,#ec4899)] p-8 text-white shadow-[0_28px_80px_rgba(88,28,135,0.18)]">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/65">កាតាឡុក ៣</p>
        <h3 class="mt-4 text-[2.2rem] font-semibold tracking-tight">ប្រតិបត្តិការបុគ្គលិកសាកល្បង</h3>
        <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80">កំណត់បទពិសោធន៍សាធារណៈសម្រាប់បុគ្គលិកសាកល្បង ដោយគ្រប់គ្រងបញ្ជីឋានន្តរស័ក្តិ និងបញ្ជីឯកសារដែលបង្ហាញលើទម្រង់ចុះគោត្តនាម-នាម។</p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('admin.home', ['section' => 'test-taking-staff-ranks']) }}" class="inline-flex items-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">គ្រប់គ្រងបញ្ជីឋានន្តរស័ក្តិ</a>
            <a href="{{ route('admin.home', ['section' => 'test-taking-staff-documents']) }}" class="inline-flex items-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15">គ្រប់គ្រងបញ្ជីឯកសារ</a>
        </div>
    </article>

    <div class="grid gap-4">
        @foreach ($opsCards as $card)
            <article class="dashboard-mini-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
                <p class="mt-3 text-sm text-slate-500">{{ $card['meta'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="grid gap-6 xl:grid-cols-2">
    <div class="dashboard-surface p-6">
        <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">ចំណុចផ្តោតការងារ</h3>
        <div class="mt-5 space-y-4">
            <div class="rounded-[24px] border border-slate-200 bg-[#f8fafc] p-5">
                <p class="text-sm font-semibold text-slate-900">ការសម្របសម្រួលតុចុះឈ្មោះ</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">រក្សាទម្រង់ខ្មែរសាធារណៈឲ្យស្របជាមួយជម្រើសឋានន្តរស័ក្តិ និងតម្រូវការឯកសារចុងក្រោយ។</p>
            </div>
            <div class="rounded-[24px] border border-slate-200 bg-[#f8fafc] p-5">
                <p class="text-sm font-semibold text-slate-900">ការត្រៀមខ្លួនសម្រាប់ថ្ងៃប្រឡង</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">ពិនិត្យការចុះឈ្មោះបុគ្គលិកដែលចូលមក និងធ្វើបច្ចុប្បន្នភាពបញ្ជីសាធារណៈរាល់ពេលតម្រូវការផ្លាស់ប្តូរ។</p>
            </div>
        </div>
    </div>

    <div class="dashboard-surface p-6">
        <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950">ការរុករករហ័ស</h3>
        <div class="mt-5 grid gap-3">
            <a href="{{ route('admin.home', ['section' => 'test-taking-staff-ranks']) }}" class="rounded-[22px] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">បញ្ជីឋានន្តរស័ក្តិ</a>
            <a href="{{ route('admin.home', ['section' => 'test-taking-staff-documents']) }}" class="rounded-[22px] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">បញ្ជីឯកសារ</a>
            <a href="{{ route('admin.home', ['section' => 'register-staff']) }}" class="rounded-[22px] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">គណនីបុគ្គលិកចុះឈ្មោះ</a>
        </div>
    </div>
</section>
