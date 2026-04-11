@extends('app')

@section('body')
    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-[calc(100vh-3.5rem)] lg:grid-cols-[312px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'register-staff'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => 'កែប្រែព័ត៌មានបុគ្គលិកចុះឈ្មោះ',
                        'subtitle' => 'អ្នកគ្រប់គ្រង',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="mx-auto max-w-3xl">
                            <div class="dashboard-surface p-6">
                                <h3 class="text-2xl font-semibold tracking-tight text-slate-950">កែប្រែព័ត៌មាន</h3>
                                <p class="mt-2 text-sm text-slate-500">កែប្រែព័ត៌មានមូលដ្ឋានរបស់បុគ្គលិកសាកល្បង។</p>

                                <form method="POST" action="{{ route('admin.test-taking-staff-registrations.update', $registration) }}" class="mt-8 space-y-6">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div>
                                            <label class="form-label">ឈ្មោះ (ខ្មែរ)</label>
                                            <input type="text" name="name_kh" value="{{ old('name_kh', $registration->name_kh) }}" required class="form-input bg-[#f8fafc]">
                                            @error('name_kh')
                                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label">ឈ្មោះ (ឡាតាំង)</label>
                                            <input type="text" name="name_latin" value="{{ old('name_latin', $registration->name_latin) }}" class="form-input bg-[#f8fafc]">
                                            @error('name_latin')
                                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div>
                                            <label class="form-label">ឋានន្តរស័ក្តិ</label>
                                            <select name="test_taking_staff_rank_id" class="form-input bg-[#f8fafc]">
                                                <option value="">ជ្រើសរើសឋានន្តរស័ក្តិ</option>
                                                @foreach ($ranks as $rank)
                                                    <option value="{{ $rank->id }}" @selected(old('test_taking_staff_rank_id', $registration->test_taking_staff_rank_id) == $rank->id)>
                                                        {{ $rank->name_kh }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('test_taking_staff_rank_id')
                                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label">លេខទូរស័ព្ទ</label>
                                            <input type="text" name="phone_number" value="{{ old('phone_number', $registration->phone_number) }}" class="form-input bg-[#f8fafc]">
                                            @error('phone_number')
                                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div>
                                            <label class="form-label">ថ្ងៃខែឆ្នាំកំណើត</label>
                                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($registration->date_of_birth)->format('Y-m-d')) }}" class="form-input bg-[#f8fafc]">
                                            @error('date_of_birth')
                                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="form-label">ថ្ងៃចូលបម្រើយោធា</label>
                                            <input type="date" name="military_service_day" value="{{ old('military_service_day', optional($registration->military_service_day)->format('Y-m-d')) }}" class="form-input bg-[#f8fafc]">
                                            @error('military_service_day')
                                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 border-t border-slate-200 pt-6">
                                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
                                            រក្សាទុក
                                        </button>
                                        <a href="{{ route('admin.test-taking-staff-registrations.show', $registration) }}" class="inline-flex items-center rounded-2xl px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                            បដិសេធ
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
