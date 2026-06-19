<div class="dm-scope min-h-screen bg-[#F6F6F6] text-brand-black font-sans">
    @php
        $isMinuman = $menu->jenis_menu === 'Minuman';
        $isMakanan = $menu->jenis_menu === 'Makanan';
        $hasTemperature = $isMinuman && $menu->tipe_minuman === 'Keduanya';
        $isDinginOnly = $isMinuman && $menu->tipe_minuman === 'Dingin';
        $isPanasOnly = $isMinuman && $menu->tipe_minuman === 'Panas';
        $showSugar = $isMinuman;
        $showIce = $isMinuman && in_array($menu->tipe_minuman, ['Dingin', 'Keduanya'], true);
        $isPedas = $isMakanan && $menu->kategori_makanan === 'Pedas';
        $stock = (int) ($menu->stock ?? 0);
        $maxQty = $stock > 0 ? min($stock, 99) : 99;
        $tableNo = session('id_meja_no', $meja->no_meja ?? 'XXX');
        $imgType = $menu->jenis_menu === 'Makanan' ? 'food' : 'drink';
        $imgSrc = $menu->gambar_menu
            ? (str_starts_with($menu->gambar_menu, 'http')
                ? $menu->gambar_menu
                : asset("images/{$imgType}/{$menu->gambar_menu}"))
            : null;
    @endphp

    <header class="dm-header bg-brand-dark px-[18px] pt-[14px] pb-[12px] safe-top">
        <div class="mx-auto flex max-w-[390px] md:max-w-4xl lg:max-w-5xl items-center justify-between">
            <p class="flex-1 text-white text-[12px] leading-4 font-bold tracking-[0.6px] capitalize">Tambah Menu</p>
            <div class="flex h-9 w-9 shrink-0 items-center justify-center">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito" class="h-full w-full object-contain">
            </div>
            <p class="flex-1 text-right text-white text-[12px] leading-4 font-bold tracking-[0.6px] uppercase">
                TABLE {{ $tableNo }}
            </p>
        </div>
    </header>

    <main class="mx-auto max-w-[390px] px-4 pb-6 sm:px-[18px] md:max-w-4xl md:px-8 md:pt-3 lg:max-w-5xl">
        <div class="dm-enter pt-3 pb-1" style="animation-delay: 0.12s">
            <a href="{{ session('id_meja_no') ? route('konsumen.beranda', session('id_meja_no')) : '#' }}"
               data-dm-back
               class="inline-flex items-center gap-3 text-brand-black active:opacity-70">
                <svg class="h-5 w-5 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="text-[20px] leading-8 font-bold tracking-[1px]">Kembali</span>
            </a>
        </div>

        <form id="detail-form" method="POST" action="{{ route('konsumen.keranjang.tambah') }}" class="flex flex-col md:grid md:grid-cols-12 md:gap-6 md:items-stretch w-full gap-3">
            @csrf
            <input type="hidden" name="id_menu" value="{{ $menu->id_menu }}">
            <input type="hidden" name="jumlah" id="qty-input" value="1">
            <input type="hidden" name="catatan" id="catatan-input" value="">
            <input type="hidden" name="harga_tambahan" id="harga-tambahan-input" value="0">

            <section class="dm-enter flex w-full flex-col overflow-hidden rounded-[22px] bg-white p-4 ring-1 ring-brand-dark/[0.06] shadow-[0_12px_34px_-16px_rgba(70,0,1,0.28)] md:col-span-5 md:p-6" style="animation-delay: 0.16s">
                <div class="dm-image mx-auto flex aspect-square w-full max-w-[248px] items-center justify-center overflow-hidden rounded-[18px] bg-gradient-to-br from-brand-red-muted/20 via-white to-white ring-1 ring-brand-dark/[0.05] md:aspect-auto md:h-auto md:min-h-[300px] md:flex-1 md:max-w-none">
                    @if ($imgSrc)
                        <img src="{{ $imgSrc }}" alt="{{ $menu->nama_menu }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-[10px] text-brand-gray">No Image</span>
                    @endif
                </div>

                <div class="mt-5 flex w-full flex-col gap-1.5 text-left">
                    <h1 class="text-[24px] leading-8 font-bold tracking-[0.5px] text-brand-black md:text-[28px] md:leading-9">{{ $menu->nama_menu }}</h1>
                    <p class="text-[24px] leading-8 font-bold tracking-[0.4px] text-brand-red md:text-[26px] md:leading-9">
                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                    </p>
                    @if (!empty($menu->komposisi))
                        <p class="mt-1 text-[12px] leading-[16px] tracking-[0.4px] font-semibold text-brand-red/85">{{ $menu->komposisi }}</p>
                    @endif
                    @if (!empty($menu->deskripsi))
                        <p class="mt-1.5 text-[12.5px] leading-[19px] tracking-[0.2px] text-brand-gray-dark text-justify">{{ $menu->deskripsi }}</p>
                    @endif
                </div>
            </section>

            <section id="dm-options" class="dm-enter flex w-full flex-col gap-4 rounded-[22px] bg-white p-4 ring-1 ring-brand-dark/[0.06] shadow-[0_12px_34px_-16px_rgba(70,0,1,0.28)] md:col-span-7 md:p-6" style="animation-delay: 0.28s">
                <div class="flex flex-col gap-2">
                    <p class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Jumlah Pemesanan</p>
                    <div class="flex h-[52px] w-full items-center justify-between rounded-[14px] border border-brand-dark/10 bg-[#F7F4F4] px-2 shadow-[inset_0_1px_2px_rgba(70,0,1,0.05)]">
                        <button type="button" id="qty-minus" class="qty-btn flex items-center justify-center" aria-label="Kurangi jumlah">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                        </button>
                        <span id="qty-display" class="flex-1 text-center text-[18px] leading-6 tracking-[0.5px] text-brand-black font-bold">1</span>
                        <button type="button" id="qty-plus" class="qty-btn flex items-center justify-center" aria-label="Tambah jumlah">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" /></svg>
                        </button>
                    </div>
                    @if ($stock > 0 && $stock <= 5)
                        <p class="text-[10px] leading-3 tracking-[0.5px] text-state-red">
                            Menu ini hanya tersedia {{ $stock }} porsi
                        </p>
                    @elseif ($stock === 0)
                        <p class="text-[10px] leading-3 tracking-[0.5px] text-state-red">
                            Menu ini sedang tidak tersedia
                        </p>
                    @endif
                </div>

                @if ($hasTemperature)
                    <fieldset class="flex flex-col gap-[7px]" data-group="suhu" data-label="Suhu">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Suhu Minuman</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip" data-value="Dingin" data-selected="true">Dingin</button>
                            <button type="button" class="opt-chip" data-value="Panas" data-selected="false">Panas</button>
                        </div>
                    </fieldset>
                @elseif ($isDinginOnly || $isPanasOnly)
                    <fieldset class="flex flex-col gap-[7px]" data-group="suhu" data-label="Suhu">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Suhu Minuman</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip" data-value="{{ $menu->tipe_minuman }}" data-selected="true">{{ $menu->tipe_minuman }}</button>
                        </div>
                    </fieldset>
                @endif

                @if ($showSugar)
                    <fieldset class="flex flex-col gap-[7px]" data-group="sugar" data-label="Sugar">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Sugar Level</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="No Sugar" data-selected="true">No Sugar</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Less Sugar" data-selected="false">Less Sugar</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Normal" data-selected="false">Normal</button>
                        </div>
                    </fieldset>
                @endif

                @if ($isMinuman)
                    <fieldset class="flex flex-col gap-[7px]" data-group="extra-espresso" data-label="Extra Espresso" data-optional="true">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Extra Espresso</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="+1 Shot Espresso" data-extra-price="2000" data-selected="false">
                                <span>+1 Shot</span>
                                <span class="opt-chip-price">(Rp 2.000)</span>
                            </button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="+2 Shots Espresso" data-extra-price="4000" data-selected="false">
                                <span>+2 Shots</span>
                                <span class="opt-chip-price">(Rp 4.000)</span>
                            </button>
                        </div>
                    </fieldset>
                @endif

                @if ($showIce)
                    <fieldset id="ice-fieldset" class="flex flex-col gap-[7px]" data-group="ice" data-label="Ice">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Ice Level</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip" data-value="Normal" data-selected="true">Normal</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Less Ice" data-selected="false">Less Ice</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="No Ice" data-selected="false">No Ice</button>
                        </div>
                    </fieldset>
                @endif

                @if ($isMakanan)
                    <fieldset class="flex flex-col gap-[7px]" data-group="free-mineral" data-label="Free Mineral">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Free Mineral</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip" data-value="Biasa" data-selected="true">Biasa</button>
                            <button type="button" class="opt-chip" data-value="Dingin" data-selected="false">Dingin</button>
                        </div>
                    </fieldset>

                    <fieldset class="flex flex-col gap-[7px]" data-group="extra-telur" data-label="Extra Telur" data-optional="true">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Extra Telur</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Telur Mata Sapi" data-extra-price="2000" data-selected="false">
                                <span>Telur Mata Sapi</span>
                                <span class="opt-chip-price">(Rp 2.000)</span>
                            </button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="+1 Telur Dadar" data-extra-price="2000" data-selected="false">
                                <span>+1 Telur Dadar</span>
                                <span class="opt-chip-price">(Rp 2.000)</span>
                            </button>
                        </div>
                    </fieldset>

                    <fieldset id="kematangan-fieldset" class="flex flex-col gap-[7px]" data-group="kematangan" data-label="Tingkat Kematangan Telur" style="display: none;">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Tingkat Kematangan Telur</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip" data-value="Matang" data-selected="true">Matang</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Setengah Matang" data-selected="false">Setengah Matang</button>
                        </div>
                    </fieldset>
                @endif

                @if ($isPedas)
                    <fieldset class="flex flex-col gap-[7px]" data-group="chili" data-label="Chili Oil">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.5px] text-brand-black">Chili Oil</legend>
                        <div class="flex flex-wrap gap-2.5">
                            <button type="button" class="opt-chip" data-value="Dicampur ke kuah" data-selected="true">Dicampur ke kuah</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Dipisah" data-selected="false">Dipisah</button>
                        </div>
                    </fieldset>
                @endif

            </section>
        </form>
    </main>

    {{-- Sticky footer: stays pinned to bottom of scroll container.
         Tidak pakai `fixed` karena #menu-sheet-panel punya `will-change: transform`
         yang bikin fixed jadi anchored ke panel (bukan viewport) → ikut scroll.
         Sticky bottom-0 pin ke bottom container scroll (panel di sheet, body di standalone).
         Footer adalah sibling of <main>, jadi dm-scope (parent) yang menentukan lebar. --}}
    <div class="dm-fixed-footer sticky bottom-0 z-30 border-t border-brand-dark/8 bg-white/95 backdrop-blur-md shadow-[0_-8px_24px_rgba(70,0,1,0.10)]"
         style="padding-bottom: max(14px, env(safe-area-inset-bottom));">
        <div class="mx-auto flex max-w-[390px] items-center justify-between gap-4 px-4 pt-[14px] sm:px-[18px] md:max-w-4xl lg:max-w-5xl">
            <div class="min-w-0 flex-shrink-0">
                <p class="text-[10px] leading-3 tracking-[1px] font-semibold text-brand-gray uppercase">Harga Total</p>
                <p id="dm-subtotal" data-base-price="{{ (int) $menu->harga }}"
                   class="mt-1 text-[24px] leading-7 font-bold tracking-[0.5px] text-brand-dark md:text-[28px] md:leading-8">
                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                </p>
            </div>
            <button type="submit"
                    form="detail-form"
                    class="flex max-w-[320px] flex-1 shrink-0 items-center justify-center gap-2.5 rounded-[16px] bg-gradient-to-br from-brand-red to-brand-dark px-6 py-4 text-[15px] font-bold leading-5 tracking-[0.5px] text-white shadow-[0_8px_20px_-6px_rgba(70,0,1,0.55)] transition-all hover:-translate-y-0.5 hover:shadow-[0_12px_26px_-6px_rgba(70,0,1,0.7)] active:scale-[0.97] disabled:translate-y-0 disabled:bg-none disabled:bg-brand-gray disabled:shadow-none disabled:cursor-not-allowed"
                    @if ($stock === 0) disabled @endif>
                @if ($stock === 0)
                    Stok Habis
                @else
                    <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.137a59.769 59.769 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    <span>Tambah Ke Keranjang</span>
                @endif
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const scopes = document.querySelectorAll('.dm-scope');
        const root = scopes[scopes.length - 1];
        if (!root || root.dataset.dmReady === '1') return;
        root.dataset.dmReady = '1';

        let qty = 1;
        const subtotalEl = root.querySelector('#dm-subtotal');
        const extraPriceInput = root.querySelector('#harga-tambahan-input');
        const basePrice = subtotalEl ? Number(subtotalEl.dataset.basePrice || 0) : 0;
        const rupiahFormatter = new Intl.NumberFormat('id-ID');

        function selectedExtraTotal() {
            let total = 0;
            root.querySelectorAll('.opt-chip[data-selected="true"][data-extra-price]').forEach((chip) => {
                const group = chip.closest('[data-group]');
                if (group && group.style.display === 'none') return;
                total += Number(chip.dataset.extraPrice || 0);
            });
            return total;
        }

        function renderSubtotal() {
            const extraTotal = selectedExtraTotal();
            if (extraPriceInput) extraPriceInput.value = extraTotal;
            if (subtotalEl) subtotalEl.textContent = `Rp ${rupiahFormatter.format((basePrice + extraTotal) * qty)}`;
        }

        function updateConditionalGroups() {
            const suhuGroup = root.querySelector('[data-group="suhu"]');
            const iceFieldset = root.querySelector('#ice-fieldset');
            if (suhuGroup && iceFieldset) {
                const selected = suhuGroup.querySelector('.opt-chip[data-selected="true"]');
                const isDingin = selected && /Dingin/i.test(selected.dataset.value || '');
                iceFieldset.style.display = isDingin ? '' : 'none';
            }

            const telurGroup = root.querySelector('[data-group="extra-telur"]');
            const kematanganFieldset = root.querySelector('#kematangan-fieldset');
            if (telurGroup && kematanganFieldset) {
                const selected = telurGroup.querySelector('.opt-chip[data-selected="true"]');
                kematanganFieldset.style.display = selected ? '' : 'none';
            }

            renderSubtotal();
        }

        root.querySelectorAll('[data-group]').forEach((group) => {
            const isOptional = group.dataset.optional === 'true';
            group.querySelectorAll('.opt-chip').forEach((chip) => {
                chip.addEventListener('click', () => {
                    const wasSelected = chip.getAttribute('data-selected') === 'true';
                    group.querySelectorAll('.opt-chip').forEach((item) => item.setAttribute('data-selected', 'false'));
                    if (!(isOptional && wasSelected)) chip.setAttribute('data-selected', 'true');
                    updateConditionalGroups();
                });
            });
        });

        const MAX_QTY = {{ (int) $maxQty }};
        const qtyDisplay = root.querySelector('#qty-display');
        const qtyInput = root.querySelector('#qty-input');
        const qtyMinus = root.querySelector('#qty-minus');
        const qtyPlus = root.querySelector('#qty-plus');

        function renderQty() {
            if (!qtyDisplay || !qtyInput) return;
            qtyDisplay.textContent = qty;
            qtyInput.value = qty;
            if (qtyMinus) qtyMinus.disabled = qty <= 1;
            if (qtyPlus) qtyPlus.disabled = qty >= MAX_QTY;
            renderSubtotal();
        }

        if (qtyMinus) qtyMinus.addEventListener('click', () => {
            if (qty > 1) {
                qty -= 1;
                renderQty();
            }
        });

        if (qtyPlus) qtyPlus.addEventListener('click', () => {
            if (qty < MAX_QTY) {
                qty += 1;
                renderQty();
            }
        });

        const form = root.querySelector('#detail-form');
        if (form) {
            form.addEventListener('submit', () => {
                renderSubtotal();
                const parts = [];
                root.querySelectorAll('[data-group]').forEach((group) => {
                    if (group.style.display === 'none') return;
                    const label = group.dataset.label;
                    const selected = group.querySelector('.opt-chip[data-selected="true"]');
                    if (label && selected) parts.push(`${label}: ${selected.dataset.value}`);
                });
                const catInput = root.querySelector('#catatan-input');
                if (catInput) catInput.value = parts.join(' | ').slice(0, 255);
            });
        }

        const backBtn = root.querySelector('[data-dm-back]');
        if (backBtn) {
            backBtn.addEventListener('click', (event) => {
                if (window.closeMenuSheet) {
                    event.preventDefault();
                    window.closeMenuSheet();
                }
            });
        }

        updateConditionalGroups();
        renderQty();
    })();
</script>
