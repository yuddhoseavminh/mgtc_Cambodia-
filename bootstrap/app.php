<?php

use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('staff') || $request->is('staff/*')) {
                return route('staff.login');
            }

            return route('login');
        });
        $middleware->alias([
            'staff.password.change' => \App\Http\Middleware\EnsureStaffPasswordChange::class,
        ]);
        $middleware->validateCsrfTokens();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            if ($request->is('applications') && $request->isMethod('post')) {
                return redirect()
                    ->route('registration.form')
                    ->withErrors([
                        'upload_total' => 'ទំហំឯកសារសរុបធំពេក។ សូមរក្សាទំហំឯកសារនីមួយៗក្រោម 15 MB និងទំហំសរុបក្រោម 50 MB។',
                    ]);
            }

            if ($request->is('test-taking-staff-registrations') && $request->isMethod('post')) {
                return redirect()
                    ->route('test-taking-staff.form')
                    ->withInput()
                    ->withErrors([
                        'upload_total' => 'ទំហំឯកសារសរុបធំពេក។ សូមរក្សាទំហំឯកសារនីមួយៗក្រោម 15 MB និងទំហំសរុបក្រោម 50 MB។',
                    ]);
            }

            return null;
        });
    })->create();
