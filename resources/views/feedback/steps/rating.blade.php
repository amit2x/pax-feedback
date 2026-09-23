@extends('layouts.public')

@section('title', __('messages.how_was_overall'))

@section('content')
<div class="paf-progress">
    <div class="paf-progress-bar" style="width:20%"></div>
</div>

<form id="paf-wizard" method="POST" action="{{ route('feedback.submit') }}" enctype="multipart/form-data">
    @csrf

    <input type="hidden" id="overall_rating" name="overall_rating" value="">
    <input type="hidden" id="feedback_type" name="feedback_type" value="">
    <input type="hidden" id="category_id" name="category_id" value="">
    <input type="hidden" id="subcategory_id" name="subcategory_id" value="">
    <input type="hidden" id="is_anonymous" name="is_anonymous" value="1">
    <input type="hidden" name="preferred_contact_method" value="none">

    <div data-step="rating">
        <div class="paf-card">
            <h2 class="h6 mb-3">{{ __('messages.how_was_overall') }}</h2>

            <div class="paf-rating mb-3">
                <button type="button" class="paf-rating-button" data-rating="1" aria-label="Very Poor">😡</button>
                <button type="button" class="paf-rating-button" data-rating="2" aria-label="Poor">😞</button>
                <button type="button" class="paf-rating-button" data-rating="3" aria-label="Average">😐</button>
                <button type="button" class="paf-rating-button" data-rating="4" aria-label="Good">🙂</button>
                <button type="button" class="paf-rating-button" data-rating="5" aria-label="Excellent">😍</button>
            </div>

            <div class="d-flex justify-content-between small text-muted mb-3">
                <span>{{ __('messages.rating.1') }}</span>
                <span>{{ __('messages.rating.5') }}</span>
            </div>

            <div class="paf-footer-actions">
                <button type="button" class="btn btn-primary" data-goto="type">
                    {{ __('messages.continue') }}
                </button>
            </div>
        </div>
    </div>

    <div data-step="type" style="display:none;">
        <div class="paf-card">
            <h2 class="h6 mb-3">{{ __('messages.what_share') }}</h2>

            <div class="mb-3">
                <button type="button" class="paf-type-card mb-2" data-type="compliment">
                    <span class="paf-type-icon">😊</span>
                    <span>{{ __('messages.types.compliment') }}</span>
                </button>
                <button type="button" class="paf-type-card mb-2" data-type="suggestion">
                    <span class="paf-type-icon">💡</span>
                    <span>{{ __('messages.types.suggestion') }}</span>
                </button>
                <button type="button" class="paf-type-card mb-2" data-type="complaint">
                    <span class="paf-type-icon">⚠️</span>
                    <span>{{ __('messages.types.complaint') }}</span>
                </button>
                <button type="button" class="paf-type-card" data-type="query">
                    <span class="paf-type-icon">❓</span>
                    <span>{{ __('messages.types.query') }}</span>
                </button>
            </div>

            <div class="paf-footer-actions">
                <button type="button" class="btn btn-outline-secondary" data-goto="rating">{{ __('messages.back')
                    }}</button>
                <button type="button" class="btn btn-primary" data-goto="category">{{ __('messages.continue')
                    }}</button>
            </div>
        </div>
    </div>

    <div data-step="category" style="display:none;">
        <div class="paf-card">
            <h2 class="h6 mb-3">{{ __('messages.what_happened') }}</h2>

            <div class="paf-choice-grid mb-3">
                @foreach ($categories as $category)
                <button type="button" class="paf-type-card" data-category="{{ $category->id }}">
                    <span class="paf-type-icon">{{ $category->icon ?? '•' }}</span>
                    <span>{{ $category->localizedName() }}</span>
                </button>
                @endforeach
            </div>

            <div id="subcategory-container" class="mt-3"></div>

            <div class="paf-footer-actions">
                <button type="button" class="btn btn-outline-secondary" data-goto="type">{{ __('messages.back')
                    }}</button>
                <button type="button" class="btn btn-primary" data-goto="comment">{{ __('messages.continue') }}</button>
            </div>
        </div>
    </div>

    <div data-step="comment" style="display:none;">
        <div class="paf-card">
            <h2 class="h6 mb-3">{{ __('messages.tell_us_more') }}</h2>

            <textarea id="comment" name="comment" class="form-control" rows="5" maxlength="500"></textarea>
            <div class="text-right small text-muted"><span id="comment-counter">0/500</span></div>

            <div class="paf-footer-actions">
                <button type="button" class="btn btn-outline-secondary" data-goto="category">{{ __('messages.back')
                    }}</button>
                <button type="button" class="btn btn-primary" data-goto="contact">{{ __('messages.continue') }}</button>
            </div>
        </div>
    </div>

    <div data-step="contact" style="display:none;">
        <div class="paf-card">
            <h2 class="h6 mb-3">{{ __('messages.contact_prompt') }}</h2>

            <button type="button" class="paf-type-card mb-2" data-contact="no">
                <span>{{ __('messages.contact_no') }}</span>
            </button>
            <button type="button" class="paf-type-card" data-contact="yes">
                <span>{{ __('messages.contact_yes') }}</span>
            </button>

            <div id="contact-fields" class="mt-3" style="display:none;">
                <div class="form-group">
                    <label for="name">{{ __('messages.name') }}</label>
                    <input type="text" id="name" name="name" class="form-control" maxlength="100" autocomplete="name">
                </div>
                <div class="form-group">
                    <label for="mobile">{{ __('messages.mobile') }}</label>
                    <input type="tel" id="mobile" name="mobile" class="form-control" maxlength="15" autocomplete="tel">
                </div>
                <div class="form-group">
                    <label for="email">{{ __('messages.email') }}</label>
                    <input type="email" id="email" name="email" class="form-control" maxlength="255"
                        autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="preferred_contact_method">{{ __('messages.preferred_contact_method') }}</label>
                    <select id="preferred_contact_method" name="preferred_contact_method" class="form-control">
                        <option value="email">Email</option>
                        <option value="mobile">Mobile</option>
                    </select>
                </div>
            </div>

            <div id="paf-error" class="alert alert-danger mt-3" style="display:none;"></div>

            <div class="paf-footer-actions">
                <button type="button" class="btn btn-outline-secondary" data-goto="comment">{{ __('messages.back')
                    }}</button>
                <button type="button" id="paf-submit-btn" class="btn btn-success">{{ __('messages.submit') }}</button>
            </div>
        </div>
    </div>
</form>

{{-- The submit form is the same wizard form; we submit via Axios. --}}
<form id="paf-submit-form" method="POST" action="{{ route('feedback.submit') }}" enctype="multipart/form-data"
    style="display:none;">
    @csrf
</form>
@endsection
