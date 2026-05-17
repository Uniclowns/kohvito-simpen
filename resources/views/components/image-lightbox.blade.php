<div id="image-lightbox"
     class="hidden fixed inset-0 z-[100] bg-black/75 backdrop-blur-sm items-center justify-center p-8"
     onclick="if(event.target === this) closeImageLightbox()"
     role="dialog" aria-modal="true" aria-label="Pratinjau gambar menu">

    <button type="button"
            class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-all"
            onclick="closeImageLightbox()" aria-label="Tutup pratinjau">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <img id="image-lightbox-img" src="" alt=""
         class="max-w-[90vw] max-h-[90vh] object-contain rounded-xl shadow-[0_8px_32px_rgba(0,0,0,0.4)]">
</div>

@once
<script>
window.openImageLightbox = function (src) {
    if (!src) return;
    const box = document.getElementById('image-lightbox');
    const img = document.getElementById('image-lightbox-img');
    img.src = src;
    box.classList.remove('hidden');
    box.classList.add('flex');
    document.body.style.overflow = 'hidden';
};

window.closeImageLightbox = function () {
    const box = document.getElementById('image-lightbox');
    box.classList.add('hidden');
    box.classList.remove('flex');
    const otherOpen = document.querySelector('[data-confirm-modal]:not(.hidden), [data-form-modal]:not(.hidden)');
    if (!otherOpen) document.body.style.overflow = '';
};

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const box = document.getElementById('image-lightbox');
        if (box && !box.classList.contains('hidden')) closeImageLightbox();
    }
});
</script>
@endonce
