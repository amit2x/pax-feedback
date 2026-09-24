<?php

namespace App\Services;

use App\Models\User;
use Elkomy\LaravelOtp\Facades\Otp;
use Elkomy\LaravelOtp\LaravelOtp;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminOtpService
{
    /**
     * Purpose key — matches config('otp.purposes.admin_login').
     */
    public const PURPOSE = 'admin_login';

    /**
     * Whether the OTP flow should be bypassed for this request.
     *
     * Requires BOTH:
     *   - config('feedback.admin_otp_bypass') === true
     *   - config('app.debug') === true
     *
     * Production runs with APP_DEBUG=false, so the bypass cannot
     * accidentally activate there.
     */
    public function shouldBypass(): bool
    {
        if (! (bool) config('feedback.admin_otp_bypass', false)) {
            return false;
        }

        if (! (bool) config('app.debug', false)) {
            Log::warning(
                'ADMIN_OTP_BYPASS is enabled but APP_DEBUG is off. '
                .'OTP bypass refused for safety.'
            );

            return false;
        }

        return true;
    }

    /**
     * Send a fresh OTP to the user's email using the package.
     */
    public function send(User $user): void
    {
        $this->otp()->send($user->email, self::PURPOSE);
    }

    /**
     * Verify the submitted code against the stored OTP for this user.
     */
    public function verify(User $user, string $code): bool
    {
        return (bool) $this->otp()->verify($user->email, self::PURPOSE, $code);
    }

    /**
     * Re-send OTP for this user (respects package-level throttle).
     */
    public function resend(User $user): bool
    {
        try {
            return (bool) $this->otp()->resend($user->email, self::PURPOSE);
        } catch (Throwable $e) {
            Log::warning('Admin OTP resend failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Resolve the OTP service from the container.
     * This keeps us decoupled from the facade's exact class name.
     */
    private function otp(): object
    {
        // The package typically binds a service under one of these keys.
        // We try the most common ones in order.
        $candidates = [
            Otp::class,
            LaravelOtp::class,
            'elkomy.otp',
        ];

        foreach ($candidates as $key) {
            try {
                if (class_exists($key) || app()->bound($key)) {
                    return app($key);
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        throw new \RuntimeException(
            'OTP service could not be resolved. '
            .'Check that elkomy/laravel-otp is installed and the facade alias is published.'
        );
    }
}
