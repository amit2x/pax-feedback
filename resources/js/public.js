import $ from 'jquery';
import 'bootstrap';
import axios from 'axios';

window.$ = window.jQuery = $;
window.axios = axios;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrf = document.querySelector('meta[name="csrf-token"]');
if (csrf) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.getAttribute('content');
}

// ------------- Feedback Wizard -------------
class FeedbackWizard {
    constructor() {
        this.form = document.getElementById('paf-wizard');
        if (!this.form) return;

        this.state = {
            rating: null,
            type: null,
            categoryId: null,
            subcategoryId: null,
            comment: '',
            isAnonymous: true,
            wantsContact: false,
        };

        this.bindRating();
        this.bindType();
        this.bindCategory();
        this.bindComment();
        this.bindContact();
        this.bindSubmit();
        this.bindNavigation();
        this.updateProgress();
    }

    bindRating() {
        document.querySelectorAll('[data-rating]').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-rating]').forEach((b) => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                this.state.rating = parseInt(btn.dataset.rating, 10);
                const input = document.getElementById('overall_rating');
                if (input) input.value = this.state.rating;
            });
        });
    }

    bindType() {
        document.querySelectorAll('[data-type]').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-type]').forEach((b) => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                this.state.type = btn.dataset.type;
                const input = document.getElementById('feedback_type');
                if (input) input.value = this.state.type;
            });
        });
    }

    bindCategory() {
        document.querySelectorAll('[data-category]').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-category]').forEach((b) => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                this.state.categoryId = btn.dataset.category;
                const input = document.getElementById('category_id');
                if (input) input.value = this.state.categoryId;

                const subWrap = document.getElementById('subcategory-container');
                if (!subWrap) return;

                subWrap.innerHTML = '<p class="text-muted">Loading…</p>';

                axios.get('/feedback/api/subcategories', {
                    params: { category_id: this.state.categoryId },
                })
                    .then((res) => {
                        subWrap.innerHTML = '';
                        res.data.forEach((sub) => {
                            const b = document.createElement('button');
                            b.type = 'button';
                            b.className = 'paf-type-card';
                            b.dataset.subcategory = sub.id;
                            const span = document.createElement('span');
                            span.className = 'paf-type-icon';
                            span.textContent = '•';
                            const label = document.createElement('span');
                            label.textContent = sub.name;
                            b.appendChild(span);
                            b.appendChild(label);
                            b.addEventListener('click', () => {
                                subWrap.querySelectorAll('[data-subcategory]').forEach((x) => x.classList.remove('is-selected'));
                                b.classList.add('is-selected');
                                this.state.subcategoryId = sub.id;
                                const input = document.getElementById('subcategory_id');
                                if (input) input.value = sub.id;
                            });
                            subWrap.appendChild(b);
                        });
                        if (!res.data.length) {
                            subWrap.innerHTML = '<p class="text-muted">No specific options</p>';
                        }
                    })
                    .catch(() => {
                        subWrap.innerHTML = '<p class="text-danger">Could not load options.</p>';
                    });
            });
        });
    }

    bindComment() {
        const textarea = document.getElementById('comment');
        if (!textarea) return;
        textarea.addEventListener('input', () => {
            this.state.comment = textarea.value;
            const counter = document.getElementById('comment-counter');
            if (counter) counter.textContent = `${textarea.value.length}/500`;
        });
    }

    bindContact() {
        document.querySelectorAll('[data-contact]').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-contact]').forEach((b) => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                this.state.wantsContact = btn.dataset.contact === 'yes';
                this.state.isAnonymous = !this.state.wantsContact;
                const field = document.getElementById('contact-fields');
                if (field) field.style.display = this.state.wantsContact ? 'block' : 'none';
                const isAnon = document.getElementById('is_anonymous');
                if (isAnon) isAnon.value = this.state.isAnonymous ? '1' : '0';
            });
        });
    }

    bindNavigation() {
        document.querySelectorAll('[data-goto]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const step = btn.dataset.goto;
                this.showStep(step);
            });
        });
    }

    showStep(name) {
        document.querySelectorAll('[data-step]').forEach((el) => {
            el.style.display = el.dataset.step === name ? 'block' : 'none';
        });
        this.updateProgress();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    updateProgress() {
        const visible = document.querySelector('[data-step]:not([style*="display: none"])');
        const bar = document.querySelector('.paf-progress-bar');
        if (!bar) return;
        const steps = ['rating', 'type', 'category', 'comment', 'contact'];
        const index = steps.indexOf(visible?.dataset.step || 'rating');
        bar.style.width = `${Math.max(5, ((index + 1) / steps.length) * 100)}%`;
    }

    bindSubmit() {
        const submitBtn = document.getElementById('paf-submit-btn');
        if (!submitBtn) return;

        submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (submitBtn.disabled) return;
            submitBtn.disabled = true;
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting…';

            const form = document.getElementById('paf-wizard');
            const formData = new FormData(form);

            axios.post(form.action, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            })
                .then((res) => {
                    if (res.data && res.data.redirect) {
                        window.location.href = res.data.redirect;
                    } else {
                        window.location.reload();
                    }
                })
                .catch((err) => {
                    const msg = (err.response && err.response.data && err.response.data.message)
                        ? err.response.data.message
                        : 'Something went wrong. Please try again.';
                    const alertBox = document.getElementById('paf-error');
                    if (alertBox) {
                        alertBox.textContent = msg;
                        alertBox.style.display = 'block';
                    } else {
                        alert(msg);
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FeedbackWizard();
});
