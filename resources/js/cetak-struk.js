/**
 * Direct thermal receipt printing for the cashier panel.
 *
 * Any element carrying [data-cetak-struk] with a [data-cetak-url] posts to the
 * server, which streams ESC/POS bytes to the networked 80mm printer. Feedback is
 * shown through a lightweight toast so the cashier never leaves the page.
 */

/**
 * Shows a transient toast message at the bottom of the screen.
 *
 * @param {string} message
 * @param {'success'|'error'} [type='success']
 * @returns {void}
 */
function showStrukToast(message, type = 'success') {
    document.getElementById('kvt-struk-toast')?.remove();

    const toast = document.createElement('div');
    toast.id = 'kvt-struk-toast';
    toast.setAttribute('role', 'status');
    toast.textContent = message;
    toast.style.cssText = [
        'position:fixed', 'left:50%', 'bottom:28px',
        'transform:translateX(-50%) translateY(12px)',
        'z-index:9999', 'max-width:90vw', 'padding:12px 20px',
        'border-radius:9px', 'color:#fff', 'font-size:14px',
        'line-height:20px', 'letter-spacing:0.5px',
        'box-shadow:0 8px 24px rgba(0,0,0,0.22)',
        'opacity:0', 'transition:opacity .25s ease, transform .25s ease',
        'background:' + (type === 'success' ? '#460001' : '#E52E2D'),
    ].join(';');

    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(12px)';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}

/**
 * Sends the print request for one order and manages the button's loading state.
 *
 * @param {HTMLButtonElement} button
 * @returns {Promise<void>}
 */
async function handleCetak(button) {
    if (button.dataset.loading === '1') return;

    const url = button.dataset.cetakUrl;
    if (!url) return;

    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';
    const label = button.querySelector('[data-cetak-text]');
    const originalText = label ? label.textContent : '';

    button.dataset.loading = '1';
    button.disabled = true;
    button.style.opacity = '0.7';
    if (label) label.textContent = 'Mencetak…';

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        });

        // Sesi login habis -> arahkan kembali ke halaman login.
        if (response.status === 401) {
            showStrukToast('Sesi login habis. Mengarahkan ke halaman login…', 'error');
            setTimeout(() => { window.location.href = '/login'; }, 1600);
            return;
        }

        // Token halaman kedaluwarsa (CSRF) -> minta muat ulang halaman.
        if (response.status === 419) {
            showStrukToast('Halaman kedaluwarsa. Muat ulang halaman (F5) lalu cetak lagi.', 'error');
            return;
        }

        const data = await response.json().catch(() => ({}));

        if (response.ok && data.ok) {
            showStrukToast(data.message || 'Struk berhasil dicetak.', 'success');
        } else {
            showStrukToast(data.message || 'Gagal mencetak struk.', 'error');
        }
    } catch (error) {
        showStrukToast('Gagal menghubungi server. Coba lagi.', 'error');
    } finally {
        button.dataset.loading = '0';
        button.disabled = false;
        button.style.opacity = '';
        if (label) label.textContent = originalText;
    }
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-cetak-struk]');
    if (!button) return;

    event.preventDefault();
    handleCetak(button);
});
