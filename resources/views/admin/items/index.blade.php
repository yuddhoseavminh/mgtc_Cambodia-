@extends('app')

@section('body')
    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-[calc(100vh-3.5rem)] lg:grid-cols-[312px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'items'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => 'Items',
                        'subtitle' => 'Versioned CRUD',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="dashboard-surface overflow-hidden p-6 sm:p-7">
                            <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-2xl font-semibold tracking-tight text-slate-950">Items</h3>
                                    <p class="mt-2 text-sm text-slate-500">Latest item data with preserved version history.</p>
                                </div>
                                <a href="{{ route('admin.items.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    New Item
                                </a>
                            </div>

                            <div class="mt-6 overflow-x-auto">
                                <table class="admin-data-table min-w-full text-left">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                                            <th class="px-4 py-4">Image</th>
                                            <th class="px-4 py-4">Title</th>
                                            <th class="px-4 py-4">Price</th>
                                            <th class="px-4 py-4">Version</th>
                                            <th class="px-4 py-4">Updated By</th>
                                            <th class="px-4 py-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($items as $item)
                                            <tr class="border-b border-slate-100 text-sm text-slate-700 last:border-b-0">
                                                <td class="px-4 py-5">
                                                    @if ($item->image_url)
                                                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-12 w-12 rounded-xl object-cover ring-1 ring-slate-200">
                                                    @else
                                                        <div class="h-12 w-12 rounded-xl bg-slate-100"></div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-5 font-semibold text-slate-950">{{ $item->title }}</td>
                                                <td class="px-4 py-5">${{ number_format((float) $item->price, 2) }}</td>
                                                <td class="px-4 py-5">v{{ $item->version_no }}</td>
                                                <td class="px-4 py-5">{{ $item->updater?->name ?? '-' }}</td>
                                                <td class="px-4 py-5 text-right">
                                                    <div class="flex flex-wrap justify-end gap-2">
                                                        <a href="{{ route('admin.items.show', $item) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">View</a>
                                                        <a href="{{ route('admin.items.edit', $item) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-16 text-center text-sm text-slate-500">No items yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($items->hasPages())
                                <div class="mt-6 border-t border-slate-200 pt-5">
                                    {{ $items->links() }}
                                </div>
                            @endif
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
