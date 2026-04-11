<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffPasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $staff = $request->user('staff');

        if (! $staff) {
            return $next($request);
        }

        if ($staff->must_change_password && ! $request->routeIs('staff.password.*', 'staff.logout')) {
            return redirect()->route('staff.password.edit');
        }

        return $next($request);
    }
}
