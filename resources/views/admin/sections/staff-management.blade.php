<section class="dashboard-surface overflow-hidden p-6 sm:p-7">
    <div class="flex flex-col gap-6 border-b border-slate-200 pb-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">បុគ្គលិកក្រុមការងារទី៣ទី៣</p>
                <h3 class="mt-2 text-[2rem] font-semibold tracking-tight text-slate-950">គ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣</h3>
                <p class="mt-3 text-sm leading-7 text-slate-500">បង្កើត មើល កែប្រែ និងគ្រប់គ្រងកំណត់ត្រាបុគ្គលិកជាមួយរូបភាព តួនាទី និងឯកសារភ្ជាប់។</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                    <span class="font-semibold text-slate-900">{{ $teamStaffMembers->total() }}</span>&nbsp;កំណត់ត្រាបុគ្គលិក
                </div>
                <a href="{{ route('admin.home', ['section' => 'staff-team-ranks']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    គ្រប់គ្រងឋានន្តរស័ក្តិ
                </a>
                <a href="{{ route('team-staff.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-[0_16px_32px_rgba(15,23,42,0.14)] transition hover:bg-slate-800">
                    បន្ថែមបុគ្គលិកថ្មី
                </a>
            </div>
        </div>

        <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
            <form
                id="team-staff-filter-form"
                method="GET"
                action="{{ route('admin.home') }}"
                class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between xl:gap-6"
                data-auto-submit-form
                data-auto-submit-delay="350"
            >
                <input type="hidden" name="section" value="staff-management">
                <div class="relative min-w-0 xl:max-w-[580px] xl:flex-1">
                    <span class="pointer-events-none absolute left-4 top-1/2 z-10 -translate-y-1/2 text-slate-400">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path stroke-linecap="round" d="m20 20-3.5-3.5"></path>
                        </svg>
                    </span>
                    <input
                        id="staff-search"
                        type="text"
                        name="staff_search"
                        value="{{ $teamStaffFilters['search'] }}"
                        placeholder="ស្វែងរកតាមអត្តលេខ ឬឈ្មោះ"
                        aria-label="ស្វែងរកបុគ្គលិកតាមអត្តលេខ ឬឈ្មោះ"
                        class="form-input h-12 w-full min-w-0 rounded-2xl border-slate-200 bg-white pr-4 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[#356AE6] focus:ring-4 focus:ring-[#356AE6]/10"
                        style="padding-left: 2.75rem;"
                        data-auto-submit-input
                    >
                </div>
                <div class="flex min-w-0 flex-col gap-3 xl:ml-auto xl:w-auto xl:flex-row xl:items-center xl:justify-end">
                    <select name="staff_gender" class="form-input h-12 w-full min-w-0 bg-white xl:w-[160px]" data-auto-submit-select>
                        <option value="">ភេទទាំងអស់</option>
                        @php
                            $genderMapLocal = [
                                'Male'   => 'ប្រុស',
                                'Female' => 'ស្រី',
                                'Other'  => 'ផ្សេងទៀត',
                            ];
                        @endphp
                        @foreach ($teamStaffGenders as $gender)
                            <option value="{{ $gender }}" @selected($teamStaffFilters['gender'] === $gender)>
                                {{ $genderMapLocal[$gender] ?? $gender }}
                            </option>
                        @endforeach
                    </select>
                    <select name="staff_role" class="form-input h-12 w-full min-w-0 bg-white xl:w-[160px]" data-auto-submit-select>
                        <option value="">តួនាទីទាំងអស់</option>
                        @foreach ($teamStaffRoles as $role)
                            <option value="{{ $role }}" @selected($teamStaffFilters['role'] === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    <select name="staff_position" class="form-input h-12 w-full min-w-0 bg-white xl:w-[180px]" data-auto-submit-select>
                        <option value="">មុខតំណែងទាំងអស់</option>
                        @foreach ($teamStaffPositions as $position)
                            <option value="{{ $position }}" @selected($teamStaffFilters['position'] === $position)>{{ $position }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex h-12 min-w-[110px] items-center justify-center rounded-2xl bg-[#356AE6] px-5 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                        ស្វែងរក
                    </button>
                    <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex h-12 items-center justify-center whitespace-nowrap rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
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
                        <th class="whitespace-nowrap px-6 py-4">រូបភាព</th>
                        <th class="whitespace-nowrap px-6 py-4">គោត្តនាម-នាម</th>
                        <th class="whitespace-nowrap px-6 py-4">ឈ្មោះឡាតាំង</th>
                        <th class="whitespace-nowrap px-6 py-4">អត្តលេខ</th>
                        <th class="whitespace-nowrap px-6 py-4">ឋានន្តរស័ក្តិយោធា</th>
                        <th class="whitespace-nowrap px-6 py-4">ថ្ងៃខែឆ្នាំកំណើត</th>
                        <th class="whitespace-nowrap px-6 py-4">ថ្ងៃចូលបម្រើកងទ័ព</th>
                        <th class="whitespace-nowrap px-6 py-4">ភេទ</th>
                        <th class="whitespace-nowrap px-6 py-4">មុខតំណែង</th>
                        <th class="whitespace-nowrap px-6 py-4">លេខទូរស័ព្ទ</th>
                        <th class="whitespace-nowrap px-6 py-4">តួនាទី</th>
                        <th class="whitespace-nowrap px-6 py-4 text-right">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $staffBadgeClasses = [
                            'bg-sky-100 text-sky-700 ring-1 ring-inset ring-sky-200',
                            'bg-emerald-100 text-emerald-700 ring-1 ring-inset ring-emerald-200',
                            'bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200',
                            'bg-violet-100 text-violet-700 ring-1 ring-inset ring-violet-200',
                            'bg-rose-100 text-rose-700 ring-1 ring-inset ring-rose-200',
                            'bg-cyan-100 text-cyan-700 ring-1 ring-inset ring-cyan-200',
                            'bg-lime-100 text-lime-700 ring-1 ring-inset ring-lime-200',
                            'bg-fuchsia-100 text-fuchsia-700 ring-1 ring-inset ring-fuchsia-200',
                        ];

                        $genderMap = [
                            'Male'   => 'ប្រុស',
                            'Female' => 'ស្រី',
                            'Other'  => 'ផ្សេងទៀត',
                        ];

                        $khmerMonths = [
                            1 => 'មករា', 2 => 'កុម្ភៈ', 3 => 'មីនា', 4 => 'មេសា', 
                            5 => 'ឧសភា', 6 => 'មិថុនា', 7 => 'កក្កដា', 8 => 'សីហា', 
                            9 => 'កញ្ញា', 10 => 'តុលា', 11 => 'វិច្ឆិកា', 12 => 'ធ្នូ'
                        ];

                        $formatKhmerDate = static function ($date) use ($khmerMonths): string {
                            if (!$date instanceof \DateTimeInterface) return (string) ($date ?: '-');
                            
                            $day = $date->format('d');
                            $month = (int) $date->format('m');
                            $year = $date->format('Y');

                            return "{$day} {$khmerMonths[$month]} {$year}";
                        };

                        $resolveStaffBadgeClass = static function (?string $value) use ($staffBadgeClasses): string {
                            $value = trim((string) $value);

                            if ($value === '') {
                                return 'bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200';
                            }

                            return $staffBadgeClasses[(int) sprintf('%u', crc32($value)) % count($staffBadgeClasses)];
                        };
                    @endphp

                    @forelse ($teamStaffMembers as $staff)
                        @php
                            $militaryRankBadgeClass = $resolveStaffBadgeClass($staff->military_rank);
                            $roleBadgeClass = $resolveStaffBadgeClass($staff->role);
                        @endphp
                        <tr class="border-t border-slate-100 text-sm text-slate-700 transition hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-6 py-5 font-semibold text-slate-950" data-label="ល.រ" data-table-primary>{{ $staff->sequence_no }}</td>
                            <td class="whitespace-nowrap px-6 py-5" data-label="រូបភាព">
                                @if ($staff->hasStoredAvatar())
                                    <button
                                        type="button"
                                        class="inline-flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full cursor-zoom-in transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#356AE6]/30"
                                        data-avatar-preview-trigger
                                        data-avatar-preview-url="{{ route('team-staff.avatar', $staff) }}"
                                        data-avatar-preview-name="{{ $staff->name_latin }}"
                                        title="ចុចដើម្បីមើលរូបភាពធំ"
                                        aria-label="មើលរូបភាពធំសម្រាប់ {{ $staff->name_latin }}"
                                    >
                                        <img src="{{ route('team-staff.avatar', $staff) }}" alt="{{ $staff->name_latin }}" class="h-full w-full rounded-full object-cover ring-1 ring-slate-200">
                                    </button>
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700 ring-1 ring-slate-200">
                                        {{ strtoupper(substr($staff->name_latin ?: $staff->name_kh, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 font-semibold text-slate-950" data-label="ឈ្មោះខ្មែរ">{{ $staff->name_kh }}</td>
                            <td class="whitespace-nowrap px-6 py-5" data-label="ឈ្មោះឡាតាំង">{{ $staff->name_latin }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="លេខអត្តសញ្ញាណ">{{ $staff->id_number }}</td>
                            <td class="whitespace-nowrap px-6 py-5" data-label="ឋានន្តរស័ក្តិ">
                                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $militaryRankBadgeClass }}">
                                    {{ $staff->military_rank ?: '-' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="ថ្ងៃខែឆ្នាំកំណើត">{{ $formatKhmerDate($staff->dob) }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="ថ្ងៃចូលបម្រើកងទ័ព">{{ $formatKhmerDate($staff->date_of_enlistment) }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="ភេទ">{{ $genderMap[$staff->gender] ?? $staff->gender }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-slate-500" data-label="តួនាទី">{{ $staff->position }}</td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm font-medium text-slate-600" data-label="លេខទូរស័ព្ទ">{{ $staff->phone_number }}</td>
                            <td class="whitespace-nowrap px-6 py-5" data-label="សិទ្ធិ">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold tracking-wide {{ $roleBadgeClass }}">{{ $staff->role }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-right" data-label="សកម្មភាព" data-table-actions>
                                <div class="flex flex-nowrap items-center justify-end gap-2">
                                    <a href="{{ route('team-staff.show', $staff) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 shadow-sm">មើល</a>
                                    <a href="{{ route('team-staff.edit', $staff) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 shadow-sm">កែប្រែ</a>
                                    <form method="POST" action="{{ route('team-staff.destroy', $staff) }}" class="m-0" data-swal-confirm data-swal-title="បញ្ជាក់ការលុប" data-swal-text="តើអ្នកពិតជាចង់លុបកំណត់ត្រាបុគ្គលិកនេះមែនទេ?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-xl bg-orange-50 px-3.5 py-2 text-xs font-semibold text-orange-600 transition hover:bg-orange-100 hover:text-orange-700 shadow-sm">លុប</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-20">
                                <div class="mx-auto flex max-w-md flex-col items-center text-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="8.5" cy="7" r="4"></circle>
                                            <path d="M20 8v6"></path>
                                            <path d="M23 11h-6"></path>
                                        </svg>
                                    </div>
                                    <h4 class="mt-5 text-lg font-semibold text-slate-950">មិនមានកំណត់ត្រាបុគ្គលិកទេ</h4>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">មិនមានកំណត់ត្រាបុគ្គលិកក្រុមការងារទី៣ ដែលត្រូវនឹងតម្រងបច្ចុប្បន្នទេ។ បង្កើតកំណត់ត្រាថ្មីដើម្បីចាប់ផ្តើមបញ្ជីក្រុមរបស់អ្នក។</p>
                                    <a href="{{ route('team-staff.create') }}" class="mt-5 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                        បន្ថែមបុគ្គលិកថ្មី
                                    </a>
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
            បង្ហាញ {{ $teamStaffMembers->firstItem() ?? 0 }}-{{ $teamStaffMembers->lastItem() ?? 0 }} នៃ {{ $teamStaffMembers->total() }} កំណត់ត្រាបុគ្គលិក
        </p>
        @if ($teamStaffMembers->hasPages())
            <div>
                {{ $teamStaffMembers->links() }}
            </div>
        @endif
    </div>
</section>

<div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm" data-avatar-preview-modal aria-hidden="true">
    <div class="absolute inset-0" data-avatar-preview-close></div>
    <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl" data-avatar-preview-panel>
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">មើលរូបភាព</p>
                <p class="mt-1 truncate text-sm font-semibold text-slate-900 sm:text-base" data-avatar-preview-name>-</p>
                <p class="mt-1 text-xs text-slate-500">ចុចក្រៅផ្ទៃមើល ឬចុច `Esc` ដើម្បីបិទ។</p>
            </div>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700" data-avatar-preview-close aria-label="បិទការមើលរូបភាព">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        <div class="bg-slate-50 p-5 sm:p-6">
            <div class="flex items-center justify-center overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white p-4">
                <img src="" alt="ការមើលរូបភាព" class="max-h-[70vh] w-full rounded-[1.25rem] object-contain" data-avatar-preview-image>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="#" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100" data-avatar-preview-link>
                    បើករូបភាពពេញ
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const filterForm = document.querySelector('[data-auto-submit-form]');

        if (filterForm && filterForm.dataset.autoSubmitReady !== 'true') {
            filterForm.dataset.autoSubmitReady = 'true';

            const delay = Number(filterForm.dataset.autoSubmitDelay || 350);
            let timerId = null;

            const submitForm = () => {
                if (timerId) {
                    window.clearTimeout(timerId);
                }

                filterForm.requestSubmit();
            };

            filterForm.querySelectorAll('[data-auto-submit-input]').forEach((input) => {
                input.addEventListener('input', () => {
                    if (timerId) {
                        window.clearTimeout(timerId);
                    }

                    timerId = window.setTimeout(submitForm, delay);
                });
            });

            filterForm.querySelectorAll('[data-auto-submit-select]').forEach((select) => {
                select.addEventListener('change', submitForm);
            });
        }

        const modal = document.querySelector('[data-avatar-preview-modal]');

        if (!modal) {
            return;
        }

        const image = modal.querySelector('[data-avatar-preview-image]');
        const name = modal.querySelector('[data-avatar-preview-name]');
        const link = modal.querySelector('[data-avatar-preview-link]');

        const openModal = ({ previewUrl, previewName }) => {
            image.src = previewUrl || '';
            name.textContent = previewName || 'ការមើលរូបភាព';
            link.href = previewUrl || '#';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        };

        const closeModal = () => {
            image.src = '';
            link.href = '#';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        };

        document.querySelectorAll('[data-avatar-preview-trigger]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                openModal({
                    previewUrl: trigger.dataset.avatarPreviewUrl,
                    previewName: trigger.dataset.avatarPreviewName,
                });
            });
        });

        modal.querySelectorAll('[data-avatar-preview-close]').forEach((element) => {
            element.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                closeModal();
            }
        });
    })();
</script>
