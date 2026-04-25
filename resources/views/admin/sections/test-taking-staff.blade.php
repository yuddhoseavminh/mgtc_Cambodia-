@php
    $testTakingStaffCards = [
        [
            'label' => 'បុគ្គលិកបានចុះឈ្មោះ',
            'value' => $stats['totalTestTakingStaffRegistrations'],
            'meta' => 'កំណត់ត្រាបុគ្គលិកសាកល្បងទាំងអស់ក្នុងប្រព័ន្ធ',
            'tone' => 'bg-sky-50 text-sky-700 ring-sky-100',
        ],
        [
            'label' => 'ឋានន្តរស័ក្តិ',
            'value' => $stats['totalTestTakingStaffRanks'],
            'meta' => 'ជម្រើសឋានន្តរស័ក្តិដែលបង្ហាញលើទម្រង់ចុះឈ្មោះ',
            'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        ],
        [
            'label' => 'ឯកសារតម្រូវ',
            'value' => $stats['totalTestTakingStaffDocuments'],
            'meta' => 'ប្រភេទឯកសារដែលបេក្ខជនត្រូវដាក់ភ្ជាប់',
            'tone' => 'bg-amber-50 text-amber-700 ring-amber-100',
        ],
        [
            'label' => 'ចុះឈ្មោះខែនេះ',
            'value' => $stats['currentMonthTestTakingStaffRegistrations'],
            'meta' => 'កំណត់ត្រាដែលបានទទួលក្នុងខែបច្ចុប្បន្ន',
            'tone' => 'bg-violet-50 text-violet-700 ring-violet-100',
        ],
    ];
@endphp

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($testTakingStaffCards as $card)
        <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_34px_rgba(15,23,42,0.05)]">
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-semibold text-slate-700">{{ $card['label'] }}</p>
                <span class="rounded-full px-3 py-1 text-[11px] font-semibold ring-1 {{ $card['tone'] }}">
                    ស្ថិតិ
                </span>
            </div>
            <p class="mt-5 text-[2rem] font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $card['meta'] }}</p>
        </article>
    @endforeach
</section>

<section class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.06)] sm:p-7">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">បុគ្គលិកសាកល្បង</p>
            <h3 class="mt-2 text-[1.6rem] font-semibold tracking-tight text-slate-950">កំណត់ត្រាចុះឈ្មោះបុគ្គលិកសាកល្បងថ្មីៗ</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">មើលព័ត៌មានបុគ្គលិកសាកល្បង និងបើកទៅកាន់ការគ្រប់គ្រងពេញលេញតាមរចនាប័ទ្មដូចបុគ្គលិកក្រុមការងារ។</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.home', ['section' => 'register-staff']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
                មើលបញ្ជីទាំងអស់
            </a>
            <a href="{{ route('admin.home', ['section' => 'test-taking-staff-template']) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                កែគំរូទម្រង់
            </a>
        </div>
    </div>

    @if ($testTakingStaffPreview->isNotEmpty())
        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($testTakingStaffPreview as $registration)
                @php
                    $avatarUrl = route('test-taking-staff-registrations.avatar', [
                        'testTakingStaffRegistration' => $registration,
                        'v' => md5((string) $registration->avatar_path.'|'.optional($registration->updated_at)->timestamp),
                    ]);
                @endphp
                <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5 transition hover:shadow-[0_16px_32px_rgba(15,23,42,0.08)]">
                    <div class="flex items-start gap-4">
                        @if ($registration->hasStoredAvatar())
                            <img src="{{ $avatarUrl }}" alt="{{ $registration->name_latin }}" class="h-14 w-14 rounded-full object-cover ring-1 ring-slate-200">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-base font-bold text-white">
                                {{ strtoupper(substr($registration->name_latin ?: $registration->name_kh, 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-base font-semibold text-slate-950">{{ $registration->name_kh }}</p>
                                    <p class="truncate text-sm text-slate-500">{{ $registration->name_latin }}</p>
                                </div>
                                <span class="max-w-[8rem] truncate rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">
                                    {{ $registration->rank?->name_kh ?? '-' }}
                                </span>
                            </div>

                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">លេខទូរស័ព្ទ</span>
                                    <span class="truncate font-medium text-slate-700">{{ $registration->phone_number ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">ថ្ងៃបម្រើយោធា</span>
                                    <span class="truncate font-medium text-slate-700">{{ optional($registration->military_service_day)?->khFormat('d/m/Y') ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">ឯកសារ</span>
                                    <span class="truncate font-medium text-slate-700">{{ $registration->documents->count() }} ឯកសារ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('admin.test-taking-staff-registrations.show', $registration) }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            មើល
                        </a>
                        <a href="{{ route('admin.test-taking-staff-registrations.edit', $registration) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            កែប្រែ
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
        @if ($testTakingStaffPreview->hasPages())
            <div class="mt-6 border-t border-slate-100 pt-6">
                {{ $testTakingStaffPreview->links() }}
            </div>
        @endif
    @else
        <div class="mt-6 rounded-[1.7rem] border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">មិនមានទិន្នន័យ</p>
            <h4 class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">មិនទាន់មានបុគ្គលិកសាកល្បង</h4>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                នៅពេលមានការចុះឈ្មោះពីទម្រង់បុគ្គលិកសាកល្បង កាតបុគ្គលិកនឹងបង្ហាញនៅទីនេះ។
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('test-taking-staff.form') }}" class="inline-flex items-center justify-center rounded-2xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#1d4ed8]">
                    បើកទម្រង់ចុះឈ្មោះ
                </a>
                <a href="{{ route('admin.home', ['section' => 'test-taking-staff-ranks']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    គ្រប់គ្រងឋានន្តរស័ក្តិ
                </a>
            </div>
        </div>
    @endif
</section>
