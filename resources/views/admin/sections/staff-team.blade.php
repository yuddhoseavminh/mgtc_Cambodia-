@php
    $teamCards = [
        [
            'label' => 'បុគ្គលិកសរុប',
            'value' => $teamStaffMembers->total(),
            'meta' => 'កំណត់ត្រាបុគ្គលិកទាំងអស់ក្នុងប្រព័ន្ធ',
            'tone' => 'bg-sky-50 text-sky-700 ring-sky-100',
        ],
        [
            'label' => 'អ្នកប្រើប្រាស់គ្រប់គ្រង',
            'value' => $stats['adminTeamUsers'],
            'meta' => 'គណនីដែលមានសិទ្ធិអ្នកគ្រប់គ្រង',
            'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        ],
        [
            'label' => 'កំពុងរង់ចាំ',
            'value' => $stats['pendingApplications'],
            'meta' => 'ពាក្យស្នើសុំដែលត្រូវពិនិត្យ',
            'tone' => 'bg-amber-50 text-amber-700 ring-amber-100',
        ],
        [
            'label' => 'បានអនុម័ត',
            'value' => $stats['approvedApplications'],
            'meta' => 'ពាក្យស្នើសុំដែលបានអនុម័តរួច',
            'tone' => 'bg-violet-50 text-violet-700 ring-violet-100',
        ],
    ];
@endphp

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($teamCards as $card)
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
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">ក្រុមការងារសំខាន់</p>
            <h3 class="mt-2 text-[1.6rem] font-semibold tracking-tight text-slate-950">សមាជិកក្រុមការងារ</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">មើលព័ត៌មានបុគ្គលិក និងកែប្រែបានលឿនតាមរយៈកាតសង្ខេប។</p>
        </div>
        <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
            មើលបញ្ជីទាំងអស់
        </a>
    </div>

    @if ($staffTeamPreview->isNotEmpty())
        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($staffTeamPreview as $staff)
                <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5 transition hover:shadow-[0_16px_32px_rgba(15,23,42,0.08)]">
                    <div class="flex items-start gap-4">
                        @if ($staff->hasStoredAvatar())
                            <img src="{{ route('team-staff.avatar', $staff) }}" alt="{{ $staff->name_latin }}" class="h-14 w-14 rounded-full object-cover ring-1 ring-slate-200">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-base font-bold text-white">
                                {{ strtoupper(substr($staff->name_latin ?: $staff->name_kh, 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-base font-semibold text-slate-950">{{ $staff->name_kh }}</p>
                                    <p class="truncate text-sm text-slate-500">{{ $staff->name_latin }}</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">{{ $staff->role }}</span>
                            </div>

                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">មុខតំណែង</span>
                                    <span class="truncate font-medium text-slate-700">{{ $staff->position }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">ឋានន្តរសក្តិ</span>
                                    <span class="truncate font-medium text-slate-700">{{ $staff->military_rank }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('team-staff.show', $staff) }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            មើល
                        </a>
                        <a href="{{ route('team-staff.edit', $staff) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            កែប្រែ
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
        @if ($staffTeamPreview->hasPages())
            <div class="mt-6 border-t border-slate-100 pt-6">
                {{ $staffTeamPreview->links() }}
            </div>
        @endif
    @else
        <div class="mt-6 rounded-[1.7rem] border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">មិនមានទិន្នន័យ</p>
            <h4 class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">មិនទាន់មានសមាជិកក្រុមការងារ</h4>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                សូមបន្ថែមបុគ្គលិកថ្មី ដើម្បីបង្ហាញកាតបុគ្គលិកនៅផ្នែកនេះ។
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('team-staff.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#1d4ed8]">
                    បន្ថែមបុគ្គលិកថ្មី
                </a>
                <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    ចូលទៅគ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣
                </a>
            </div>
        </div>
    @endif
</section>
