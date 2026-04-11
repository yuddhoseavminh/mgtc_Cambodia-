@php
    $currentUser = auth()->user();
    $moduleLabelsKh = [
        'dashboard' => 'ផ្ទាំងគ្រប់គ្រង',
        'reports' => 'របាយការណ៍',
        'applications' => 'ពាក្យស្នើសុំ',
        'documents' => 'ឯកសារ',
        'courses' => 'វគ្គសិក្សា',
        'ranks' => 'ឋានន្តរសក្តិ',
        'levels' => 'កម្រិតសិក្សា',
        'design-template' => 'គំរូទំព័រដើម',
        'course-template' => 'គំរូវគ្គសិក្សា',
        'staff-team' => 'បុគ្គលិកក្រុម',
        'staff-management' => 'គ្រប់គ្រងបុគ្គលិក',
        'staff-team-documents' => 'ឯកសារបុគ្គលិកក្រុម',
        'staff-team-ranks' => 'ឋានន្តរសក្តិបុគ្គលិកក្រុម',
        'test-taking-staff' => 'បុគ្គលិកសាកល្បង',
        'test-taking-staff-template' => 'គំរូបុគ្គលិកសាកល្បង',
        'test-taking-staff-ranks' => 'ឋានន្តរសក្តិបុគ្គលិកសាកល្បង',
        'test-taking-staff-documents' => 'ឯកសារបុគ្គលិកសាកល្បង',
        'register-staff' => 'បុគ្គលិកបានចុះឈ្មោះ',
        'users' => 'អ្នកប្រើប្រាស់',
        'profile' => 'ប្រវត្តិរូប',
    ];
    $actionLabelsKh = [
        'create' => 'បង្កើត',
        'read' => 'មើល',
        'update' => 'កែប្រែ',
        'delete' => 'លុប',
    ];
    $userSummary = [
        ['label' => 'គណនីសរុប', 'value' => $stats['totalUsers'], 'tone' => 'bg-slate-100 text-slate-700'],
        ['label' => 'គ្រប់គ្រង', 'value' => $stats['adminTeamUsers'], 'tone' => 'bg-emerald-100 text-emerald-700'],
        ['label' => 'បុគ្គលិក', 'value' => $stats['registerStaffUsers'], 'tone' => 'bg-sky-100 text-sky-700'],
    ];
    $canCreateUsers = (bool) ($currentUser?->canDo('users', 'create') || $currentUser?->is_admin);
    $canUpdateUsers = (bool) ($currentUser?->canDo('users', 'update') || $currentUser?->is_admin);
    $canDeleteUsers = (bool) ($currentUser?->canDo('users', 'delete') || $currentUser?->is_admin);
    $createSystemUserUrl = \Illuminate\Support\Facades\Route::has('admin.users.create')
        ? route('admin.users.create')
        : url('/admin/users/create');
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

@if (session('status'))
    <section class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
        {{ session('status') }}
    </section>
@endif

<section class="dashboard-surface p-6">
    <div class="flex flex-col gap-3 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-[1.6rem] font-semibold tracking-tight text-slate-950">អ្នកប្រើប្រាស់ប្រព័ន្ធ</h3>
            <p class="mt-2 text-sm text-slate-500">បង្កើត កែប្រែ ឬលុបគណនីអ្នកប្រើប្រាស់ក្នុងប្រព័ន្ធ។</p>
        </div>
        @if ($canCreateUsers)
            <a href="{{ $createSystemUserUrl }}" class="inline-flex items-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">
                បង្កើតអ្នកប្រើប្រាស់ប្រព័ន្ធ
            </a>
        @endif
    </div>

    @if ($errors->has('users'))
        <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ $errors->first('users') }}
        </div>
    @endif

    <div class="mt-5 space-y-4">
        @forelse ($users as $user)
            @php
                $userRole = $user->role ?: ($user->is_admin ? 'Management' : 'Staff');
                $userRoleLabel = $userRole === 'Management' ? 'គ្រប់គ្រង' : 'បុគ្គលិក';
                $userPermissions = collect($user->permissions ?? [])
                    ->filter(fn ($permission) => is_string($permission) && $permission !== '')
                    ->values();
            @endphp
            <details class="overflow-hidden rounded-[1.6rem] border border-slate-200 bg-white">
                <summary class="flex cursor-pointer list-none flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="truncate text-base font-semibold text-slate-950">{{ $user->name }}</p>
                        <p class="mt-1 break-all text-sm text-slate-500">{{ $user->email }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $userRole === 'Management' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                            {{ $userRoleLabel }}
                        </span>
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            {{ optional($user->created_at)?->format('Y-m-d H:i') ?: 'មិនស្គាល់' }}
                        </span>
                    </div>
                </summary>

                <div class="border-t border-slate-200 px-5 py-5">
                    <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-600">សិទ្ធិអ្នកប្រើប្រាស់</p>
                        @if ($user->isSuperAdmin())
                            <p class="mt-2 text-sm font-semibold text-emerald-700">អ្នកគ្រប់គ្រង: មានសិទ្ធិទាំងអស់</p>
                        @elseif ($userPermissions->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($userPermissions as $permission)
                                    @php
                                        [$moduleKey, $actionKey] = array_pad(explode('.', $permission, 2), 2, null);
                                        $permissionLabel = ($actionLabelsKh[$actionKey] ?? $actionKey).' / '.($moduleLabelsKh[$moduleKey] ?? $moduleKey);
                                    @endphp
                                    <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                        {{ $permissionLabel }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-sm text-slate-500">មិនទាន់បានកំណត់សិទ្ធិ។</p>
                        @endif
                    </div>

                    @if ($canUpdateUsers)
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid gap-4">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-4 lg:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-900">ឈ្មោះពេញ</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="form-input">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-900">អ៊ីមែល</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="form-input">
                                </div>
                            </div>

                            <div class="grid gap-4 lg:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-900">ពាក្យសម្ងាត់ថ្មី</label>
                                    <input type="password" name="password" class="form-input" placeholder="ទុកឱ្យទទេ បើមិនប្តូរពាក្យសម្ងាត់">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-900">បញ្ជាក់ពាក្យសម្ងាត់ថ្មី</label>
                                    <input type="password" name="password_confirmation" class="form-input">
                                </div>
                            </div>

                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <input type="hidden" name="is_admin" value="0">
                                <input type="checkbox" name="is_admin" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked($user->is_admin)>
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-900">សិទ្ធិអ្នកគ្រប់គ្រង</span>
                                    <span class="mt-1 block text-sm leading-6 text-slate-500">អ្នកគ្រប់គ្រងអាចចូលដំណើរការមុខងារទាំងអស់ក្នុងផ្ទាំងគ្រប់គ្រង។</span>
                                </span>
                            </label>

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="inline-flex min-h-[2.9rem] items-center justify-center rounded-2xl bg-slate-950 px-5 text-sm font-semibold text-white">
                                    រក្សាទុកការកែប្រែ
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                            អ្នកមិនមានសិទ្ធិកែប្រែអ្នកប្រើប្រាស់នេះទេ។
                        </div>
                    @endif

                    @if ($canDeleteUsers)
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-[2.9rem] items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 px-5 text-sm font-semibold text-rose-700">
                                លុបអ្នកប្រើប្រាស់
                            </button>
                        </form>
                    @endif
                </div>
            </details>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                មិនមានអ្នកប្រើប្រាស់ក្នុងប្រព័ន្ធទេ។
            </div>
        @endforelse
    </div>
</section>
