{{-- Pembayaran Konsumen
    Route: konsumen.pembayaran (/pembayaran/{noPesanan})
    Controller: BayarController@qris
    Variables: $pesanan
--}}
<x-layouts.konsumen
    :title="'Pembayaran - ' . $pesanan->no_pesanan . ' - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] pb-[124px] lg:pb-0 font-sans text-brand-black kvt-konsumen-mobile-view">
    @php
        $mejaNo = $pesanan->meja->no_meja ?? session('id_meja_no');
        $orderItemCount = (int) $pesanan->detailPesanan->sum('jumlah');
        $canCancel = $pesanan->status_pembayaran !== 'lunas' && $pesanan->status_pesanan === 'menunggu konfirmasi';
        $driver = config('services.bayar.driver', 'mock');
        $hasMidtransQr = $driver === 'midtrans' && ! empty($pesanan->qr_url);
        $qrImageSrc = $hasMidtransQr ? $pesanan->qr_url : asset('images/payment/qris-code.png');
        $qrDownloadHref = $hasMidtransQr
            ? route('konsumen.bayar.qr', $pesanan->no_pesanan)
            : asset('images/payment/qris-code.png');
        $merchantId = $hasMidtransQr
            ? (config('services.midtrans.merchant_id') ?: 'Midtrans QRIS Sandbox')
            : 'NMID: 9360096784567567688';
    @endphp

    <header class="bg-brand-dark px-[18px] pt-[14px] pb-[12px] safe-top">
        <div class="mx-auto flex max-w-[390px] md:max-w-4xl lg:max-w-5xl xl:max-w-6xl items-center justify-between">
            <p class="flex-1 text-[12px] font-bold leading-4 tracking-[0.6px] text-white">Pembayaran</p>
            <div class="flex h-8 w-8 shrink-0 items-center justify-center">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito"
                    class="h-full w-full object-contain">
            </div>
            <p class="flex-1 text-right text-[12px] font-bold leading-4 tracking-[0.6px] text-white">
                TABLE {{ $mejaNo ?? 'XXX' }}
            </p>
        </div>
    </header>

    <main class="mx-auto max-w-[390px] md:max-w-4xl lg:max-w-5xl xl:max-w-6xl px-[18px] pb-6">
        <div class="pt-3 pb-6">
            @if ($canCancel)
                <button type="button"
                    onclick="openAppModal('confirm-batal-pesanan')"
                    class="inline-flex items-center gap-3 text-brand-black active:opacity-70">
                    <svg class="h-5 w-5 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-[20px] font-bold leading-7 tracking-[1px]">Kembali</span>
                </button>
            @else
                <a href="{{ route('konsumen.lacak.detail', $pesanan->no_pesanan) }}"
                    class="inline-flex items-center gap-3 text-brand-black active:opacity-70">
                    <svg class="h-5 w-5 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-[20px] font-bold leading-7 tracking-[1px]">Kembali</span>
                </a>
            @endif
        </div>

        @if ($errors->has('batal'))
            <div class="mb-4 rounded-[9px] border border-red-200 bg-red-50 px-3 py-2 text-[10px] font-bold leading-3 text-red-800">
                {{ $errors->first('batal') }}
            </div>
        @endif

        <div class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2 md:items-start md:gap-8">
            <!-- Left Column: QRIS image and download buttons -->
            <div class="pay-enter flex flex-col items-center gap-[17px] rounded-2xl bg-white p-4 shadow-[2px_4px_4px_rgba(0,0,0,0.18)] sm:p-6">
                <div class="flex w-full items-end justify-between border-b border-brand-gray-light pb-2">
                    <div class="flex h-6 items-end">
                        <span class="text-[24px] font-black leading-6 tracking-[-1px] text-black">QRIS</span>
                    </div>
                    <img src="{{ asset('images/payment/gpn-logo.png') }}" alt="GPN"
                        class="h-[35px] w-[28px] object-contain">
                </div>

                <div class="flex w-full max-w-[229px] flex-col items-center gap-3" data-anim="fade-up">
                    <p class="w-full text-center text-[14px] leading-5 tracking-[0.7px] text-black">
                        {{ $merchantId }}
                    </p>
                    @if ($driver === 'midtrans' && empty($pesanan->qr_url))
                        <div class="flex aspect-square w-full max-w-[213px] items-center justify-center rounded-[12px] border border-dashed border-brand-red/40 bg-white px-3 text-center text-[11px] leading-4 text-brand-red">
                            QR Code belum siap. Segarkan halaman dalam beberapa detik atau hubungi kasir.
                        </div>
                    @else
                        <img src="{{ $qrImageSrc }}" alt="QRIS pembayaran Kohvito {{ $pesanan->no_pesanan }}"
                            class="aspect-square h-auto w-full max-w-[213px] rounded-[6px] bg-white object-contain p-1.5"
                            referrerpolicy="no-referrer">
                    @endif
                </div>

                @if ($hasMidtransQr && ! config('services.midtrans.is_production'))
                    <div class="w-full rounded-[10px] border border-dashed border-brand-red/30 bg-white p-3 text-left">
                        <p class="mb-1.5 text-[10px] font-bold uppercase tracking-[0.8px] text-brand-red">
                            Sandbox helper · QR Image URL
                        </p>
                        <p class="mb-2 text-[10px] leading-3 text-black/70">
                            Tempel URL berikut ke
                            <a href="https://simulator.sandbox.midtrans.com/v2/qris/index" target="_blank"
                               class="underline decoration-dotted">Midtrans QRIS Simulator</a> kolom
                            <strong>QR Code Image URL</strong> → klik <strong>Pay</strong> untuk simulasi settlement.
                        </p>
                        <div class="flex items-stretch gap-2">
                            <code id="kvt-qr-url"
                                  class="block min-w-0 flex-1 overflow-x-auto rounded-[6px] bg-[#F3F3F3] px-2 py-1.5 font-mono text-[10px] leading-3 text-black/80 whitespace-nowrap">{{ $pesanan->qr_url }}</code>
                            <button type="button"
                                    onclick="(function(btn){
                                        const url = document.getElementById('kvt-qr-url').textContent.trim();
                                        const done = ()=>{ const o=btn.textContent; btn.textContent='Tersalin'; setTimeout(()=>btn.textContent=o,1400); };
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(url).then(done, ()=>{ window.prompt('Salin URL ini:', url); });
                                        } else {
                                            window.prompt('Salin URL ini:', url);
                                        }
                                    })(this)"
                                    class="shrink-0 rounded-[6px] bg-brand-red px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.8px] text-white active:opacity-80">
                                Salin
                            </button>
                        </div>
                    </div>
                @endif

                <div class="w-full flex flex-col gap-2 mt-2">
                    @if ($driver === 'midtrans' && empty($pesanan->qr_url))
                        <button type="button" disabled
                            class="flex h-8 w-full cursor-not-allowed items-center justify-center rounded-[9px] bg-[#E5E5E5] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-brand-red/60 shadow-[2px_4px_2px_rgba(0,0,0,0.15)]">
                            QR Belum Siap
                        </button>
                    @else
                        <a href="{{ $qrDownloadHref }}"
                            @if (! $hasMidtransQr) download="qris-kohvito-{{ $pesanan->no_pesanan }}.png" @endif
                            class="flex h-8 w-full items-center justify-center rounded-[9px] bg-[#CCCCCC] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-brand-red shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                            Unduh QR Code
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Column: Merchant info, pay instructions, check status CTA (5 columns) -->
            <div class="flex flex-col gap-4 rounded-2xl bg-white p-4 shadow-[2px_4px_4px_rgba(0,0,0,0.18)] sm:p-6">
                <div class="w-full text-black">
                    <h3 class="text-[16px] font-bold text-brand-dark border-b border-brand-gray-light pb-2 mb-3">Langkah-Langkah Pembayaran</h3>
                    <div class="mt-[10px] text-[13px] leading-5 tracking-[0.6px] flex flex-col gap-2">
                        <p><strong>1. Scan/Unduh QRIS:</strong> Gunakan aplikasi dompet digital (OVO, GoPay, ShopeePay) atau M-Banking Anda.</p>
                        <p><strong>2. Lakukan pembayaran:</strong> Pastikan nama merchant sesuai dan nominal tagihan terisi otomatis.</p>
                        <p><strong>3. Pembayaran diverifikasi:</strong> Sistem akan mendeteksi status pembayaran secara otomatis.</p>
                        <p><strong>4. Pesanan diproses:</strong> Pesanan Anda langsung diterima oleh kasir &amp; barista/koki.</p>
                    </div>
                </div>

                <div class="w-full mt-4">
                    <a href="{{ route('konsumen.lacak.detail', $pesanan->no_pesanan) }}"
                        class="flex h-10 w-full items-center justify-center rounded-[9px] bg-brand-red px-3 py-2 text-[14px] font-bold leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-[0.98]">
                        Cek Status Pembayaran
                    </a>
                </div>
            </div>
        </div>
    </main>

    <x-konsumen-bottom-nav
        active="keranjang"
        :mejaNo="$mejaNo"
        :cartCount="$orderItemCount"
        :lacakHref="route('konsumen.lacak.detail', $pesanan->no_pesanan)" />

    @if ($canCancel)
        <x-konsumen-confirm-modal
            id="confirm-batal-pesanan"
            title="Apakah Anda Yakin Ingin Membatalkan Pesanan?"
            subtitle="Pesanan Anda akan dibatalkan"
            confirmLabel="Ya, Batal"
            cancelLabel="Kembali"
            variant="danger"
            :action="route('konsumen.pesanan.batal', $pesanan->no_pesanan)"
            method="DELETE" />
    @endif

    {{-- Popup hasil pembayaran — Figma 1432-22970 (sukses) & 1518-19333 (gagal) --}}
    <x-popup-success
        id="popup-bayar-sukses"
        heading="Berhasil Melakukan Pembayaran"
        description="Mohon Bersabar Yaaa, Pesanan Anda Sedang Kami Proses"
        gif="success.gif"
        buttonLabel="Tutup" />

    <x-popup-success
        id="popup-bayar-gagal"
        heading="Pembayaran Gagal Dilakukan"
        description="Sistem sedang bermasalah, silahkan coba kembali"
        gif="failed.gif"
        illustration="error.svg"
        buttonLabel="Kembali" />

    <script>
        (function () {
            const lacakUrl = @json(route('konsumen.lacak.detail', $pesanan->no_pesanan));
            const params = new URLSearchParams(window.location.search);

            // Popup gagal bila gateway/simulator menandai pembayaran gagal.
            if (params.get('result') === 'gagal') {
                setTimeout(() => window.openAppModal && window.openAppModal('popup-bayar-gagal'), 200);
            }

            function showSuccessThenTrack() {
                const modal = document.getElementById('popup-bayar-sukses');
                if (!modal) { window.location.replace(lacakUrl); return; }
                window.openAppModal && window.openAppModal('popup-bayar-sukses');
                // Setelah popup ditutup (Tutup / X / klik di luar), arahkan ke Lacak Pesanan.
                modal.querySelectorAll('button').forEach(btn =>
                    btn.addEventListener('click', () => window.location.replace(lacakUrl)));
                modal.addEventListener('click', e => { if (e.target === modal) window.location.replace(lacakUrl); });
            }

            @if ($pesanan->status_pembayaran !== 'lunas')
            const syncUrl = @json(route('konsumen.bayar.sync', $pesanan->no_pesanan));
            const intervalMs = 4000;
            const maxAttempts = 60; // ~4 menit total
            let attempts = 0;

            async function pollOnce() {
                attempts++;
                try {
                    const r = await fetch(syncUrl, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                    if (!r.ok) return false;
                    const data = await r.json();
                    if (data && data.status_pembayaran === 'lunas') { showSuccessThenTrack(); return true; }
                    if (data && data.status_pembayaran === 'gagal') {
                        window.openAppModal && window.openAppModal('popup-bayar-gagal');
                        return true;
                    }
                } catch (e) { /* network blip — coba lagi tick berikutnya */ }
                return false;
            }

            async function loop() {
                if (document.hidden) return; // hemat saat tab di belakang
                const done = await pollOnce();
                if (done || attempts >= maxAttempts) clearInterval(timer);
            }

            setTimeout(pollOnce, 1500);
            const timer = setInterval(loop, intervalMs);
            document.addEventListener('visibilitychange', () => { if (!document.hidden) pollOnce(); });
            @else
            // Sudah lunas saat halaman dibuka — langsung tampilkan popup sukses.
            setTimeout(showSuccessThenTrack, 300);
            @endif
        })();
    </script>
</x-layouts.konsumen>
