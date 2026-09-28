<?php

namespace App\Services;

use App\Models\User;
use Elkomy\LaravelOtp\Exceptions\OtpNotFoundException;
use Elkomy\LaravelOtp\Exceptions\OtpResendTooSoonException;
use Elkomy\LaravelOtp\Facades\Otp;
use Elkomy\LaravelOtp\LaravelOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
     *
     * @throws ValidationException
     */
    public function send(User $user): void
    {
        try {
            $this->otp()->send($user->email, self::PURPOSE);
        } catch (OtpResendTooSoonException $e) {
            // Safely redirect back with a readable validation error message
            throw ValidationException::withMessages([
                'otp' => $e->getMessage() ?: 'An OTP was already sent. Please wait before trying again.',
            ]);
        }
    }

    /**
     * Verify the submitted code against the stored OTP for this user.
     */
    /* public function verify(User $user, string $code): bool
    {
        try {
            return (bool) $this->otp()->verify($user->email, self::PURPOSE, $code);
        } catch (OtpNotFoundException $e) {
            // No valid OTP exists or it expired, so verification fails
            Log::info('Admin OTP verification failed: No active token found.', [
                'user_id' => $user->id
            ]);

            return false;
        } catch (Throwable $e) {
            // Catch-all for any other unexpected failures
            Log::error('Unexpected Admin OTP verification failure', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    } */

            /**
     * Verify the submitted code against the stored OTP for this user.
     */
    public function verify(User $user, string $code): bool
    {
        try {
            // Fix: Swapped self::PURPOSE and $code positions to match the package signature
            return (bool) $this->otp()->verify($user->email, $code, self::PURPOSE);
        } catch (OtpNotFoundException $e) {
            // No valid OTP exists or it expired, so verification fails
            Log::info('Admin OTP verification failed: No active token found.', [
                'user_id' => $user->id
            ]);

            return false;
        } catch (Throwable $e) {
            // Catch-all for any other unexpected failures
            Log::error('Unexpected Admin OTP verification failure', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
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
    /* private function otp(): object
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
    } */

            /**
     * Resolve the OTP service instance or return the static facade proxy.
     */
    private function otp(): object
    {
        // 1. Try container bindings first (string keys bind the underlying service)
        $bindings = ['elkomy.otp', LaravelOtp::class];

        foreach ($bindings as $key) {
            if (app()->bound($key)) {
                return app($key);
            }
        }

        // 2. Fallback: Return a generic proxy object that calls the static Facade.
        // This stops it from breaking if the container string key isn't bound.
        if (class_exists(Otp::class)) {
            return new class {
                public function __call($method, $parameters)
                {
                    return Otp::$method(...$parameters);
                }
            };
        }

        throw new \RuntimeException(
            'OTP service could not be resolved. '
            .'Check that elkomy/laravel-otp is installed properly.'
        );
    }

}
