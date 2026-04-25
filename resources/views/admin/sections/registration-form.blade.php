@php
    $statusLabels = [
        'Pending' => 'រង់ចាំ',
        'Reviewed' => 'បានពិនិត្យ',
        'Approved' => 'អនុម័ត',
        'Rejected' => 'បដិសេធ',
    ];

    $memberCards = [
        [
            'label' => 'ការស្នើសុំសរុប',
            'value' => $applications->total(),
            'meta' => 'កំណត់ត្រាចុះឈ្មោះសិក្ខាកាមដែលត្រូវនឹងទិន្នន័យបច្ចុប្បន្ន',
            'tone' => 'bg-sky-50 text-sky-700 ring-sky-100',
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

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($memberCards as $card)
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
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">ការស្នើសុំថ្មីៗ</p>
            <h3 class="mt-2 text-[1.6rem] font-semibold tracking-tight text-slate-950">សមាជិកចុះឈ្មោះសិក្ខាកាម</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">មើលព័ត៌មានសិក្ខាកាម និងកែប្រែបានលឿនតាមរយៈកាតសង្ខេប។</p>
        </div>
        <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
            មើលបញ្ជីទាំងអស់
        </a>
    </div>

    @if ($applications->isNotEmpty())
        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($applications as $application)
                @php
                    $initialSource = trim((string) ($application->latin_name ?: $application->khmer_name ?: 'S'));
                    $initialParts = preg_split('/\s+/', $initialSource, -1, PREG_SPLIT_NO_EMPTY);
                    $initial = collect($initialParts)
                        ->take(2)
                        ->map(fn ($part) => mb_substr($part, 0, 1))
                        ->implode('');
                    $initial = mb_strtoupper($initial ?: mb_substr($initialSource, 0, 1));
                    $rankName = $application->rank?->name_kh ?: $application->rank?->name_en;
                @endphp
                <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5 transition hover:shadow-[0_16px_32px_rgba(15,23,42,0.08)]">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-base font-bold uppercase text-white">
                            {{ $initial }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-base font-semibold text-slate-950">{{ $application->khmer_name }}</p>
                                    <p class="truncate text-sm text-slate-500">{{ $application->latin_name ?: '-' }}</p>
                                </div>
                                <span class="max-w-[8rem] truncate rounded-full px-3 py-1 text-[11px] font-semibold {{ $statusClasses[$application->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200' }}">
                                    {{ $statusLabels[$application->status] ?? $application->status }}
                                </span>
                            </div>

                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">មុខតំណែង</span>
                                    <span class="truncate font-medium text-slate-700">{{ $application->position ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">វគ្គសិក្សា</span>
                                    <span class="truncate font-medium text-slate-700">{{ $application->course?->name ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-400">ឋានន្តរស័ក្តិ</span>
                                    <span class="truncate font-medium text-slate-700">{{ $rankName ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('admin.applications.show', $application) }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            មើល
                        </a>
                        <a href="{{ route('admin.applications.edit', $application) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            កែប្រែ
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
        @if ($applications->hasPages())
            <div class="mt-6 border-t border-slate-100 pt-6">
                {{ $applications->links() }}
            </div>
        @endif
    @else
        <div class="mt-6 rounded-[1.7rem] border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">មិនមានទិន្នន័យ</p>
            <h4 class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">មិនទាន់មានសមាជិកចុះឈ្មោះសិក្ខាកាម</h4>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                នៅពេលមានពាក្យស្នើសុំថ្មី កាតសមាជិកនឹងបង្ហាញនៅផ្នែកនេះ។
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('registration.form') }}" class="inline-flex items-center justify-center rounded-2xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#1d4ed8]">
                    បើកទម្រង់ចុះឈ្មោះ
                </a>
                <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    ចូលទៅគ្រប់គ្រងពាក្យស្នើសុំ
                </a>
            </div>
        </div>
    @endif
</section>
