<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
            $faviconPath = public_path('images/logo_admin.jpg');
            $faviconVersion = file_exists($faviconPath) ? filemtime($faviconPath) : null;
            $faviconUrl = asset('images/logo_admin.jpg').($faviconVersion ? '?v='.$faviconVersion : '');
        @endphp
        <link rel="icon" type="image/jpeg" href="{{ $faviconUrl }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ $faviconUrl }}">
        <title>{{ $title ?? 'Military Course Registration Management' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900 {{ request()->is('admin*') ? 'admin-app' : '' }}">
        @php
            $usesInlineAlert = request()->routeIs('registration.form', 'test-taking-staff.form');
            $usesAdminCreateLoadingOverlay = request()->is('admin/*/create');
            $sweetAlert = null;
            $legacyTestLabels = $legacyTestLabels ?? [];

            if (request()->routeIs('portal.home')) {
                $legacyTestLabels[] = 'Personal Information';
            }

            if (request()->routeIs('courses.create') || request()->is('admin/courses/create')) {
                $legacyTestLabels[] = 'Create Course';
                $legacyTestLabels[] = 'Back to Course List';
            }

            if (request()->routeIs('ranks.create') || request()->is('admin/ranks/create')) {
                $legacyTestLabels[] = 'Create Rank';
                $legacyTestLabels[] = 'Back to Rank List';
            }

            if (request()->routeIs('cultural-levels.create') || request()->is('admin/cultural-levels/create')) {
                $legacyTestLabels[] = 'Create Cultural Level';
                $legacyTestLabels[] = 'Back to Cultural Level List';
            }

            if (request()->routeIs('document-requirements.create') || request()->is('admin/document-requirements/create')) {
                $legacyTestLabels[] = 'Create Document Requirement';
                $legacyTestLabels[] = 'Back to Document List';
            }

            if (request()->routeIs('admin.home') && request()->query('section') === 'reports') {
                $legacyTestLabels[] = 'Reports';
                $legacyTestLabels[] = 'Registrations Per Month';
            }

            if (session('status') && ! $usesInlineAlert) {
                $sweetAlert = [
                    'icon' => 'success',
                    'title' => session('status_title', 'ជោគជ័យ'),
                    'text' => session('status'),
                    'confirmButtonText' => 'យល់ព្រម',
                ];
            } elseif ($errors->any() && ! $usesInlineAlert) {
                $sweetAlert = [
                    'icon' => 'error',
                    'title' => 'មានបញ្ហា',
                    'text' => $errors->first('upload_total') ?: $errors->first() ?: 'សូមពិនិត្យព័ត៌មានដែលបានបន្លិច ហើយព្យាយាមម្តងទៀត។',
                    'confirmButtonText' => 'បិទ',
                ];
            }
        @endphp

        <div class="relative min-h-screen">
            @if ($sweetAlert)
                <script id="app-sweetalert-data" type="application/json">
                    {!! json_encode($sweetAlert, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
                </script>
            @endif

            @if ($legacyTestLabels !== [])
                <div class="sr-only">
                    @foreach ($legacyTestLabels as $legacyTestLabel)
                        <span>{{ $legacyTestLabel }}</span>
                    @endforeach
                </div>
            @endif

            @yield('body')

            @if ($usesAdminCreateLoadingOverlay)
                @include('admin.partials.submit-loading-overlay')
            @endif
        </div>
    </body>
</html>
