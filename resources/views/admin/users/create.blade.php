@extends('app')

@section('body')
    @php
        $permissionModules = [
            ['key' => 'dashboard', 'label' => 'ផ្ទាំងគ្រប់គ្រង'],
            ['key' => 'reports', 'label' => 'របាយការណ៍'],
            ['key' => 'applications', 'label' => 'ពាក្យស្នើសុំ'],
            ['key' => 'documents', 'label' => 'ឯកសារ'],
            ['key' => 'courses', 'label' => 'វគ្គសិក្សា'],
            ['key' => 'ranks', 'label' => 'ឋានន្តរសក្តិ'],
            ['key' => 'levels', 'label' => 'កម្រិតសិក្សា'],
            ['key' => 'design-template', 'label' => 'គំរូទំព័រដើម'],
            ['key' => 'course-template', 'label' => 'គំរូវគ្គសិក្សា'],
            ['key' => 'staff-team', 'label' => 'បុគ្គលិកក្រុម'],
            ['key' => 'staff-management', 'label' => 'គ្រប់គ្រងបុគ្គលិក'],
            ['key' => 'staff-team-documents', 'label' => 'ឯកសារបុគ្គលិកក្រុម'],
            ['key' => 'staff-team-ranks', 'label' => 'ឋានន្តរសក្តិបុគ្គលិកក្រុម'],
            ['key' => 'test-taking-staff', 'label' => 'បុគ្គលិកសាកល្បង'],
            ['key' => 'test-taking-staff-template', 'label' => 'គំរូបុគ្គលិកសាកល្បង'],
            ['key' => 'test-taking-staff-ranks', 'label' => 'ឋានន្តរសក្តិបុគ្គលិកសាកល្បង'],
            ['key' => 'test-taking-staff-documents', 'label' => 'ឯកសារបុគ្គលិកសាកល្បង'],
            ['key' => 'register-staff', 'label' => 'បុគ្គលិកបានចុះឈ្មោះ'],
            ['key' => 'users', 'label' => 'អ្នកប្រើប្រាស់'],
            ['key' => 'profile', 'label' => 'ប្រវត្តិរូប'],
        ];
        $permissionActions = [
            'create' => 'បង្កើត',
            'read' => 'មើល',
            'update' => 'កែប្រែ',
            'delete' => 'លុប',
        ];
        $defaultPermissions = [];
        foreach ($permissionModules as $module) {
            foreach (array_keys($permissionActions) as $actionKey) {
                $defaultPermissions[] = $module['key'] . '.' . $actionKey;
            }
        }
        $selectedPermissions = (array) old(
            'permissions',
            old('is_admin', '1') === '1' ? $defaultPermissions : []
        );
        $selectedRole = old('role', ! empty($selectedPermissions) ? 'Management' : 'Staff');
    @endphp

    <div class="admin-dashboard w-full">
        <div class="dashboard-shell">
            <div class="admin-dashboard-grid grid lg:grid-cols-[286px_minmax(0,1fr)]">
                @include('admin.partials.sidebar', ['section' => $section])

                <main class="admin-main flex min-h-full flex-col bg-transparent">
                    @include('admin.partials.topbar', [
                        'title' => 'បង្កើតអ្នកប្រើប្រាស់ប្រព័ន្ធ',
                        'subtitle' => 'សិទ្ធិប្រព័ន្ធ',
                        'filters' => $filters,
                        'pendingNotifications' => $pendingNotifications,
                        'currentSection' => $section,
                    ])

                    <div class="admin-content flex-1 space-y-6 p-4 sm:p-6 lg:p-8">
                        @if ($errors->any())
                            <section class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                                {{ $errors->first() }}
                            </section>
                        @endif

                        <section class="dashboard-surface p-6 sm:p-7">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-5">
                                <div>
                                    <h2 class="text-[1.6rem] font-semibold tracking-tight text-slate-950">បង្កើតអ្នកប្រើប្រាស់ប្រព័ន្ធ</h2>
                                    <p class="mt-2 text-sm text-slate-500">បង្កើតគណនីអ្នកប្រើប្រាស់ និងជ្រើសសិទ្ធិ (បង្កើត/មើល/កែប្រែ/លុប) តាមតម្រូវការ។</p>
                                </div>
                                <a href="{{ route('admin.home', ['section' => 'users']) }}" class="inline-flex min-h-[2.9rem] items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700">
                                    ត្រឡប់ទៅបញ្ជីអ្នកប្រើប្រាស់
                                </a>
                            </div>

                            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 space-y-5">
                                @csrf

                                <div class="grid gap-4 lg:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-900">ឈ្មោះ / ឈ្មោះចូលប្រើ</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-input bg-slate-50" placeholder="ឈ្មោះពេញ ឬ ឈ្មោះចូលប្រើ" required>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-900">អ៊ីមែល / លេខសម្គាល់ចូល / ឈ្មោះចូលប្រើ</label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-input bg-slate-50" placeholder="admin@gmail.com" required>
                                    </div>
                                </div>

                                <div class="grid gap-4 lg:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-900">ពាក្យសម្ងាត់</label>
                                        <input type="password" name="password" class="form-input bg-slate-50" placeholder="យ៉ាងតិច 12 តួអក្សរ" required>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-900">បញ្ជាក់ពាក្យសម្ងាត់</label>
                                        <input type="password" name="password_confirmation" class="form-input bg-slate-50" placeholder="បញ្ចូលម្តងទៀត" required>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-600">តួនាទីដែលត្រូវកំណត់</p>
                                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-3 py-3">
                                            <input type="radio" name="role" value="Management" class="mt-1 h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500" data-role-radio @checked($selectedRole === 'Management')>
                                            <span class="min-w-0">
                                                <span class="block text-sm font-semibold text-slate-900">គ្រប់គ្រង</span>
                                                <span class="mt-1 block text-xs text-slate-500">អាចចូលផ្ទាំងគ្រប់គ្រង និងគ្រប់គ្រងមុខងារទាំងអស់។</span>
                                            </span>
                                        </label>
                                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-3 py-3">
                                            <input type="radio" name="role" value="Staff" class="mt-1 h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500" data-role-radio @checked($selectedRole === 'Staff')>
                                            <span class="min-w-0">
                                                <span class="block text-sm font-semibold text-slate-900">បុគ្គលិក</span>
                                                <span class="mt-1 block text-xs text-slate-500">តួនាទីកំណត់ មានសិទ្ធិតាមអ្វីដែលបានជ្រើសតែប៉ុណ្ណោះ។</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4">
                                    <label class="flex items-start gap-3">
                                        <input type="hidden" name="is_admin" value="0">
                                        <input
                                            type="checkbox"
                                            name="is_admin"
                                            value="1"
                                            class="mt-1 h-4 w-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500"
                                            data-permission-select-all
                                            @checked(old('is_admin', ! empty($selectedPermissions) ? '1' : '0') === '1')
                                        >
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-emerald-800">សិទ្ធិគ្រប់គ្រងពេញលេញ</span>
                                            <span class="mt-1 block text-sm leading-6 text-emerald-700">ពេលជ្រើសសិទ្ធិ នឹងកំណត់តួនាទីជា «គ្រប់គ្រង»។</span>
                                        </span>
                                    </label>

                                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.08em] text-emerald-700">ជ្រើសសិទ្ធិ (បង្កើត/មើល/កែប្រែ/លុប)</p>
                                    <div class="mt-2 overflow-x-auto rounded-xl border border-emerald-100 bg-white">
                                        <table class="min-w-full text-sm text-emerald-900">
                                            <thead class="bg-emerald-50 text-xs uppercase tracking-[0.08em] text-emerald-700">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-semibold">ម៉ូឌុល (ជ្រើសទាំងអស់)</th>
                                                    @foreach ($permissionActions as $actionLabel)
                                                        <th class="px-2 py-2 text-center font-semibold">{{ $actionLabel }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($permissionModules as $module)
                                                    <tr class="border-t border-emerald-100">
                                                        <td class="px-3 py-2 font-medium">
                                                            <label class="inline-flex items-center gap-2">
                                                                <input
                                                                    type="checkbox"
                                                                    class="h-4 w-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500"
                                                                    data-module-select-all
                                                                    data-module-key="{{ $module['key'] }}"
                                                                >
                                                                <span>{{ $module['label'] }}</span>
                                                            </label>
                                                        </td>
                                                        @foreach (array_keys($permissionActions) as $actionKey)
                                                            @php
                                                                $permissionValue = $module['key'] . '.' . $actionKey;
                                                            @endphp
                                                            <td class="px-2 py-2 text-center">
                                                                <input
                                                                    type="checkbox"
                                                                    name="permissions[]"
                                                                    value="{{ $permissionValue }}"
                                                                    class="h-4 w-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500"
                                                                    data-permission-item
                                                                    data-module-key="{{ $module['key'] }}"
                                                                    @checked(in_array($permissionValue, $selectedPermissions, true))
                                                                >
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="grid gap-3 border-t border-slate-200 pt-4 sm:grid-cols-2">
                                    <a href="{{ route('admin.home', ['section' => 'users']) }}" class="inline-flex min-h-[3rem] items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700">
                                        បោះបង់
                                    </a>
                                    <button type="submit" class="inline-flex min-h-[3rem] items-center justify-center rounded-2xl bg-blue-600 px-5 text-sm font-semibold text-white">
                                        បង្កើតអ្នកប្រើប្រាស់
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>

                    <footer class="admin-footer-band flex flex-col gap-3 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <p>&copy; {{ now()->year }} Copyright By Yuddho Seavminh</p>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">V1.0.0</span>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const selectAllCheckbox = document.querySelector('[data-permission-select-all]');
            const permissionCheckboxes = Array.from(document.querySelectorAll('[data-permission-item]'));
            const moduleSelectAllCheckboxes = Array.from(document.querySelectorAll('[data-module-select-all]'));
            const roleRadios = Array.from(document.querySelectorAll('[data-role-radio]'));

            if (!selectAllCheckbox || permissionCheckboxes.length === 0) {
                return;
            }

            const setSelectedRole = (roleValue) => {
                roleRadios.forEach((radio) => {
                    radio.checked = radio.value === roleValue;
                });
            };

            const syncAllCheckbox = () => {
                selectAllCheckbox.checked = permissionCheckboxes.every((checkbox) => checkbox.checked);
            };

            const syncModuleCheckbox = (moduleKey) => {
                const moduleCheckbox = moduleSelectAllCheckboxes.find((checkbox) => checkbox.dataset.moduleKey === moduleKey);

                if (!moduleCheckbox) {
                    return;
                }

                const modulePermissionCheckboxes = permissionCheckboxes.filter((checkbox) => checkbox.dataset.moduleKey === moduleKey);
                moduleCheckbox.checked = modulePermissionCheckboxes.length > 0 && modulePermissionCheckboxes.every((checkbox) => checkbox.checked);
            };

            const syncAllModuleCheckboxes = () => {
                moduleSelectAllCheckboxes.forEach((checkbox) => {
                    syncModuleCheckbox(checkbox.dataset.moduleKey);
                });
            };

            const syncAssignedRole = () => {
                const hasPermissions = permissionCheckboxes.some((checkbox) => checkbox.checked);
                setSelectedRole(hasPermissions ? 'Management' : 'Staff');
            };

            selectAllCheckbox.addEventListener('change', () => {
                permissionCheckboxes.forEach((checkbox) => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                syncAllModuleCheckboxes();
                syncAssignedRole();
            });

            moduleSelectAllCheckboxes.forEach((moduleCheckbox) => {
                moduleCheckbox.addEventListener('change', () => {
                    const moduleKey = moduleCheckbox.dataset.moduleKey;
                    const modulePermissionCheckboxes = permissionCheckboxes.filter((checkbox) => checkbox.dataset.moduleKey === moduleKey);

                    modulePermissionCheckboxes.forEach((checkbox) => {
                        checkbox.checked = moduleCheckbox.checked;
                    });

                    syncAllCheckbox();
                    syncAssignedRole();
                });
            });

            permissionCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    syncModuleCheckbox(checkbox.dataset.moduleKey);
                    syncAllCheckbox();
                    syncAssignedRole();
                });
            });

            roleRadios.forEach((radio) => {
                radio.addEventListener('change', () => {
                    if (!radio.checked) {
                        return;
                    }

                    if (radio.value === 'Staff') {
                        permissionCheckboxes.forEach((checkbox) => {
                            checkbox.checked = false;
                        });
                        selectAllCheckbox.checked = false;
                        syncAllModuleCheckboxes();
                    }
                });
            });

            syncAllModuleCheckboxes();
            syncAllCheckbox();
        })();
    </script>
@endsection
