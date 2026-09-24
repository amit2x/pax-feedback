@extends('layouts.admin-auth')

@section('title', 'Two-Factor Verification')

@section('content')
<div class="paf-card">
    <h2 class="paf-step-title text-center mb-1">Enter Verification Code</h2>
    <p class="paf-step-hint text-center">
        We sent a 6-digit code to your email address.
    </p>

    @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.2fa.verify') }}">
        @csrf

        <div class="mb-3">
            <label for="code" class="form-label small text-muted">Verification Code</label>
            <input type="text" id="code" name="code" class="form-control text-center" required inputmode="numeric"
                autocomplete="one-time-code" maxlength="6" pattern="\d{6}" autofocus
                style="font-size:1.5rem; letter-spacing:8px;">
        </div>

        <button type="submit" class="btn paf-btn-primary w-100 mb-2">Verify & Sign In</button>
    </form>

    <form method="POST" action="{{ route('admin.2fa.resend') }}">
        @csrf
        <button type="submit" class="btn paf-btn-outline w-100">Resend Code</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('admin.login') }}" class="small text-muted">Back to login</a>
    </div>
</div>
@endsection
