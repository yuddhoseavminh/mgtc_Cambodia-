@extends('app')

@section('body')
    @php
        $details = [
            'គោត្តនាម-នាម' => $registration->name_kh,
            'ឈ្មោះឡាតាំង' => $registration->name_latin,
            'ឋានន្តរស័ក្តិ' => $registration->rank?->name_kh,
            'ថ្ងៃខែឆ្នាំកំណើត' => optional($registration->date_of_birth)?->khFormat('d/m/Y'),
            'ថ្ងៃចូលបម្រើ' => optional($registration->military_service_day)?->khFormat('d/m/Y'),
            'លេខទូរស័ព្ទ' => $registration->phone_number,
            'ថ្ងៃដាក់ស្នើ' => optional($registration->submitted_at ?? $registration->created_at)?->khFormat('d/m/Y H:i'),
        ];
        
        $documentGroups = collect($registration->documents)
            ->groupBy(fn ($doc) => $doc->documentRequirement?->name_kh ?? 'ឯកសារ')
            ->map(fn ($documents, $label) => [
                'label' => $label,
                'files' => $documents->values()->all(),
            ])
            ->values();
            
        $previewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        $imagePreviewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-[calc(100vh-3.5rem)] lg:grid-cols-[312px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'register-staff'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => 'ព័ត៌មានបុគ្គលិកចុះឈ្មោះ',
                        'subtitle' => 'អ្នកគ្រប់គ្រង',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.95fr]">
                            <div class="space-y-6">
                                <div class="dashboard-surface p-6">
                                    <div class="flex flex-wrap items-center gap-4">
                                        @if ($registration->hasStoredAvatar())
                                            <img src="{{ route('test-taking-staff-registrations.avatar', $registration) }}" alt="Avatar" class="h-24 w-24 rounded-full object-cover shadow-sm ring-1 ring-slate-200">
                                        @else
                                            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 text-3xl font-bold text-slate-400">
                                                {{ strtoupper(substr($registration->name_latin ?: $registration->name_kh, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">គ្រប់គ្រងបុគ្គលិកចុះឈ្មោះ</p>
                                            <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $registration->name_kh }}</h3>
                                            <p class="mt-2 text-sm text-slate-500">{{ $registration->name_latin }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                                        @foreach ($details as $label => $value)
                                            <div class="dashboard-soft-surface px-4 py-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $label }}</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $value ?: '-' }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="dashboard-surface p-6">
                                    <h3 class="text-2xl font-semibold tracking-tight text-slate-950">រាល់ឯកសារភ្ជាប់</h3>
                                    <div class="mt-6 grid gap-4">
                                        @forelse ($documentGroups as $documentGroup)
                                            <div class="dashboard-soft-surface px-4 py-4">
                                                <p class="text-lg font-semibold text-slate-900">{{ $documentGroup['label'] }}</p>
                                                <div class="mt-4 space-y-3">
                                                    @foreach ($documentGroup['files'] as $document)
                                                        @php
                                                            $extension = strtolower(pathinfo($document->original_name ?? '', PATHINFO_EXTENSION));
                                                            $canPreviewInline = in_array($extension, $previewableExtensions, true);
                                                            $previewKind = in_array($extension, $imagePreviewableExtensions, true) ? 'image' : ($extension === 'pdf' ? 'pdf' : 'other');
                                                        @endphp
                                                        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 lg:flex-row lg:items-center lg:justify-between">
                                                            <p class="min-w-0 text-sm text-slate-600">{{ $document->original_name ?? basename($document->file_path) }}</p>
                                                            <div class="flex flex-wrap items-center gap-3">
                                                                <button
                                                                    type="button"
                                                                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                                                    data-document-preview-trigger
                                                                    data-preview-url="{{ route('test-taking-staff-registrations.documents.show', [$registration, $document]) }}"
                                                                    data-download-url="{{ route('test-taking-staff-registrations.documents.download', [$registration, $document]) }}"
                                                                    data-document-name="{{ $document->original_name ?? basename($document->file_path) }}"
                                                                    data-preview-supported="{{ $canPreviewInline ? 'true' : 'false' }}"
                                                                    data-preview-kind="{{ $previewKind }}"
                                                                >
                                                                    មើល
                                                                </button>
                                                                <a href="{{ route('test-taking-staff-registrations.documents.download', [$registration, $document]) }}" class="inline-flex items-center rounded-xl bg-[#356AE6] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#204ec7]">ទាញយក</a>
                                                                <form method="POST" action="{{ route('test-taking-staff-registrations.documents.update', [$registration, $document]) }}" enctype="multipart/form-data" class="flex" data-auto-submit-form>
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <label class="inline-flex cursor-pointer items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                                                        <span>កែប្រែ</span>
                                                                        <input type="file" name="document_file" class="hidden" onchange="this.form.submit()" accept=".pdf,.jpg,.jpeg,.png,.webp">
                                                                    </label>
                                                                </form>
                                                                <form method="POST" action="{{ route('test-taking-staff-registrations.documents.destroy', [$registration, $document]) }}" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបឯកសារមែនទេ?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex items-center rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">លុប</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-sm text-slate-500">មិនមានឯកសារភ្ជាប់ទេ។</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="dashboard-surface p-6">
                                    <div class="flex flex-col gap-3">
                                        <a href="{{ route('admin.test-taking-staff-registrations.edit', $registration) }}" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                                            កែប្រែព័ត៌មានបុគ្គលិក
                                        </a>
                                        <form method="POST" action="{{ route('admin.test-taking-staff-registrations.destroy', $registration) }}" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបមែនទឬ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-rose-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-600">លុបកំណត់ត្រា</button>
                                        </form>
                                        
                                        <a href="{{ route('admin.home', ['section' => 'register-staff']) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-[#f8fafc] px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">ត្រឡប់ទៅកម្មវិធីគ្រប់គ្រងបុគ្គលិក</a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>
    
    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/55 px-4 py-6 backdrop-blur-sm" data-document-preview-modal aria-hidden="true">
        <div class="absolute inset-0" data-document-preview-close></div>
        <div class="relative z-10 flex h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl" data-document-preview-panel>
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">មើលឯកសារ</p>
                    <p class="mt-1 truncate text-sm font-semibold text-slate-900 sm:text-base" data-document-preview-name>-</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="#" target="_blank" rel="noreferrer" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" data-document-preview-open>បើក</a>
                    <a href="#" class="inline-flex items-center rounded-full bg-[#356AE6] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#204ec7]" data-document-preview-download>ទាញយក</a>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700" data-document-preview-close aria-label="បិទការមើល">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"></path>
                            </svg>
                    </button>
                </div>
            </div>
            <div class="flex min-h-0 flex-1 flex-col bg-slate-50 p-4 sm:p-5">
                <p class="hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" data-document-preview-note>
                    ប្រភេទឯកសារនេះអាចមិនអាចមើលក្នុងផ្ទាំងលេចឡើងបានទេ។ សូមប្រើ បើក ឬ ទាញយក ប្រសិនបើផ្ទៃមើលនៅទទេ។
                </p>
                <div class="mt-4 hidden min-h-0 flex-1 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white" data-document-preview-image-wrapper>
                    <img src="" alt="ការមើលឯកសារ" class="max-h-full max-w-full object-contain" data-document-preview-image>
                </div>
                <iframe src="about:blank" class="mt-4 hidden min-h-0 min-w-0 flex-1 w-full rounded-2xl border border-slate-200 bg-white" data-document-preview-frame title="ការមើលឯកសារ"></iframe>
            </div>
        </div>
    </div>
    
    <script>
        (() => {
            const modal = document.querySelector('[data-document-preview-modal]');
            if (!modal) return;

            const frame = modal.querySelector('[data-document-preview-frame]');
            const imageWrapper = modal.querySelector('[data-document-preview-image-wrapper]');
            const image = modal.querySelector('[data-document-preview-image]');
            const name = modal.querySelector('[data-document-preview-name]');
            const openLink = modal.querySelector('[data-document-preview-open]');
            const downloadLink = modal.querySelector('[data-document-preview-download]');
            const note = modal.querySelector('[data-document-preview-note]');
            const closeButton = modal.querySelector('button[data-document-preview-close]');
            let previousFocusedElement = null;

            const updatePreviewNote = (shouldShow) => note.classList.toggle('hidden', !shouldShow);

            const showPreviewFallback = () => {
                image.src = '';
                frame.src = 'about:blank';
                imageWrapper.classList.add('hidden');
                imageWrapper.classList.remove('flex');
                frame.classList.add('hidden');
                frame.classList.remove('block');
                updatePreviewNote(true);
            };

            const setPreviewMode = (previewKind, previewUrl) => {
                const isImage = previewKind === 'image';
                imageWrapper.classList.toggle('hidden', !isImage);
                imageWrapper.classList.toggle('flex', isImage);
                frame.classList.toggle('hidden', isImage);
                frame.classList.toggle('block', !isImage);

                if (isImage) {
                    image.src = previewUrl || '';
                    frame.src = 'about:blank';
                    return;
                }
                image.src = '';
                frame.src = previewUrl || 'about:blank';
            };

            const openModal = ({ previewUrl, downloadUrl, documentName, previewSupported, previewKind }) => {
                previousFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
                name.textContent = documentName || 'ការមើលឯកសារ';
                setPreviewMode(previewKind, previewUrl);
                openLink.href = previewUrl || '#';
                downloadLink.href = downloadUrl || '#';
                updatePreviewNote(!previewSupported);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
                closeButton?.focus();
            };

            const closeModal = () => {
                if (document.activeElement instanceof HTMLElement && modal.contains(document.activeElement)) {
                    document.activeElement.blur();
                }
                setPreviewMode('other', 'about:blank');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                previousFocusedElement?.focus();
            };

            image.addEventListener('error', () => {
                if (modal.getAttribute('aria-hidden') === 'false') showPreviewFallback();
            });

            frame.addEventListener('error', () => {
                if (modal.getAttribute('aria-hidden') === 'false') showPreviewFallback();
            });

            document.querySelectorAll('[data-document-preview-trigger]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    openModal({
                        previewUrl: trigger.dataset.previewUrl,
                        downloadUrl: trigger.dataset.downloadUrl,
                        documentName: trigger.dataset.documentName,
                        previewSupported: trigger.dataset.previewSupported === 'true',
                        previewKind: trigger.dataset.previewKind || 'other',
                    });
                });
            });

            modal.querySelectorAll('[data-document-preview-close]').forEach((element) => {
                element.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') closeModal();
            });
        })();
    </script>
@endsection
