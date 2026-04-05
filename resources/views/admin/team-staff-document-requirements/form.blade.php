@extends('app')

@section('body')
    @php
        $isEdit = $mode === 'edit';
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-[calc(100vh-3.5rem)] lg:grid-cols-[312px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'staff-team-documents'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => $isEdit ? 'កែប្រែឯកសារបុគ្គលិកក្រុម' : 'បង្កើតឯកសារបុគ្គលិកក្រុម',
                        'subtitle' => 'កាតាឡុកក្រុម',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => 'staff-team-documents',
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">{{ $isEdit ? 'កែប្រែឯកសារបុគ្គលិកក្រុម' : 'បង្កើតឯកសារបុគ្គលិកក្រុម' }}</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $isEdit ? 'ធ្វើបច្ចុប្បន្នភាពឈ្មោះ ស្លាកតំណ និងលំដាប់សម្រាប់ប្រភេទឯកសារបុគ្គលិកក្រុម។' : 'បន្ថែមប្រភេទឯកសារថ្មី សម្រាប់ប្រើនៅក្នុងទម្រង់បុគ្គលិកក្រុម។' }}
                                </p>
                            </div>

                            <a href="{{ route('admin.home', ['section' => 'staff-team-documents']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                ត្រឡប់ទៅបញ្ជីឯកសារ
                            </a>
                        </section>

                        <section class="dashboard-surface mt-6 p-6">
                            <form
                                method="POST"
                                action="{{ $isEdit ? route('team-staff-document-requirements.update', $documentRequirement) : route('team-staff-document-requirements.store') }}"
                                class="grid gap-5 md:grid-cols-2"
                                data-ajax-form
                                data-ajax-redirect="{{ route('admin.home', ['section' => 'staff-team-documents']) }}"
                                data-ajax-success-title="ជោគជ័យ"
                                data-ajax-success-text="{{ $isEdit ? 'បានកែប្រែប្រភេទឯកសារដោយជោគជ័យ។' : 'បានបង្កើតប្រភេទឯកសារដោយជោគជ័យ។' }}"
                            >
                                @csrf
                                @if ($isEdit)
                                    @method('PUT')
                                @endif

                                <div class="md:col-span-2">
                                    <label class="form-label">ឈ្មោះជាភាសាខ្មែរ</label>
                                    <input type="text" name="name_kh" value="{{ old('name_kh', $documentRequirement->name_kh) }}" class="form-input bg-[#f8fafc]" placeholder="បញ្ចូលឈ្មោះឯកសារ">
                                    @include('partials.field-error', ['name' => 'name_kh'])
                                </div>

                                <div>
                                    <label class="form-label">ស្លាកតំណ</label>
                                    <input type="text" name="slug" value="{{ old('slug', $documentRequirement->slug) }}" class="form-input bg-[#f8fafc]" placeholder="ស្លាកតំណផ្ទាល់ខ្លួន (ស្រេចចិត្ត)">
                                    @include('partials.field-error', ['name' => 'slug'])
                                </div>

                                <div>
                                    <label class="form-label">លំដាប់</label>
                                    <input type="number" name="sort_order" min="1" value="{{ old('sort_order', $documentRequirement->sort_order) }}" class="form-input bg-[#f8fafc]">
                                    @include('partials.field-error', ['name' => 'sort_order'])
                                </div>

                                <div>
                                    <label class="form-label">ស្ថានភាព</label>
                                    <select name="is_active" class="form-input bg-[#f8fafc]">
                                        <option value="1" @selected((string) old('is_active', (int) $documentRequirement->is_active) === '1')>សកម្ម</option>
                                        <option value="0" @selected((string) old('is_active', (int) $documentRequirement->is_active) === '0')>មិនសកម្ម</option>
                                    </select>
                                    @include('partials.field-error', ['name' => 'is_active'])
                                </div>

                                <div class="md:col-span-2 flex flex-wrap gap-3">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                                        {{ $isEdit ? 'រក្សាទុកការកែប្រែ' : 'បង្កើតប្រភេទឯកសារ' }}
                                    </button>
                                    <a href="{{ route('admin.home', ['section' => 'staff-team-documents']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                        បោះបង់
                                    </a>
                                </div>
                            </form>
                        </section>
                    </div>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
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
