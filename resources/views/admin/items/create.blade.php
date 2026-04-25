@extends('app')

@section('body')
    <div class="w-full">
        <div class="dashboard-shell">
            <div class="grid min-h-[calc(100vh-3.5rem)] lg:grid-cols-[312px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => 'items'])

                <main class="flex min-h-full flex-col bg-[#f5f7fb]">
                    @include('admin.partials.topbar', [
                        'title' => 'Create Item',
                        'subtitle' => 'Version 1',
                        'filters' => ['search' => ''],
                        'pendingNotifications' => 0,
                    ])

                    <div class="flex-1 p-4 sm:p-6">
                        <section class="mx-auto max-w-3xl">
                            <div class="dashboard-surface p-6">
                                <h3 class="text-2xl font-semibold tracking-tight text-slate-950">Create Item</h3>
                                <p class="mt-2 text-sm text-slate-500">This creates the main item and version 1 history row.</p>

                                <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                                    @include('admin.items._form', ['item' => $item])
                                </form>
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
