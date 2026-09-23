@extends('layouts.public')

@section('title', __('messages.app_name'))

@section('content')
<div class="paf-card">
    @if ($location)
    <p class="text-muted mb-1">{{ __('messages.experience_at') }}</p>
    <h2 class="h5 mb-0">
        {{ $location->service?->name ?? $location->name }}
    </h2>
    <p class="text-muted small mb-3">
        {{ $location->checkpoint_label }}
        @if ($location->terminal) · {{ $location->terminal->name }} @endif
    </p>
    @else
    <p class="text-muted mb-3">{{ __('messages.tagline') }}</p>
    @endif

    <form method="POST" action="{{ route('feedback.start') }}">
        @csrf

        <p class="mb-2 font-weight-bold">{{ __('messages.language') }}</p>

        <div class="paf-lang-switcher mb-4">
            <button type="submit" name="language_code" value="en" class="btn btn-outline-primary">English</button>
            <button type="submit" name="language_code" value="hi" class="btn btn-outline-primary">हिंदी</button>
            <button type="submit" name="language_code" value="bn" class="btn btn-outline-primary">বাংলা</button>
        </div>

        <p class="text-center text-muted small mb-0">
            {{ __('messages.takes_less_than_a_minute') }}
        </p>
    </form>
</div>
@endsection
