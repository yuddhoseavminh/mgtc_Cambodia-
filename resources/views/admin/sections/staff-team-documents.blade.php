<section class="dashboard-surface overflow-hidden p-6 sm:p-7">
    <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">កាតាឡុកក្រុម</p>
            <h3 class="mt-2 text-[2rem] font-semibold tracking-tight text-slate-950">បញ្ជីឯកសារបុគ្គលិកក្រុម</h3>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-500">គ្រប់គ្រងប្រភេទឯកសារដែលប្រើនៅក្នុងទម្រង់បង្កើត និងកែប្រែបុគ្គលិកក្រុម។</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                <span class="font-semibold text-slate-900">{{ $teamStaffDocumentRequirements->count() }}</span> ប្រភេទឯកសារសរុប
            </div>
            <a href="{{ route('team-staff-document-requirements.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-[0_16px_32px_rgba(15,23,42,0.14)] transition hover:bg-slate-800">
                បន្ថែមឯកសារ
            </a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="admin-data-table min-w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <th class="px-6 py-4">ឈ្មោះជាភាសាខ្មែរ</th>
                        <th class="px-6 py-4">ស្លាកតំណ</th>
                        <th class="px-6 py-4">លំដាប់</th>
                        <th class="px-6 py-4">ស្ថានភាព</th>
                        <th class="px-6 py-4 text-right">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teamStaffDocumentRequirements as $documentRequirement)
                        <tr class="border-t border-slate-100 text-sm text-slate-700 transition hover:bg-slate-50/70">
                            <td class="px-6 py-5 font-semibold text-slate-950" data-label="ឈ្មោះជាភាសាខ្មែរ" data-table-primary>{{ $documentRequirement->name_kh }}</td>
                            <td class="px-6 py-5" data-label="ស្លាកតំណ">
                                <span class="rounded-xl bg-slate-100 px-3 py-1.5 font-medium text-slate-600">{{ $documentRequirement->slug }}</span>
                            </td>
                            <td class="px-6 py-5 font-medium text-slate-900" data-label="លំដាប់">{{ $documentRequirement->sort_order }}</td>
                            <td class="px-6 py-5" data-label="ស្ថានភាព">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $documentRequirement->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $documentRequirement->is_active ? 'សកម្ម' : 'មិនសកម្ម' }}
                                </span>
                            </td>
                            <td class="px-6 py-5" data-label="សកម្មភាព" data-table-actions>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('team-staff-document-requirements.edit', $documentRequirement) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        កែប្រែ
                                    </a>
                                    <form method="POST" action="{{ route('team-staff-document-requirements.destroy', $documentRequirement) }}" data-swal-confirm data-swal-title="បញ្ជាក់ការលុប" data-swal-text="តើអ្នកពិតជាចង់លុបប្រភេទឯកសារនេះមែនទេ?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-xl bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                            លុប
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">មិនមានប្រភេទឯកសារបុគ្គលិកក្រុមទេ។</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
