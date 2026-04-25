{{-- ==================== Modal Container ==================== --}}
<div
    id="tt-doc-modal-backdrop"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
>
    <div
        id="tt-doc-modal-panel"
        class="relative w-full max-w-md scale-95 rounded-2xl bg-white p-6 opacity-0 shadow-2xl shadow-slate-900/20 transition-all duration-200"
        style="will-change: transform, opacity;"
    >
        <div id="tt-doc-modal-loader" class="flex flex-col items-center justify-center gap-3 py-12">
            <svg class="h-8 w-8 animate-spin text-[#356AE6]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-sm text-slate-500">កំពុងផ្ទុក...</p>
        </div>
        <div id="tt-doc-modal-content" class="hidden"></div>
    </div>
</div>

<section class="dashboard-surface overflow-hidden p-6 sm:p-7">
    <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">កាតាឡុក ៣</p>
            <h3 class="mt-2 text-[2rem] font-semibold tracking-tight text-slate-950">បញ្ជីឯកសារបុគ្គលិកសាកល្បង</h3>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-500">គ្រប់គ្រងបញ្ជីឯកសារដែលបង្ហាញលើទម្រង់ចុះឈ្មោះសាធារណៈរបស់បុគ្គលិកសាកល្បង។</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                <span class="font-semibold text-slate-900">{{ $testTakingStaffDocumentRequirements->count() }}</span> តម្រូវការសរុប
            </div>
            <button
                type="button"
                data-tt-doc-modal-open="{{ route('test-taking-staff-document-requirements.create') }}"
                class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(53,106,230,0.22)] transition hover:bg-[#204ec7]"
            >
                បន្ថែមឯកសារ
            </button>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="admin-data-table min-w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <th class="px-6 py-4">ឈ្មោះជាភាសាខ្មែរ</th>


                        <th class="px-6 py-4">លំដាប់</th>
                        <th class="px-6 py-4">ស្ថានភាព</th>
                        <th class="px-6 py-4 text-right">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($testTakingStaffDocumentRequirements as $documentRequirement)
                        <tr class="border-t border-slate-100 text-sm text-slate-700 transition hover:bg-slate-50/70">
                            <td class="px-6 py-5 font-semibold text-slate-950" data-label="ឈ្មោះជាភាសាខ្មែរ" data-table-primary>{{ $documentRequirement->name_kh }}</td>


                            <td class="px-6 py-5 font-medium text-slate-900" data-label="លំដាប់">{{ $documentRequirement->sort_order }}</td>
                            <td class="px-6 py-5" data-label="ស្ថានភាព">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $documentRequirement->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $documentRequirement->is_active ? 'សកម្ម' : 'មិនសកម្ម' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap" data-label="សកម្មភាព" data-table-actions>
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        data-tt-doc-modal-open="{{ route('test-taking-staff-document-requirements.edit', $documentRequirement) }}"
                                        class="inline-flex shrink-0 items-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        កែប្រែ
                                    </button>
                                    <form method="POST" action="{{ route('test-taking-staff-document-requirements.destroy', $documentRequirement) }}" data-swal-confirm data-swal-title="បញ្ជាក់ការលុប" data-swal-text="តើអ្នកពិតជាចង់លុបតម្រូវការឯកសារនេះមែនទេ?" style="display:contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex shrink-0 items-center rounded-xl bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                            លុប
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-sm text-slate-500">មិនមានតម្រូវការឯកសារបុគ្គលិកសាកល្បងទេ។</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ==================== Modal JS ==================== --}}
<script>
(function () {
    var backdrop = document.getElementById('tt-doc-modal-backdrop');
    var panel    = document.getElementById('tt-doc-modal-panel');
    var loader   = document.getElementById('tt-doc-modal-loader');
    var content  = document.getElementById('tt-doc-modal-content');

    function openModal(url) {
        content.classList.add('hidden');
        content.innerHTML = '';
        loader.classList.remove('hidden');

        backdrop.classList.remove('hidden');
        backdrop.classList.add('flex');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(function () {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });

        window.axios.get(url, { headers: { Accept: 'text/html' } })
            .then(function (res) {
                content.innerHTML = res.data;
                loader.classList.add('hidden');
                content.classList.remove('hidden');
                bindModalContent();
                if (typeof window.initAdminActionFlows === 'function') {
                    window.initAdminActionFlows();
                }
            })
            .catch(function () {
                closeModal();
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'មានបញ្ហា',
                        text: 'មិនអាចផ្ទុកទម្រង់បានទេ។ សូមព្យាយាមម្តងទៀត។',
                        confirmButtonText: 'បិទ',
                        confirmButtonColor: '#356AE6',
                    });
                }
            });
    }

    function closeModal() {
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        setTimeout(function () {
            backdrop.classList.add('hidden');
            backdrop.classList.remove('flex');
            document.body.style.overflow = '';
            content.innerHTML = '';
            content.classList.add('hidden');
            loader.classList.remove('hidden');
        }, 180);
    }

    function bindModalContent() {
        content.querySelectorAll('[data-tt-doc-modal-close]').forEach(function (btn) {
            btn.addEventListener('click', closeModal);
        });
    }

    document.querySelectorAll('[data-tt-doc-modal-open]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            openModal(trigger.dataset.ttDocModalOpen);
        });
    });

    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !backdrop.classList.contains('hidden')) closeModal();
    });
})();
</script>
