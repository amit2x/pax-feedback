@extends('layouts.public')

@section('title', __('messages.how_was_overall'))

@section('content')
    @include('feedback.partials._progress')

    @if ($qr && $qr->location)
        <div class="paf-card text-center py-3">
            <div class="small text-muted">{{ __('messages.experience_at') }}</div>
            <div class="fw-semibold">
                {{ $qr->location->service?->name ?? $qr->location->name }}
            </div>
            <div class="small text-muted">
                {{ $qr->location->checkpoint_label }}
                @if ($qr->location->terminal) · {{ $qr->location->terminal->name }} @endif
            </div>
        </div>
    @endif

    <form id="paf-wizard" method="POST" action="{{ route('feedback.submit') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <input type="hidden" id="overall_rating" name="overall_rating" value="">
        <input type="hidden" id="feedback_type" name="feedback_type" value="">
        <input type="hidden" id="category_id" name="category_id" value="">
        <input type="hidden" id="subcategory_id" name="subcategory_id" value="">
        <input type="hidden" id="is_anonymous" name="is_anonymous" value="1">

        @include('feedback.partials._step-rating')
        @include('feedback.partials._step-type')
        @include('feedback.partials._step-category')
        @include('feedback.partials._step-share')       {{-- merged --}}
        @include('feedback.partials._step-contact')
    </form>
@endsection
