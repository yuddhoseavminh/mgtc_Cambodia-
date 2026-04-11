<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\TeamStaff;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::guard('staff')->check()) {
            return $request->user('staff')?->must_change_password
                ? redirect()->route('staff.password.edit')
                : redirect()->route('staff.profile.show');
        }

        return view('staff.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = preg_replace('/\s+/', ' ', trim($credentials['username'])) ?? '';
        $username = Str::lower($username);
        $password = trim($credentials['password']);
        $staff = TeamStaff::query()
            ->whereRaw('LOWER(username) = ?', [$username])
            ->first();

        if (! $staff || ! $this->matchesAnyPasswordVariant($password, $staff->password)) {
            throw ValidationException::withMessages([
                'username' => 'Username or password is incorrect.',
            ]);
        }

        Auth::guard('staff')->login($staff);

        $request->session()->regenerate();

        $staff = $request->user('staff');

        if (! $staff?->is_active) {
            Auth::guard('staff')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => 'This staff account is inactive.',
            ]);
        }

        $staff->forceFill([
            'last_login_at' => now(),
        ])->save();

        return $staff->must_change_password
            ? redirect()->route('staff.password.edit')
            : redirect()->route('staff.profile.show');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')
            ->with('status', 'You have been logged out.');
    }

    private function matchesAnyPasswordVariant(string $password, string $hashedPassword): bool
    {
        $variants = collect([
            $password,
            $this->convertLocalizedDigits($password),
            $this->convertAsciiDigitsToKhmer($password),
        ])->filter()->unique()->values();

        foreach ($variants as $variant) {
            if (Hash::check($variant, $hashedPassword)) {
                return true;
            }
        }

        return false;
    }

    private function convertLocalizedDigits(string $value): string
    {
        return strtr($value, [
            '០' => '0',
            '១' => '1',
            '២' => '2',
            '៣' => '3',
            '៤' => '4',
            '៥' => '5',
            '៦' => '6',
            '៧' => '7',
            '៨' => '8',
            '៩' => '9',
        ]);
    }

    private function convertAsciiDigitsToKhmer(string $value): string
    {
        return strtr($value, [
            '0' => '០',
            '1' => '១',
            '2' => '២',
            '3' => '៣',
            '4' => '៤',
            '5' => '៥',
            '6' => '៦',
            '7' => '៧',
            '8' => '៨',
            '9' => '៩',
        ]);
    }
}
