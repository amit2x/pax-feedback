<div class="paf-card" data-step style="display:none;">
    <h2 class="paf-step-title">{{ __('messages.add_photo') }}</h2>
    <p class="text-muted small mb-3">{{ __('messages.add_photo_hint') }}</p>

    <div data-photo-uploader>
        <button type="button" class="paf-media-tile w-100 border-0" data-photo-pick>
            <span class="paf-media-icon">📷</span>
            <span class="paf-media-label">{{ __('messages.add_photo') }}</span>
            <span class="paf-media-hint">{{ __('messages.max_photos', ['count' => 2]) }}</span>
        </button>

        <input type="file" data-photo-input accept="image/jpeg,image/png,image/webp" multiple hidden>

        <div class="paf-photo-grid" data-photo-grid></div>

        <div data-photo-error class="alert alert-danger mt-3" style="display:none;"></div>
    </div>

    <div class="paf-footer-actions">
        <button type="button" class="btn btn-outline-secondary" data-goto="voice">{{ __('messages.back') }}</button>
        <button type="button" class="btn paf-btn-primary" data-goto="contact">{{ __('messages.continue') }}</button>
    </div>
</div>
