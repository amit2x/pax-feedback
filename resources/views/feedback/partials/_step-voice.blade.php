<div class="paf-card" data-step style="display:none;">
    <h2 class="paf-step-title">{{ __('messages.record_voice') }}</h2>
    <p class="text-muted small mb-3">{{ __('messages.record_voice_hint') }}</p>

    <div data-voice-recorder>
        {{-- Idle state --}}
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
            <button type="button" class="btn btn-outline-secondary w-100 mt-2" data-recorder-reset>
                🔄 {{ __('messages.re_record') }}
            </button>
        </div>

        <div data-recorder-error class="alert alert-danger mt-3" style="display:none;"></div>
    </div>

    <div class="paf-footer-actions">
        <button type="button" class="btn btn-outline-secondary" data-goto="comment">{{ __('messages.back') }}</button>
        <button type="button" class="btn paf-btn-primary" data-goto="photo">{{ __('messages.continue') }}</button>
    </div>
</div>
