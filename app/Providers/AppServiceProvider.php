<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;

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
        //
        RateLimiter::for('donation', function ($request) {
            return Limit::perMinute(5)->by(Auth::id() ?: $request->ip());
        });

        // RateLimiter::for('mualaf', function ($request) {
        //     return Limit::perMinute(5)->by($request->ip());
        // });
    }
}
