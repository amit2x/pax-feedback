<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/sass/app.scss'])
</head>

<body class="paf-body">
    <header class="paf-brand-bar" role="banner">
        <div class="paf-sky" aria-hidden="true">
            <span class="paf-cloud paf-cloud--1"></span>
            <span class="paf-cloud paf-cloud--2"></span>
            <span class="paf-aircraft">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M21.5 12c0 .6-.4 1.2-1 1.3l-4.2 1.1-3.4 5.7c-.2.3-.5.5-.9.5h-1.3c-.4 0-.7-.4-.6-.8l1.2-4.8-3.5.9-.9 1.6c-.2.3-.5.5-.9.5h-1c-.3 0-.6-.3-.5-.7l.7-2.5-.7-2.5c-.1-.4.2-.7.5-.7h1c.4 0 .7.2.9.5l.9 1.6 3.5.9-1.2-4.8c-.1-.4.2-.8.6-.8h1.3c.4 0 .7.2.9.5l3.4 5.7 4.2 1.1c.6.1 1 .7 1 1.3z" />
                </svg>
            </span>
            <span class="paf-trail"></span>
        </div>

        <div class="paf-brand-bar__inner">
            <div class="paf-brand-logo">
                <img src="{{ asset('images/aai-logo.png') }}" alt="AAI" width="40" height="40">
            </div>
            <div class="paf-brand-titles">
                <h1 class="paf-brand-title">Admin Panel</h1>
                <div class="paf-brand-subtitle">Airports Authority of India</div>
            </div>
        </div>
    </header>

    <main class="paf-main">
        <div class="paf-container" style="max-width:420px;">
            @yield('content')
        </div>
    </main>

    <footer class="paf-footer">
        &copy; {{ date('Y') }} Airports Authority of India
    </footer>
</body>

</html>
