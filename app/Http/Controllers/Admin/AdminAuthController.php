<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        $login = $this->normalizeLogin((string) ($credentials['login'] ?? $credentials['email'] ?? $credentials['username'] ?? ''));
        $password = $credentials['password'];

        if ($login === '') {
            throw ValidationException::withMessages([
                'email' => 'Email, login ID, or username is required.',
            ]);
        }

        $user = $this->resolveLoginUser($login);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email/login ID/username or password is incorrect.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $authenticatedUser = Auth::user();

        if (! $authenticatedUser?->isAdmin()) {
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
                    'username' => $authenticatedUser->name,
                    'name' => $authenticatedUser->name,
                    'email' => $authenticatedUser->email,
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

    private function normalizeLogin(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value)) ?? '';
    }

    private function resolveLoginUser(string $login): ?User
    {
        $normalizedLogin = $this->normalizeLogin($login);
        $lowerLogin = Str::lower($normalizedLogin);

        return User::query()
            ->whereRaw('LOWER(email) = ?', [$lowerLogin])
            ->orWhereRaw('LOWER(name) = ?', [$lowerLogin])
            ->first();
    }
}
