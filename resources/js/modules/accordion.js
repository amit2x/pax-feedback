export default class Accordion {
    /**
     * @param {HTMLElement} root — container that holds all [data-accordion] blocks
     */
    constructor(root) {
        this.root = root;
        if (!this.root) return;

        this.items = Array.from(this.root.querySelectorAll('[data-accordion]'));

        this.items.forEach((item) => {
            const toggle = item.querySelector('[data-accordion-toggle]');
            const panel = item.querySelector('[data-accordion-panel]');
            if (!toggle || !panel) return;

            toggle.addEventListener('click', () => this.toggle(item));
        });
    }

    toggle(item) {
        const toggle = item.querySelector('[data-accordion-toggle]');
        const panel = item.querySelector('[data-accordion-panel]');
        const isOpen = !panel.hasAttribute('hidden');

        if (isOpen) {
            panel.setAttribute('hidden', '');
            toggle.setAttribute('aria-expanded', 'false');
            item.classList.remove('is-open');
        } else {
            panel.removeAttribute('hidden');
            toggle.setAttribute('aria-expanded', 'true');
            item.classList.add('is-open');

            // Scroll the panel into view (nice on mobile)
            requestAnimationFrame(() => {
                const rect = panel.getBoundingClientRect();
                const viewportH = window.innerHeight || document.documentElement.clientHeight;
                // Only scroll if panel is below the fold
                if (rect.bottom > viewportH - 20) {
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }
    }

    /**
     * Programmatically open an accordion by a data attribute key.
     * @param {string} selector — e.g. '[data-accordion-voice]'
     */
    openBySelector(selector) {
        const item = this.root.querySelector(selector);
        if (!item) return;
        const panel = item.querySelector('[data-accordion-panel]');
        if (panel && panel.hasAttribute('hidden')) this.toggle(item);
    }

    closeAll() {
        this.items.forEach((item) => {
            const panel = item.querySelector('[data-accordion-panel]');
            const toggle = item.querySelector('[data-accordion-toggle]');
            if (panel && !panel.hasAttribute('hidden')) {
                panel.setAttribute('hidden', '');
                toggle.setAttribute('aria-expanded', 'false');
                item.classList.remove('is-open');
            }
        });
    }
}
