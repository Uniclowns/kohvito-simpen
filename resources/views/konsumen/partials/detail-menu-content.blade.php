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

    <main class="mx-auto max-w-[390px] px-4 pb-[140px] sm:px-[18px] md:max-w-4xl lg:max-w-5xl">
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

        <form id="detail-form" method="POST" action="{{ route('konsumen.keranjang.tambah') }}" class="flex flex-col md:grid md:grid-cols-12 md:gap-8 md:items-start w-full gap-[15px]">
            @csrf
            <input type="hidden" name="id_menu" value="{{ $menu->id_menu }}">
            <input type="hidden" name="jumlah" id="qty-input" value="1">
            <input type="hidden" name="catatan" id="catatan-input" value="">
            <input type="hidden" name="harga_tambahan" id="harga-tambahan-input" value="0">

            <section class="flex w-full flex-col md:col-span-5">
                <div class="dm-image mx-auto mt-2 flex h-[180px] w-[180px] items-center justify-center overflow-hidden rounded-[9px] bg-brand-gray-extralight">
                    @if ($imgSrc)
                        <img src="{{ $imgSrc }}" alt="{{ $menu->nama_menu }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-[10px] text-brand-gray">No Image</span>
                    @endif
                </div>

                <div class="dm-enter mt-4 flex w-full flex-col gap-1 text-left" style="animation-delay: 0.24s">
                    <h1 class="text-[24px] leading-[32px] font-bold tracking-[1.2px] text-brand-black">{{ $menu->nama_menu }}</h1>
                    <p class="text-[24px] leading-[32px] font-bold tracking-[1.2px] text-brand-black">
                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                    </p>
                    @if (!empty($menu->komposisi))
                        <p class="mt-0.5 text-[12px] leading-[16px] tracking-[0.6px] text-[rgba(70,0,1,0.72)] font-medium">{{ $menu->komposisi }}</p>
                    @endif
                    @if (!empty($menu->deskripsi))
                        <p class="text-[12px] leading-[16px] tracking-[0.6px] text-brand-gray-dark text-justify">{{ $menu->deskripsi }}</p>
                    @endif
                </div>
            </section>

            <section id="dm-options" class="dm-enter flex w-full flex-col gap-[15px] md:col-span-7" style="animation-delay: 0.32s">
                <div class="flex flex-col gap-[7px]">
                    <p class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Jumlah Pemesanan</p>
                    <div class="flex h-10 w-full items-center justify-between rounded-[9px] bg-[rgba(70,0,1,0.18)] px-2">
                        <button type="button" id="qty-minus" class="qty-btn flex items-center justify-center rounded-[9px] text-brand-dark bg-white" aria-label="Kurangi jumlah">
                            <span class="text-[16px] font-bold leading-5">&minus;</span>
                        </button>
                        <span id="qty-display" class="flex-1 text-center text-[14px] leading-5 tracking-[0.7px] text-black font-bold">1</span>
                        <button type="button" id="qty-plus" class="qty-btn flex items-center justify-center rounded-[9px] text-brand-dark bg-white" aria-label="Tambah jumlah">
                            <span class="text-[16px] font-bold leading-5">&#43;</span>
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
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Suhu Minuman</legend>
                        <div class="flex flex-wrap gap-[15px]">
                            <button type="button" class="opt-chip" data-value="Dingin" data-selected="true">Dingin</button>
                            <button type="button" class="opt-chip" data-value="Panas" data-selected="false">Panas</button>
                        </div>
                    </fieldset>
                @elseif ($isDinginOnly || $isPanasOnly)
                    <fieldset class="flex flex-col gap-[7px]" data-group="suhu" data-label="Suhu">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Suhu Minuman</legend>
                        <div class="flex flex-wrap gap-[15px]">
                            <button type="button" class="opt-chip" data-value="{{ $menu->tipe_minuman }}" data-selected="true">{{ $menu->tipe_minuman }}</button>
                        </div>
                    </fieldset>
                @endif

                @if ($showSugar)
                    <fieldset class="flex flex-col gap-[7px]" data-group="sugar" data-label="Sugar">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Sugar Level</legend>
                        <div class="flex flex-wrap gap-[15px]">
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="No Sugar" data-selected="true">No Sugar</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Less Sugar" data-selected="false">Less Sugar</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Normal" data-selected="false">Normal</button>
                        </div>
                    </fieldset>
                @endif

                @if ($isMinuman)
                    <fieldset class="flex flex-col gap-[7px]" data-group="extra-espresso" data-label="Extra Espresso" data-optional="true">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Extra Espresso</legend>
                        <div class="flex flex-wrap gap-[15px]">
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
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Ice Level</legend>
                        <div class="flex flex-wrap gap-[15px]">
                            <button type="button" class="opt-chip" data-value="Normal" data-selected="true">Normal</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Less Ice" data-selected="false">Less Ice</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="No Ice" data-selected="false">No Ice</button>
                        </div>
                    </fieldset>
                @endif

                @if ($isMakanan)
                    <fieldset class="flex flex-col gap-[7px]" data-group="free-mineral" data-label="Free Mineral">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Free Mineral</legend>
                        <div class="flex flex-wrap gap-[15px]">
                            <button type="button" class="opt-chip" data-value="Biasa" data-selected="true">Biasa</button>
                            <button type="button" class="opt-chip" data-value="Dingin" data-selected="false">Dingin</button>
                        </div>
                    </fieldset>

                    <fieldset class="flex flex-col gap-[7px]" data-group="extra-telur" data-label="Extra Telur" data-optional="true">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Extra Telur</legend>
                        <div class="flex flex-wrap gap-[15px]">
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
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Tingkat Kematangan Telur</legend>
                        <div class="flex flex-wrap gap-[15px]">
                            <button type="button" class="opt-chip" data-value="Matang" data-selected="true">Matang</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Setengah Matang" data-selected="false">Setengah Matang</button>
                        </div>
                    </fieldset>
                @endif

                @if ($isPedas)
                    <fieldset class="flex flex-col gap-[7px]" data-group="chili" data-label="Chili Oil">
                        <legend class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Chili Oil</legend>
                        <div class="flex flex-wrap gap-[15px]">
                            <button type="button" class="opt-chip" data-value="Dicampur ke kuah" data-selected="true">Dicampur ke kuah</button>
                            <button type="button" class="opt-chip whitespace-nowrap" data-value="Dipisah" data-selected="false">Dipisah</button>
                        </div>
                    </fieldset>
                @endif

                <div class="flex flex-col gap-[7px]">
                    <label for="dm-note-input" class="text-[14px] leading-5 font-bold tracking-[0.7px] text-black capitalize">Catatan Tambahan</label>
                    <textarea id="dm-note-input" class="dm-note" placeholder="Tulis catatan untuk barista/koki..."
                              maxlength="200"></textarea>
                </div>
            </section>
        </form>
    </main>

    <div class="dm-fixed-footer fixed inset-x-0 bottom-0 z-30 bg-white shadow-[0_-4px_16px_rgba(0,0,0,0.08)]">
        <div class="mx-auto flex max-w-[390px] flex-col items-stretch justify-between gap-3 px-4 pb-[54px] pt-[18px] min-[360px]:flex-row min-[360px]:items-center min-[360px]:gap-[17px] sm:px-[18px] md:max-w-4xl lg:max-w-5xl">
            <div class="min-w-0">
                <p class="text-[10px] leading-3 tracking-[0.5px] text-brand-gray">Harga Total</p>
                <p id="dm-subtotal" data-base-price="{{ (int) $menu->harga }}" class="text-[14px] leading-5 font-bold tracking-[0.7px] text-brand-black">
                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                </p>
            </div>
            <button type="submit"
                    form="detail-form"
                    class="w-full shrink-0 rounded-[9px] bg-brand-red px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-[0.98] min-[360px]:w-auto"
                    @if ($stock === 0) disabled @endif>
                Tambah Ke Keranjang
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
                const noteEl = root.querySelector('#dm-note-input');
                const userNote = noteEl ? noteEl.value.trim() : '';
                if (userNote) parts.push(`Catatan: ${userNote}`);
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
