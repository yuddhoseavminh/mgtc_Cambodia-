{{-- ==================== Modal Container ==================== --}}
<div
    id="course-modal-backdrop"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="course-modal-title"
>
    <div
        id="course-modal-panel"
        class="relative w-full max-w-lg scale-95 rounded-2xl bg-white p-6 opacity-0 shadow-2xl shadow-slate-900/20 transition-all duration-200"
        style="will-change: transform, opacity;"
    >
        {{-- Spinner shown while loading --}}
        <div id="course-modal-loader" class="flex flex-col items-center justify-center gap-3 py-12">
            <svg class="h-8 w-8 animate-spin text-[#356AE6]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-sm text-slate-500">កំពុងផ្ទុក...</p>
        </div>

        {{-- Content injected via AJAX --}}
        <div id="course-modal-content" class="hidden"></div>
    </div>
</div>

{{-- ==================== Header ==================== --}}
<section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">គ្រប់គ្រងវគ្គសិក្សា</h3>
        <p class="mt-2 text-sm text-slate-500">គ្រប់គ្រងកាតាឡុកវគ្គសិក្សា រយៈពេល និងជម្រើសបើកទទួលចុះឈ្មោះ។</p>
    </div>

    <button
        type="button"
        id="course-create-btn"
        data-course-modal-open="{{ route('courses.create') }}"
        class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(53,106,230,0.22)] transition hover:bg-[#204ec7]"
    >
        បន្ថែមវគ្គសិក្សា
    </button>
</section>

{{-- ==================== Table ==================== --}}
<section class="dashboard-surface p-5 sm:p-6">
    <div class="overflow-x-auto">
        <table class="admin-data-table min-w-full text-left">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                    <th class="px-4 py-4">ឈ្មោះវគ្គសិក្សា</th>
                    <th class="px-4 py-4">រយៈពេល</th>
                    <th class="px-4 py-4">ពិពណ៌នា</th>
                    <th class="px-4 py-4">ស្ថានភាព</th>
                    <th class="px-4 py-4 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $course)
                    <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                        <td class="px-4 py-5 font-semibold text-slate-950" data-label="វគ្គសិក្សា" data-table-primary>{{ $course->name }}</td>
                        <td class="px-4 py-5" data-label="រយៈពេល">{{ $course->duration }}</td>
                        <td class="px-4 py-5" data-label="ការពិពណ៌នា">
                            <p class="max-w-xl truncate">{{ $course->description }}</p>
                        </td>
                        <td class="px-4 py-5" data-label="ស្ថានភាព">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $course->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $course->is_active ? 'សកម្ម' : 'មិនសកម្ម' }}
                            </span>
                        </td>
                        <td class="px-4 py-5 whitespace-nowrap" data-label="សកម្មភាព" data-table-actions>
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    data-course-modal-open="{{ route('courses.edit', $course) }}"
                                    class="inline-flex shrink-0 items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                >
                                    កែប្រែ
                                </button>
                                @if ($course->is_protected)
                                    <span class="inline-flex shrink-0 items-center rounded-xl bg-sky-100 px-3 py-2 text-xs font-semibold text-sky-700">
                                        ការពារ
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('courses.destroy', $course) }}" data-swal-confirm data-swal-title="បញ្ជាក់ការលុប" data-swal-text="តើអ្នកពិតជាចង់លុបវគ្គសិក្សានេះមែនទេ?" style="display:contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex shrink-0 items-center rounded-xl bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 transition hover:bg-rose-100">
                                            លុប
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center text-sm text-slate-500">មិនមានវគ្គសិក្សាទេ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- ==================== Course Modal JS ==================== --}}
<script>
(function () {
    var backdrop   = document.getElementById('course-modal-backdrop');
    var panel      = document.getElementById('course-modal-panel');
    var loader     = document.getElementById('course-modal-loader');
    var content    = document.getElementById('course-modal-content');

    function openModal(url) {
        // Reset state
        content.classList.add('hidden');
        content.innerHTML = '';
        loader.classList.remove('hidden');

        // Show backdrop + animate panel in
        backdrop.classList.remove('hidden');
        backdrop.classList.add('flex');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(function () {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });

        // Fetch the form fragment
        window.axios.get(url, { headers: { Accept: 'text/html' } })
            .then(function (res) {
                content.innerHTML = res.data;
                loader.classList.add('hidden');
                content.classList.remove('hidden');
                bindModalContent();
                // Re-run admin action flows on newly injected form
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
        // Close buttons inside the injected form
        content.querySelectorAll('[data-course-modal-close]').forEach(function (btn) {
            btn.addEventListener('click', closeModal);
        });

        // After a successful form submit, close modal and reload to refresh table
        var form = content.querySelector('form');
        if (form) {
            // Override the action-flow reload to also close modal
            form.addEventListener('courseModalSuccess', function () {
                closeModal();
            });
        }
    }

    // Open modal on any [data-course-modal-open] click
    document.querySelectorAll('[data-course-modal-open]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            var url = trigger.dataset.courseModalOpen;
            openModal(url);
        });
    });

    // Close on backdrop click
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) {
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !backdrop.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Intercept form submission inside modal to keep it in-modal via AJAX
    // (The existing initAdminActionFlows() handles this, but we need the reload
    //  to also close the modal first.)
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!content.contains(form)) return;

        // Let the existing action flow handle the AJAX; we hook into its reload
        var originalReload = window.location.reload.bind(window.location);
        // Patch reload once so modal closes before reload
        var patched = false;
        if (!patched) {
            patched = true;
            var reloadDescriptor = Object.getOwnPropertyDescriptor(Location.prototype, 'reload');
            // Instead of patching, we listen for the success Swal close and then reload
            // The action flows already do window.location.reload() after success swal — that's fine.
        }
    }, true);
})();
</script>
