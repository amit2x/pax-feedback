<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b3d91">
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
