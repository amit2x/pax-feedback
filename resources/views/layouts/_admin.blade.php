<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b3d91">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>

    {{-- CSP-nonced inline: apply saved theme before paint to avoid flash --}}
    <script nonce="{{ $csp_nonce ?? '' }}">
        (function () {
            try {
                var t = localStorage.getItem('paf.admin.theme');
                if (t === 'dark' || t === 'light') {
                    document.documentElement.setAttribute('data-theme', t);
                } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/sass/app.scss', 'resources/js/admin.js'])
</head>
<body class="admin-body">
    <div class="admin-shell">

        {{-- Sidebar --}}
        <aside class="admin-sidebar" role="navigation" aria-label="Admin navigation">
            <ul class="admin-nav">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.feedback.index') }}"
                       class="{{ request()->routeIs('admin.feedback.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">💬</span>
                        <span>Feedback</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories.index') }}"
                       class="{{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">🗂️</span>
                        <span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.locations.index') }}"
                       class="{{ request()->routeIs('admin.locations.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">📍</span>
                        <span>Locations</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.qr-codes.index') }}"
                       class="{{ request()->routeIs('admin.qr-codes.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">🔲</span>
                        <span>QR Codes</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.departments.index') }}"
                       class="{{ request()->routeIs('admin.departments.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">🏢</span>
                        <span>Departments</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}"
                       class="{{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">📈</span>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="{{ request()->routeIs('admin.audit-logs.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">📜</span>
                        <span>Audit Logs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                        <span class="admin-nav-icon">👥</span>
                        <span>Users</span>
                    </a>
                </li>
            </ul>
        </aside>

        {{-- Main --}}
        <div class="admin-main">

            {{-- Header --}}
            <header class="admin-header" role="banner">
                <a href="{{ route('admin.dashboard') }}" class="admin-header-brand">
                    <div class="admin-header-logo">
                        <img src="{{ asset('images/aai-logo.png') }}" alt="AAI" width="30" height="30">
                    </div>
                    <div class="admin-header-titles">
                        <h1 class="admin-header-title">Passenger Feedback</h1>
                        <div class="admin-header-sub">Admin Panel</div>
                    </div>
                </a>

                <div class="admin-header-actions">
                    <span class="admin-header-user d-none d-md-inline">
                        {{ auth()->user()->name }}
                    </span>

                    <button type="button"
                            class="theme-toggle"
                            data-theme-toggle
                            aria-label="Toggle light and dark mode"
                            title="Toggle theme">
                        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                        </svg>
                    </button>

                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn-admin-ghost">Logout</button>
                    </form>
                </div>
            </header>

            {{-- Content --}}
            <main class="admin-body-content" role="main">
                @if (session('status'))
                    <div class="admin-alert admin-alert--success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="admin-alert admin-alert--danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
