<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class StaffPasswordController extends Controller
{
    public function edit(Request $request): View
    {
        return view('staff.password', [
            'staff' => $request->user('staff'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $staff = $request->user('staff');

        $validated = $request->validate([
            'current_password' => [$staff?->must_change_password ? 'nullable' : 'required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if (! $staff) {
            abort(403);
        }

        if (! $staff->must_change_password && ! Hash::check($validated['current_password'], $staff->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        if (Hash::check($validated['password'], $staff->password)) {
            throw ValidationException::withMessages([
                'password' => 'New password must be different from the current password.',
            ]);
        }

        $staff->update([
            'password' => $validated['password'],
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        return redirect()->route('staff.profile.show')
            ->with('status', 'Password changed successfully.');
    }
}
