<div class="paf-card" data-step style="display:none;">
    <h2 class="paf-step-title">{{ __('messages.tell_us_more') }}</h2>

    <textarea
        id="comment"
        name="comment"
        class="form-control"
        rows="5"
        maxlength="500"
        placeholder="{{ __('messages.tell_us_more') }}"
    ></textarea>
    <div class="paf-counter"><span id="comment-counter">0/500</span></div>

    <div class="paf-footer-actions">
        <button type="button" class="btn btn-outline-secondary" data-goto="category">{{ __('messages.back') }}</button>
        <button type="button" class="btn paf-btn-primary" data-goto="voice">{{ __('messages.continue') }}</button>
    </div>
</div>
