<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $routeName = (string) optional($request->route())->getName();

        if (! $user || ! $user->hasAdminAccess()) {
            if (! $request->expectsJson()) {
                abort(403, 'Administrator access is required.');
            }

            return new JsonResponse([
                'message' => 'Administrator access is required.',
            ], 403);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Let AdminPageController choose the first allowed section (UI-hide behavior).
        if ($routeName === 'admin.home') {
            return $next($request);
        }

        $requiredPermission = $this->resolveRequiredPermission($request);

        if ($requiredPermission === null) {
            if ($request->isMethod('GET') && ! $request->expectsJson()) {
                return redirect()->route('admin.home', ['section' => 'profile']);
            }

            if (! $request->expectsJson()) {
                abort(403, 'You do not have permission to access this page.');
            }

            return new JsonResponse([
                'message' => 'You do not have permission to access this page.',
            ], 403);
        }

        if (! $user->hasPermission($requiredPermission)) {
            if ($request->isMethod('GET') && ! $request->expectsJson()) {
                return redirect()->route('admin.home', ['section' => 'profile']);
            }

            if (! $request->expectsJson()) {
                abort(403, 'You do not have permission for this action.');
            }

            return new JsonResponse([
                'message' => 'You do not have permission for this action.',
            ], 403);
        }

        return $next($request);
    }

    private function resolveRequiredPermission(Request $request): ?string
    {
        $routeName = (string) optional($request->route())->getName();
        $action = $this->resolveAction($request);

        if ($routeName === 'admin.home') {
            $section = (string) $request->query('section', 'overview');
            $sectionPermissionMap = [
                'overview' => 'dashboard.read',
                'reports' => 'reports.read',
                'applications' => 'applications.read',
                'documents' => 'documents.read',
                'courses' => 'courses.read',
                'ranks' => 'ranks.read',
                'levels' => 'levels.read',
                'design-template' => 'design-template.read',
                'course-template' => 'course-template.read',
                'staff-team' => 'staff-team.read',
                'staff-management' => 'staff-management.read',
                'staff-team-documents' => 'staff-team-documents.read',
                'staff-team-ranks' => 'staff-team-ranks.read',
                'test-taking-staff' => 'test-taking-staff.read',
                'test-taking-staff-template' => 'test-taking-staff-template.read',
                'test-taking-staff-ranks' => 'test-taking-staff-ranks.read',
                'test-taking-staff-documents' => 'test-taking-staff-documents.read',
                'register-staff' => 'register-staff.read',
                'users' => 'users.read',
                'profile' => 'profile.read',
            ];

            return $sectionPermissionMap[$section] ?? null;
        }

        if ($routeName === 'admin.users.index') {
            return 'users.read';
        }

        if ($routeName === 'admin.profile.update') {
            return 'profile.update';
        }

        if ($routeName === 'admin.portal-content.show') {
            return 'design-template.read';
        }

        if ($routeName === 'admin.portal-content.update' || $routeName === 'admin.portal-content.course-template.update' || $routeName === 'admin.portal-content.test-taking-staff-template.update') {
            return 'design-template.update';
        }

        if (str_starts_with($routeName, 'admin.users.')) {
            return 'users.' . $action;
        }

        if (str_starts_with($routeName, 'admin.applications.')) {
            return 'applications.' . $action;
        }

        if (str_starts_with($routeName, 'admin.documents.')) {
            return 'documents.read';
        }

        if (str_starts_with($routeName, 'team-staff.documents.')) {
            return 'staff-team-documents.' . $action;
        }

        if (str_starts_with($routeName, 'team-staff-ranks.')) {
            return 'staff-team-ranks.' . $action;
        }

        if (str_starts_with($routeName, 'team-staff-document-requirements.')) {
            return 'staff-team-documents.' . $action;
        }

        if (str_starts_with($routeName, 'test-taking-staff-ranks.')) {
            return 'test-taking-staff-ranks.' . $action;
        }

        if (str_starts_with($routeName, 'test-taking-staff-document-requirements.')) {
            return 'test-taking-staff-documents.' . $action;
        }

        if (str_starts_with($routeName, 'test-taking-staff-registrations.')) {
            return 'register-staff.read';
        }

        if ($routeName === 'team-staff.update-military-rank') {
            return 'staff-management.update';
        }

        if (str_starts_with($routeName, 'team-staff.')) {
            if ($routeName === 'team-staff.index') {
                return 'staff-management.read';
            }

            return 'staff-management.' . $action;
        }

        if (str_starts_with($routeName, 'document-requirements.')) {
            return 'documents.' . $action;
        }

        if (str_starts_with($routeName, 'ranks.')) {
            return 'ranks.' . $action;
        }

        if (str_starts_with($routeName, 'courses.')) {
            return 'courses.' . $action;
        }

        if (str_starts_with($routeName, 'cultural-levels.')) {
            return 'levels.' . $action;
        }

        if ($routeName === 'admin.dashboard') {
            return 'dashboard.read';
        }

        return null;
    }

    private function resolveAction(Request $request): string
    {
        return match (strtoupper($request->method())) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'read',
        };
    }
}
