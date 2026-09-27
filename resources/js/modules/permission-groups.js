export default class PermissionGroups {
    constructor() {
        this.groupToggles = document.querySelectorAll('[data-group-toggle]');
        if (!this.groupToggles.length) return;

        this.groupToggles.forEach((toggle) => {
            const slug = toggle.dataset.groupToggle;
            const items = document.querySelectorAll(`[data-group-item="${slug}"]`);

            if (!items.length) return;

            // Initialize: if any child is checked, group toggle reflects that
            // (but doesn't auto-check the group if only some are checked —
            //  use indeterminate for the "partial" state)
            const checkedCount = [...items].filter((i) => i.checked).length;
            toggle.indeterminate = checkedCount > 0 && checkedCount < items.length;
            toggle.checked = checkedCount === items.length;

            // Toggle all children when group toggle changes
            toggle.addEventListener('change', () => {
                items.forEach((item) => {
                    if (!item.disabled) {
                        item.checked = toggle.checked;
                    }
                });
                toggle.indeterminate = false;
            });

            // Update group toggle when a child changes
            items.forEach((item) => {
                item.addEventListener('change', () => {
                    const checked = [...items].filter((i) => i.checked).length;
                    toggle.indeterminate = checked > 0 && checked < items.length;
                    toggle.checked = checked === items.length;
                });
            });
        });
    }
}
