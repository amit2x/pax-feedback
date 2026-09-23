<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('feedback-public', function (Request $request) {
            return [
                Limit::perMinute(300)->by('ip:'.$request->ip()),
                Limit::perMinute(100)->by('sess:'.$request->session()->getId()),
                Limit::perMinute(50)->by('qr:'.($request->route('token') ?? 'none')),
            ];
        });

        RateLimiter::for('feedback-submit', function (Request $request) {
            return [
                Limit::perMinute(30)->by('ip:'.$request->ip()),
                Limit::perMinute(20)->by('sess:'.$request->session()->getId()),
            ];
        });

        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(50)->by('ip:'.$request->ip());
        });
    }
}
