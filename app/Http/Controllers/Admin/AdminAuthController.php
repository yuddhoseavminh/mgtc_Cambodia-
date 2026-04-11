<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.home');
        }

        return view('admin.login');
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'authenticated' => false,
            ]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => [
                'username' => $user->name,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['nullable', 'string'],
            'login' => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim((string) ($credentials['login'] ?? $credentials['email'] ?? $credentials['username'] ?? ''));
        $password = $credentials['password'];

        if ($login === '') {
            throw ValidationException::withMessages([
                'email' => 'Email, login ID, or username is required.',
            ]);
        }

        $authenticated = Auth::attempt([
            'email' => $login,
            'password' => $password,
        ]) || Auth::attempt([
            'name' => $login,
            'password' => $password,
        ]);

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'email' => 'Email/login ID/username or password is incorrect.',
            ]);
        }

        $request->session()->regenerate();

        if (! $request->user()?->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This account does not have administrator access.',
                ], 403);
            }

            throw ValidationException::withMessages([
                'email' => 'This account does not have administrator access.',
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login successful.',
                'user' => [
                    'username' => $request->user()->name,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ],
            ]);
        }

        return redirect()->route('admin.home');
    }

    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged out successfully.',
            ]);
        }

        return redirect()->route('login')->with('status', 'Logged out successfully.');
    }
}
