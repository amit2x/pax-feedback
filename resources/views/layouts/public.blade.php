<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar','fa','ur']) ? 'rtl' : 'ltr' }}">
<head>
    {{-- Core --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- Security (CSRF + defense-in-depth) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    {{-- SEO --}}
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="description" content="{{ __('messages.meta_description') }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="keywords" content="airport, feedback, passenger, survey, AAI, NSCBI">

    {{-- Theme color (light + dark) --}}
    <meta name="theme-color" content="#0b3d91">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#0b1220">
    <meta name="color-scheme" content="light dark">

    {{-- iOS / PWA --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ __('messages.app_name') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">

    {{-- Open Graph (in case of sharing) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="@yield('title', __('messages.app_name'))">
    <meta property="og:description" content="{{ __('messages.meta_description') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ app()->getLocale() }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', __('messages.app_name'))">
    <meta name="twitter:description" content="{{ __('messages.meta_description') }}">

    <title>@yield('title', __('messages.app_name'))</title>

    {{-- Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    {{-- Fonts (self-host via @fontsource or fall back to system) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    @vite(['resources/sass/app.scss', 'resources/js/public.js'])

    @stack('head')
</head>

<body class="paf-body">
    <a href="#paf-content" class="paf-skip">{{ __('messages.skip_to_content') }}</a>

    {{-- ============================================================
         Brand header — logo + animated aircraft takeoff
         ============================================================ --}}
    <header class="paf-brand-bar" role="banner">
    {{-- Sky layer: clouds + aircraft + contrail --}}
    <div class="paf-sky" aria-hidden="true">
        <span class="paf-cloud paf-cloud--1"></span>
        <span class="paf-cloud paf-cloud--2"></span>
        <span class="paf-cloud paf-cloud--3"></span>


    </div>

    {{-- Foreground: logo + title --}}
    <div class="paf-brand-bar__inner">
        <div class="paf-brand-logo">
            <img
                src="{{ asset('images/aai-logo.png') }}"
                alt="{{ __('messages.brand_subtitle') }}"
                width="40"
                height="40"
                loading="eager"
                decoding="async">
        </div>

        <div class="paf-brand-titles">
            <h1 class="paf-brand-title">{{ __('messages.app_name') }}</h1>
            <div class="paf-brand-subtitle">{{ __('messages.brand_subtitle') }}</div>
        </div>
    </div>
</header>

    {{-- ============================================================
         Main content
         ============================================================ --}}
    <main id="paf-content" class="paf-main" role="main">
        <div class="paf-container">
            @yield('content')
        </div>
    </main>

    {{-- ============================================================
         Footer
         ============================================================ --}}
    <footer class="paf-footer" role="contentinfo">
        <div>
            &copy; {{ date('Y') }}
            <a href="https://www.aai.aero" target="_blank" rel="noopener noreferrer">
                Airports Authority of India
            </a>
        </div>

        <div class="paf-trust-line">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
            </svg>
            <span>{{ __('messages.privacy_short') }}</span>
        </div>

        <div class="paf-footer-meta">
            <span>{{ __('messages.footer_secure') }}</span>
            <span class="paf-dot" aria-hidden="true"></span>
            <span>{{ __('messages.footer_anonymous') }}</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
