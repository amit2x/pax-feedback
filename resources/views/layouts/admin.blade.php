@php
    $themeCookie = request()->cookie('paf_admin_theme');
    $themeAttr = in_array($themeCookie, ['light', 'dark'], true) ? $themeCookie : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @if ($themeAttr) data-theme="{{ $themeAttr }}" @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b3d91" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0b1220" media="(prefers-color-scheme: dark)">
    <meta name="color-scheme" content="light dark">

    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/admin.js'])
    @stack('head')
</head>

<body class="paf-body paf-admin-body">
    <header class="paf-admin-header" role="banner">
        <div class="paf-admin-header__inner">
            <a href="{{ route('admin.dashboard') }}" class="paf-admin-brand">
                <span class="paf-admin-brand__logo">
                    <img src="{{ asset('images/aai-logo.png') }}" alt="AAI" width="32" height="32">
                </span>
                <span class="paf-admin-brand__text">
                    <span class="paf-admin-brand__title">{{ config('app.name') }}</span>
                    <span class="paf-admin-brand__subtitle">Admin Panel</span>
                </span>
            </a>

            <div class="paf-admin-user">
                <button type="button" class="paf-theme-toggle" data-theme-toggle
                    aria-label="Toggle light and dark mode" aria-pressed="false">
                    <svg class="paf-theme-icon paf-theme-icon--moon" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                    </svg>
                    <svg class="paf-theme-icon paf-theme-icon--sun" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <circle cx="12" cy="12" r="4" />
                        <path
                            d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                    </svg>
                </button>

                <span class="paf-admin-user__name">{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="paf-admin-user__logout">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="paf-admin-layout">
        <aside class="paf-admin-sidebar" role="navigation" aria-label="Admin navigation">
            @include('admin.partials._sidebar')
        </aside>

        <main class="paf-admin-main" role="main">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <footer class="paf-admin-footer">
        &copy; {{ date('Y') }} Airports Authority of India
    </footer>

    @stack('scripts')
</body>

</html>
