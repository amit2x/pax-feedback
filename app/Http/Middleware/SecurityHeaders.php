<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Apply security-related HTTP response headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | Content Security Policy
        |--------------------------------------------------------------------------
        */

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self'",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' data: https://fonts.bunny.net",
            "img-src 'self' data: blob:",
            "connect-src 'self'",
            "media-src 'self' blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "frame-src 'none'",
            "upgrade-insecure-requests",
        ]);

        $response->headers->set(
            'Content-Security-Policy',
            $csp
        );

        /*
        |--------------------------------------------------------------------------
        | Clickjacking Protection
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'X-Frame-Options',
            'DENY'
        );

        /*
        |--------------------------------------------------------------------------
        | MIME Sniffing Protection
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        /*
        |--------------------------------------------------------------------------
        | Referrer Policy
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        /*
        |--------------------------------------------------------------------------
        | Permissions Policy
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'Permissions-Policy',
            implode(', ', [
                'accelerometer=()',
                'autoplay=()',
                'camera=()',
                'display-capture=()',
                'encrypted-media=()',
                'fullscreen=(self)',
                'geolocation=()',
                'gyroscope=()',
                'magnetometer=()',
                'microphone=()',
                'payment=()',
                'picture-in-picture=()',
                'publickey-credentials-get=()',
                'screen-wake-lock=()',
                'usb=()',
            ])
        );

        /*
        |--------------------------------------------------------------------------
        | Cross-Origin Policies
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'Cross-Origin-Opener-Policy',
            'same-origin'
        );

        $response->headers->set(
            'Cross-Origin-Resource-Policy',
            'same-origin'
        );

        /*
        |--------------------------------------------------------------------------
        | HSTS
        |--------------------------------------------------------------------------
        */

        if (
            app()->environment('production') &&
            $request->isSecure()
        ) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Technology Disclosure
        |--------------------------------------------------------------------------
        */

        $response->headers->remove('X-Powered-By');

        /*
        |--------------------------------------------------------------------------
        | Cross-Domain Policy
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'X-Permitted-Cross-Domain-Policies',
            'none'
        );

        return $response;
    }
}
