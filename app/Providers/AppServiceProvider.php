<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('admin-login', function (Request $request) {
            $login = trim((string) ($request->input('login', $request->input('email', $request->input('username', '')))));

            return Limit::perMinute(5)
                ->by(strtolower($login).'|'.$request->ip())
                ->response(function () use ($request) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'មានការព្យាយាមចូលប្រព័ន្ធច្រើនពេក។ សូមរង់ចាំបន្តិច ហើយព្យាយាមម្តងទៀត។',
                        ], 429);
                    }

                    return back()
                        ->withInput($request->except('password'))
                        ->withErrors([
                            'email' => 'មានការព្យាយាមចូលប្រព័ន្ធច្រើនពេក។ សូមរង់ចាំបន្តិច ហើយព្យាយាមម្តងទៀត។',
                        ])
                        ->setStatusCode(429);
                });
        });

        RateLimiter::for('public-submissions', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function () use ($request) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'មានការបញ្ជូនច្រើនពេកក្នុងរយៈពេលខ្លី។ សូមរង់ចាំបន្តិច ហើយព្យាយាមម្តងទៀត។',
                        ], 429);
                    }

                    return back()
                        ->withInput()
                        ->withErrors([
                            'submission' => 'មានការបញ្ជូនច្រើនពេកក្នុងរយៈពេលខ្លី។ សូមរង់ចាំបន្តិច ហើយព្យាយាមម្តងទៀត។',
                        ])
                        ->setStatusCode(429);
                });
        });

        Carbon::macro('khFormat', function (string $format): string {
            /** @var \Illuminate\Support\Carbon $this */
            return strtr($this->format($format), [
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
        });
    }
}
