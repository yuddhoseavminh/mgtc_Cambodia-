<section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">បញ្ជីឋានន្តរស័ក្តិបុគ្គលិកសាកល្បង</h3>
        <p class="mt-2 text-sm text-slate-500">គ្រប់គ្រងជម្រើសឋានន្តរស័ក្តិដែលបង្ហាញលើទម្រង់ចុះឈ្មោះសាធារណៈរបស់បុគ្គលិកសាកល្បង។</p>
    </div>

    <a href="{{ route('test-taking-staff-ranks.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(53,106,230,0.22)] transition hover:bg-[#204ec7]">
        បន្ថែមឋានន្តរស័ក្តិ
    </a>
</section>

<section class="dashboard-surface p-5 sm:p-6">
    <div class="overflow-x-auto">
        <table class="admin-data-table min-w-full text-left">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                    <th class="px-4 py-4">គោត្តនាម-នាម</th>
                    <th class="px-4 py-4">ឈ្មោះអង់គ្លេស</th>
                    <th class="px-4 py-4">លំដាប់</th>
                    <th class="px-4 py-4">ស្ថានភាព</th>
                    <th class="px-4 py-4 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($testTakingStaffRanks as $rank)
                    <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                        <td class="px-4 py-5 font-semibold text-slate-950" data-label="ឈ្មោះខ្មែរ" data-table-primary>{{ $rank->name_kh }}</td>
                        <td class="px-4 py-5" data-label="ឈ្មោះអង់គ្លេស">{{ $rank->name_en }}</td>
                        <td class="px-4 py-5" data-label="លំដាប់">{{ $rank->sort_order }}</td>
                        <td class="px-4 py-5" data-label="ស្ថានភាព">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $rank->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $rank->is_active ? 'សកម្ម' : 'មិនសកម្ម' }}
                            </span>
                        </td>
                        <td class="px-4 py-5" data-label="សកម្មភាព" data-table-actions>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('test-taking-staff-ranks.edit', $rank) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                                    កែប្រែ
                                </a>
                                <form method="POST" action="{{ route('test-taking-staff-ranks.destroy', $rank) }}" data-swal-confirm data-swal-title="បញ្ជាក់ការលុប" data-swal-text="តើអ្នកពិតជាចង់លុបឋានន្តរស័ក្តិនេះមែនទេ?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-xl bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 transition hover:bg-rose-100">
                                        លុប
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center text-sm text-slate-500">មិនមានឋានន្តរស័ក្តិបុគ្គលិកសាកល្បងទេ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
