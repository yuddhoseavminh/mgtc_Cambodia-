@extends('app')

@section('body')
    @php
        $isEdit = $mode === 'edit';
        $existingDocuments = collect($teamStaff->documents ?? [])->values();
        $oldDocumentLabels = collect(old('documents_labels', []))->values();
        $documentRows = max(4, $existingDocuments->count(), $oldDocumentLabels->count());
        $documentTypeOptions = collect($documentTypeSuggestions)->filter()->values();
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-screen lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'staff-management'])

                <main class="flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => $isEdit ? 'កែប្រែបុគ្គលិកក្រុម' : 'បង្កើតបុគ្គលិកក្រុម',
                        'subtitle' => 'កាតាឡុក / បុគ្គលិកក្រុម',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => 'staff-management',
                    ])

                    <div class="flex-1 p-4 sm:p-6 lg:p-8">
                        <div class="mx-auto w-full max-w-[980px] space-y-6">
                            <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#ffffff,#f8fbff)] p-6 shadow-[0_20px_50px_rgba(15,23,42,0.06)] sm:p-7">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div class="max-w-3xl">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Staff Catalog</p>
                                        <h3 class="mt-2 text-[1.85rem] font-semibold tracking-tight text-slate-950">
                                            {{ $isEdit ? 'កែប្រែព័ត៌មានបុគ្គលិកក្រុម' : 'បង្កើតព័ត៌មានបុគ្គលិកក្រុមថ្មី' }}
                                        </h3>
                                        <p class="mt-2 text-sm leading-7 text-slate-500">
                                            បំពេញព័ត៌មានតាមលំដាប់ពីលើចុះក្រោម ដើម្បីឱ្យការបង្កើតបុគ្គលិកថ្មីមានភាពច្បាស់ ស្អាត និងងាយពិនិត្យ។
                                        </p>
                                    </div>

                                    <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                        ត្រឡប់ទៅបញ្ជី
                                    </a>
                                </div>
                            </section>

                            <form
                                method="POST"
                                action="{{ $isEdit ? route('team-staff.update', $teamStaff) : route('team-staff.store') }}"
                                enctype="multipart/form-data"
                                class="space-y-6"
                                data-ajax-form
                                data-ajax-redirect="{{ route('admin.home', ['section' => 'staff-management']) }}"
                                data-ajax-success-title="ជោគជ័យ"
                                data-ajax-success-text="{{ $isEdit ? 'បានកែប្រែបុគ្គលិកដោយជោគជ័យ។' : 'បានបង្កើតបុគ្គលិកដោយជោគជ័យ។' }}"
                            >
                                @csrf
                                @if ($isEdit)
                                    @method('PUT')
                                @endif

                                <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                                    <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <h4 class="text-lg font-semibold text-slate-950">ព័ត៌មានមូលដ្ឋាន</h4>
                                                <p class="mt-1 text-sm text-slate-500">បំពេញព័ត៌មានសំខាន់ៗរបស់បុគ្គលិកក្នុងប្លង់ grid ដែលងាយមើល និងងាយបញ្ចូល។</p>
                                            </div>
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">Section 01</span>
                                        </div>

                                        <div class="grid gap-5 md:grid-cols-2">
                                            <div>
                                                <div class="mb-2 flex min-h-[2.75rem] items-start justify-between gap-3">
                                                    <label class="form-label !mb-0">លេខលំដាប់</label>
                                                    <span class="invisible text-xs font-semibold">.</span>
                                                </div>
                                                <input type="text" value="{{ old('sequence_no', $teamStaff->sequence_no) }}" class="form-input bg-slate-50" readonly>
                                            </div>

                                            <div>
                                                <div class="mb-2 flex min-h-[2.75rem] flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                    <label class="form-label !mb-0">ឋានន្តរស័ក្តិយោធា</label>
                                                    <a href="{{ route('admin.home', ['section' => 'staff-team-ranks']) }}" class="text-xs font-semibold text-[#356AE6] hover:underline">+ បង្កើតឋានន្តរស័ក្តិបុគ្គលិក</a>
                                                </div>
                                                <select name="military_rank" class="form-input bg-slate-50 text-slate-700">
                                                    <option value="">ជ្រើសរើសឋានន្តរស័ក្តិបុគ្គលិក</option>
                                                    @foreach ($rankSuggestions as $rankSuggestion)
                                                        <option value="{{ $rankSuggestion }}" @selected(old('military_rank', $teamStaff->military_rank) === $rankSuggestion)>{{ $rankSuggestion }}</option>
                                                    @endforeach
                                                </select>
                                                @include('partials.field-error', ['name' => 'military_rank'])
                                            </div>

                                            <div>
                                                <label class="form-label">គោត្តនាម-នាម</label>
                                                <input type="text" name="name_kh" value="{{ old('name_kh', $teamStaff->name_kh) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលឈ្មោះជាភាសាខ្មែរ">
                                                @include('partials.field-error', ['name' => 'name_kh'])
                                            </div>

                                            <div>
                                                <label class="form-label">ឈ្មោះឡាតាំង</label>
                                                <input type="text" name="name_latin" value="{{ old('name_latin', $teamStaff->name_latin) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលឈ្មោះជាអក្សរឡាតាំង">
                                                @include('partials.field-error', ['name' => 'name_latin'])
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="form-label">អត្តលេខ</label>
                                                <input type="text" name="id_number" value="{{ old('id_number', $teamStaff->id_number) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលអត្តលេខ">
                                                @include('partials.field-error', ['name' => 'id_number'])
                                            </div>

                                            <div>
                                                <label class="form-label">ភេទ</label>
                                                <select name="gender" class="form-input bg-slate-50">
                                                    <option value="">ជ្រើសរើសភេទ</option>
                                                    @foreach ($genderOptions as $genderOption)
                                                        <option value="{{ $genderOption }}" @selected(old('gender', $teamStaff->gender) === $genderOption)>{{ ['Male' => 'ប្រុស', 'Female' => 'ស្រី', 'Other' => 'ផ្សេងទៀត'][$genderOption] ?? $genderOption }}</option>
                                                    @endforeach
                                                </select>
                                                @include('partials.field-error', ['name' => 'gender'])
                                            </div>

                                            <div>
                                                <label class="form-label">លេខទូរស័ព្ទ</label>
                                                <input type="text" name="phone_number" value="{{ old('phone_number', $teamStaff->phone_number) }}" class="form-input bg-slate-50" placeholder="បញ្ចូលលេខទូរស័ព្ទ">
                                                @include('partials.field-error', ['name' => 'phone_number'])
                                            </div>

                                            <div>
                                                <label class="form-label">មុខតំណែង</label>
                                                <input
                                                    type="text"
                                                    name="position"
                                                    list="position-options"
                                                    value="{{ old('position', $teamStaff->position) }}"
                                                    class="form-input bg-slate-50"
                                                    placeholder="បញ្ចូលមុខតំណែង"
                                                >
                                                @include('partials.field-error', ['name' => 'position'])
                                            </div>

                                            <div>
                                                <label class="form-label">តួនាទី</label>
                                                <input
                                                    type="text"
                                                    name="role"
                                                    value="{{ old('role', $teamStaff->role) }}"
                                                    class="form-input bg-slate-50"
                                                    placeholder="បញ្ចូលតួនាទី"
                                                >
                                                @include('partials.field-error', ['name' => 'role'])
                                            </div>
                                        </div>
                                    </section>

                                    <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7 xl:sticky xl:top-6 xl:self-start">
                                        <div class="mb-6 flex flex-col gap-3">
                                            <div>
                                                <h4 class="text-lg font-semibold text-slate-950">រូបភាពបុគ្គលិក</h4>
                                                <p class="mt-1 text-sm text-slate-500">ផ្ទុករូបភាពមួយសន្លឹកសម្រាប់បញ្ជី និងទំព័រលម្អិត។</p>
                                            </div>
                                            <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">Section 02</span>
                                        </div>

                                        <div class="grid gap-5 md:grid-cols-[minmax(0,1fr)_12rem] md:items-start">
                                            <div class="space-y-4">
                                                <input type="file" name="avatar_image" accept=".jpg,.jpeg,.png,.webp" class="block w-full rounded-[1.35rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-[1.1rem] file:border-0 file:bg-[#e8efff] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#2c5dd6] hover:file:bg-[#dce7ff]" data-avatar-input>
                                                <p class="text-xs text-slate-500">អនុញ្ញាត JPG, PNG, WEBP ទំហំអតិបរមា 5MB</p>
                                                @include('partials.field-error', ['name' => 'avatar_image'])
                                            </div>

                                            <div class="mx-auto w-full max-w-[12rem] rounded-[1.35rem] border border-slate-200 bg-slate-50 p-4 md:mx-0" data-avatar-preview-wrapper>
                                                <p class="text-center text-xs font-semibold text-slate-400">មើលរូបភាព</p>
                                                <div class="mt-3 flex min-h-[9rem] items-center justify-center">
                                                    @if ($isEdit && $teamStaff->hasStoredAvatar())
                                                        <img src="{{ route('team-staff.avatar', $teamStaff) }}" alt="{{ $teamStaff->name_latin }}" class="mx-auto rounded-full object-cover ring-1 ring-slate-200" style="width: 8.5rem; height: 8.5rem;" data-avatar-preview-image data-initial-src="{{ route('team-staff.avatar', $teamStaff) }}">
                                                        <div class="hidden mx-auto grid place-items-center rounded-full border border-dashed border-slate-300 bg-white text-center text-sm text-slate-400" style="width: 8.5rem; height: 8.5rem;" data-avatar-preview-empty>
                                                            មិនទាន់មានរូបភាព
                                                        </div>
                                                    @else
                                                        <img src="" alt="Avatar preview" class="mx-auto hidden rounded-full object-cover ring-1 ring-slate-200" style="width: 8.5rem; height: 8.5rem;" data-avatar-preview-image data-initial-src="">
                                                        <div class="mx-auto grid place-items-center rounded-full border border-dashed border-slate-300 bg-white text-center text-sm text-slate-400" style="width: 8.5rem; height: 8.5rem;" data-avatar-preview-empty>
                                                            មិនទាន់មានរូបភាព
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </section>

                                <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_30px_rgba(15,23,42,0.05)] sm:p-7">
                                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h4 class="text-lg font-semibold text-slate-950">ឯកសារ</h4>
                                            <p class="mt-1 text-sm text-slate-500">បន្ថែមឯកសារជាជួរៗ ដោយជ្រើសប្រភេទឯកសារជាមុន ហើយបន្ទាប់មកផ្ទុកឯកសារតាមលំដាប់។</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3">
                                            <a href="{{ route('admin.home', ['section' => 'staff-team-documents']) }}" class="inline-flex items-center justify-center rounded-[1.1rem] border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">
                                                + បង្កើតប្រភេទឯកសារ
                                            </a>
                                            <button type="button" class="inline-flex items-center justify-center rounded-[1.1rem] bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800" data-add-document-row>
                                                + បន្ថែមឯកសារ
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid gap-5 md:grid-cols-2" data-document-rows data-next-index="{{ $documentRows }}">
                                        @for ($i = 0; $i < $documentRows; $i++)
                                            <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50 p-4" data-document-row>
                                                <div class="flex items-center justify-between gap-3">
                                                    <p class="text-sm font-semibold text-slate-800" data-document-title>ឯកសារ {{ $i + 1 }}</p>
                                                    <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold text-slate-500 transition hover:bg-slate-100" data-remove-document-row>
                                                        លុបជួរ
                                                    </button>
                                                </div>

                                                @php($selectedDocumentLabel = old('documents_labels.'.$i, $existingDocuments->get($i)['label'] ?? ''))
                                                <div class="mt-3 space-y-3">
                                                    <select name="documents_labels[]" class="form-input bg-white">
                                                        <option value="">ជ្រើសប្រភេទឯកសារ</option>
                                                        @foreach ($documentTypeOptions as $documentTypeOption)
                                                            <option value="{{ $documentTypeOption }}" @selected($selectedDocumentLabel === $documentTypeOption)>{{ $documentTypeOption }}</option>
                                                        @endforeach
                                                        @if ($selectedDocumentLabel !== '' && ! $documentTypeOptions->contains($selectedDocumentLabel))
                                                            <option value="{{ $selectedDocumentLabel }}" selected>{{ $selectedDocumentLabel }}</option>
                                                        @endif
                                                    </select>
                                                    @php($documentLabelErrorName = 'documents_labels.'.$i)
                                                    @include('partials.field-error', ['name' => $documentLabelErrorName])

                                                    <input type="file" name="documents[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" class="block w-full rounded-[1.35rem] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-[1.1rem] file:border-0 file:bg-[#e8efff] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#2c5dd6] hover:file:bg-[#dce7ff]">
                                                    @php($documentErrorName = 'documents.'.$i)
                                                    @include('partials.field-error', ['name' => $documentErrorName])
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <template data-document-row-template>
                                        <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50 p-4" data-document-row>
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-sm font-semibold text-slate-800" data-document-title>ឯកសារ __NUMBER__</p>
                                                <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold text-slate-500 transition hover:bg-slate-100" data-remove-document-row>
                                                    លុបជួរ
                                                </button>
                                            </div>

                                            <div class="mt-3 space-y-3">
                                                <select name="documents_labels[]" class="form-input bg-white">
                                                    <option value="">ជ្រើសប្រភេទឯកសារ</option>
                                                    @foreach ($documentTypeOptions as $documentTypeOption)
                                                        <option value="{{ $documentTypeOption }}">{{ $documentTypeOption }}</option>
                                                    @endforeach
                                                </select>

                                                <input type="file" name="documents[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" class="block w-full rounded-[1.35rem] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-[1.1rem] file:border-0 file:bg-[#e8efff] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#2c5dd6] hover:file:bg-[#dce7ff]">
                                            </div>
                                        </div>
                                    </template>

                                    @include('partials.field-error', ['name' => 'documents'])
                                    @include('partials.field-error', ['name' => 'documents_labels'])

                                    @if ($isEdit && $existingDocuments->isNotEmpty())
                                        <div class="mt-6 rounded-[1.35rem] border border-slate-200 bg-white p-4">
                                            <p class="text-sm font-semibold text-slate-900">ឯកសារបច្ចុប្បន្ន</p>
                                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                @foreach ($existingDocuments as $index => $document)
                                                    <a href="{{ route('team-staff.documents.download', [$teamStaff, $index]) }}" class="inline-flex items-center rounded-[1.1rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                                        {{ $document['label'] ?? 'ឯកសារ '.($index + 1) }}: {{ $document['original_name'] ?? 'ឯកសារ' }}
                                                    </a>
                                                @endforeach
                                            </div>
                                            <p class="mt-3 text-sm text-slate-500">បើអ្នកផ្ទុកឯកសារថ្មី វានឹងជំនួសបញ្ជីឯកសារចាស់ទាំងអស់។</p>
                                        </div>
                                    @endif
                                </section>

                                <section class="sticky bottom-3 z-10 rounded-[1.6rem] border border-slate-200 bg-white/95 p-4 shadow-[0_10px_25px_rgba(15,23,42,0.08)] backdrop-blur">
                                    <div class="flex flex-wrap items-center justify-end gap-3">
                                        <button type="reset" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            កំណត់ឡើងវិញ
                                        </button>
                                        <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="inline-flex items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            បោះបង់
                                        </a>
                                        <button type="submit" class="inline-flex items-center justify-center rounded-[1.35rem] bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                            {{ $isEdit ? 'រក្សាទុកការកែប្រែ' : 'រក្សាទុកបុគ្គលិក' }}
                                        </button>
                                    </div>
                                </section>
                            </form>
                        </div>
                    </div>

                    <datalist id="position-options">
                        @foreach ($positionSuggestions as $positionSuggestion)
                            <option value="{{ $positionSuggestion }}"></option>
                        @endforeach
                    </datalist>

                    <script>
                        (() => {
                            const avatarInput = document.querySelector('[data-avatar-input]');
                            const avatarPreviewImage = document.querySelector('[data-avatar-preview-image]');
                            const avatarPreviewEmpty = document.querySelector('[data-avatar-preview-empty]');

                            if (avatarInput && avatarPreviewImage && avatarPreviewEmpty) {
                                let previewObjectUrl = null;

                                avatarInput.addEventListener('change', (event) => {
                                    const [file] = event.target.files || [];
                                    const initialSrc = avatarPreviewImage.dataset.initialSrc || '';

                                    if (previewObjectUrl) {
                                        URL.revokeObjectURL(previewObjectUrl);
                                        previewObjectUrl = null;
                                    }

                                    if (! file) {
                                        if (initialSrc !== '') {
                                            avatarPreviewImage.src = initialSrc;
                                            avatarPreviewImage.classList.remove('hidden');
                                            avatarPreviewEmpty.classList.add('hidden');
                                            avatarPreviewEmpty.classList.remove('grid');
                                        } else {
                                            avatarPreviewImage.src = '';
                                            avatarPreviewImage.classList.add('hidden');
                                            avatarPreviewEmpty.classList.remove('hidden');
                                            avatarPreviewEmpty.classList.add('grid');
                                        }

                                        return;
                                    }

                                    previewObjectUrl = URL.createObjectURL(file);
                                    avatarPreviewImage.src = previewObjectUrl;
                                    avatarPreviewImage.classList.remove('hidden');
                                    avatarPreviewEmpty.classList.add('hidden');
                                    avatarPreviewEmpty.classList.remove('grid');
                                });
                            }

                            const rowsContainer = document.querySelector('[data-document-rows]');
                            const addRowButton = document.querySelector('[data-add-document-row]');
                            const rowTemplate = document.querySelector('[data-document-row-template]');

                            if (!rowsContainer || !addRowButton || !rowTemplate) {
                                return;
                            }

                            const updateTitles = () => {
                                rowsContainer.querySelectorAll('[data-document-row]').forEach((row, index) => {
                                    const title = row.querySelector('[data-document-title]');

                                    if (title) {
                                        title.textContent = `ឯកសារ ${index + 1}`;
                                    }
                                });
                            };

                            const bindRemove = (row) => {
                                const removeButton = row.querySelector('[data-remove-document-row]');

                                if (! removeButton) {
                                    return;
                                }

                                removeButton.addEventListener('click', () => {
                                    const rows = rowsContainer.querySelectorAll('[data-document-row]');

                                    if (rows.length <= 1) {
                                        row.querySelectorAll('input').forEach((input) => {
                                            input.value = '';
                                        });

                                        row.querySelectorAll('select').forEach((select) => {
                                            select.selectedIndex = 0;
                                        });

                                        return;
                                    }

                                    row.remove();
                                    updateTitles();
                                });
                            };

                            rowsContainer.querySelectorAll('[data-document-row]').forEach(bindRemove);

                            addRowButton.addEventListener('click', () => {
                                const rowCount = rowsContainer.querySelectorAll('[data-document-row]').length + 1;
                                const html = rowTemplate.innerHTML.replace('__NUMBER__', rowCount.toString());
                                rowsContainer.insertAdjacentHTML('beforeend', html);
                                const newRow = rowsContainer.querySelectorAll('[data-document-row]')[rowCount - 1];

                                if (newRow) {
                                    bindRemove(newRow);
                                }

                                updateTitles();
                            });
                        })();
                    </script>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <p>&copy; {{ now()->year }} ប្រព័ន្ធការចុះឈ្មោះសិក្ខាកាមវគ្គសិក្សាយោធា</p>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">API ដំណើរការ</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">V1.0</span>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    </div>
@endsection
