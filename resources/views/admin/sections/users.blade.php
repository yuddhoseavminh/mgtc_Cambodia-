@php
    $userSummary = [
        ['label' => 'អ្នកប្រើប្រាស់សរុប', 'value' => $stats['totalUsers'], 'tone' => 'bg-slate-100 text-slate-700'],
        ['label' => 'អ្នកគ្រប់គ្រង', 'value' => $stats['adminTeamUsers'], 'tone' => 'bg-emerald-100 text-emerald-700'],
        ['label' => 'បុគ្គលិកចុះឈ្មោះ', 'value' => $stats['registerStaffUsers'], 'tone' => 'bg-sky-100 text-sky-700'],
    ];
@endphp

<section class="grid gap-4 xl:grid-cols-3">
    @foreach ($userSummary as $card)
        <article class="dashboard-mini-card p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $card['label'] }}</p>
            <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
            <span class="mt-4 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $card['tone'] }}">{{ $card['label'] }}</span>
        </article>
    @endforeach
</section>

<section class="dashboard-surface p-6">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h3 class="text-[1.9rem] font-semibold tracking-tight text-slate-950">បញ្ជីអ្នកប្រើប្រាស់</h3>
            <p class="mt-2 text-sm text-slate-500">ទិដ្ឋភាពគណនីរួមសម្រាប់អ្នកគ្រប់គ្រង និងបុគ្គលិកចុះឈ្មោះ។</p>
        </div>
        <a href="{{ route('admin.home', ['section' => 'profile']) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">បើកប្រវត្តិរូប</a>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="admin-data-table min-w-full text-left">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                    <th class="px-4 py-4">ឈ្មោះ</th>
                    <th class="px-4 py-4">អ៊ីមែល</th>
                    <th class="px-4 py-4">តួនាទី</th>
                    <th class="px-4 py-4">ថ្ងៃចូលរួម</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                        <td class="px-4 py-5 font-semibold text-slate-950" data-label="ឈ្មោះ" data-table-primary>{{ $user->name }}</td>
                        <td class="px-4 py-5" data-label="អ៊ីមែល">{{ $user->email }}</td>
                        <td class="px-4 py-5" data-label="តួនាទី">
                            @if ($user->is_admin)
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">អ្នកគ្រប់គ្រង</span>
                            @else
                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">បុគ្គលិកចុះឈ្មោះ</span>
                            @endif
                        </td>
                        <td class="px-4 py-5">{{ optional($user->created_at)?->khFormat('d/m/Y H:i') ?: 'មិនមាន' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-sm text-slate-500">មិនមានគណនីអ្នកប្រើប្រាស់ទេ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
