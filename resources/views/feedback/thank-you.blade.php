@extends('layouts.public')

@section('title', __('messages.thank_you'))

@section('content')
    <div class="paf-card text-center paf-success-card">

        @include('feedback.partials._success-check')

        <h2 class="paf-step-title mb-1 mt-3">{{ __('messages.thank_you') }}</h2>
        <p class="text-muted small mb-3">{{ __('messages.your_reference') }}</p>

        <div class="paf-reference-box mb-3" role="textbox" aria-readonly="true">
            {{ $reference }}
        </div>

        <p class="text-muted small mb-4">{{ __('messages.thank_you_hint') }}</p>

        <a href="https://www.nscbiairport.com"
           class="btn paf-btn-primary w-100"
           target="_blank"
           rel="noopener noreferrer">
            <span aria-hidden="true">🌐</span>
            {{ __('messages.visit_airport_website') }}
        </a>

        <a href="{{ route('feedback.welcome') }}"
           class="btn paf-btn-outline w-100 mt-2">
            {{ __('messages.submit_another') }}
        </a>
    </div>
@endsection
