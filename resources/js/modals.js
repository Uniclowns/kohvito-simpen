/**
 * Shared modal helpers for admin, cashier, and consumer Blade components.
 *
 * Every modal is identified by id and can opt into flex centering by carrying
 * data-konsumen-confirm-modal. Legacy helper names remain as aliases so older
 * Blade call sites continue to work during the clean-code migration.
 */
const modalSelector = '.fixed:not(.hidden)';

/**
 * Returns the modal element for a given id.
 *
 * @param {string} id
 * @returns {HTMLElement|null}
 */
function findModal(id) {
    return document.getElementById(id);
}

/**
 * Locks or unlocks page scrolling based on currently visible modal count.
 *
 * @returns {void}
 */
function syncBodyScrollLock() {
    document.body.style.overflow = document.querySelector(modalSelector) ? 'hidden' : '';
}

/**
 * Opens a modal by id without changing any business flow.
 *
 * @param {string} id
 * @returns {void}
 */
function openAppModal(id) {
    const modal = findModal(id);
    if (!modal) return;

    modal.classList.remove('hidden');
    if (modal.hasAttribute('data-konsumen-confirm-modal')) {
        modal.classList.add('flex');
    }
    document.body.style.overflow = 'hidden';
}

/**
 * Closes a modal by id and releases scroll only when no other modal is open.
 *
 * @param {string} id
 * @returns {void}
 */
function closeAppModal(id) {
    const modal = findModal(id);
    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    syncBodyScrollLock();
}

/**
 * Toggles a modal's visibility. This keeps support for older generic calls.
 *
 * @param {string} id
 * @returns {void}
 */
function toggleAppModal(id) {
    const modal = findModal(id);
    if (!modal) return;

    if (modal.classList.contains('hidden')) {
        openAppModal(id);
    } else {
        closeAppModal(id);
    }
}

window.openAppModal = openAppModal;
window.closeAppModal = closeAppModal;
window.toggleAppModal = toggleAppModal;

// Backward-compatible aliases while Blade markup is migrated to the single API.
window.openConfirmModal = openAppModal;
window.closeConfirmModal = closeAppModal;
window.openKonsumenConfirmModal = openAppModal;
window.closeKonsumenConfirmModal = closeAppModal;
window.toggleModal = toggleAppModal;

/**
 * Closes the top-most modal on Escape. The DOM order is used as the z-stack
 * because all Blade modal components are rendered near the end of each view.
 */
document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;

    const visibleModals = Array.from(document.querySelectorAll(modalSelector));
    const topModal = visibleModals.at(-1);
    if (topModal?.id) closeAppModal(topModal.id);
});
