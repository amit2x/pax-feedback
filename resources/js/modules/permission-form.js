export default class PermissionForm {
    constructor() {
        this.hint = document.querySelector('[data-prefix-hint]');
        this.nameInput = document.getElementById('name');
        if (!this.hint || !this.nameInput || this.nameInput.disabled) return;

        this.hint.addEventListener('change', () => {
            const prefix = this.hint.value;
            if (!prefix) return;

            const current = this.nameInput.value.trim();
            const afterDot = current.includes('.')
                ? current.split('.').slice(1).join('.')
                : current;

            this.nameInput.value = `${prefix}.${afterDot}`;
            this.nameInput.focus();
            this.nameInput.setSelectionRange(
                this.nameInput.value.length,
                this.nameInput.value.length
            );
        });
    }
}
