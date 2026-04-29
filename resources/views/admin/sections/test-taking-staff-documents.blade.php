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
            <p class="text-sm text-slate-500">áž€áŸ†áž–áž»áž„áž•áŸ’áž‘áž»áž€...</p>
        </div>
        <div id="tt-doc-modal-content" class="hidden"></div>
    </div>
</div>

<section class="dashboard-surface overflow-hidden p-6 sm:p-7">
    <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">áž€áž¶ážáž¶áž¡áž»áž€ áŸ£</p>
            <h3 class="mt-2 text-[2rem] font-semibold tracking-tight text-slate-950">áž”áž‰áŸ’áž‡áž¸áž¯áž€ážŸáž¶ážšáž”áž»áž‚áŸ’áž‚áž›áž·áž€ážŸáž¶áž€áž›áŸ’áž”áž„</h3>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-500">áž‚áŸ’ážšáž”áŸ‹áž‚áŸ’ážšáž„áž”áž‰áŸ’áž‡áž¸áž¯áž€ážŸáž¶ážšážŠáŸ‚áž›áž”áž„áŸ’áž áž¶áž‰áž›áž¾áž‘áž˜áŸ’ážšáž„áŸ‹áž…áž»áŸ‡ážˆáŸ’áž˜áŸ„áŸ‡ážŸáž¶áž’áž¶ážšážŽáŸˆážšáž”ážŸáŸ‹áž”áž»áž‚áŸ’áž‚áž›áž·áž€ážŸáž¶áž€áž›áŸ’áž”áž„áŸ”</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                <span class="font-semibold text-slate-900">{{ $testTakingStaffDocumentRequirements->count() }}</span> ážáž˜áŸ’ážšáž¼ážœáž€áž¶ážšážŸážšáž»áž”
            </div>
            <button
                type="button"
                data-tt-doc-modal-open="{{ route('test-taking-staff-document-requirements.create') }}"
                class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(53,106,230,0.22)] transition hover:bg-[#204ec7]"
            >
                áž”áž“áŸ’ážáŸ‚áž˜áž¯áž€ážŸáž¶ážš
            </button>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="admin-data-table min-w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <th class="px-6 py-4">ážˆáŸ’áž˜áŸ„áŸ‡áž‡áž¶áž—áž¶ážŸáž¶ážáŸ’áž˜áŸ‚ážš</th>


                        <th class="px-6 py-4">áž›áŸ†ážŠáž¶áž”áŸ‹</th>
                        <th class="px-6 py-4">ážŸáŸ’ážáž¶áž“áž—áž¶áž–</th>
                        <th class="px-6 py-4">Telegram</th>
                        <th class="px-6 py-4 text-right">ážŸáž€áž˜áŸ’áž˜áž—áž¶áž–</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($testTakingStaffDocumentRequirements as $documentRequirement)
                        <tr class="border-t border-slate-100 text-sm text-slate-700 transition hover:bg-slate-50/70">
                            <td class="px-6 py-5 font-semibold text-slate-950" data-label="ážˆáŸ’áž˜áŸ„áŸ‡áž‡áž¶áž—áž¶ážŸáž¶ážáŸ’áž˜áŸ‚ážš" data-table-primary>{{ $documentRequirement->name_kh }}</td>


                            <td class="px-6 py-5 font-medium text-slate-900" data-label="áž›áŸ†ážŠáž¶áž”áŸ‹">{{ $documentRequirement->sort_order }}</td>
                            <td class="px-6 py-5" data-label="ážŸáŸ’ážáž¶áž“áž—áž¶áž–">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $documentRequirement->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $documentRequirement->is_active ? 'ážŸáž€áž˜áŸ’áž˜' : 'áž˜áž·áž“ážŸáž€áž˜áŸ’áž˜' }}
                                </span>
                            </td>
                            <td class="px-6 py-5" data-label="Telegram">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $documentRequirement->send_to_telegram ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $documentRequirement->send_to_telegram ? 'Send' : 'Skip' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap" data-label="ážŸáž€áž˜áŸ’áž˜áž—áž¶áž–" data-table-actions>
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        data-tt-doc-modal-open="{{ route('test-taking-staff-document-requirements.edit', $documentRequirement) }}"
                                        class="inline-flex shrink-0 items-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        áž€áŸ‚áž”áŸ’ážšáŸ‚
                                    </button>
                                    <form method="POST" action="{{ route('test-taking-staff-document-requirements.destroy', $documentRequirement) }}" data-swal-confirm data-swal-title="áž”áž‰áŸ’áž‡áž¶áž€áŸ‹áž€áž¶ážšáž›áž»áž”" data-swal-text="ážáž¾áž¢áŸ’áž“áž€áž–áž·ážáž‡áž¶áž…áž„áŸ‹áž›áž»áž”ážáž˜áŸ’ážšáž¼ážœáž€áž¶ážšáž¯áž€ážŸáž¶ážšáž“áŸáŸ‡áž˜áŸ‚áž“áž‘áŸ?" style="display:contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex shrink-0 items-center rounded-xl bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                            áž›áž»áž”
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">áž˜áž·áž“áž˜áž¶áž“ážáž˜áŸ’ážšáž¼ážœáž€áž¶ážšáž¯áž€ážŸáž¶ážšáž”áž»áž‚áŸ’áž‚áž›áž·áž€ážŸáž¶áž€áž›áŸ’áž”áž„áž‘áŸáŸ”</td>
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
                        title: 'áž˜áž¶áž“áž”áž‰áŸ’áž áž¶',
                        text: 'áž˜áž·áž“áž¢áž¶áž…áž•áŸ’áž‘áž»áž€áž‘áž˜áŸ’ážšáž„áŸ‹áž”áž¶áž“áž‘áŸáŸ” ážŸáž¼áž˜áž–áŸ’áž™áž¶áž™áž¶áž˜áž˜áŸ’ážáž„áž‘áŸ€ážáŸ”',
                        confirmButtonText: 'áž”áž·áž‘',
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
