<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('messages.app_name'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/public.js'])
</head>

<body>
    <header class="paf-container text-center pt-4">
        <div class="mb-2">
            <span style="font-size:2rem;">✈</span>
        </div>
        <h1 class="h5 mb-0">{{ __('messages.app_name') }}</h1>
    </header>

    <main class="paf-container">
        @yield('content')
    </main>

    <footer class="paf-container text-center text-muted small py-4">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>

</html>
