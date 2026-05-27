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

/**
 * Sets the mobile sidebar open state.
 *
 * @param {boolean} open
 * @returns {void}
 */
function setSidebarOpen(open) {
    if (!sidebar || !overlay) return;

    sidebar.classList.toggle('is-open', open);
    overlay.classList.toggle('hidden', !open);
    document.body.style.overflow = open ? 'hidden' : '';
}

toggleButtons.forEach((button) => {
    button.addEventListener('click', () => {
        setSidebarOpen(!sidebar?.classList.contains('is-open'));
    });
});

overlay?.addEventListener('click', () => setSidebarOpen(false));

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') setSidebarOpen(false);
});
