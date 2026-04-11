<section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">គ្រប់គ្រងបុគ្គលិកសាកល្បង</h3>
        <p class="mt-2 text-sm text-slate-500">គ្រប់គ្រងការដាក់ស្នើសាធារណៈដែលទទួលបានពីទម្រង់ចុះឈ្មោះបុគ្គលិកសាកល្បង។</p>
    </div>

    <a href="{{ route('admin.home', ['section' => 'test-taking-staff-template']) }}" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(53,106,230,0.22)] transition hover:bg-[#204ec7]">
        គំរូរចនា
    </a>
</section>

<section class="dashboard-surface p-5 sm:p-6">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-wrap gap-3">
            <span class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-600">ការដាក់ស្នើសាធារណៈ</span>
            <button type="submit" form="register-staff-filter-form" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                អនុវត្ត
            </button>
        </div>

        <form id="register-staff-filter-form" method="GET" action="{{ route('admin.home') }}" class="grid w-full min-w-0 gap-3 md:grid-cols-2 xl:grid-cols-[minmax(240px,1fr)_220px_auto]" data-auto-submit-form data-auto-submit-delay="350">
            <input type="hidden" name="section" value="register-staff">
            <input type="text" name="register_staff_search" value="{{ $registerStaffFilters['search'] }}" placeholder="ស្វែងរកការដាក់ស្នើ" class="form-input h-12 min-w-0 bg-[#f8fafc]" data-auto-submit-input>
            <select name="register_staff_rank" class="form-input h-12 min-w-0 bg-[#f8fafc]" data-auto-submit-select>
                <option value="">ឋានន្តរស័ក្តិទាំងអស់</option>
                @foreach ($testTakingStaffRanks as $rank)
                    <option value="{{ $rank->id }}" @selected((string) $registerStaffFilters['rank'] === (string) $rank->id)>{{ $rank->name_kh ?: $rank->name_en }}</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#356AE6] px-5 text-sm font-semibold text-white transition hover:bg-[#204ec7] md:col-span-2 xl:col-span-1">
                ស្វែងរក
            </button>
        </form>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="admin-data-table min-w-full text-left">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                    <th class="px-4 py-4">រូបភាព</th>
                    <th class="px-4 py-4">គោត្តនាម-នាម</th>
                    <th class="px-4 py-4">ឈ្មោះឡាតាំង</th>
                    <th class="px-4 py-4">ឋានន្តរស័ក្តិ</th>
                    <th class="px-4 py-4">លេខទូរស័ព្ទ</th>
                    <th class="px-4 py-4">ថ្ងៃខែឆ្នាំកំណើត</th>
                    <th class="px-4 py-4">ថ្ងៃបម្រើយោធា</th>
                    <th class="px-4 py-4">ឯកសារ</th>
                    <th class="px-4 py-4">ថ្ងៃដាក់ស្នើ</th>
                    <th class="px-4 py-4 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($testTakingStaffRegistrations as $registration)
                    <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                        <td class="px-4 py-5" data-label="រូបភាព" data-table-primary>
                            @if ($registration->hasStoredAvatar())
                                <button
                                    type="button"
                                    class="inline-flex cursor-pointer rounded-full transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#356AE6]/30"
                                    data-avatar-preview-trigger
                                    data-avatar-preview-url="{{ route('test-taking-staff-registrations.avatar', $registration) }}"
                                    data-avatar-preview-name="{{ $registration->name_latin }}"
                                    title="ចុចដើម្បីមើលរូបភាពធំ"
                                    aria-label="មើលរូបភាពធំសម្រាប់ {{ $registration->name_latin }}"
                                >
                                    <img src="{{ route('test-taking-staff-registrations.avatar', $registration) }}" alt="{{ $registration->name_latin }}" class="h-12 w-12 rounded-full object-cover ring-1 ring-slate-200">
                                </button>
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700">
                                    {{ strtoupper(substr($registration->name_latin ?: $registration->name_kh, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-5 font-semibold text-slate-950" data-label="ឈ្មោះខ្មែរ">{{ $registration->name_kh }}</td>
                        <td class="px-4 py-5" data-label="ឈ្មោះឡាតាំង">{{ $registration->name_latin }}</td>
                        <td class="px-4 py-5" data-label="ឋានន្តរស័ក្តិ">
                            {{ $registration->rank?->name_kh ?? '-' }}
                            @if ($registration->rank?->name_en)
                                <div class="text-xs text-slate-400">{{ $registration->rank->name_en }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-5" data-label="លេខទូរស័ព្ទ">{{ $registration->phone_number }}</td>
                        <td class="px-4 py-5" data-label="ថ្ងៃខែកំណើត">{{ optional($registration->date_of_birth)?->khFormat('d/m/Y') ?: '-' }}</td>
                        <td class="px-4 py-5" data-label="ថ្ងៃបម្រើយោធា">{{ optional($registration->military_service_day)?->khFormat('d/m/Y') ?: '-' }}</td>
                        <td class="px-4 py-5" data-label="ឯកសារ">
                            @if ($registration->documents->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($registration->documents as $document)
                                        <a href="{{ route('test-taking-staff-registrations.documents.download', [$registration, $document]) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                                            {{ $document->documentRequirement?->name_kh ?? 'ឯកសារ' }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-400">មិនមានឯកសារ</span>
                            @endif
                        </td>
                        <td class="px-4 py-5" data-label="ថ្ងៃដាក់ស្នើ">{{ optional($registration->submitted_at ?? $registration->created_at)?->khFormat('d/m/Y H:i') ?: '-' }}</td>
                        <td class="px-4 py-5" data-label="សកម្មភាព" data-table-actions>
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.test-taking-staff-registrations.show', $registration) }}" class="inline-flex items-center rounded-xl px-2 py-2 text-sm font-semibold text-[#356AE6] transition hover:text-[#204ec7]">
                                    មើល
                                </a>
                                <a href="{{ route('admin.test-taking-staff-registrations.edit', $registration) }}" class="inline-flex items-center rounded-xl px-2 py-2 text-sm font-semibold text-slate-600 transition hover:text-slate-900">
                                    កែប្រែ
                                </a>
                                <form method="POST" action="{{ route('admin.test-taking-staff-registrations.destroy', $registration) }}" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបកំណត់ត្រានេះមែនទេ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-xl px-2 py-2 text-sm font-semibold text-rose-500 transition hover:text-rose-700">
                                        លុប
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-16 text-center text-sm text-slate-500">មិនមានកំណត់ត្រាបុគ្គលិកចុះឈ្មោះដែលត្រូវនឹងតម្រងបច្ចុប្បន្នទេ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5 flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">
            បង្ហាញ {{ $testTakingStaffRegistrations->firstItem() ?? 0 }}-{{ $testTakingStaffRegistrations->lastItem() ?? 0 }} នៃ {{ $testTakingStaffRegistrations->total() }} កំណត់ត្រាបុគ្គលិកចុះឈ្មោះ
        </p>
        @if ($testTakingStaffRegistrations->hasPages())
            <div>
                {{ $testTakingStaffRegistrations->links() }}
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
