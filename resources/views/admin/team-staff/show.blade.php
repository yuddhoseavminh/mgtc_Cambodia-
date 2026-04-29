@extends('app')

@section('body')
    @php
        $roleLabels = [
            // 'Admin' => 'អ្នកគ្រប់គ្រង',
            // 'Manager' => 'អ្នកចាត់ការ',
            // 'Staff' => 'បុគ្គលិក',
            // 'Viewer' => 'អ្នកមើល',
        ];
        $genderLabels = [
            'Male' => 'ប្រុស',
            'Female' => 'ស្រី',
            'Other' => 'ផ្សេងទៀត',
        ];
        $uploaderLabels = [
            'admin' => 'អ្នកគ្រប់គ្រង',
            'staff' => 'បុគ្គលិក',
        ];
        $details = [
            'លេខរៀង' => $teamStaff->sequence_no,
            'ឋានន្តរស័ក្តិ' => $teamStaff->military_rank,
            'ឈ្មោះជាភាសាខ្មែរ' => $teamStaff->name_kh,
            'ឈ្មោះឡាតាំង' => $teamStaff->name_latin,
            'អត្តលេខ' => $teamStaff->id_number,
            'ភេទ' => $genderLabels[$teamStaff->gender] ?? $teamStaff->gender,
            'មុខតំណែង' => $teamStaff->position,
            'តួនាទី' => $roleLabels[$teamStaff->role] ?? $teamStaff->role,
            'លេខទូរស័ព្ទ' => $teamStaff->phone_number,
            'ថ្ងៃចូលបម្រើកងទ័ព' => optional($teamStaff->date_of_enlistment)?->khFormat('d/m/Y'),
            'បង្កើតនៅ' => optional($teamStaff->created_at)?->format('d/m/Y H:i'),
            'កែប្រែចុងក្រោយ' => optional($teamStaff->updated_at)?->format('d/m/Y H:i'),
        ];
        $documents = collect($teamStaff->documents ?? [])->values();
        $indexedDocuments = $documents
            ->map(fn ($document, $index) => [...$document, 'document_index' => $index])
            ->values();
        $documentRequirements = collect($documentRequirements ?? [])->values();
        $documentsByRequirementSlug = $indexedDocuments
            ->filter(fn ($document) => filled($document['requirement_slug'] ?? null))
            ->groupBy(fn ($document) => $document['requirement_slug']);
        $legacyDocuments = $indexedDocuments
            ->filter(fn ($document) => blank($document['requirement_slug'] ?? null))
            ->values();
        $requiredDocumentCount = $documentRequirements->count();
        $uploadedRequiredCount = $documentRequirements
            ->filter(fn ($documentRequirement) => $documentsByRequirementSlug->has($documentRequirement->slug))
            ->count();
        $missingRequiredCount = max(0, $requiredDocumentCount - $uploadedRequiredCount);
        $previewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        $imagePreviewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $statusMeta = function (?string $status): array {
            $value = strtolower(trim((string) $status));

            return match ($value) {
                'approved' => ['label' => 'អនុម័ត', 'class' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'],
                'rejected' => ['label' => 'បដិសេធ', 'class' => 'bg-rose-100 text-rose-700 ring-1 ring-rose-200'],
                default => ['label' => 'រង់ចាំ', 'class' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200'],
            };
        };
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-screen lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'staff-management'])

                <main class="flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => 'ព័ត៌មានលម្អិតបុគ្គលិក',
                        'subtitle' => 'បញ្ជី / បុគ្គលិកក្រុម',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => 'staff-management',
                    ])

                    <div class="flex-1 p-4 sm:p-5 lg:p-6">
                        <div class="mx-auto w-full max-w-[1160px] space-y-5">
                            @if (session('status'))
                                <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <section class="grid gap-4 xl:grid-cols-[minmax(0,1.45fr)_300px]">
                                <div class="space-y-5">
                                    <div class="dashboard-surface overflow-hidden p-0">
                                        <div class="border-b border-slate-200 bg-[linear-gradient(135deg,#ffffff,#f8fbff)] px-5 py-4 sm:px-5">
                                            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_276px] lg:items-center">
                                                <div class="flex min-w-0 items-center gap-5">
                                                    @if ($teamStaff->hasStoredAvatar())
                                                        <img src="{{ route('team-staff.avatar', $teamStaff) }}" alt="{{ $teamStaff->name_latin }}" class="h-14 w-14 rounded-[1rem] object-cover ring-1 ring-slate-200 sm:h-16 sm:w-16">
                                                    @else
                                                        <div class="flex h-14 w-14 items-center justify-center rounded-[1rem] bg-slate-900 text-lg font-bold text-white ring-1 ring-slate-200 sm:h-16 sm:w-16 sm:text-xl">
                                                            {{ strtoupper(substr($teamStaff->name_latin ?: $teamStaff->name_kh ?: 'S', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div class="min-w-0">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">ប្រវត្តិបុគ្គលិក</p>
                                                        <h3 class="mt-1 truncate text-xl font-semibold tracking-tight text-slate-950 sm:text-2xl">{{ $teamStaff->name_kh }}</h3>
                                                        <p class="mt-1 truncate text-sm text-slate-500">{{ $teamStaff->name_latin ?: '-' }}</p>
                                                        <div class="mt-2.5 flex flex-wrap gap-2">
                                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-700">{{ $teamStaff->military_rank ?: 'មិនមានឋានន្តរស័ក្តិ' }}</span>
                                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">{{ $roleLabels[$teamStaff->role] ?? ($teamStaff->role ?: 'មិនមានតួនាទី') }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-3 gap-2 lg:grid-cols-1">
                                                    <div class="flex min-h-[72px] flex-col items-center justify-center rounded-[1rem] border border-slate-200 bg-white px-3 py-3 text-center lg:min-h-[68px]">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">ត្រូវការ</p>
                                                        <p class="mt-1 text-lg font-semibold text-slate-950">{{ $requiredDocumentCount }}</p>
                                                    </div>
                                                    <div class="flex min-h-[72px] flex-col items-center justify-center rounded-[1rem] border border-slate-200 bg-white px-3 py-3 text-center lg:min-h-[68px]">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">បានបញ្ចូល</p>
                                                        <p class="mt-1 text-lg font-semibold text-emerald-700">{{ $uploadedRequiredCount }}</p>
                                                    </div>
                                                    <div class="flex min-h-[72px] flex-col items-center justify-center rounded-[1rem] border border-slate-200 bg-white px-3 py-3 text-center lg:min-h-[68px]">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">ខ្វះ</p>
                                                        <p class="mt-1 text-lg font-semibold text-amber-600">{{ $missingRequiredCount }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-5">
                                            <div class="mb-4 flex items-center justify-between gap-3">
                                                <div>
                                                    <h4 class="text-lg font-semibold text-slate-950">ព័ត៌មានលម្អិត</h4>
                                                    <p class="mt-1 text-sm text-slate-500">ព័ត៌មានសង្ខេបសម្រាប់កំណត់ត្រាបុគ្គលិកនេះ។</p>
                                                </div>
                                            </div>

                                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                                @foreach ($details as $label => $value)
                                                    <div class="flex min-h-[84px] flex-col justify-center rounded-[1rem] border border-slate-200 bg-slate-50/80 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $label }}</p>
                                                        <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $value ?: '-' }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dashboard-surface p-5">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <h3 class="text-xl font-semibold tracking-tight text-slate-950">ឯកសារដែលត្រូវការ</h3>
                                                <p class="mt-1 text-sm text-slate-500">គ្រប់គ្រងឯកសារសិក្ខាកាមតាមប្រភេទដោយមានទីតាំងថេរ។</p>
                                            </div>
                                            <a href="{{ route('admin.home', ['section' => 'staff-team-documents']) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                                គ្រប់គ្រងប្រភេទឯកសារ
                                            </a>
                                        </div>

                                        @if ($documentRequirements->isNotEmpty())
                                            <div class="mt-5 grid gap-3">
                                                @foreach ($documentRequirements as $documentRequirement)
                                                    @php
                                                        $requirementDocuments = $documentsByRequirementSlug->get($documentRequirement->slug, collect())->values();
                                                    @endphp
                                                    <article class="rounded-[1.25rem] border border-slate-200 bg-white px-4 py-4 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
                                                        <div class="min-w-0 space-y-3">
                                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                                    <p class="text-sm font-semibold text-slate-950">{{ $documentRequirement->name_kh }}</p>
                                                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $requirementDocuments->isNotEmpty() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                                        {{ $requirementDocuments->isNotEmpty() ? $requirementDocuments->count().' ឯកសារ' : 'ខ្វះឯកសារ' }}
                                                                    </span>
                                                                </div>

                                                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                                                    <p class="truncate text-sm font-medium text-slate-700">
                                                                        {{ $requirementDocuments->isNotEmpty() ? 'បានបញ្ចូល '.$requirementDocuments->count().' ឯកសារសម្រាប់តម្រូវការនេះ' : 'មិនទាន់មានឯកសារបញ្ចូលទេ' }}
                                                                    </p>
                                                                    @if ($requirementDocuments->isNotEmpty())
                                                                        <p class="mt-1 text-xs text-slate-500">
                                                                            ឯកសារចុងក្រោយ៖ {{ $requirementDocuments->last()['original_name'] ?? '-' }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                @if ($requirementDocuments->isNotEmpty())
                                                                    <div class="space-y-2">
                                                                        @foreach ($requirementDocuments as $document)
                                                                            @php
                                                                                $extension = strtolower(pathinfo($document['original_name'] ?? '', PATHINFO_EXTENSION));
                                                                                $canPreviewInline = in_array($extension, $previewableExtensions, true);
                                                                                $previewKind = in_array($extension, $imagePreviewableExtensions, true) ? 'image' : ($extension === 'pdf' ? 'pdf' : 'other');
                                                                                $statusInfo = $statusMeta($document['status'] ?? null);
                                                                                $statusKey = strtolower((string) ($document['status'] ?? 'pending'));
                                                                                $isStaffUpload = strtolower((string) ($document['uploaded_by'] ?? '')) === 'staff';
                                                                                $canReview = $isStaffUpload && $statusKey === 'pending';
                                                                                $canApprove = $canReview;
                                                                                $canReject = $canReview;
                                                                            @endphp
                                                                            <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white px-3 py-3 sm:flex-row sm:items-start sm:justify-between">
                                                                                <div class="min-w-0">
                                                                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $document['original_name'] ?? '-' }}</p>
                                                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200">
                                                                                            {{ $uploaderLabels[strtolower((string) ($document['uploaded_by'] ?? 'admin'))] ?? ucfirst((string) ($document['uploaded_by'] ?? 'admin')) }}
                                                                                        </span>
                                                                                        @if (!empty($document['uploaded_at']))
                                                                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200">
                                                                                                {{ \Illuminate\Support\Carbon::parse($document['uploaded_at'])->format('d/m/Y H:i') }}
                                                                                            </span>
                                                                                        @endif
                                                                                        <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $statusInfo['class'] }}">
                                                                                            {{ $statusInfo['label'] }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="flex flex-wrap gap-2 sm:justify-end">
                                                                                    <button
                                                                                        type="button"
                                                                                        class="inline-flex min-h-[36px] items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                                                                        data-document-preview-trigger
                                                                                        data-preview-url="{{ route('team-staff.documents.show', [$teamStaff, $document['document_index']]) }}"
                                                                                        data-download-url="{{ route('team-staff.documents.download', [$teamStaff, $document['document_index']]) }}"
                                                                                        data-document-name="{{ $document['original_name'] ?? $documentRequirement->name_kh }}"
                                                                                        data-preview-supported="{{ $canPreviewInline ? 'true' : 'false' }}"
                                                                                        data-preview-kind="{{ $previewKind }}"
                                                                                    >
                                                                                        មើល
                                                                                    </button>
                                                                                    <a href="{{ route('team-staff.documents.download', [$teamStaff, $document['document_index']]) }}" class="inline-flex min-h-[36px] items-center rounded-lg bg-[#356AE6] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#204ec7]">
                                                                                        ទាញយក
                                                                                    </a>
                                                                                    @if ($canApprove)
                                                                                        <form method="POST" action="{{ route('team-staff.documents.update-status', [$teamStaff, $document['document_index']]) }}">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <input type="hidden" name="status" value="Approved">
                                                                                            <button type="submit" class="inline-flex min-h-[36px] items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                                                                                Approve
                                                                                            </button>
                                                                                        </form>
                                                                                    @endif
                                                                                    @if ($canReject)
                                                                                        <form method="POST" action="{{ route('team-staff.documents.update-status', [$teamStaff, $document['document_index']]) }}">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <input type="hidden" name="status" value="Rejected">
                                                                                            <button type="submit" class="inline-flex min-h-[36px] items-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                                                                                Reject
                                                                                            </button>
                                                                                        </form>
                                                                                    @endif
                                                                                    <form method="POST" action="{{ route('team-staff.documents.destroy', [$teamStaff, $document['document_index']]) }}" data-swal-confirm data-swal-title="លុបឯកសារ?" data-swal-text="វានឹងលុបឯកសារនេះចេញពីបញ្ជីតម្រូវការ។">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit" class="inline-flex min-h-[36px] items-center rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                                                            លុប
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                        </div>
                                                    </article>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="mt-6 rounded-[1.45rem] border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                                                <p class="text-base font-semibold text-slate-900">មិនទាន់មានតម្រូវការឯកសារនៅឡើយ</p>
                                                <p class="mt-2 text-sm leading-6 text-slate-500">សូមបង្កើតតម្រូវការឯកសារសម្រាប់បុគ្គលិកក្រុមជាមុនសិន ដើម្បីគ្រប់គ្រងឯកសារសិក្ខាកាមនៅទីនេះ។</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($legacyDocuments->isNotEmpty())
                                        <div class="dashboard-surface p-5">
                                            <h3 class="text-lg font-semibold tracking-tight text-slate-950">ឯកសារចាស់</h3>
                                            <p class="mt-1 text-sm text-slate-500">ឯកសារដែលភ្ជាប់ជាមួយកំណត់ត្រានេះ ប៉ុន្តែមិនស្ថិតក្នុងបញ្ជីតម្រូវការដែលកំពុងប្រើ។</p>
                                            <div class="mt-4 grid gap-3">
                                                @foreach ($legacyDocuments as $document)
                                                    @php($documentIndex = $document['document_index'] ?? null)
                                                    @if ($documentIndex !== null)
                                                        <a href="{{ route('team-staff.documents.download', [$teamStaff, $documentIndex]) }}" class="flex min-h-[68px] items-center justify-between rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3 transition hover:bg-white">
                                                            <div>
                                                                <p class="text-sm font-semibold text-slate-900">{{ $document['label'] ?? 'ឯកសារ' }}</p>
                                                                <p class="mt-1 text-xs text-slate-500">{{ $document['original_name'] ?? '-' }}</p>
                                                            </div>
                                                            <span class="text-xs font-semibold text-[#356AE6]">ទាញយក</span>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-5 xl:sticky xl:top-5 xl:self-start">
                                    <div class="dashboard-surface p-5">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">សកម្មភាពរហ័ស</p>
                                        <div class="mt-4 grid gap-3">
                                            <a href="{{ route('team-staff.edit', $teamStaff) }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl bg-[#356AE6] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                                                កែប្រែព័ត៌មាន
                                            </a>
                                            <button type="button" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50" data-password-reset-trigger>
                                                កំណត់លេខសម្ងាត់ថ្មី
                                            </button>
                                            <form method="POST" action="{{ route('team-staff.destroy', $teamStaff) }}" data-swal-confirm data-swal-title="លុបកំណត់ត្រាបុគ្គលិក?" data-swal-text="វានឹងលុបកំណត់ត្រាបុគ្គលិក និងឯកសារទាំងអស់ដែលភ្ជាប់ជាមួយ។">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex min-h-[44px] w-full items-center justify-center rounded-xl bg-rose-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-rose-600">
                                                    លុបបុគ្គលិក
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                                ត្រឡប់ទៅការគ្រប់គ្រងបុគ្គលិកក្រុមការងារទី៣
                                            </a>
                                        </div>
                                    </div>

                                    <div class="dashboard-surface p-5">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">ស្ថានភាពឯកសារ</p>
                                        <div class="mt-4 space-y-3">
                                            <div class="rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ការគ្របដណ្តប់</p>
                                                <p class="mt-1 text-lg font-semibold text-slate-950">{{ $uploadedRequiredCount }} / {{ $requiredDocumentCount }}</p>
                                            </div>
                                            <div class="rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ខ្វះតម្រូវការ</p>
                                                <p class="mt-1 text-lg font-semibold text-amber-600">{{ $missingRequiredCount }}</p>
                                            </div>
                                            <div class="rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ឯកសារចាស់</p>
                                                <p class="mt-1 text-lg font-semibold text-slate-950">{{ $legacyDocuments->count() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <p>&copy; {{ now()->year }} ប្រព័ន្ធចុះឈ្មោះយោធា</p>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">API ដំណើរការ</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">V1.0</span>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/55 px-4 py-6 backdrop-blur-sm" data-password-reset-modal aria-hidden="true">
        <div class="absolute inset-0" data-password-reset-close></div>
        <div class="relative z-10 w-full max-w-md overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl" data-password-reset-panel>
            <form method="POST" action="{{ route('team-staff.update-password', $teamStaff) }}">
                @csrf
                @method('PATCH')
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">កំណត់លេខសម្ងាត់ថ្មី</p>
                        <p class="mt-1 truncate text-sm font-semibold text-slate-900 sm:text-base">{{ $teamStaff->name_kh }}</p>
                    </div>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700" data-password-reset-close aria-label="បិទ">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4 bg-slate-50 p-5 sm:p-6">
                    <div>
                        <label class="form-label" for="new_password">លេខសម្ងាត់ថ្មី</label>
                        <input type="password" id="new_password" name="new_password" class="form-input bg-white" placeholder="បញ្ចូលលេខសម្ងាត់ថ្មី..." required minlength="6" maxlength="50" autocomplete="new-password">
                        <p class="mt-2 text-xs text-slate-500">គណនី និងលេខសម្ងាត់នេះនឹងតម្រូវឱ្យបុគ្គលិកប្តូរនៅពេលចូលប្រព័ន្ធលើកក្រោយ។</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-white px-5 py-4 sm:px-6">
                    <button type="button" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" data-password-reset-close>
                        បោះបង់
                    </button>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#356AE6] px-5 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                        រក្សាទុកការកំណត់
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/55 backdrop-blur-sm px-4 py-6" data-document-preview-modal aria-hidden="true">
        <div class="absolute inset-0" data-document-preview-close></div>
        <div class="relative z-10 flex h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl" data-document-preview-panel>
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">ការមើលឯកសារ</p>
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
                    ប្រភេទឯកសារនេះប្រហែលជាមិនអាចមើលផ្ទាល់ក្នុងទំព័របានទេ។ សូមប្រើ បើក ឬ ទាញយក ប្រសិនបើផ្ទាំងមើលទទេ។
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

        (() => {
            const modal = document.querySelector('[data-password-reset-modal]');
            if (!modal) return;

            const input = modal.querySelector('input[name="new_password"]');
            const form = modal.querySelector('form');
            let previousFocusedElement = null;

            const openModal = () => {
                previousFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
                input?.focus();
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                form?.reset();
                previousFocusedElement?.focus();
            };

            document.querySelectorAll('[data-password-reset-trigger]').forEach(trigger => {
                trigger.addEventListener('click', openModal);
            });

            modal.querySelectorAll('[data-password-reset-close]').forEach(btn => {
                btn.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
