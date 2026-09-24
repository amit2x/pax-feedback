<div class="paf-card" data-step="category" style="display:none;">
    <h2 class="paf-step-title">{{ __('messages.what_happened') }}</h2>

    <div class="paf-choice-grid mb-3">
        @foreach ($categories as $category)
            <button type="button" class="paf-type-card" data-category="{{ $category->id }}">
                <span class="paf-type-icon">{{ $category->icon ?? '•' }}</span>
                <span class="paf-type-label">{{ $category->localizedName() }}</span>
            </button>
        @endforeach
    </div>

    <div id="subcategory-container"></div>

    <div class="paf-footer-actions">
        <button type="button" class="btn paf-btn-outline" data-goto="type">{{ __('messages.back') }}</button>
        <button type="button" class="btn paf-btn-primary" data-goto="share">{{ __('messages.continue') }}</button>
    </div>
</div>
