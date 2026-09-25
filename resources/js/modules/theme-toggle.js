const COOKIE_NAME = 'paf_admin_theme';
const COOKIE_MAX_AGE = 60 * 60 * 24 * 365;   // 1 year, in seconds
const COOKIE_PATH = '/';

// ---------- Cookie helpers ----------
function readCookie(name) {
    const prefix = name + '=';
    const parts = document.cookie ? document.cookie.split('; ') : [];
    for (const part of parts) {
        if (part.indexOf(prefix) === 0) {
            return decodeURIComponent(part.substring(prefix.length));
        }
    }
    return null;
}

function writeCookie(name, value, maxAge) {
    const parts = [
        `${name}=${encodeURIComponent(value)}`,
        `Max-Age=${maxAge}`,
        `Path=${COOKIE_PATH}`,
        'SameSite=Lax',
    ];
    // Secure flag only when served over HTTPS
    if (window.location.protocol === 'https:') {
        parts.push('Secure');
    }
    document.cookie = parts.join('; ');
}

// ---------- Toggle class ----------
export default class ThemeToggle {
    constructor() {
        this.buttons = document.querySelectorAll('[data-theme-toggle]');
        if (!this.buttons.length) return;

        // If no server-side theme was applied (first visit, no cookie),
        // fall back to system preference via CSS — no JS action needed.
        // If we have a cookie, the server already set data-theme on <html>.

        // Ensure the DOM attribute is always present, for clarity in DevTools.
        this.ensureAttribute();

        this.buttons.forEach((btn) => {
            btn.addEventListener('click', () => this.toggle());
        });

        // Update the aria-pressed state on load
        this.syncButtonState();

        // React to OS-level changes only if user hasn't chosen
        this.watchSystemPreference();
    }

    getCurrent() {
        const attr = document.documentElement.getAttribute('data-theme');
        if (attr === 'light' || attr === 'dark') return attr;
        // No attribute → system decides
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    hasUserPreference() {
        const cookie = readCookie(COOKIE_NAME);
        return cookie === 'light' || cookie === 'dark';
    }

    ensureAttribute() {
        // If server set it, we're done. If not, we don't force it —
        // CSS media query handles first visit.
        // This method is intentionally a no-op unless the user has a cookie
        // but the attribute isn't present (shouldn't happen, but defensive).
        const attr = document.documentElement.getAttribute('data-theme');
        if (!attr && this.hasUserPreference()) {
            document.documentElement.setAttribute('data-theme', readCookie(COOKIE_NAME));
        }
    }

    toggle() {
        const next = this.getCurrent() === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        writeCookie(COOKIE_NAME, next, COOKIE_MAX_AGE);
        this.syncButtonState();
    }

    syncButtonState() {
        const current = this.getCurrent();
        this.buttons.forEach((btn) => {
            btn.setAttribute('aria-pressed', current === 'dark' ? 'true' : 'false');
            btn.setAttribute(
                'aria-label',
                current === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'
            );
        });
    }

    watchSystemPreference() {
        const mq = window.matchMedia('(prefers-color-scheme: dark)');
        mq.addEventListener('change', () => {
            // Only auto-switch if the user hasn't explicitly chosen a theme.
            if (!this.hasUserPreference()) {
                this.syncButtonState();
            }
        });
    }
}
