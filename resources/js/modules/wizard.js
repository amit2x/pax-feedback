function uuidv4() {
    if (window.crypto && typeof window.crypto.randomUUID === 'function') {
        return window.crypto.randomUUID();
    }
    // Fallback for older browsers (uses CSPRNG if available)
    const bytes = new Uint8Array(16);
    if (window.crypto && window.crypto.getRandomValues) {
        window.crypto.getRandomValues(bytes);
    } else {
        for (let i = 0; i < 16; i++) bytes[i] = Math.floor(Math.random() * 256);
    }
    bytes[6] = (bytes[6] & 0x0f) | 0x40;
    bytes[8] = (bytes[8] & 0x3f) | 0x80;
    const hex = [...bytes].map((b) => b.toString(16).padStart(2, '0'));
    return `${hex.slice(0, 4).join('')}-${hex.slice(4, 6).join('')}-${hex.slice(6, 8).join('')}-${hex.slice(8, 10).join('')}-${hex.slice(10, 16).join('')}`;
}

function getOrCreateSubmissionUuid() {
    const KEY = 'paf.submission_uuid';
    let uuid = null;
    try {
        uuid = window.sessionStorage.getItem(KEY);
        if (!uuid) {
            uuid = uuidv4();
            window.sessionStorage.setItem(KEY, uuid);
        }
    } catch (e) {
        // sessionStorage blocked — generate ephemeral UUID
        uuid = uuidv4();
    }
    return uuid;
}

function clearSubmissionUuid() {
    try {
        window.sessionStorage.removeItem('paf.submission_uuid');
    } catch (e) {
        /* noop */
    }
}

import http from './http.js';
import VoiceRecorder from './voice-recorder.js';
import PhotoUploader from './photo-uploader.js';

export default class Wizard {
    constructor() {
        this.form = document.getElementById('paf-wizard');
        if (!this.form) return;

        // NEW: merged share step replaces separate voice + photo
        this.steps = ['rating', 'type', 'category', 'share', 'contact'];
        this.currentIndex = 0;

        this.state = {
            rating: null,
            type: null,
            categoryId: null,
            subcategoryId: null,
            comment: '',
            isAnonymous: true,
            wantsContact: false,
        };

        this.voice = null;
        this.photos = null;
        this.submitting = false;

        this.initVoice();
        this.initPhotos();
        this.initAccordions();
        this.bindRating();
        this.bindType();
        this.bindCategory();
        this.bindComment();
        this.bindContact();
        this.bindNavigation();
        this.bindSubmit();

        this.showStep(0);
    }

    // ---------- INIT ----------

    initVoice() {
        const root = this.form.querySelector('[data-voice-recorder]');
        if (!root) return;

        this.voice = new VoiceRecorder({ root, maxSeconds: 60 });

        // Hook into recorder state changes to toggle the "voice added" badge
        const badge = this.form.querySelector('[data-voice-badge]');
        const originalOnStop = this.voice.onStop.bind(this.voice);
        const originalReset = this.voice.reset.bind(this.voice);

        this.voice.onStop = () => {
            originalOnStop();
            if (badge) badge.removeAttribute('hidden');
        };

        this.voice.reset = () => {
            originalReset();
            if (badge) badge.setAttribute('hidden', '');
        };
    }

    initPhotos() {
        const root = this.form.querySelector('[data-photo-uploader]');
        if (!root) return;

        this.photos = new PhotoUploader({ root, maxFiles: 2, maxBytesPerFile: 5 * 1024 * 1024 });

        // Hook into uploader render() to update the "photos added" badge
        const badge = this.form.querySelector('[data-photo-badge]');
        const badgeText = this.form.querySelector('[data-photo-badge-text]');
        const originalRender = this.photos.render.bind(this.photos);

        this.photos.render = () => {
            originalRender();
            const count = this.photos.getFiles().length;
            if (badge && badgeText) {
                if (count > 0) {
                    badgeText.textContent = `${count} photo${count > 1 ? 's' : ''} added`;
                    badge.removeAttribute('hidden');
                } else {
                    badge.setAttribute('hidden', '');
                }
            }
        };
    }

