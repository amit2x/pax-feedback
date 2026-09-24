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
];
