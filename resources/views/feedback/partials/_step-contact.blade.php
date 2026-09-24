<div class="paf-card" data-step="contact" style="display:none;">
    <h2 class="paf-step-title">{{ __('messages.contact_prompt') }}</h2>

    <button type="button" class="paf-type-card mb-2" data-contact="no">
        <span class="paf-type-label">{{ __('messages.contact_no') }}</span>
    </button>

    <button type="button" class="paf-type-card" data-contact="yes">
        <span class="paf-type-label">{{ __('messages.contact_yes') }}</span>
    </button>

    <div id="contact-fields" class="mt-3" style="display:none;">
        <div class="mb-3">
            <label for="name" class="form-label small text-muted">{{ __('messages.name') }}</label>
            <input type="text" id="name" name="name" class="form-control" maxlength="100" autocomplete="name">
        </div>

        <div class="mb-3">
            <label for="mobile" class="form-label small text-muted">{{ __('messages.mobile') }}</label>
            <input type="tel" id="mobile" name="mobile" class="form-control" maxlength="15" autocomplete="tel" inputmode="numeric">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label small text-muted">{{ __('messages.email') }}</label>
            <input type="email" id="email" name="email" class="form-control" maxlength="255" autocomplete="email">
        </div>

        <div class="mb-3">
            <label for="preferred_contact_method" class="form-label small text-muted">
                {{ __('messages.preferred_contact_method') }}
            </label>
            <select id="preferred_contact_method" name="preferred_contact_method" class="form-select">
                <option value="email">Email</option>
                <option value="mobile">Mobile</option>
            </select>
        </div>

        <p class="paf-consent">{{ __('messages.contact_consent') }}</p>
    </div>

    <div id="paf-error" class="alert alert-danger mt-3" style="display:none;"></div>

    <div class="paf-footer-actions">
        <button type="button" class="btn paf-btn-outline" data-goto="share">{{ __('messages.back') }}</button>
        <button type="button" id="paf-submit-btn" class="btn paf-btn-primary">
            {{ __('messages.submit') }}
        </button>
    </div>
</div>
