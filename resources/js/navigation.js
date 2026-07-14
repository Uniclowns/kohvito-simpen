/**
 * Handles the admin/kasir off-canvas sidebar on mobile viewports.
 *
 * DOM contract:
 * - [data-sidebar-toggle] opens/closes the sidebar.
 * - [data-app-sidebar] is the off-canvas sidebar element.
 * - [data-sidebar-overlay] is the mobile scrim.
 */
const sidebar = document.querySelector('[data-app-sidebar]');
const overlay = document.querySelector('[data-sidebar-overlay]');
const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
const isSuperadminSidebar = sidebar?.classList.contains('superadmin-sidebar');

/**
 * Sets the mobile sidebar open state.
 *
 * @param {boolean} open
 * @returns {void}
 */
function setSidebarOpen(open) {
    if (!sidebar || !overlay) return;

    const wasOpen = sidebar.classList.contains('is-open');
    sidebar.classList.toggle('is-open', open);
    overlay.classList.toggle('hidden', !open);
    document.body.style.overflow = open ? 'hidden' : '';
    toggleButtons.forEach((button) => {
        if (button.hasAttribute('aria-expanded')) button.setAttribute('aria-expanded', String(open));
    });

    if (isSuperadminSidebar && open) {
        sidebar.querySelector('a:not([aria-disabled="true"])')?.focus();
    } else if (isSuperadminSidebar && wasOpen && !open) {
        toggleButtons[0]?.focus();
    }
}

toggleButtons.forEach((button) => {
    button.addEventListener('click', () => {
        setSidebarOpen(!sidebar?.classList.contains('is-open'));
    });
});

overlay?.addEventListener('click', () => setSidebarOpen(false));

document.addEventListener('keydown', (event) => {
    if (event.key === 'Tab' && isSuperadminSidebar && sidebar?.classList.contains('is-open')) {
        const elements = Array.from(sidebar.querySelectorAll('a:not([tabindex="-1"]), button:not([disabled])'));
        const first = elements[0];
        const last = elements.at(-1);

        if (!sidebar.contains(document.activeElement)) {
            event.preventDefault();
            first?.focus();
        } else if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last?.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first?.focus();
        }
        return;
    }

    if (event.key === 'Escape') setSidebarOpen(false);
});
