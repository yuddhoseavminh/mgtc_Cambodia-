<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function create(): View
    {
        abort_unless(request()->user()?->canDo('users', 'create') || request()->user()?->is_admin, 403);

        return view('admin.users.create', [
            'section' => 'users',
            'filters' => ['search' => ''],
            'pendingNotifications' => Application::query()->where('status', 'Pending')->count(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
            'role' => ['required', Rule::in(['Staff', 'Management'])],
            'is_admin' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:100'],
        ]);

        $selectedPermissions = collect($validated['permissions'] ?? [])
            ->filter(static fn (mixed $permission): bool => is_string($permission) && $permission !== '');
        $isAdmin = (bool) ($validated['is_admin'] ?? false);
        $role = $validated['role'];
        $permissions = $selectedPermissions->values()->all();

        abort_unless($request->user()?->canDo('users', 'create') || $request->user()?->is_admin, 403);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => $isAdmin,
            'role' => $role,
            'permissions' => $permissions,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User account created successfully.',
                'user' => $user,
            ], 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'users'])
            ->with('status', 'User account created successfully.');
    }

    public function update(Request $request, User $user): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->canDo('users', 'update') || $request->user()?->is_admin, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $isAdmin = (bool) ($validated['is_admin'] ?? false);

        if ($request->user()?->is($user) && ! $isAdmin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You cannot remove your own administrator access.',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'users'])
                ->withErrors(['users' => 'You cannot remove your own administrator access.']);
        }

        if ($user->is_admin && ! $isAdmin && ! $this->hasAnotherAdmin($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'At least one administrator account must remain active.',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'users'])
                ->withErrors(['users' => 'At least one administrator account must remain active.']);
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $isAdmin,
        ];

        if (filled($validated['password'] ?? null)) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User account updated successfully.',
                'user' => $user->fresh(),
            ]);
        }

        return redirect()
            ->route('admin.home', ['section' => 'users'])
            ->with('status', 'User account updated successfully.');
    }

    public function destroy(Request $request, User $user): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->canDo('users', 'delete') || $request->user()?->is_admin, 403);

        if ($request->user()?->is($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You cannot delete the account you are currently using.',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'users'])
                ->withErrors(['users' => 'You cannot delete the account you are currently using.']);
        }

        if ($user->is_admin && ! $this->hasAnotherAdmin($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'At least one administrator account must remain active.',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'users'])
                ->withErrors(['users' => 'At least one administrator account must remain active.']);
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User account deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.home', ['section' => 'users'])
            ->with('status', 'User account deleted successfully.');
    }

    private function hasAnotherAdmin(User $user): bool
    {
        return User::query()
            ->where('is_admin', true)
            ->whereKeyNot($user->id)
            ->exists();
    }
}
