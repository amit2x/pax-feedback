export default class VoiceRecorder {
    /**
     * @param {Object} opts
     * @param {HTMLElement} opts.root       — container for recorder UI
     * @param {number} opts.maxSeconds      — max recording duration
     */
    constructor({ root, maxSeconds = 60 }) {
        this.root = root;
        this.maxSeconds = maxSeconds;
        this.mediaRecorder = null;
        this.chunks = [];
        this.blob = null;
        this.stream = null;
        this.timer = null;
        this.startedAt = null;

        this.startBtn = root.querySelector('[data-recorder-start]');
        this.stopBtn = root.querySelector('[data-recorder-stop]');
        this.resetBtn = root.querySelector('[data-recorder-reset]');
        this.timerEl = root.querySelector('[data-recorder-timer]');
        this.previewEl = root.querySelector('[data-recorder-preview]');
        this.errorEl = root.querySelector('[data-recorder-error]');
        this.idleEl = root.querySelector('[data-recorder-idle]');
        this.activeEl = root.querySelector('[data-recorder-active]');
        this.doneEl = root.querySelector('[data-recorder-done]');

        this.bind();
    }

    bind() {
        if (this.startBtn) {
            this.startBtn.addEventListener('click', () => this.start());
        }
        if (this.stopBtn) {
            this.stopBtn.addEventListener('click', () => this.stop());
        }
        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.reset());
        }
    }

    async start() {
        this.clearError();

        if (!navigator.mediaDevices || !window.MediaRecorder) {
            this.showError('Voice recording is not supported on this browser.');
            return;
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100,
                },
            });
        } catch (e) {
            this.showError('Microphone access was denied.');
            return;
        }

        const mimeType = this.pickMimeType();
        if (!mimeType) {
            this.showError('No supported audio format available.');
            this.cleanupStream();
            return;
        }

        this.chunks = [];
        this.mediaRecorder = new MediaRecorder(this.stream, { mimeType });

        this.mediaRecorder.addEventListener('dataavailable', (e) => {
            if (e.data && e.data.size > 0) this.chunks.push(e.data);
        });

        this.mediaRecorder.addEventListener('stop', () => this.onStop());

        this.mediaRecorder.start();
        this.startedAt = Date.now();
        this.setState('active');
        this.startTimer();
    }

    stop() {
        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.stop();
        }
        this.stopTimer();
    }

    reset() {
        this.blob = null;
        this.chunks = [];
        this.startedAt = null;
        if (this.previewEl) {
            this.previewEl.pause();
            this.previewEl.removeAttribute('src');
            this.previewEl.load();
        }
        if (this.timerEl) this.timerEl.textContent = '00:00 / 01:00';
        this.setState('idle');
    }

    onStop() {
        const blob = new Blob(this.chunks, { type: this.chunks[0]?.type || 'audio/webm' });
        this.blob = blob;

        if (this.previewEl) {
            const url = URL.createObjectURL(blob);
            this.previewEl.src = url;
            this.previewEl.load();
        }

        this.setState('done');
        this.cleanupStream();
    }

    startTimer() {
        const update = () => {
            const elapsed = Math.min(
                this.maxSeconds,
                Math.floor((Date.now() - this.startedAt) / 1000)
            );
            if (this.timerEl) {
                this.timerEl.textContent =
                    `${this.format(elapsed)} / ${this.format(this.maxSeconds)}`;
            }
            if (elapsed >= this.maxSeconds) {
                this.stop();
            }
        };
        update();
        this.timer = setInterval(update, 250);
    }

    stopTimer() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    format(seconds) {
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        return `${m}:${s}`;
    }

    pickMimeType() {
        const candidates = [
            'audio/webm;codecs=opus',
            'audio/webm',
            'audio/ogg;codecs=opus',
            'audio/mp4',
        ];
        return candidates.find((t) => MediaRecorder.isTypeSupported(t)) || null;
    }

    cleanupStream() {
        if (this.stream) {
            this.stream.getTracks().forEach((t) => t.stop());
            this.stream = null;
        }
    }

    setState(state) {
        if (this.idleEl) this.idleEl.style.display = state === 'idle' ? 'block' : 'none';
        if (this.activeEl) this.activeEl.style.display = state === 'active' ? 'block' : 'none';
        if (this.doneEl) this.doneEl.style.display = state === 'done' ? 'block' : 'none';
    }

    showError(msg) {
        if (this.errorEl) {
            this.errorEl.textContent = msg;
            this.errorEl.style.display = 'block';
        }
    }

    clearError() {
        if (this.errorEl) {
            this.errorEl.textContent = '';
            this.errorEl.style.display = 'none';
        }
    }

    /**
     * @returns {File|null}
     */
    getFile() {
        if (!this.blob) return null;
        const ext = this.blob.type.includes('ogg') ? 'ogg' : 'webm';
        return new File([this.blob], `voice.${ext}`, { type: this.blob.type });
    }

    hasRecording() {
        return this.blob !== null;
    }
}
