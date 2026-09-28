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
        /** @var Response $response */
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | Content Security Policy (CSP)
        |--------------------------------------------------------------------------
        |
        | Restricts the sources from which browsers may load resources.
        |
        | NOTE:
        | 'unsafe-inline' is currently allowed ONLY for styles because
        | Bootstrap/plugins may require inline style attributes.
        |
        | JavaScript intentionally does NOT allow 'unsafe-inline'.
        |
        */

        $csp = implode('; ', [
            "default-src 'self'",

            // JavaScript must come from this application.
            "script-src 'self'",

            // Bootstrap/plugins may require inline CSS.
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",

            // Application + Bunny Fonts.
            "font-src 'self' data: https://fonts.bunny.net",

            // Local images, inline images and generated blobs.
            "img-src 'self' data: blob:",

            // AJAX / Fetch / XHR connections.
            "connect-src 'self'",

            // Audio/video/blob resources.
            "media-src 'self' blob:",

            // Prevent legacy plugin content.
            "object-src 'none'",

            // Prevent manipulation of the document base URL.
            "base-uri 'self'",

            // Forms may submit only to this application.
            "form-action 'self'",

            // Completely prevent iframe embedding.
            "frame-ancestors 'none'",

            // Prevent this application from embedding external frames.
            "frame-src 'none'",

            // Force HTTP resources to HTTPS.
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
        |
        | CSP frame-ancestors is the modern protection.
        | X-Frame-Options provides legacy/scanner compatibility.
        |
        */

        $response->headers->set(
            'X-Frame-Options',
            'DENY'
        );


        /*
        |--------------------------------------------------------------------------
        | MIME Type Sniffing Protection
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );


        /*
        |--------------------------------------------------------------------------
        | Referrer Information Protection
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
        |
        | Disable browser features not required by the application.
        |
        | microphone=(self) is retained because the application may use
        | microphone functionality.
        |
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
                'microphone=(self)',
                'payment=()',
                'picture-in-picture=()',
                'publickey-credentials-get=()',
                'screen-wake-lock=()',
                'usb=()',
            ])
        );


        /*
        |--------------------------------------------------------------------------
        | Cross-Origin Isolation / Resource Protection
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
        | HTTPS Strict Transport Security (HSTS)
        |--------------------------------------------------------------------------
        |
        | Only send HSTS over HTTPS in production.
        |
        | Do NOT add "preload" unless the entire parent domain and all
        | required subdomains are intentionally ready for HSTS preloading.
        |
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
        | Remove Technology Disclosure Headers
        |--------------------------------------------------------------------------
        |
        | PHP/LiteSpeed may add X-Powered-By AFTER Laravel processes the
        | response. Therefore expose_php should ALSO be disabled in php.ini.
        |
        */

        $response->headers->remove('X-Powered-By');


        /*
        |--------------------------------------------------------------------------
        | Legacy Headers
        |--------------------------------------------------------------------------
        |
        | These are largely obsolete but harmless for older scanners/browsers.
        |
        */

        $response->headers->set(
            'X-Permitted-Cross-Domain-Policies',
            'none'
        );


        return $response;
    }
}
