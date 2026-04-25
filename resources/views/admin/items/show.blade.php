@extends('app')

@section('body')
    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-[calc(100vh-3.5rem)] lg:grid-cols-[312px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'items'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => 'Item Details',
                        'subtitle' => 'Latest and history',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
                            <div class="space-y-6">
                                <div class="dashboard-surface p-6">
                                    <div class="flex flex-col gap-5 sm:flex-row">
                                        @if ($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-32 w-32 rounded-3xl object-cover ring-1 ring-slate-200">
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Current Version v{{ $item->version_no }}</p>
                                            <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $item->title }}</h3>
                                            <p class="mt-2 text-xl font-semibold text-slate-700">${{ number_format((float) $item->price, 2) }}</p>
                                            <p class="mt-3 text-sm leading-7 text-slate-500">{{ $item->description ?: 'No description.' }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-200 pt-6">
                                        <a href="{{ route('admin.items.edit', $item) }}" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">Edit / New Version</a>
                                        <a href="{{ route('admin.items.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back</a>
                                        <form method="POST" action="{{ route('admin.items.destroy', $item) }}" onsubmit="return confirm('Soft delete this item? Version rows and image files will remain.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">Soft Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="dashboard-surface p-6">
                                <h3 class="text-2xl font-semibold tracking-tight text-slate-950">Version History</h3>
                                <div class="mt-6 space-y-4">
                                    @foreach ($item->versions as $version)
                                        <article class="rounded-2xl border border-slate-200 bg-white p-4">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-950">Version {{ $version->version_no }}</p>
                                                    <p class="mt-1 text-xs text-slate-500">{{ $version->created_at?->format('Y-m-d h:i A') }} by {{ $version->updater?->name ?? 'System' }}</p>
                                                </div>
                                                @if ($version->is_current)
                                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">Current</span>
                                                @endif
                                            </div>

                                            <div class="mt-4 flex gap-4">
                                                @if ($version->image_url)
                                                    <a href="{{ $version->image_url }}" target="_blank" rel="noreferrer">
                                                        <img src="{{ $version->image_url }}" alt="{{ $version->title }}" class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200">
                                                    </a>
                                                @endif
                                                <div class="min-w-0 text-sm">
                                                    <p class="font-semibold text-slate-900">{{ $version->title }}</p>
                                                    <p class="mt-1 text-slate-600">${{ number_format((float) $version->price, 2) }}</p>
                                                    <p class="mt-2 line-clamp-3 text-slate-500">{{ $version->description ?: 'No description.' }}</p>
                                                    @if ($version->change_note)
                                                        <p class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500">{{ $version->change_note }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
