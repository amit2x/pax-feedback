<div class="paf-card" data-step="share" style="display:none;">
    <h2 class="paf-step-title">{{ __('messages.share_title') }}</h2>
    <p class="paf-step-hint">{{ __('messages.share_hint') }}</p>

    {{-- 1. Comment — always visible --}}
    <div class="paf-share-section">
        <label for="comment" class="paf-share-label">
            <span class="paf-share-icon">✍️</span>
            <span>{{ __('messages.write_comment') }}</span>
            <span class="paf-share-optional">{{ __('messages.optional') }}</span>
        </label>

        <textarea
            id="comment"
            name="comment"
            class="form-control"
            rows="4"
            maxlength="500"
            placeholder="{{ __('messages.comment_placeholder') }}"
        ></textarea>
        <div class="paf-counter"><span id="comment-counter">0/500</span></div>
    </div>

    {{-- 2. Voice — collapsible --}}
    <div class="paf-share-section mt-4" data-accordion>
        <button
            type="button"
            class="paf-accordion-toggle"
            data-accordion-toggle
            aria-expanded="false"
            aria-controls="paf-voice-panel"
            id="paf-voice-header">
            <span class="paf-share-icon" aria-hidden="true">🎙</span>
            <span class="paf-accordion-title">{{ __('messages.record_voice') }}</span>
            <span class="paf-share-optional">{{ __('messages.optional') }}</span>
            <span class="paf-accordion-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </span>
        </button>

        <div
            class="paf-accordion-panel"
            id="paf-voice-panel"
            data-accordion-panel
            role="region"
            aria-labelledby="paf-voice-header"
            hidden>

            <div data-voice-recorder>
                {{-- Idle --}}
                <div data-recorder-idle>
                    <button type="button" class="paf-media-tile w-100 border-0" data-recorder-start>
                        <span class="paf-media-icon">🎙</span>
                        <span class="paf-media-label">{{ __('messages.start_recording') }}</span>
                        <span class="paf-media-hint">{{ __('messages.max_duration', ['seconds' => 60]) }}</span>
                    </button>
                </div>

                {{-- Recording --}}
                <div data-recorder-active style="display:none;">
                    <div class="paf-recorder">
                        <div class="paf-recorder-timer">
                            <span class="paf-recorder-dot"></span>
                            <span data-recorder-timer>00:00 / 01:00</span>
                        </div>
                        <button type="button" class="btn btn-danger w-100" data-recorder-stop>
                            ⏹ {{ __('messages.stop_recording') }}
                        </button>
                    </div>
                </div>

                {{-- Done --}}
                <div data-recorder-done style="display:none;">
                    <audio data-recorder-preview class="paf-audio-preview" controls></audio>
                    <button type="button" class="btn paf-btn-outline w-100 mt-2" data-recorder-reset>
                        🔄 {{ __('messages.re_record') }}
                    </button>
                </div>

                <div data-recorder-error class="alert alert-danger mt-3" style="display:none;"></div>
            </div>
        </div>

        {{-- Recording indicator (shown when collapsed but has recording) --}}
        <span class="paf-accordion-badge" data-voice-badge hidden>
            <span class="paf-badge-dot" aria-hidden="true"></span>
            <span>{{ __('messages.voice_added') }}</span>
        </span>
    </div>

    {{-- 3. Photo — collapsible --}}
    <div class="paf-share-section mt-4" data-accordion>
        <button
            type="button"
            class="paf-accordion-toggle"
            data-accordion-toggle
            aria-expanded="false"
            aria-controls="paf-photo-panel"
            id="paf-photo-header">
            <span class="paf-share-icon" aria-hidden="true">📷</span>
            <span class="paf-accordion-title">{{ __('messages.add_photo') }}</span>
            <span class="paf-share-optional">{{ __('messages.optional') }}</span>
            <span class="paf-accordion-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </span>
        </button>

        <div
            class="paf-accordion-panel"
            id="paf-photo-panel"
            data-accordion-panel
            role="region"
            aria-labelledby="paf-photo-header"
            hidden>

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
        </div>

        {{-- Photo counter badge --}}
        <span class="paf-accordion-badge" data-photo-badge hidden>
            <span class="paf-badge-dot" aria-hidden="true"></span>
            <span data-photo-badge-text>{{ __('messages.photos_added', ['count' => 0]) }}</span>
        </span>
    </div>

    <div class="paf-footer-actions">
        <button type="button" class="btn paf-btn-outline" data-goto="category">{{ __('messages.back') }}</button>
        <button type="button" class="btn paf-btn-primary" data-goto="contact">{{ __('messages.continue') }}</button>
    </div>
</div>
