@extends('layouts.admin-auth')

@section('title', 'Admin Login')

@section('content')
<div class="paf-card">
    <h2 class="paf-step-title text-center mb-1">Admin Login</h2>
    <p class="paf-step-hint text-center">Authorized personnel only</p>

    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}" autocomplete="off" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label small text-muted">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required
                autofocus autocomplete="username" maxlength="255">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small text-muted">Password</label>
            <input type="password" id="password" name="password" class="form-control" required
                autocomplete="current-password" maxlength="255">
        </div>

        <div class="mb-3">
            <label for="captcha" class="form-label small text-muted">
                Verify you are human
            </label>
            <div class="paf-captcha-row">
                <span class="paf-captcha-question" aria-live="polite">
                    {{ $captchaQuestion }} = ?
                </span>
                <input type="text" id="captcha" name="captcha" class="form-control" required inputmode="numeric"
                    pattern="[0-9\-]+" autocomplete="off" placeholder="?" maxlength="5">
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" id="remember" name="remember" value="1" class="form-check-input">
            <label for="remember" class="form-check-label small text-muted">Remember me</label>
        </div>

        <button type="submit" class="btn paf-btn-primary w-100">Sign In</button>
    </form>

    <p class="text-center small text-muted mt-3 mb-0">
    </p>
</div>
@endsection
