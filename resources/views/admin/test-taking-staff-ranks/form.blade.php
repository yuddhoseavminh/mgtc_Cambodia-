@extends('app')

@section('body')
    @php
        $isEdit = $mode === 'edit';
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-[calc(100vh-3.5rem)] lg:grid-cols-[312px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'test-taking-staff-ranks'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => $isEdit ? 'កែប្រែឋានន្តរស័ក្តិបុគ្គលិកសាកល្បង' : 'បង្កើតឋានន្តរស័ក្តិបុគ្គលិកសាកល្បង',
                        'subtitle' => 'កាតាឡុក ៣ / បុគ្គលិកសាកល្បង',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => 'test-taking-staff-ranks',
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">{{ $isEdit ? 'កែប្រែឋានន្តរស័ក្តិបុគ្គលិកសាកល្បង' : 'បង្កើតឋានន្តរស័ក្តិបុគ្គលិកសាកល្បង' }}</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $isEdit ? 'ធ្វើបច្ចុប្បន្នភាពឈ្មោះឋានន្តរស័ក្តិ និងលំដាប់សាធារណៈដែលប្រើនៅលើទម្រង់ចុះឈ្មោះបុគ្គលិកសាកល្បង។' : 'បន្ថែមជម្រើសឋានន្តរស័ក្តិសាធារណៈសម្រាប់ទម្រង់ចុះឈ្មោះបុគ្គលិកសាកល្បង។' }}
                                </p>
                            </div>

                            <a href="{{ route('admin.home', ['section' => 'test-taking-staff-ranks']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                ត្រឡប់ទៅបញ្ជីឋានន្តរស័ក្តិ
                            </a>
                        </section>

                        <section class="dashboard-surface mt-6 p-6">
                            <form
                                method="POST"
                                action="{{ $isEdit ? route('test-taking-staff-ranks.update', $rank) : route('test-taking-staff-ranks.store') }}"
                                class="grid gap-5 md:grid-cols-2"
                                data-ajax-form
                                data-ajax-redirect="{{ route('admin.home', ['section' => 'test-taking-staff-ranks']) }}"
                                data-ajax-success-title="áž‡áŸ„áž‚áž‡áŸáž™"
                                data-ajax-success-text="{{ $isEdit ? 'áž”áž¶áž“áž€áŸ‚áž”áŸ’ážšáŸ‚áž‹áž¶áž“áž“áŸ’ážážšážŸáŸáž€áŸ’აჟិអ្នកបុគ្គលិកសាកល្បងដោយជោគជ័យ។' : 'áž”áž¶áž“áž”აჟ„ា្កើតឋានន្តរស័ក្តិបុគ្គលិកសាកល្បងដោយជោគជ័យ។' }}"
                            >
                                @csrf
                                @if ($isEdit)
                                    @method('PUT')
                                @endif

                                <div>
                                    <label class="form-label">ឈ្មោះជាភាសាខ្មែរ</label>
                                    <input type="text" name="name_kh" value="{{ old('name_kh', $rank->name_kh) }}" class="form-input bg-[#f8fafc]" placeholder="បញ្ចូលឈ្មោះឋានន្តរស័ក្តិខ្មែរ">
                                    @include('partials.field-error', ['name' => 'name_kh'])
                                </div>

                                <div>
                                    <label class="form-label">ឈ្មោះជាភាសាអង់គ្លេស</label>
                                    <input type="text" name="name_en" value="{{ old('name_en', $rank->name_en) }}" class="form-input bg-[#f8fafc]" placeholder="បញ្ចូលឈ្មោះឋានន្តរស័ក្តិអង់គ្លេស">
                                    @include('partials.field-error', ['name' => 'name_en'])
                                </div>

                                <div>
                                    <label class="form-label">លំដាប់</label>
                                    <input type="number" name="sort_order" min="1" value="{{ old('sort_order', $rank->sort_order) }}" class="form-input bg-[#f8fafc]">
                                    @include('partials.field-error', ['name' => 'sort_order'])
                                </div>

                                <div>
                                    <label class="form-label">ស្ថានភាព</label>
                                    <select name="is_active" class="form-input bg-[#f8fafc]">
                                        <option value="1" @selected((string) old('is_active', (int) $rank->is_active) === '1')>សកម្ម</option>
                                        <option value="0" @selected((string) old('is_active', (int) $rank->is_active) === '0')>មិនសកម្ម</option>
                                    </select>
                                    @include('partials.field-error', ['name' => 'is_active'])
                                </div>

                                <div class="md:col-span-2 flex flex-wrap gap-3">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                                        {{ $isEdit ? 'រក្សាទុកការកែប្រែ' : 'បង្កើតឋានន្តរស័ក្តិ' }}
                                    </button>
                                    <a href="{{ route('admin.home', ['section' => 'test-taking-staff-ranks']) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                        បោះបង់
                                    </a>
                                </div>
                            </form>
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
@endsection
