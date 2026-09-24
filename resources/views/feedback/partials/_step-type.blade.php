<div class="paf-card" data-step="type" style="display:none;">
    <h2 class="paf-step-title">{{ __('messages.what_share') }}</h2>

    <div class="d-grid gap-2">
        <button type="button" class="paf-type-card" data-type="compliment">
            <span class="paf-type-icon">😊</span>
            <span class="paf-type-label">{{ __('messages.types.compliment') }}</span>
        </button>

        <button type="button" class="paf-type-card" data-type="suggestion">
            <span class="paf-type-icon">💡</span>
            <span class="paf-type-label">{{ __('messages.types.suggestion') }}</span>
        </button>

        <button type="button" class="paf-type-card" data-type="complaint">
            <span class="paf-type-icon">⚠️</span>
            <span class="paf-type-label">{{ __('messages.types.complaint') }}</span>
        </button>

        <button type="button" class="paf-type-card" data-type="query">
            <span class="paf-type-icon">❓</span>
            <span class="paf-type-label">{{ __('messages.types.query') }}</span>
        </button>
    </div>

    <div class="paf-footer-actions">
        <button type="button" class="btn paf-btn-outline" data-goto="rating">{{ __('messages.back') }}</button>
        <button type="button" class="btn paf-btn-primary" data-goto="category">{{ __('messages.continue') }}</button>
    </div>
</div>
