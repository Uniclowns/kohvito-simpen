{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  MENU DETAIL SHEET  —  shared AJAX overlay                       ║
     ║                                                                  ║
     ║  Mobile : slide-up bottom sheet (sits below the top bar).        ║
     ║  Desktop: centered modal dialog (see app.css @media ≥768px).     ║
     ║                                                                  ║
     ║  Open with openMenuSheet(id). It fetches                          ║
     ║  /menu/{id}/detail?partial=1 and injects the detail partial.     ║
     ║  Used by konsumen/beranda (catalog) and konsumen/keranjang (Edit).║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<div id="menu-sheet" class="fixed inset-x-0 bottom-0 z-60 hidden" style="top: var(--kvt-header-h);"
    aria-hidden="true" role="dialog" aria-modal="true" aria-label="Detail menu">
    <div id="menu-sheet-scrim" class="absolute inset-0 bg-black/55 backdrop-blur-[2px]"
        onclick="closeMenuSheet()"></div>

    <div id="menu-sheet-panel"
        class="absolute inset-x-0 bottom-0 top-0 bg-[#F6F6F6] max-w-md mx-auto overflow-y-auto overscroll-contain shadow-[0_-12px_40px_rgba(0,0,0,0.35)]"
        style="-webkit-overflow-scrolling: touch; touch-action: pan-y;">

        {{-- Drag indicator (mobile only) — also clickable to close --}}
        <button type="button" onclick="closeMenuSheet()"
            class="sticky top-0 z-10 flex w-full items-center justify-center bg-transparent pt-2 pb-1 md:hidden">
            <span class="block h-1.5 w-12 rounded-full bg-brand-gray-light/80"></span>
        </button>

        {{-- Close (✕) button — desktop modal affordance --}}
        <button type="button" onclick="closeMenuSheet()" aria-label="Tutup detail menu"
            class="absolute right-4 top-4 z-20 hidden h-9 w-9 items-center justify-center rounded-full bg-white/90 text-brand-dark shadow-[0_2px_10px_rgba(0,0,0,0.2)] transition hover:scale-105 hover:bg-white active:scale-95 md:flex">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Default loading state (replaced by injected partial on success) --}}
        <div id="menu-sheet-loader"
            class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-brand-dark">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                    stroke-dasharray="40 60" />
            </svg>
            <p class="text-[12px] font-bold tracking-wide text-brand-gray">Memuat detail menu...</p>
        </div>

        {{-- Slot where the fetched partial gets injected --}}
        <div id="menu-sheet-body" class="relative"></div>
    </div>
</div>

<script>
    // ============ Detail Menu Sheet (slide-up sheet / centered modal) ============
    // Loads /menu/{id}/detail?partial=1 via AJAX and injects the HTML fragment
    // into the sheet panel, then animates it open. Injected <script> tags are
    // re-executed by cloning them (innerHTML alone does not run them).
    // Wrapped in a guarded IIFE so the component can be included on multiple
    // pages (beranda, keranjang) without redeclaration collisions.
    (function () {
        if (window.__menuSheetReady) return;
        window.__menuSheetReady = true;

        // Remember pre-sheet scroll so we can restore it on close.
        let returnScrollY = 0;

        // ─── Robust body scroll lock (iOS Safari–safe) ───
        // `body { overflow: hidden }` alone is bypassed on iOS — body still scrolls
        // and scroll chains from the panel into the catalog beneath. The cure is
        // pinning <body> in place with `position: fixed; top: -<scrollY>px` while
        // the sheet is open, then restoring scroll on close.
        function lockBodyScroll() {
            returnScrollY = window.scrollY || document.documentElement.scrollTop || 0;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${returnScrollY}px`;
            document.body.style.left = '0';
            document.body.style.right = '0';
            document.body.style.width = '100%';
            document.body.style.overflow = 'hidden';
        }
        function unlockBodyScroll() {
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            document.body.style.width = '';
            document.body.style.overflow = '';
            if (returnScrollY > 0) {
                window.scrollTo(0, returnScrollY);
                returnScrollY = 0;
            }
        }

        window.openMenuSheet = async function (id) {
            const sheet = document.getElementById('menu-sheet');
            const body = document.getElementById('menu-sheet-body');
            const loader = document.getElementById('menu-sheet-loader');
            if (!sheet || !body) return;

            // Reset & reveal
            body.innerHTML = '';
            if (loader) loader.style.display = '';
            sheet.classList.remove('hidden');
            sheet.setAttribute('aria-hidden', 'false');
            lockBodyScroll();

            // Force the browser to register the initial translateY(100%) state
            // BEFORE we toggle .is-open — otherwise display:none→block in the
            // same frame as the class change makes the browser skip the slide
            // animation and the panel just pops in.
            void sheet.offsetHeight; // sync reflow
            requestAnimationFrame(() => { // first paint of initial state
                requestAnimationFrame(() => { // then trigger transition
                    sheet.classList.add('is-open');
                });
            });

            try {
                const res = await fetch(`/menu/${id}/detail?partial=1`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const html = await res.text();

                body.innerHTML = html;
                if (loader) loader.style.display = 'none';

                // <script> tags inserted via innerHTML do NOT execute — clone them
                body.querySelectorAll('script').forEach(oldScript => {
                    const newScript = document.createElement('script');
                    for (const attr of oldScript.attributes) {
                        newScript.setAttribute(attr.name, attr.value);
                    }
                    newScript.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            } catch (e) {
                body.innerHTML = `
                    <div class="p-8 text-center">
                        <p class="text-brand-gray text-sm mb-4">Gagal memuat detail menu.</p>
                        <button type="button" onclick="closeMenuSheet()" class="bg-brand-dark text-white px-5 py-2.5 rounded-[9px] text-sm font-bold">Tutup</button>
                    </div>`;
                if (loader) loader.style.display = 'none';
            }
        };

        window.closeMenuSheet = function () {
            const sheet = document.getElementById('menu-sheet');
            const body = document.getElementById('menu-sheet-body');
            if (!sheet) return;

            sheet.classList.remove('is-open');
            sheet.setAttribute('aria-hidden', 'true');

            // After the slide-down transition completes, hide the sheet entirely
            // and restore the user's prior scroll position via unlockBodyScroll().
            setTimeout(() => {
                sheet.classList.add('hidden');
                if (body) body.innerHTML = '';
                unlockBodyScroll();
            }, 420);
        };

        // ESC closes the sheet
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const sheet = document.getElementById('menu-sheet');
                if (sheet && sheet.classList.contains('is-open')) {
                    window.closeMenuSheet();
                }
            }
        });
    })();
</script>
