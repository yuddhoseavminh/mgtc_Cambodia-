@php
    $khmerDigits = [
        '0' => '០',
        '1' => '១',
        '2' => '២',
        '3' => '៣',
        '4' => '៤',
        '5' => '៥',
        '6' => '៦',
        '7' => '៧',
        '8' => '៨',
        '9' => '៩',
    ];

    $statusLabels = [
        'Pending' => 'រង់ចាំ',
        'Reviewed' => 'បានពិនិត្យ',
        'Approved' => 'អនុម័ត',
        'Rejected' => 'បដិសេធ',
    ];

    $genderLabels = [
        'Male' => 'ប្រុស',
        'Female' => 'ស្រី',
        'Other' => 'ផ្សេងៗ',
    ];
    $recentApplicationsList = $recentApplications ?? collect();

    $applicationCards = [
        [
            'label' => 'ពាក្យស្នើសុំសរុប',
            'value' => $stats['totalApplicants'],
            'meta' => 'កំណត់ត្រាចុះឈ្មោះសិក្ខាកាមទាំងអស់ក្នុងប្រព័ន្ធ',
            'tone' => 'bg-sky-50 text-sky-700 ring-sky-100',
        ],
        [
            'label' => 'កំពុងរង់ចាំ',
            'value' => $stats['pendingApplications'],
            'meta' => 'ពាក្យស្នើសុំដែលត្រូវការការពិនិត្យ និងសម្រេចចិត្ត',
            'tone' => 'bg-amber-50 text-amber-700 ring-amber-100',
        ],
        [
            'label' => 'បានអនុម័ត',
            'value' => $stats['approvedApplications'],
            'meta' => 'បេក្ខជនដែលបានទទួលការអនុម័តសម្រាប់វគ្គសិក្សា',
            'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        ],
        [
            'label' => 'ចុះឈ្មោះខែនេះ',
            'value' => $stats['currentMonthApplications'],
            'meta' => 'ពាក្យស្នើសុំដែលបានទទួលក្នុងខែបច្ចុប្បន្ន',
            'tone' => 'bg-violet-50 text-violet-700 ring-violet-100',
        ],
    ];

    $applicationBadgeClasses = [
        'bg-sky-100 text-sky-700 ring-1 ring-inset ring-sky-200',
        'bg-emerald-100 text-emerald-700 ring-1 ring-inset ring-emerald-200',
        'bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200',
        'bg-violet-100 text-violet-700 ring-1 ring-inset ring-violet-200',
        'bg-rose-100 text-rose-700 ring-1 ring-inset ring-rose-200',
        'bg-cyan-100 text-cyan-700 ring-1 ring-inset ring-cyan-200',
        'bg-lime-100 text-lime-700 ring-1 ring-inset ring-lime-200',
        'bg-fuchsia-100 text-fuchsia-700 ring-1 ring-inset ring-fuchsia-200',
    ];

    $resolveApplicationBadgeClass = static function (?string $value) use ($applicationBadgeClasses): string {
        $value = trim((string) $value);

        if ($value === '') {
            return 'bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200';
        }

        return $applicationBadgeClasses[(int) sprintf('%u', crc32($value)) % count($applicationBadgeClasses)];
    };
@endphp





<section class="dashboard-surface mt-6 overflow-hidden p-6 sm:p-7">
    <div class="flex flex-col gap-6 border-b border-slate-200 pb-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ការចុះឈ្មោះសិក្ខាកាម</p>
                <h3 class="mt-2 text-[2rem] font-semibold tracking-tight text-slate-950">គ្រប់គ្រងពាក្យស្នើសុំចុះឈ្មោះ</h3>
                <p class="mt-3 text-sm leading-7 text-slate-500">មើល កែប្រែ អនុម័ត និងគ្រប់គ្រងកំណត់ត្រាសិក្ខាកាមតាមរចនាប័ទ្មដូចផ្នែកបុគ្គលិកសាកល្បង។</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                    <span class="font-semibold text-slate-900">{{ $applications->total() }}</span>&nbsp;ពាក្យស្នើសុំ
                </div>
                <a href="{{ route('registration.form') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    បើកទម្រង់
                </a>
                <a href="{{ route('admin.home', ['section' => 'design-template']) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-[0_16px_32px_rgba(15,23,42,0.14)] transition hover:bg-slate-800">
                    គំរូរចនា
                </a>
            </div>
        </div>

        <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
            <form
                id="applications-filter-form"
                method="GET"
                action="{{ route('admin.home') }}"
                class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between xl:gap-6"
                data-auto-submit-form
                data-auto-submit-delay="350"
            >
                <input type="hidden" name="section" value="applications">
                <div class="relative min-w-0 xl:max-w-[580px] xl:flex-1">
                    <span class="pointer-events-none absolute left-4 top-1/2 z-10 -translate-y-1/2 text-slate-400">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path stroke-linecap="round" d="m20 20-3.5-3.5"></path>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="ស្វែងរកតាមឈ្មោះ អត្តលេខ លេខទូរស័ព្ទ ឬអង្គភាព"
                        aria-label="ស្វែងរកពាក្យស្នើសុំចុះឈ្មោះសិក្ខាកាម"
                        class="form-input h-12 w-full min-w-0 rounded-2xl border-slate-200 bg-white pr-4 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[#356AE6] focus:ring-4 focus:ring-[#356AE6]/10"
                        style="padding-left: 2.75rem;"
                        data-auto-submit-input
                    >
                </div>

                <div class="flex min-w-0 flex-col gap-3 xl:ml-auto xl:w-auto xl:flex-row xl:items-center xl:justify-end">
                    <select name="status" class="form-input h-12 w-full min-w-0 bg-white xl:w-[170px]" data-auto-submit-select>
                        <option value="">ស្ថានភាពទាំងអស់</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status] ?? $status }}</option>
                        @endforeach
                    </select>

                    <select name="rank" class="form-input h-12 w-full min-w-0 bg-white xl:w-[210px]" data-auto-submit-select>
                        <option value="">ឋានន្តរស័ក្តិទាំងអស់</option>
                        @foreach ($ranks as $rank)
                            <option value="{{ $rank->id }}" @selected((string) $filters['rank'] === (string) $rank->id)>{{ $rank->name_kh }}</option>
                        @endforeach
                    </select>

                    <select name="course" class="form-input h-12 w-full min-w-0 bg-white xl:w-[220px]" data-auto-submit-select>
                        <option value="">វគ្គសិក្សាទាំងអស់</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected((string) ($filters['course'] ?? '') === (string) $course->id)>{{ $course->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="inline-flex h-12 min-w-[110px] items-center justify-center rounded-2xl bg-[#356AE6] px-5 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                        ស្វែងរក
                    </button>
                    <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="inline-flex h-12 items-center justify-center whitespace-nowrap rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        កំណត់ឡើងវិញ
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="admin-data-table team-staff-management-table min-w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-[11px] font-semibold uppercase tracking-[0.15em] text-slate-500">
                        <th class="whitespace-nowrap px-6 py-4">ល.រ</th>
                        <th class="whitespace-nowrap px-6 py-4">គោត្តនាម-នាម</th>
                        <th class="whitespace-nowrap px-6 py-4">ឋានន្តរស័ក្តិ</th>
                        <th class="whitespace-nowrap px-6 py-4">វគ្គសិក្សា</th>
                        <th class="whitespace-nowrap px-6 py-4">ភេទ</th>
                        <th class="whitespace-nowrap px-6 py-4">អត្តលេខ</th>
                        <th class="whitespace-nowrap px-6 py-4">មុខតំណែង</th>
                        <th class="whitespace-nowrap px-6 py-4">អង្គភាព</th>
                        <th class="whitespace-nowrap px-6 py-4">ថ្ងៃដាក់ស្នើ</th>
                        <th class="whitespace-nowrap px-6 py-4">ស្ថានភាព</th>
                        <th class="whitespace-nowrap px-6 py-4 text-right">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        @php
                            $rankName = $application->rank?->name_kh ?: $application->rank?->name_en;
                            $rankBadgeClass = $resolveApplicationBadgeClass($rankName);
                            $rowNumber = ($applications->firstItem() ?? 1) + $loop->index;
                        @endphp
                        <tr class="border-t border-slate-100 text-sm text-slate-700 transition hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-6 py-5 font-semibold text-slate-950" data-label="ល.រ" data-table-primary>
                                {{ strtr((string) $rowNumber, $khmerDigits) }}
                            </td>
                            <td class="min-w-[14rem] px-6 py-5" data-label="គោត្តនាម-នាម">
                                <p class="font-semibold text-slate-950">{{ $application->khmer_name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $application->latin_name ?: '-' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5" data-label="ឋានន្តរស័ក្តិ">
                                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $rankBadgeClass }}">
                                    {{ $rankName ?: '-' }}
                                </span>
                            </td>
                            <td class="min-w-[12rem] px-6 py-5 text-slate-600" data-label="វគ្គសិក្សា">{{ $application->course?->name ?: '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="ភេទ">{{ $genderLabels[$application->gender] ?? $application->gender ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="អត្តលេខ">{{ $application->id_number ? strtr((string) $application->id_number, $khmerDigits) : '-' }}</td>
                            <td class="min-w-[10rem] px-6 py-5 text-slate-500" data-label="មុខតំណែង">{{ $application->position ?? '-' }}</td>
                            <td class="min-w-[10rem] px-6 py-5 text-slate-500" data-label="អង្គភាព">{{ $application->unit ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="ថ្ងៃដាក់ស្នើ">{{ optional($application->submitted_at ?? $application->created_at)?->timezone('Asia/Phnom_Penh')->khFormat('d/m/Y h:i A') ?: '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-5" data-label="ស្ថានភាព">
                                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $statusClasses[$application->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200' }}">
                                    {{ $statusLabels[$application->status] ?? $application->status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-right" data-label="សកម្មភាព" data-table-actions>
                                <div class="flex flex-nowrap items-center justify-end gap-2">
                                    <a href="{{ route('admin.applications.show', $application) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900">មើល</a>
                                    <a href="{{ route('admin.applications.edit', $application) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900">កែប្រែ</a>
                                    @if ($application->status !== 'Approved')
                                        <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="m-0" data-ajax-form data-ajax-redirect="{{ route('admin.home', ['section' => 'applications']) }}" data-ajax-success-title="ជោគជ័យ" data-ajax-success-text="បានអនុម័តពាក្យស្នើសុំដោយជោគជ័យ។">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Approved">
                                            <input type="hidden" name="admin_notes" value="{{ $application->admin_notes }}">
                                            <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-xl bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100 hover:text-emerald-800">
                                                អនុម័ត
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.applications.destroy', $application) }}" class="m-0" data-swal-confirm data-swal-title="បញ្ជាក់ការលុប" data-swal-text="តើអ្នកពិតជាចង់លុបពាក្យស្នើសុំនេះមែនទេ?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-xl bg-orange-50 px-3.5 py-2 text-xs font-semibold text-orange-600 shadow-sm transition hover:bg-orange-100 hover:text-orange-700">
                                            លុប
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-20">
                                <div class="mx-auto flex max-w-md flex-col items-center text-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 6h8"></path>
                                            <path d="M8 10h8"></path>
                                            <path d="M8 14h5"></path>
                                            <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                                        </svg>
                                    </div>
                                    <h4 class="mt-5 text-lg font-semibold text-slate-950">មិនមានពាក្យស្នើសុំទេ</h4>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">មិនមានកំណត់ត្រាដែលត្រូវនឹងតម្រងបច្ចុប្បន្នទេ។ សាកល្បងកំណត់តម្រងឡើងវិញ ឬបើកទម្រង់ចុះឈ្មោះសាធារណៈ។</p>
                                    <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                                        <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            កំណត់ឡើងវិញ
                                        </a>
                                        <a href="{{ route('registration.form') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                            បើកទម្រង់ចុះឈ្មោះ
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5 flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">
            បង្ហាញ {{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} នៃ {{ $applications->total() }} ពាក្យស្នើសុំ
        </p>
        @if ($applications->hasPages())
            <div>
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</section>

<script>
    (() => {
        const form = document.querySelector('[data-auto-submit-form]');

        if (!form || form.dataset.autoSubmitReady === 'true') {
            return;
        }

        form.dataset.autoSubmitReady = 'true';

        const delay = Number(form.dataset.autoSubmitDelay || 350);
        let timerId = null;

        const submitForm = () => {
            if (timerId) {
                window.clearTimeout(timerId);
            }

            form.requestSubmit();
        };

        form.querySelectorAll('[data-auto-submit-input]').forEach((input) => {
            input.addEventListener('input', () => {
                if (timerId) {
                    window.clearTimeout(timerId);
                }

                timerId = window.setTimeout(submitForm, delay);
            });
        });

        form.querySelectorAll('[data-auto-submit-select]').forEach((select) => {
            select.addEventListener('change', submitForm);
        });
    })();
</script>
