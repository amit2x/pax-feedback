@extends('layouts.public')

@section('title', __('messages.thank_you'))

@section('content')
<div class="paf-card text-center">
    <div style="font-size:3rem;">✅</div>
    <h2 class="h5 mb-2">{{ __('messages.thank_you') }}</h2>
    <p class="text-muted small mb-4">{{ __('messages.your_reference') }}</p>

    <div class="paf-reference-box mb-3">{{ $reference }}</div>

    <p class="text-muted small mb-0">
        {{ __('messages.tagline') }}
    </p>
</div>
@endsection
