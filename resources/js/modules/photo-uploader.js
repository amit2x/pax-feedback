export default class PhotoUploader {
    /**
     * @param {Object} opts
     * @param {HTMLElement} opts.root
     * @param {number} opts.maxFiles
     * @param {number} opts.maxBytesPerFile
     */
    constructor({ root, maxFiles = 2, maxBytesPerFile = 5 * 1024 * 1024 }) {
        this.root = root;
        this.maxFiles = maxFiles;
        this.maxBytesPerFile = maxBytesPerFile;
        this.files = [];

        this.inputEl = root.querySelector('[data-photo-input]');
        this.pickBtn = root.querySelector('[data-photo-pick]');
        this.gridEl = root.querySelector('[data-photo-grid]');
        this.errorEl = root.querySelector('[data-photo-error]');

        this.bind();
    }

    bind() {
        if (this.pickBtn && this.inputEl) {
            this.pickBtn.addEventListener('click', () => this.inputEl.click());
        }
        if (this.inputEl) {
            this.inputEl.addEventListener('change', (e) => this.onSelect(e));
        }
    }

    onSelect(e) {
        this.clearError();
        const picked = Array.from(e.target.files || []);
        e.target.value = '';

        for (const file of picked) {
            if (this.files.length >= this.maxFiles) {
                this.showError(`You can upload up to ${this.maxFiles} photos.`);
                break;
            }
            if (!this.isAllowedType(file)) {
                this.showError('Only JPG, PNG, or WebP images are allowed.');
                continue;
            }
            if (file.size > this.maxBytesPerFile) {
                this.showError('Each photo must be smaller than 5 MB.');
                continue;
            }
            this.files.push(file);
        }

        this.render();
    }

    remove(index) {
        this.files.splice(index, 1);
        this.render();
    }

    isAllowedType(file) {
        return ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);
    }

    render() {
        if (!this.gridEl) return;
        this.gridEl.innerHTML = '';

        this.files.forEach((file, idx) => {
            const tile = document.createElement('div');
            tile.className = 'paf-photo-preview';

            const img = document.createElement('img');
            const url = URL.createObjectURL(file);
            img.src = url;
            img.alt = '';
            img.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'paf-photo-remove';
            btn.setAttribute('aria-label', 'Remove photo');
            btn.textContent = '×';
            btn.addEventListener('click', () => this.remove(idx));

            tile.appendChild(img);
            tile.appendChild(btn);
            this.gridEl.appendChild(tile);
        });
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
     * @returns {File[]}
     */
    getFiles() {
        return this.files.slice();
    }

    hasPhotos() {
        return this.files.length > 0;
    }
}
