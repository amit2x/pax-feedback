<?php

namespace App\Providers;

use App\Rules\ValidCaptcha;
use App\Services\AdminOtpService;
use App\Services\AuditLogger;
use App\Services\MathCaptchaService;
use App\Services\QrImageService;
use App\Services\QrTokenService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
        $this->app->singleton(MathCaptchaService::class);
        $this->app->singleton(AdminOtpService::class);
        $this->app->singleton(AuditLogger::class);
        $this->app->singleton(QrTokenService::class);
        $this->app->singleton(QrImageService::class);
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

        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            return (new ValidCaptcha)->passes($attribute, $value);
        });

        Password::defaults(function () {
            return Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }
}
