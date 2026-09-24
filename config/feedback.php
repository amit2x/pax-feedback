<?php

return [
    'media_disk' => env('FEEDBACK_MEDIA_DISK', 'local'),

    'retention' => [
        'feedback_days' => (int) env('FEEDBACK_RETENTION_FEEDBACK_DAYS', 1095),
        'voice_days' => (int) env('FEEDBACK_RETENTION_VOICE_DAYS', 180),
        'photo_days' => (int) env('FEEDBACK_RETENTION_PHOTO_DAYS', 180),
    ],

    'uploads' => [
        'max_photos' => 2,
        'max_photo_bytes' => 5_242_880,
        'max_voice_bytes' => 10_485_760,
        'max_voice_seconds' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin OTP Bypass
    |--------------------------------------------------------------------------
    |
    | When true AND app.debug is true, admin login skips the email OTP step.
    |
    | SAFETY:
    |   Both flags must be true for the bypass to activate:
    |     - ADMIN_OTP_BYPASS=true
    |     - APP_DEBUG=true
    |
    |   Production runs with APP_DEBUG=false, so this can never fire there
    |   by accident. If ADMIN_OTP_BYPASS=true appears on a production host
    |   without APP_DEBUG=true, a warning is logged and the bypass is refused.
    |
    */
    'admin_otp_bypass' => (bool) env('ADMIN_OTP_BYPASS', false),

];
