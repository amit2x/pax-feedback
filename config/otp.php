<?php

declare(strict_types=1);

return [
    'table_name' => 'otps',

    'digits' => 6,

    'expires_in_minutes' => 10,

    'max_attempts' => 5,

    'resend_throttle_seconds' => 60,

    'queue' => [
        'enabled' => true,
        'connection' => env('OTP_QUEUE_CONNECTION', null),
        'queue' => env('OTP_QUEUE_NAME', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Purpose-Specific Mail & Notification Settings
    |--------------------------------------------------------------------------
    */
    'purposes' => [
        'email_verification' => [
            'subject' => 'Verify Your Email Address',
            'greeting' => 'Hello!',
            'line' => 'Your one-time verification code is:',
            'action_text' => 'Verify Email',
            'footer' => 'This code will expire in :minutes minutes. If you did not request this, please ignore this email.',
            'view' => null,
        ],

        'password_reset' => [
            'subject' => 'Password Reset Verification Code',
            'greeting' => 'Hello!',
            'line' => 'You requested to reset your password. Use the verification code below or click the button to proceed:',
            'action_text' => 'Reset Password',
            'footer' => 'This code will expire in :minutes minutes. If you did not request a password reset, please secure your account immediately.',
            'view' => null,
        ],

        /*
         * Custom purpose for admin login 2FA.
         * Matches AdminOtpService::PURPOSE.
         */
        'admin_login' => [
            'subject' => 'Your Admin Login Code — '.env('APP_NAME', 'Passenger Feedback'),
            'greeting' => 'Hello,',
            'line' => 'Use the following code to complete your admin sign-in:',
            'action_text' => null,
            'footer' => 'This code will expire in :minutes minutes. If you did not attempt to sign in, please secure your account immediately.',
            'view' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Mail Settings (Fallback)
    |--------------------------------------------------------------------------
    */
    'mail' => [
        'subject' => 'Your Admin Login Code',
        'greeting' => 'Hello!',
        'line' => 'Your one-time verification code is:',
        'footer' => 'This code will expire in :minutes minutes. If you did not request this, please ignore this email.',
        'view' => null,
    ],
];
