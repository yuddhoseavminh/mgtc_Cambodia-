@extends('app')

@section('body')
    @php
        $portalBanner = \App\Models\PortalContent::query()->first();
        $statusClasses = [
            'Pending' => 'bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200',
            'Reviewed' => 'bg-sky-100 text-sky-700 ring-1 ring-inset ring-sky-200',
            'Approved' => 'bg-emerald-100 text-emerald-700 ring-1 ring-inset ring-emerald-200',
            'Rejected' => 'bg-rose-100 text-rose-700 ring-1 ring-inset ring-rose-200',
        ];
        $statusLabels = [
            'Pending' => 'រង់ចាំ',
            'Reviewed' => 'បានពិនិត្យ',
            'Approved' => 'អនុម័ត',
            'Rejected' => 'បដិសេធ',
        ];
        $details = [
            'គោត្តនាម-នាម' => $application->khmer_name,
            'ឈ្មោះឡាតាំង' => $application->latin_name,
            'អត្តលេខ' => $application->id_number,
            'ឋានន្តរស័ក្តិ' => $application->rank?->name_kh,
            'ថ្ងៃខែឆ្នាំកំណើត' => optional($application->date_of_birth)?->khFormat('d/m/Y'),
            'ថ្ងៃចូលបម្រើកងទ័ព' => optional($application->date_of_enlistment)?->khFormat('d/m/Y'),
            'មុខតំណែង / ភារកិច្ច' => $application->position,
            'អង្គភាព' => $application->unit,
            'វគ្គសិក្សាដាក់ពាក្យ' => $application->course?->name,
            'កម្រិតវប្បធម៌ទូទៅ' => $application->culturalLevel?->name,
            'ទីកន្លែងកំណើត' => config('military-registration.province_labels')[$application->place_of_birth] ?? $application->place_of_birth,
            'ស្ថានភាពគ្រួសារ' => config('military-registration.family_situation_labels')[$application->family_situation] ?? $application->family_situation,
            'លេខទូរស័ព្ទ' => $application->phone_number,
            'ថ្ងៃដាក់ស្នើ' => optional($application->submitted_at)?->khFormat('d/m/Y H:i'),
        ];
    @endphp 
    @php
        $reviewComment = old('admin_notes', $application->admin_notes);
        $documentGroups = collect($application->documents())
            ->groupBy('label')
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
                @include('admin.partials.sidebar', ['section' => 'applications'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => 'ព័ត៌មានលម្អិតពាក្យស្នើសុំ',
                        'subtitle' => 'អ្នកគ្រប់គ្រង',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.95fr]">
                            <div class="space-y-6">
                                {{-- <div class="dashboard-surface overflow-hidden">
                                    @if ($portalBanner?->banner_image_path)
                                        <img src="{{ route('portal.banner-image') }}" alt="រូបគម្របពាក្យស្នើសុំ" class="h-auto w-full object-contain">
                                    @else
                                        <div class="bg-[#f8fafc] px-8 py-10">
                                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">រូបគម្រប</p>
                                            <h2 class="mt-3 text-3xl font-semibold text-slate-950">ព័ត៌មានលម្អិតការចុះឈ្មោះវគ្គសិក្សាយោធា</h2>
                                            <p class="mt-2 text-sm text-slate-500">ផ្ទាំងពិនិត្យសម្រាប់ការទទួលពាក្យ និងអនុម័ត។</p>
                                        </div>
                                    @endif
                                </div> --}}

                                <div class="dashboard-surface p-6">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">ព័ត៌មានអ្នកដាក់ពាក្យ</p>
                                            <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $application->khmer_name }}</h3>
                                            <p class="mt-2 text-sm text-slate-500">{{ $application->latin_name }}</p>
                                        </div>
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$application->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200' }}">
                                            {{ $statusLabels[$application->status] ?? $application->status }}
                                        </span>
                                    </div>

                                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                                        @foreach ($details as $label => $value)
                                            <div class="dashboard-soft-surface px-4 py-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $label }}</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $value ?: '-' }}</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="dashboard-soft-surface mt-4 px-4 py-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">អាសយដ្ឋានបច្ចុប្បន្ន</p>
                                        <p class="mt-2 text-sm leading-7 text-slate-700">{{ $application->current_address }}</p>
                                    </div>
                                </div>

                                <div class="dashboard-surface p-6">
                                    <h3 class="text-2xl font-semibold tracking-tight text-slate-950">មើលឯកសារ</h3>
                                    <div class="mt-6 grid gap-4">
                                        @forelse ($documentGroups as $documentGroup)
                                            <div class="dashboard-soft-surface px-4 py-4">
                                                <p class="text-lg font-semibold text-slate-900">{{ $documentGroup['label'] }}</p>
                                                <div class="mt-4 space-y-3">
                                                    @foreach ($documentGroup['files'] as $document)
                                                        @php
                                                            $extension = strtolower(pathinfo($document['name'] ?? '', PATHINFO_EXTENSION));
                                                            $canPreviewInline = in_array($extension, $previewableExtensions, true);
                                                            $previewKind = in_array($extension, $imagePreviewableExtensions, true) ? 'image' : ($extension === 'pdf' ? 'pdf' : 'other');
                                                        @endphp
                                                        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 lg:flex-row lg:items-center lg:justify-between">
                                                            <p class="min-w-0 text-sm text-slate-600">{{ $document['name'] }}</p>
                                                            <div class="flex flex-wrap gap-3">
                                                                @if (($document['source'] ?? null) === 'managed' && isset($document['id']))
                                                                    <button
                                                                        type="button"
                                                                        class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                                                        data-document-preview-trigger
                                                                        data-preview-url="{{ route('admin.documents.show', [$application, $document['id']]) }}"
                                                                        data-download-url="{{ route('admin.documents.download', [$application, $document['id']]) }}"
                                                                        data-document-name="{{ $document['name'] }}"
                                                                        data-preview-supported="{{ $canPreviewInline ? 'true' : 'false' }}"
                                                                        data-preview-kind="{{ $previewKind }}"
                                                                    >
                                                                        មើល
                                                                    </button>
                                                                    <a href="{{ route('admin.documents.download', [$application, $document['id']]) }}" class="inline-flex items-center rounded-xl bg-[#356AE6] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#204ec7]">ទាញយក</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-sm text-slate-500">មិនមានឯកសារដែលបានផ្ទុកឡើងទេ។</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="dashboard-surface p-6">
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('admin.applications.edit', $application) }}" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">កែប្រែ</a>
                                        @if ($application->status !== 'Approved')
                                            <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="flex-1" data-ajax-form data-ajax-redirect="{{ route('admin.applications.show', $application) }}" data-ajax-success-title="ជោគជ័აჟ™" data-ajax-success-text="បានអនុម័តពាក្យស្នើសុំដោយជោគជ័យ។">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Approved">
                                                <input type="hidden" name="admin_notes" value="{{ $reviewComment }}">
                                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">អនុម័ត</button>
                                            </form>
                                        @endif
                                        @if ($application->status !== 'Rejected')
                                            <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="flex-1" data-ajax-form data-ajax-redirect="{{ route('admin.applications.show', $application) }}" data-ajax-success-title="ជោគជ័აჟ™" data-ajax-success-text="បានបដិសេធពាក្យស្នើសុំដោយជោគជ័យ។">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Rejected">
                                                <input type="hidden" name="admin_notes" value="{{ $reviewComment }}">
                                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-rose-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-600">បដិសេធ</button>
                                            </form>
                                        @endif
                                        <button type="button" onclick="window.print()" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">ទាញយក PDF</button>
                                    </div>

                                    <a href="{{ route('admin.home', ['section' => 'applications']) }}" class="mt-4 inline-flex items-center rounded-2xl border border-slate-200 bg-[#f8fafc] px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">ត្រឡប់ទៅការចុះឈ្មោះសិក្ខាកាម</a>
                                </div>

                                <div class="dashboard-surface p-6">
                                    <h3 class="text-2xl font-semibold tracking-tight text-slate-950">កំណត់ចំណាំពិនិត្យ</h3>
                                    <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="mt-6 space-y-5" data-ajax-form data-ajax-redirect="{{ route('admin.applications.show', $application) }}" data-ajax-success-title="ជោគაჟ‡័აჟ™" data-ajax-success-text="បានរក្សាទុកការពិនិត្យដោយជោគជ័យ។">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="form-label">ស្ថានភាព</label>
                                            <select name="status" class="form-input bg-[#f8fafc]">
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" @selected(old('status', $application->status) === $status)>{{ $statusLabels[$status] ?? $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">កំណត់ចំណាំអ្នកគ្រប់គ្រង</label>
                                            <textarea name="admin_notes" rows="10" class="form-input bg-[#f8fafc]" placeholder="Write a comment for this application...">{{ $reviewComment }}</textarea>
                                        </div>
                                        <button type="submit" class="inline-flex items-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">រក្សាទុកការពិនិត្យ</button>
                                    </form>
                                </div>

                                <div class="dashboard-surface p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-2xl font-semibold tracking-tight text-slate-950">Comment</h3>
                                            <p class="mt-2 text-sm text-slate-500">Saved reviewer comment for this application.</p>
                                        </div>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                            {{ $application->status }}
                                        </span>
                                    </div>

                                    <div class="dashboard-soft-surface mt-5 px-4 py-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Current Comment</p>
                                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">
                                            {{ filled(trim((string) $reviewComment)) ? $reviewComment : 'No comment yet.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <p>&copy; {{ now()->year }} ប្រព័ន្ធការចុះឈ្មោះសិក្ខាកាមវគ្គសិក្សាយោធា។</p>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">API ដំណើរការ</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">V1.0</span>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/55 backdrop-blur-sm px-4 py-6" data-document-preview-modal aria-hidden="true">
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
                    <img
                        src=""
                        alt="ការមើលឯកសារ"
                        class="max-h-full max-w-full object-contain"
                        data-document-preview-image
                    >
                </div>
                <iframe
                    src="about:blank"
                    class="mt-4 hidden min-h-0 flex-1 w-full rounded-2xl border border-slate-200 bg-white"
                    data-document-preview-frame
                    title="ការមើលឯកសារ"
                ></iframe>
            </div>
        </div>
    </div>

    <script>
        (() => {
            document.querySelectorAll('form[data-ajax-form]').forEach((form) => {
                form.dataset.disableActionFlow = 'true';
            });

            const modal = document.querySelector('[data-document-preview-modal]');

            if (!modal) {
                return;
            }

            const frame = modal.querySelector('[data-document-preview-frame]');
            const imageWrapper = modal.querySelector('[data-document-preview-image-wrapper]');
            const image = modal.querySelector('[data-document-preview-image]');
            const name = modal.querySelector('[data-document-preview-name]');
            const openLink = modal.querySelector('[data-document-preview-open]');
            const downloadLink = modal.querySelector('[data-document-preview-download]');
            const note = modal.querySelector('[data-document-preview-note]');
            const closeButton = modal.querySelector('button[data-document-preview-close]');
            let previousFocusedElement = null;

            const updatePreviewNote = (shouldShow) => {
                note.classList.toggle('hidden', !shouldShow);
            };

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
                previousFocusedElement = document.activeElement instanceof HTMLElement
                    ? document.activeElement
                    : null;
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
                if (modal.getAttribute('aria-hidden') === 'false') {
                    showPreviewFallback();
                }
            });

            frame.addEventListener('error', () => {
                if (modal.getAttribute('aria-hidden') === 'false') {
                    showPreviewFallback();
                }
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
                if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
