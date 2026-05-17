/**
 * Global Modal Toggle Function
 * Digunakan untuk menampilkan/menyembunyikan modal berdasarkan ID.
 */
window.toggleModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.toggle('hidden');
        
        // Opsional: Handle body scroll lock jika modal terbuka
        if (!modal.classList.contains('hidden')) {
            document.body.style.overflow = 'hidden';
        } else {
            // Cek apakah masih ada modal lain yang terbuka
            const otherVisibleModals = document.querySelectorAll('.fixed:not(.hidden)');
            if (otherVisibleModals.length === 0) {
                document.body.style.overflow = '';
            }
        }
    }
};

/**
 * Global Esc Key Listener
 * Menutup modal paling atas yang sedang terbuka saat tombol Esc ditekan.
 */
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        // Cari semua modal yang terbuka, urutkan berdasarkan z-index (atau asumsikan yang terakhir di DOM adalah yang teratas)
        const visibleModals = Array.from(document.querySelectorAll('.fixed:not(.hidden)'));
        if (visibleModals.length > 0) {
            // Tutup hanya modal terakhir (paling atas)
            const topModal = visibleModals[visibleModals.length - 1];
            
            // Jika itu confirm modal, gunakan closeConfirmModal
            if (topModal.hasAttribute('data-confirm-modal')) {
                if (window.closeConfirmModal) {
                    window.closeConfirmModal(topModal.id);
                } else {
                    topModal.classList.add('hidden');
                }
            } else if (topModal.hasAttribute('data-status-modal')) {
                if (window.closeStatusModal) {
                    window.closeStatusModal(topModal.id);
                } else {
                    topModal.classList.add('hidden');
                }
            } else {
                window.toggleModal(topModal.id);
            }
        }
    }
});
