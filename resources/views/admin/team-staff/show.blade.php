@extends('app')

@section('body')
    @php
        $details = [
            'ល.រ' => $teamStaff->sequence_no,
            'ឋានន្តរស័ក្តិយោធា' => $teamStaff->military_rank,
            'គោត្តនាម-នាម' => $teamStaff->name_kh,
            'ឈ្មោះឡាតាំង' => $teamStaff->name_latin,
            'អត្តលេខ' => $teamStaff->id_number,
            'ភេទ' => $teamStaff->gender,
            'មុខតំណែង' => $teamStaff->position,
            'តួនាទី' => $teamStaff->role,
            'លេខទូរស័ព្ទ' => $teamStaff->phone_number,
            'ថ្ងៃបង្កើត' => optional($teamStaff->created_at)?->khFormat('d/m/Y H:i'),
            'ថ្ងៃកែប្រែ' => optional($teamStaff->updated_at)?->khFormat('d/m/Y H:i'),
        ];
        $documents = collect($teamStaff->documents ?? [])->values();
    @endphp

    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-screen lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'staff-management'])

                <main class="flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => 'ព័ត៌មានលម្អិតបុគ្គលិក',
                        'subtitle' => 'កាតាឡុក ២ / ក្រុមបុគ្គលិកទី៣',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                        'currentSection' => 'staff-management',
                    ])

                    <div class="flex-1 p-4 sm:p-6 lg:p-8">
                        <section class="grid gap-6 xl:grid-cols-[1.3fr_0.9fr]">
                            <div class="space-y-6">
                                <div class="dashboard-surface p-6">
                                    <div class="flex flex-col gap-5 md:flex-row md:items-center">
                                        @if ($teamStaff->hasStoredAvatar())
                                            <img src="{{ route('team-staff.avatar', $teamStaff) }}" alt="{{ $teamStaff->name_latin }}" class="h-28 w-28 rounded-full object-cover ring-1 ring-slate-200">
                                        @else
                                            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-slate-900 text-3xl font-bold text-white ring-1 ring-slate-200">
                                                {{ strtoupper(substr($teamStaff->name_latin ?: $teamStaff->name_kh, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">ក្រុមបុគ្គលិកទី៣ ៣</p>
                                            <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $teamStaff->name_kh }}</h3>
                                            <p class="mt-2 text-sm text-slate-500">{{ $teamStaff->name_latin }}</p>
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $teamStaff->military_rank }}</span>
                                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $teamStaff->role }}</span>
                                            </div>
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
                                    <h3 class="text-2xl font-semibold tracking-tight text-slate-950">ឯកសារ</h3>
                                    <div class="mt-5 grid gap-3">
                                        @forelse ($documents as $index => $document)
                                            <a href="{{ route('team-staff.documents.download', [$teamStaff, $index]) }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-[#f8fafc] px-4 py-4 transition hover:bg-slate-50">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">{{ $document['label'] ?? 'ឯកសារ '.($index + 1) }}</p>
                                                    <p class="mt-1 text-sm text-slate-500">{{ $document['original_name'] ?? '-' }}</p>
                                                </div>
                                                <span class="text-sm font-semibold text-[#356AE6]">ទាញយក</span>
                                            </a>
                                        @empty
                                            <p class="text-sm text-slate-500">មិនមានឯកសារដែលបានផ្ទុកឡើងសម្រាប់កំណត់ត្រាបុគ្គលិកនេះទេ។</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="dashboard-surface p-6">
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('team-staff.edit', $teamStaff) }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">កែប្រែ</a>
                                        <form method="POST" action="{{ route('team-staff.destroy', $teamStaff) }}" class="flex-1" data-swal-confirm data-swal-title="បញ្ជាក់ការលុប" data-swal-text="តើអ្នកពិតជាចង់លុបកំណត់ត្រាបុគ្គលិកនេះមែនទេ?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-rose-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-600">លុប</button>
                                        </form>
                                    </div>

                                    <a href="{{ route('admin.home', ['section' => 'staff-management']) }}" class="mt-4 inline-flex items-center rounded-2xl border border-slate-200 bg-[#f8fafc] px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">ត្រឡប់ទៅគ្រប់គ្រងបុគ្គលិក</a>
                                </div>
                            </div>
                        </section>
                    </div>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
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
