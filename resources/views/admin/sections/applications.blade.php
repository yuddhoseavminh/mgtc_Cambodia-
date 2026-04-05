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
@endphp

<section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">ការចុះឈ្មោះសិក្ខាកាម</h3>
        <p class="mt-2 text-sm text-slate-500">គ្រប់គ្រងកំណត់ត្រាអ្នកដាក់ពាក្យ ស្ថានភាពអនុម័ត និងសកម្មភាពពិនិត្យ។</p>
    </div>

    <a href="{{ route('admin.home', ['section' => 'design-template']) }}" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(53,106,230,0.22)] transition hover:bg-[#204ec7]">
        គំរូរចនា
    </a>
</section>

<section class="dashboard-surface p-5 sm:p-6">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-wrap gap-3">
            <span class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-600">សកម្មភាពជាក្រុម</span>
            <button type="submit" form="applications-filter-form" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                អនុវត្ត
            </button>
        </div>

        <form id="applications-filter-form" method="GET" action="{{ route('admin.home') }}" class="grid w-full min-w-0 gap-3 md:grid-cols-2 xl:grid-cols-[minmax(220px,1.3fr)_repeat(3,minmax(0,0.82fr))_auto]" data-auto-submit-form data-auto-submit-delay="350">
            <input type="hidden" name="section" value="applications">
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="ស្វែងរកពាក្យស្នើសុំ" class="form-input min-w-0 bg-[#f8fafc]" data-auto-submit-input>
            <select name="status" class="form-input min-w-0 bg-[#f8fafc]" data-auto-submit-select>
                <option value="">ស្ថានភាពទាំងអស់</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status] ?? $status }}</option>
                @endforeach
            </select>
            <select name="rank" class="form-input min-w-0 bg-[#f8fafc]" data-auto-submit-select>
                <option value="">ឋានន្តរស័ក្តិទាំងអស់</option>
                @foreach ($ranks as $rank)
                    <option value="{{ $rank->id }}" @selected((string) $filters['rank'] === (string) $rank->id)>{{ $rank->name_kh }}</option>
                @endforeach
            </select>
            <select name="course" class="form-input min-w-0 bg-[#f8fafc]" data-auto-submit-select>
                <option value="">វគ្គសិក្សាទាំងអស់</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected((string) ($filters['course'] ?? '') === (string) $course->id)>{{ $course->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7] md:col-span-2 xl:col-span-1">
                ស្វែងរក
            </button>
        </form>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="admin-data-table min-w-full text-left">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                    <th class="px-4 py-4">ល.រ</th>
                    <th class="px-4 py-4">ឋានន្តរស័ក្តិ</th>
                    <th class="px-4 py-4">គោត្តនាម-នាម</th>
                    <th class="px-4 py-4">ឈ្មោះឡាតាំង</th>
                    <th class="px-4 py-4">ភេទ</th>
                    <th class="px-4 py-4">ថ្ងៃខែឆ្នាំកំណើត</th>
                    <th class="px-4 py-4">ថ្ងៃចូលបម្រើ</th>
                    <th class="px-4 py-4">អត្តលេខ</th>
                    <th class="px-4 py-4">មុខតំណែង</th>
                    <th class="px-4 py-4">អង្គភាព</th>
                    <th class="px-4 py-4">ស្ថានភាព</th>
                    <th class="px-4 py-4 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                        <td class="px-4 py-5 font-semibold text-slate-950" data-label="ល.រ" data-table-primary>
                            {{ strtr((string) (($applications->firstItem() ?? 1) + $loop->index), $khmerDigits) }}
                        </td>
                        <td class="px-4 py-5" data-label="ឋានន្តរស័ក្តិ">{{ $application->rank?->name_kh ?? '-' }}</td>
                        <td class="px-4 py-5" data-label="ឈ្មោះខ្មែរ">{{ $application->khmer_name }}</td>
                        <td class="px-4 py-5" data-label="ឈ្មោះឡាតាំង">{{ $application->latin_name }}</td>
                        <td class="px-4 py-5" data-label="ភេទ">{{ $genderLabels[$application->gender] ?? $application->gender ?? '-' }}</td>
                        <td class="px-4 py-5" data-label="ថ្ងៃខែកំណើត">{{ optional($application->date_of_birth)?->khFormat('d/m/Y') }}</td>
                        <td class="px-4 py-5" data-label="ថ្ងៃចូលបម្រើ">{{ optional($application->date_of_enlistment)?->khFormat('d/m/Y') }}</td>
                        <td class="px-4 py-5" data-label="លេខអត្តសញ្ញាណ">{{ strtr((string) $application->id_number, $khmerDigits) }}</td>
                        <td class="px-4 py-5" data-label="តួនាទី">{{ $application->position ?? '-' }}</td>
                        <td class="px-4 py-5" data-label="អង្គភាព">{{ $application->unit ?? '-' }}</td>
                        <td class="px-4 py-5" data-label="ស្ថានភាព">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$application->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200' }}">
                                {{ $statusLabels[$application->status] ?? $application->status }}
                            </span>
                        </td>
                        <td class="px-4 py-5" data-label="សកម្មភាព" data-table-actions>
                            <div class="flex justify-end gap-2">
                                @if ($application->status !== 'Approved')
                                    <form method="POST" action="{{ route('admin.applications.update', $application) }}" data-ajax-form data-ajax-redirect="{{ route('admin.home', ['section' => 'applications']) }}" data-ajax-success-title="áž‡áŸ„áž‚აჟ‡áŸაჟ™" data-ajax-success-text="បានអនុម័តពាក្យស្នើសុំដោយជោគជ័យ។">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Approved">
                                        <input type="hidden" name="admin_notes" value="{{ $application->admin_notes }}">
                                        <button type="submit" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                                            អនុម័ត
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.applications.show', $application) }}" class="inline-flex items-center rounded-xl px-2 py-2 text-sm font-semibold text-[#356AE6] transition hover:text-[#204ec7]">
                                    មើល
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-16 text-center text-sm text-slate-500">មិនមានកំណត់ត្រាចុះឈ្មោះដែលត្រូវនឹងតម្រងបច្ចុប្បន្នទេ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
