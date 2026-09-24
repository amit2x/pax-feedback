<div class="paf-card" data-step="rating">
    <h2 class="paf-step-title">{{ __('messages.how_was_overall') }}</h2>

    <div class="paf-rating mb-2" role="radiogroup" aria-label="{{ __('messages.how_was_overall') }}">
        <button type="button" class="paf-rating-button" data-rating="1" aria-label="{{ __('messages.rating.1') }}">😡</button>
        <button type="button" class="paf-rating-button" data-rating="2" aria-label="{{ __('messages.rating.2') }}">😞</button>
        <button type="button" class="paf-rating-button" data-rating="3" aria-label="{{ __('messages.rating.3') }}">😐</button>
        <button type="button" class="paf-rating-button" data-rating="4" aria-label="{{ __('messages.rating.4') }}">🙂</button>
        <button type="button" class="paf-rating-button" data-rating="5" aria-label="{{ __('messages.rating.5') }}">😍</button>
    </div>

    <div class="paf-rating-labels">
        <span>{{ __('messages.rating.1') }}</span>
        <span>{{ __('messages.rating.5') }}</span>
    </div>

    <div class="paf-footer-actions">
        <button type="button" class="btn paf-btn-primary" data-goto="type">
            {{ __('messages.continue') }}
        </button>
    </div>
</div>