    initAccordions() {
        const accordionRoot = this.form;
        if (!accordionRoot) return;
        // Lazy import to keep the initial bundle lean
        import('./accordion.js').then(({ default: Accordion }) => {
            this.accordion = new Accordion(accordionRoot);
        });
    }


    // ---------- BINDINGS ----------
    bindRating() {
        this.form.querySelectorAll('[data-rating]').forEach((btn) => {
            btn.addEventListener('click', () => {
                this.form.querySelectorAll('[data-rating]').forEach((b) =>
                    b.classList.remove('is-selected')
                );
                btn.classList.add('is-selected');
                this.state.rating = parseInt(btn.dataset.rating, 10);
                const hidden = document.getElementById('overall_rating');
                if (hidden) hidden.value = this.state.rating;
            });
        });
    }

    bindType() {
        this.form.querySelectorAll('[data-type]').forEach((btn) => {
            btn.addEventListener('click', () => {
                this.form.querySelectorAll('[data-type]').forEach((b) =>
                    b.classList.remove('is-selected')
                );
                btn.classList.add('is-selected');
                this.state.type = btn.dataset.type;
                const hidden = document.getElementById('feedback_type');
                if (hidden) hidden.value = this.state.type;
            });
        });
    }

    bindCategory() {
        this.form.querySelectorAll('[data-category]').forEach((btn) => {
            btn.addEventListener('click', () => {
                this.form.querySelectorAll('[data-category]').forEach((b) =>
                    b.classList.remove('is-selected')
                );
                btn.classList.add('is-selected');
                this.state.categoryId = btn.dataset.category;
                const hidden = document.getElementById('category_id');
                if (hidden) hidden.value = this.state.categoryId;
                this.loadSubcategories();
            });
        });
    }

