<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $purpose === 'password_reset' ? __('Password Reset Code') : __('Verification Code') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 580px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .content {
            padding: 32px 24px;
            text-align: center;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #1e1b4b;
            background-color: #eef2ff;
            border: 2px dashed #6366f1;
            border-radius: 8px;
            padding: 16px 24px;
            margin: 24px auto;
            display: inline-block;
        }
        .footer {
            background-color: #f1f5f9;
            color: #64748b;
            font-size: 13px;
            padding: 16px 24px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>{{ $purpose === 'password_reset' ? __('Reset Your Password') : __('Verify Your Account') }}</h2>
            <p>
                @if($purpose === 'password_reset')
                    {{ __('You requested to reset your password. Use the verification code below to complete the request:') }}
                @else
                    {{ __('Please use the verification code below to verify your email address:') }}
                @endif
            </p>

            <div class="otp-code">{{ $code }}</div>

            @if(! empty($actionUrl))
                <div style="margin: 24px 0;">
                    <a href="{{ $actionUrl }}" class="btn-action" style="display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);">
                        {{ $actionText ?? ($purpose === 'password_reset' ? __('Reset Password') : __('Verify Email')) }}
                    </a>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: -12px; margin-bottom: 20px;">
                    {{ __('Or use the verification code above manually.') }}
                </p>
            @endif

            <p style="color: #64748b; font-size: 14px;">
                {{ __('This code is valid for :minutes minutes.', ['minutes' => $expiresInMinutes]) }}
            </p>
            <p style="color: #94a3b8; font-size: 13px;">
                {{ __('If you did not request this, please ignore this email or contact support if you have questions.') }}
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
        </div>
    </div>
</body>
</html>
