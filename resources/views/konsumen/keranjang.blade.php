{{-- Keranjang Konsumen
    Route: konsumen.keranjang (/keranjang)
    Controller: KeranjangKonsumenController@index
    Variables: $keranjang, $totalHarga
--}}
<x-layouts.konsumen
    :title="'Keranjang Pesanan - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] pb-[124px] lg:pb-0 font-sans text-brand-black kvt-konsumen-mobile-view">
    @php
        $cartCount = array_sum(array_column($keranjang, 'jumlah'));
        $hasOrder = session('no_pesanan_baru');
        $mejaNo = session('id_meja_no');
        $ppnAmount = (int) round($totalHarga * 0.11);
        $grandTotal = $totalHarga + $ppnAmount;
    @endphp

    <header class="kvt-slide-down bg-brand-dark px-[18px] pt-[14px] pb-[12px] safe-top">
        <div class="mx-auto flex max-w-[390px] md:max-w-4xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl items-center justify-between">
            <p class="flex-1 text-[12px] font-bold leading-4 tracking-[0.6px] text-white">Keranjang Pesanan</p>
            <div class="flex h-8 w-8 shrink-0 items-center justify-center">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito"
                    class="h-full w-full object-contain">
            </div>
            <p class="flex-1 text-right text-[12px] font-bold leading-4 tracking-[0.6px] text-white">
                TABLE {{ $mejaNo ?? 'XXX' }}
            </p>
        </div>
    </header>

    <main class="mx-auto max-w-[390px] md:max-w-4xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl px-[18px] pb-5">
        <div class="pt-3 pb-[9px]">
            @if ($mejaNo)
                <a href="{{ route('konsumen.beranda', $mejaNo) }}"
                    class="inline-flex items-center gap-3 text-brand-black active:opacity-70">
                    <svg class="h-5 w-5 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-[20px] font-bold leading-7 tracking-[1px]">Kembali</span>
                </a>
            @endif
        </div>

        @if (session('success'))
            <div
                class="kvt-card mb-3 rounded-[9px] border border-green-200 bg-green-50 px-3 py-2 text-[10px] font-bold leading-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="kvt-card mb-3 rounded-[9px] border border-red-200 bg-red-50 px-3 py-2 text-[10px] font-bold leading-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->has('id_meja') || $errors->has('order') || $errors->has('item'))
            <div
                class="kvt-card mb-3 rounded-[9px] border border-red-200 bg-red-50 px-3 py-2 text-[10px] font-bold leading-3 text-red-800">
                {{ $errors->first('id_meja') ?: ($errors->first('order') ?: $errors->first('item')) }}
            </div>
        @endif

        @if (empty($keranjang) && $hasOrder)
            <section class="kvt-card rounded-[9px] bg-white p-5 text-center shadow-[2px_4px_4px_rgba(0,0,0,0.25)]">
                <p class="text-[20px] font-bold leading-7 tracking-[1px] text-brand-dark">Pesanan Berhasil Dibuat</p>
                <p class="mt-2 text-[12px] leading-4 tracking-[0.6px] text-brand-gray">Nomor Transaksi</p>
                <p
                    class="mt-2 rounded-[9px] bg-[rgba(104,31,31,0.12)] px-3 py-2 text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">
                    {{ $hasOrder }}
                </p>
                <a href="{{ route('konsumen.pembayaran', $hasOrder) }}"
                    class="mt-5 inline-flex w-full items-center justify-center rounded-[9px] bg-brand-dark px-3 py-2 text-[14px] font-bold leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                    Lanjutkan Pembayaran
                </a>
            </section>
        @elseif (empty($keranjang))
            {{-- Empty state — Figma 1509-19168 "Keranjang Kosong" --}}
            <div class="flex min-h-[58vh] flex-col items-center justify-center text-center px-6">
                <img src="{{ asset('images/illustration/empty-cart.svg') }}" alt="" class="w-[150px] h-[150px] mb-5"
                    data-anim="fade-up">
                <p class="text-[20px] font-bold leading-7 tracking-[1px] text-[#CCCCCC]">Keranjang Kosong</p>
                <p class="mt-2 text-[14px] leading-5 tracking-[0.7px] text-[#CCCCCC] max-w-[240px]">Silahkan Melakukan Pemesanan di Halaman Menu</p>
            </div>
        @else
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-12 md:items-start md:gap-8">
                <!-- Left Column: Cart items list (7 columns) -->
                <div class="md:col-span-7 flex flex-col gap-3">
                    <section class="kvt-card rounded-[9px] bg-white p-[10px]" style="animation-delay: 0.08s">
                        <div class="flex flex-col gap-[10px]" data-anim="stagger">
                            @foreach ($keranjang as $cartKey => $item)
                                @php
                                    $menuId = $item['id_menu'] ?? $cartKey;
                                    $menuModel = \App\Models\Menu::find($menuId);
                                    $imgSrc = null;
                                    if ($menuModel && $menuModel->gambar_menu) {
                                        $imgType = $menuModel->jenis_menu === 'Makanan' ? 'food' : 'drink';
                                        $imgSrc = str_starts_with($menuModel->gambar_menu, 'http')
                                            ? $menuModel->gambar_menu
                                            : asset("images/{$imgType}/{$menuModel->gambar_menu}");
                                    }

                                    $rawNotes = (string) ($item['catatan'] ?? '');
                                    preg_match('/Suhu:\s*([^|]+)/i', $rawNotes, $variantMatch);
                                    $variantLabel = trim($variantMatch[1] ?? '');
                                    $noteSummary = collect(explode('|', $rawNotes))
                                        ->map(fn ($note) => trim($note))
                                        ->filter()
                                        ->reject(fn ($note) => str_starts_with(strtolower($note), 'suhu:'))
                                        ->map(fn ($note) => trim(preg_replace('/^[^:]+:\s*/', '', $note)))
                                        ->filter()
                                        ->implode(', ');
                                @endphp

                                <article
                                    class="cart-item rounded-[9px] bg-white p-[10px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] transition-all duration-300"
                                    data-anim-item
                                    data-cart-item
                                    data-cart-key="{{ $cartKey }}">
                                    <div class="flex items-center gap-[5px] px-3">
                                        @if ($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $item['nama_menu'] }}"
                                                class="h-[54px] w-[54px] shrink-0 rounded-[9px] object-cover">
                                        @else
                                            <div
                                                class="flex h-[54px] w-[54px] shrink-0 items-center justify-center rounded-[9px] bg-brand-gray-extralight">
                                                <span class="text-[8px] text-brand-gray">No Image</span>
                                            </div>
                                        @endif

                                        <div class="min-w-0 flex-1 py-[5px]">
                                            <div class="flex min-h-[14px] items-center gap-0 text-[12px] font-bold leading-4 tracking-[0.6px] text-brand-black">
                                                <h2 class="min-w-0 truncate capitalize">
                                                    <span data-item-qty-name>{{ $item['jumlah'] }}</span> {{ $item['nama_menu'] }}
                                                </h2>
                                                @if ($variantLabel)
                                                    <span class="shrink-0 italic text-brand-dark">({{ $variantLabel }})</span>
                                                @endif
                                            </div>
                                            <p class="min-h-[14px] truncate text-[10px] leading-3 tracking-[0.5px] text-brand-gray">
                                                {{ $noteSummary ?: 'Tidak ada catatan tambahan' }}
                                            </p>
                                        </div>

                                        <p class="shrink-0 text-right text-[12px] font-bold leading-4 tracking-[0.6px] text-brand-black"
                                           data-item-subtotal>
                                            {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="mt-[10px] flex items-start gap-2">
                                        <form method="POST" action="{{ route('konsumen.keranjang.update') }}"
                                            class="min-w-0 flex-1"
                                            data-cart-form
                                            data-action-type="remove">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="cart_key" value="{{ $cartKey }}">
                                            <input type="hidden" name="id_menu" value="{{ $menuId }}">
                                            <input type="hidden" name="jumlah" value="0">
                                            <button type="submit"
                                                class="h-8 w-full rounded-[9px] bg-state-red px-1 sm:px-3 py-1.5 text-[13px] sm:text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] disabled:opacity-60">
                                                Hapus
                                            </button>
                                        </form>

                                        <a href="{{ route('konsumen.menu.detail', $menuId) }}"
                                            class="flex h-8 min-w-0 flex-1 items-center justify-center rounded-[9px] bg-brand-red px-1 sm:px-3 py-1.5 text-[13px] sm:text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                                            Edit
                                        </a>

                                        <div class="flex h-8 min-w-0 flex-1 items-center rounded-[9px] bg-[rgba(70,0,1,0.25)]">
                                            <form method="POST" action="{{ route('konsumen.keranjang.update') }}"
                                                  data-cart-form
                                                  data-action-type="minus">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="cart_key" value="{{ $cartKey }}">
                                                <input type="hidden" name="id_menu" value="{{ $menuId }}">
                                                <input type="hidden" name="jumlah" data-jumlah-input value="{{ $item['jumlah'] - 1 }}">
                                                <button type="submit"
                                                    class="flex h-8 w-6 sm:w-8 items-center justify-center rounded-l-[9px] text-[13px] sm:text-[14px] font-bold text-brand-dark disabled:opacity-50">&minus;</button>
                                            </form>
                                            <span class="flex-1 text-center text-[13px] sm:text-[14px] leading-5 tracking-[0.7px] text-black"
                                                  data-item-qty>
                                                {{ $item['jumlah'] }}
                                            </span>
                                            <form method="POST" action="{{ route('konsumen.keranjang.update') }}"
                                                  data-cart-form
                                                  data-action-type="plus">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="cart_key" value="{{ $cartKey }}">
                                                <input type="hidden" name="id_menu" value="{{ $menuId }}">
                                                <input type="hidden" name="jumlah" data-jumlah-input value="{{ $item['jumlah'] + 1 }}">
                                                <button type="submit"
                                                    class="flex h-8 w-6 sm:w-8 items-center justify-center rounded-r-[9px] text-[13px] sm:text-[14px] font-bold text-brand-dark disabled:opacity-50">&#43;</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>

                <!-- Right Column: Order totals & Checkout info card (5 columns) -->
                <div class="md:col-span-5 md:sticky md:top-6 flex flex-col gap-4">
                    <!-- Totals Card -->
                    <section class="kvt-card rounded-[9px] bg-white p-5 shadow-[2px_4px_4px_rgba(0,0,0,0.25)] flex flex-col gap-3"
                             data-cart-totals>
                        <h3 class="text-[16px] font-bold text-brand-dark border-b border-brand-gray-light pb-2">Ringkasan Pesanan</h3>
                        <div class="flex flex-col gap-[5px] text-[12px] font-bold leading-4 tracking-[0.6px]">
                            <div class="flex items-center justify-between gap-[14px]">
                                <span class="text-brand-dark">SubTotal Pemesanan</span>
                                <span class="text-brand-black" data-total-subtotal>{{ number_format($totalHarga, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-[14px]">
                                <span class="text-brand-dark">Ppn 11%</span>
                                <span class="text-brand-black" data-total-ppn>{{ number_format($ppnAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-brand-gray-light pt-[5px]">
                                <div class="flex items-center justify-between gap-[14px]">
                                    <span class="text-brand-dark">Total Pemesanan</span>
                                    <span class="text-brand-black" data-total-grand>{{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Checkout Input Section -->
                    <section class="kvt-card rounded-[9px] bg-white p-5 shadow-[2px_4px_4px_rgba(0,0,0,0.25)] flex flex-col gap-4">
                        <h3 class="text-[16px] font-bold text-brand-dark border-b border-brand-gray-light pb-2">Informasi Pemesan</h3>
                        <div class="flex flex-col gap-4">
                            <div>
                                <label for="nama_konsumen"
                                    class="text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">Nama Pesanan</label>
                                <input id="nama_konsumen"
                                    name="nama_konsumen"
                                    form="checkout-form"
                                    type="text"
                                    maxlength="255"
                                    required
                                    value="{{ old('nama_konsumen') }}"
                                    placeholder="Masukkan Nama Pesanan (Cth: Yaya)"
                                    class="mt-[5px] w-full rounded-[9px] border-none bg-[rgba(104,31,31,0.12)] p-[10px] text-[14px] leading-5 tracking-[0.7px] text-brand-black placeholder:text-brand-gray focus:outline-none focus:ring-2 focus:ring-brand-red/40">
                                <p data-name-required
                                    class="mt-[3px] text-[10px] leading-3 tracking-[0.5px] text-state-red">
                                    Nama Pesanan Wajib Di isi
                                </p>
                                @error('nama_konsumen')
                                    <p class="mt-[3px] text-[10px] leading-3 tracking-[0.5px] text-state-red">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="catatan_pesanan"
                                    class="text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">Notes Pesanan</label>
                                <textarea id="catatan_pesanan" name="catatan_pesanan" form="checkout-form" rows="4" maxlength="500"
                                    placeholder="Masukkan Notes Pesanan (opsional)"
                                    class="mt-[5px] h-[78px] w-full resize-none rounded-[9px] border-none bg-[rgba(104,31,31,0.12)] p-[10px] text-[14px] leading-5 tracking-[0.7px] text-brand-black placeholder:text-brand-gray focus:outline-none focus:ring-2 focus:ring-brand-red/40">{{ old('catatan_pesanan') }}</textarea>
                                @error('catatan_pesanan')
                                    <p class="mt-[3px] text-[10px] leading-3 tracking-[0.5px] text-state-red">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="button"
                                data-open-checkout-confirm
                                class="flex h-8 w-full items-center justify-center rounded-[9px] bg-brand-red px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                                Pesan
                            </button>
                        </div>
                    </section>
                </div>
            </div>

            <form id="checkout-form" method="POST" action="{{ route('konsumen.keranjang.pesan') }}"
                data-checkout-form>
                @csrf
            </form>

            <x-konsumen-confirm-modal
                id="confirm-pesan-konsumen"
                title="Apakah Anda Yakin Ingin Memesan Menu?"
                subtitle="Pastikan menu yang anda pesan telah sesuai"
                confirmLabel="Ya, Pesan"
                cancelLabel="Batal"
                form="checkout-form" />
        @endif
    </main>

    <x-konsumen-bottom-nav active="keranjang" :mejaNo="$mejaNo" :cartCount="$cartCount" />

    <script>
        (function () {
            const form = document.querySelector('[data-checkout-form]');
            const openButton = document.querySelector('[data-open-checkout-confirm]');
            const nameInput = document.getElementById('nama_konsumen');
            const requiredHint = document.querySelector('[data-name-required]');

            function syncNameHint() {
                if (!requiredHint || !nameInput) return;
                requiredHint.classList.toggle('hidden', nameInput.value.trim().length > 0);
            }

            if (nameInput) {
                nameInput.addEventListener('input', syncNameHint);
                syncNameHint();
            }

            if (openButton && form) {
                openButton.addEventListener('click', () => {
                    syncNameHint();
                    if (!form.reportValidity()) return;
                    window.openAppModal && window.openAppModal('confirm-pesan-konsumen');
                });
            }

            if (form) {
                form.addEventListener('submit', () => {
                    const submitBtn = document.querySelector('[data-open-checkout-confirm]');
                    if (!submitBtn) return;
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    submitBtn.textContent = 'Sedang Mengirim Pesanan...';
                });
            }
        })();
    </script>

    {{-- ──────────────────────────────────────────────────────────────────
         AJAX Cart Updater (Progressive Enhancement)
         Intercepts +/- and Hapus form submissions. Sends fetch() with
         Accept: application/json — controller returns JSON state, JS
         updates DOM in-place (no full page reload).
         Non-JS users still get the classic redirect fallback.
    ────────────────────────────────────────────────────────────────── --}}
    <script>
        (function () {
            const cartListSection = document.querySelector('[data-cart-item]')?.closest('section');
            if (!cartListSection) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const cartBadgeEls = document.querySelectorAll('[data-cart-count]'); // bottom nav badge if exists
            let inflightCount = 0;

            // ── Helpers ────────────────────────────────────────────────────
            function setBusy(form, busy) {
                form.querySelectorAll('button').forEach(b => b.disabled = busy);
                if (busy) form.classList.add('opacity-70');
                else form.classList.remove('opacity-70');
            }

            function updateItemRow(cartKey, jumlah, subtotalFmt) {
                const article = document.querySelector(`[data-cart-key="${cartKey}"]`);
                if (!article) return;

                // Qty in title ("2 Angguro") + qty display in stepper
                article.querySelectorAll('[data-item-qty-name], [data-item-qty]').forEach(el => {
                    el.textContent = jumlah;
                });
                const subtotalEl = article.querySelector('[data-item-subtotal]');
                if (subtotalEl) subtotalEl.textContent = subtotalFmt;

                // Sync hidden inputs so next click reflects new state
                const minusInput = article.querySelector('[data-action-type="minus"] [data-jumlah-input]');
                const plusInput = article.querySelector('[data-action-type="plus"] [data-jumlah-input]');
                if (minusInput) minusInput.value = String(jumlah - 1);
                if (plusInput) plusInput.value = String(jumlah + 1);

                // Disable minus when qty would go to 0 via stepper (1 → 0 is delete via Hapus button)
                const minusBtn = article.querySelector('[data-action-type="minus"] button');
                if (minusBtn) minusBtn.disabled = jumlah <= 1;
            }

            function removeItemRow(cartKey) {
                const article = document.querySelector(`[data-cart-key="${cartKey}"]`);
                if (!article) return;
                article.style.opacity = '0';
                article.style.transform = 'translateX(-12px)';
                setTimeout(() => article.remove(), 280);
            }

            function updateTotals(data) {
                const subEl = document.querySelector('[data-total-subtotal]');
                const ppnEl = document.querySelector('[data-total-ppn]');
                const grandEl = document.querySelector('[data-total-grand]');
                if (subEl) subEl.textContent = data.totalHargaFmt;
                if (ppnEl) ppnEl.textContent = data.ppnFmt;
                if (grandEl) grandEl.textContent = data.grandTotalFmt;

                // Sync nav cart badge if present
                cartBadgeEls.forEach(el => {
                    el.textContent = data.cartCount;
                    el.classList.toggle('hidden', data.cartCount === 0);
                });
            }

            // ── Event delegation: tangkap submit semua form keranjang ──────
            cartListSection.addEventListener('submit', async (event) => {
                const form = event.target.closest('[data-cart-form]');
                if (!form) return;
                event.preventDefault();
                if (form.dataset.busy === '1') return; // anti double-click
                form.dataset.busy = '1';
                inflightCount++;
                setBusy(form, true);

                try {
                    const fd = new FormData(form);
                    // Lavavel expects _method=PUT for method spoofing; FormData captures hidden input automatically
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        credentials: 'same-origin',
                        body: fd,
                    });

                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    if (!data.ok) throw new Error(data.message || 'Update gagal');

                    if (data.removed) {
                        removeItemRow(data.cartKey);
                    } else {
                        updateItemRow(data.cartKey, data.jumlah, data.subtotalFmt);
                    }
                    updateTotals(data);

                    // Cart kosong → reload supaya tampil empty state Blade yang konsisten
                    if (data.cartIsEmpty) {
                        setTimeout(() => window.location.reload(), 320);
                    }
                } catch (err) {
                    console.error('[cart-ajax]', err);
                    // Fallback graceful: submit form klasik
                    form.dataset.busy = '';
                    form.submit();
                    return;
                } finally {
                    inflightCount--;
                    if (inflightCount === 0) {
                        // small delay supaya animasi keluar tidak ke-cut
                        setTimeout(() => {
                            form.dataset.busy = '';
                            setBusy(form, false);
                        }, 80);
                    } else {
                        form.dataset.busy = '';
                        setBusy(form, false);
                    }
                }
            });
        })();
    </script>
</x-layouts.konsumen>