    loadSubcategories() {
        const wrap = document.getElementById('subcategory-container');
        if (!wrap) return;

        wrap.innerHTML = '<p class="text-muted small mb-0">Loading…</p>';

        http.get('/feedback/api/subcategories', {
            params: { category_id: this.state.categoryId },
        })
            .then((res) => {
                wrap.innerHTML = '';
                if (!res.data.length) {
                    wrap.innerHTML =
                        '<p class="text-muted small mb-0">No specific options.</p>';
                    return;
                }

                const grid = document.createElement('div');
                grid.className = 'paf-choice-grid';

                res.data.forEach((sub) => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'paf-type-card';
                    b.dataset.subcategory = sub.id;

                    const label = document.createElement('span');
                    label.className = 'paf-type-label';
                    label.textContent = sub.name;

                    b.appendChild(label);

                    b.addEventListener('click', () => {
                        grid.querySelectorAll('[data-subcategory]').forEach((x) =>
                            x.classList.remove('is-selected')
                        );
                        b.classList.add('is-selected');
                        this.state.subcategoryId = sub.id;
                        const hidden = document.getElementById('subcategory_id');
                        if (hidden) hidden.value = sub.id;
                    });

                    grid.appendChild(b);
                });

                wrap.appendChild(grid);
            })
            .catch(() => {
                wrap.innerHTML =
                    '<p class="text-danger small mb-0">Could not load options.</p>';
            });
    }

    bindComment() {
        const textarea = document.getElementById('comment');
        const counter = document.getElementById('comment-counter');
        if (!textarea) return;
        textarea.addEventListener('input', () => {
            this.state.comment = textarea.value;
            if (counter) counter.textContent = `${textarea.value.length}/500`;
        });
    }

    bindContact() {
        this.form.querySelectorAll('[data-contact]').forEach((btn) => {
            btn.addEventListener('click', () => {
                this.form.querySelectorAll('[data-contact]').forEach((b) =>
                    b.classList.remove('is-selected')
                );
                btn.classList.add('is-selected');
                this.state.wantsContact = btn.dataset.contact === 'yes';
                this.state.isAnonymous = !this.state.wantsContact;

                const fields = document.getElementById('contact-fields');
                if (fields) fields.style.display = this.state.wantsContact ? 'block' : 'none';

                const isAnon = document.getElementById('is_anonymous');
                if (isAnon) isAnon.value = this.state.isAnonymous ? '1' : '0';
            });
        });
    }

    bindNavigation() {
        this.form.querySelectorAll('[data-goto]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.goto;
                const nextIndex = this.steps.indexOf(target);
                if (nextIndex === -1) return;

                if (nextIndex > this.currentIndex && !this.canAdvanceFrom(this.currentIndex)) {
                    return;
                }

                this.showStep(nextIndex);
            });
        });
    }

    bindSubmit() {
        const submitBtn = document.getElementById('paf-submit-btn');
        if (!submitBtn) return;

        submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (this.submitting) return;
            this.submit();
        });
    }

    // ---------- STEP CONTROL ----------
    showStep(index) {
        this.currentIndex = index;
        this.form.querySelectorAll('[data-step]').forEach((el) => {
            const name = el.dataset.step;
            el.style.display = name === this.steps[index] ? 'block' : 'none';
        });
        this.updateProgress();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    updateProgress() {
        const bar = document.querySelector('.paf-progress-bar');
        if (!bar) return;
        const pct = ((this.currentIndex + 1) / this.steps.length) * 100;
        bar.style.width = `${pct}%`;
        bar.setAttribute('aria-valuenow', String(Math.round(pct)));
    }

    canAdvanceFrom(index) {
        const stepName = this.steps[index];
        switch (stepName) {
            case 'rating':
                return this.state.rating !== null;
            case 'type':
                return this.state.type !== null;
            case 'category':
                return true;
            case 'share':
                // All three optional — always advance.
                return true;
            case 'contact':
                return this.state.wantsContact ? this.validateContactFields() : true;
            default:
                return true;
        }
    }

    validateContactFields() {
        const name = document.getElementById('name');
        const mobile = document.getElementById('mobile');
        const email = document.getElementById('email');

        const hasName = name && name.value.trim().length > 0;
        const hasMobile = mobile && /^[6-9]\d{9}$/.test(mobile.value.trim());
        const hasEmail = email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());

        return hasMobile || hasEmail || hasName;
    }

    // ---------- SUBMIT ----------
    async submit() {
        this.submitting = true;
        const btn = document.getElementById('paf-submit-btn');
        const originalText = btn ? btn.textContent : '';
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Submitting…';
        }

        const fd = new FormData();

        fd.append('submission_uuid', getOrCreateSubmissionUuid());
        fd.append('overall_rating', String(this.state.rating || ''));
        fd.append('feedback_type', this.state.type || '');
        if (this.state.categoryId) fd.append('category_id', this.state.categoryId);
        if (this.state.subcategoryId) fd.append('subcategory_id', this.state.subcategoryId);
        if (this.state.comment) fd.append('comment', this.state.comment);

        fd.append('is_anonymous', this.state.isAnonymous ? '1' : '0');

        if (!this.state.isAnonymous) {
            const name = document.getElementById('name')?.value || '';
            const mobile = document.getElementById('mobile')?.value || '';
            const email = document.getElementById('email')?.value || '';
            const pref = document.getElementById('preferred_contact_method')?.value || 'none';

            if (name) fd.append('name', name);
            if (mobile) fd.append('mobile', mobile);
            if (email) fd.append('email', email);
            fd.append('preferred_contact_method', pref);
        }

        if (this.voice && this.voice.hasRecording()) {
            const file = this.voice.getFile();
            if (file) fd.append('voice', file, file.name);
        }

        if (this.photos && this.photos.hasPhotos()) {
            this.photos.getFiles().forEach((file, idx) => {
                fd.append(`photos[${idx}]`, file, file.name);
            });
        }

        try {
            const res = await http.post(this.form.action, fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

            // Clear the wizard UUID so the next visit to /feedback generates a fresh one
            clearSubmissionUuid();

            if (res.data && res.data.redirect) {
                window.location.href = res.data.redirect;
            } else {
                window.location.reload();
            }
        } catch (err) {
            this.showError(err.userMessage || 'Submission failed.');
            this.submitting = false;
            if (btn) {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }
    }

    showError(msg) {
        const box = document.getElementById('paf-error');
        if (box) {
            box.textContent = msg;
            box.style.display = 'block';
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}
