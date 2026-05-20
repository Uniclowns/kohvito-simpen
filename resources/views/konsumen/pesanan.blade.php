<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tracking Pesanan — {{ $pesanan->no_pesanan }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.5; }
            50% { transform: scale(1.15); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.5; }
        }
        .ring-pulse {
            position: relative;
        }
        .ring-pulse::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid var(--color-brand-red, #681F1F);
            animation: pulse-ring 2s infinite ease-in-out;
            top: 0;
            left: 0;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-brand-light min-h-screen text-brand-black font-sans pb-32">

    <!-- Header Premium -->
    <header class="bg-brand-dark sticky top-0 z-20 shadow-md border-b border-brand-red/10">
        <div class="max-w-md mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if (session('id_meja_no'))
                    <a href="{{ route('konsumen.beranda', session('id_meja_no')) }}"
                       class="bg-brand-red hover:bg-brand-red/80 text-white rounded-full p-2.5 transition-all shadow-sm flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                @endif
                <div>
                    <h1 class="text-sm font-bold text-brand-white leading-none">Tracking Pesanan</h1>
                    <p class="text-[10px] text-brand-red-muted mt-0.5 font-medium">Real-time Order Status</p>
                </div>
            </div>
            <div class="bg-brand-red px-3 py-1.5 rounded-full border border-brand-red-muted/20 shadow-sm flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                <span class="text-[11px] font-bold text-brand-white">Meja {{ $pesanan->meja->no_meja ?? '-' }}</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto px-4 py-6">

        <!-- Banner Pembayaran Sukses (Toast Melayang Tersembunyi) -->
        <div id="payment-toast" class="hidden mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-2xl items-center gap-2.5 shadow-sm transform transition-all duration-500 scale-95 opacity-0">
            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <span class="font-extrabold block text-[13px]">Pembayaran Berhasil!</span>
                <span class="text-[10px] text-emerald-700/90 font-medium">Terima kasih, pembayaran Anda telah dikonfirmasi oleh sistem.</span>
            </div>
        </div>

        <!-- Card Ringkasan Transaksi -->
        <div class="bg-white rounded-[32px] border border-brand-gray-extralight p-6 shadow-sm mb-6 relative overflow-hidden">
            <!-- Dekorasi Pattern Halus -->
            <div class="absolute top-0 right-0 w-24 h-24 bg-brand-light rounded-full -mr-8 -mt-8 opacity-50 z-0"></div>
            
            <div class="relative z-10">
                <span class="text-[10px] uppercase font-black tracking-widest text-brand-gray/80">Kode Transaksi</span>
                <h2 class="text-base font-mono font-black text-brand-red tracking-tight select-all mb-4">{{ $pesanan->no_pesanan }}</h2>
                
                <div class="grid grid-cols-2 gap-y-4 gap-x-2 pt-4 border-t border-brand-gray-extralight text-xs font-semibold text-brand-black">
                    <div>
                        <span class="text-[10px] text-brand-gray block mb-0.5">Pemesan</span>
                        <span class="font-extrabold">{{ $pesanan->nama_konsumen }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-brand-gray block mb-0.5">Nomor Meja</span>
                        <span class="font-extrabold">Meja {{ $pesanan->meja->no_meja ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-brand-gray block mb-0.5">Status Bayar</span>
                        <span id="badge-bayar" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $pesanan->status_pembayaran === 'lunas' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-brand-red border border-brand-red/20 animate-pulse' }}">
                            @if($pesanan->status_pembayaran === 'lunas')
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Lunas
                            @else
                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full animate-ping"></span> Belum Lunas
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] text-brand-gray block mb-0.5">Waktu Pesan</span>
                        <span class="font-medium text-brand-gray-dark text-[11px]">{{ $pesanan->created_at ? $pesanan->created_at->format('H:i') : now()->format('H:i') }} WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- STAGE STATUS TIMELINE (STEPPER) -->
        <div class="bg-white rounded-[32px] p-6 border border-brand-gray-extralight shadow-sm mb-6">
            <h3 class="text-xs font-black tracking-widest text-brand-gray-dark uppercase mb-6 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-brand-red rounded-full"></span> Progress Pesanan
            </h3>
            
            <div class="relative pl-8 space-y-8">
                <!-- Connecting Line Background -->
                <div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-brand-gray-extralight" id="timeline-line"></div>
                <!-- Connecting Line Progress Active -->
                <div class="absolute left-[15px] top-2 w-0.5 bg-emerald-500 transition-all duration-700 ease-in-out" id="timeline-progress" style="height: 0%;"></div>

                <!-- Step 1: Menunggu Konfirmasi -->
                <div class="relative flex gap-4 transition-all duration-500" id="step-1">
                    <!-- Circle -->
                    <div class="absolute -left-[27px] flex items-center justify-center w-8 h-8 rounded-full border-2 transition-all duration-500 z-10 text-white shadow-sm" id="step-circle-1">
                        <!-- Icon JS will load or Blade loads initial -->
                    </div>
                    <div>
                        <h4 class="text-xs font-black transition-colors duration-500" id="step-title-1">Menunggu Konfirmasi</h4>
                        <p class="text-[10px] text-brand-gray mt-1 leading-normal font-medium" id="step-desc-1">Pesanan Anda telah diterima oleh sistem dan sedang dikonfirmasi oleh kasir.</p>
                    </div>
                </div>

                <!-- Step 2: Diproses -->
                <div class="relative flex gap-4 transition-all duration-500" id="step-2">
                    <!-- Circle -->
                    <div class="absolute -left-[27px] flex items-center justify-center w-8 h-8 rounded-full border-2 transition-all duration-500 z-10 text-brand-gray border-brand-gray-light bg-brand-gray-extralight" id="step-circle-2">
                        <!-- Icon JS will load or Blade loads initial -->
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-brand-gray transition-colors duration-500" id="step-title-2">Sedang Disiapkan</h4>
                        <p class="text-[10px] text-brand-gray mt-1 leading-normal font-medium" id="step-desc-2">Chef dan Barista kami sedang meracik hidangan lezat pesanan Anda.</p>
                    </div>
                </div>

                <!-- Step 3: Selesai -->
                <div class="relative flex gap-4 transition-all duration-500" id="step-3">
                    <!-- Circle -->
                    <div class="absolute -left-[27px] flex items-center justify-center w-8 h-8 rounded-full border-2 transition-all duration-500 z-10 text-brand-gray border-brand-gray-light bg-brand-gray-extralight" id="step-circle-3">
                        <!-- Icon JS will load or Blade loads initial -->
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-brand-gray transition-colors duration-500" id="step-title-3">Siap Disajikan</h4>
                        <p class="text-[10px] text-brand-gray mt-1 leading-normal font-medium" id="step-desc-3">Pesanan Anda telah diantarkan ke meja. Selamat menikmati hidangan kami!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Detail Pesanan (Rincian Item) -->
        <div class="bg-white rounded-[32px] border border-brand-gray-extralight p-6 shadow-sm overflow-hidden mb-6">
            <h3 class="text-xs font-black tracking-widest text-brand-gray-dark uppercase mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-brand-red rounded-full"></span> Rincian Item Pesanan
            </h3>

            <div class="space-y-4">
                @foreach ($pesanan->detailPesanan as $item)
                <div class="flex items-start justify-between gap-4 py-3 {{ !$loop->first ? 'border-t border-dashed border-brand-gray-extralight' : '' }}">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-brand-black leading-tight">{{ $item->menu->nama_menu ?? '-' }}</h4>
                        @if($item->catatan)
                            <div class="mt-1 flex items-start gap-1 text-[10px] text-brand-red italic font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>"{{ $item->catatan }}"</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-6">
                        <span class="text-xs font-black text-brand-gray-dark">x{{ $item->jumlah }}</span>
                        <span class="text-xs font-extrabold text-brand-black min-w-[70px] text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Total Biaya Section -->
            <div class="mt-4 pt-4 border-t border-brand-gray-light flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-brand-gray font-black uppercase tracking-wider block">Total Pembayaran</span>
                    <span class="text-[10px] text-brand-gray-dark font-medium">(Termasuk Pajak & Service)</span>
                </div>
                <span class="text-base font-black text-brand-red">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CALL TO ACTION (CTA) BAR -->
        <div class="space-y-3">
            <!-- CTA 1: Bayar Sekarang (Xendit) -->
            @if ($pesanan->status_pembayaran !== 'lunas')
                <div id="payment-cta" class="transition-all duration-500">
                    <form method="POST" action="{{ route('konsumen.bayar') }}">
                        @csrf
                        <input type="hidden" name="no_pesanan" value="{{ $pesanan->no_pesanan }}">
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-brand-red to-brand-dark hover:from-brand-dark hover:to-brand-red text-white font-black py-4 px-6 rounded-2xl transition-all shadow-md hover:shadow-lg uppercase tracking-wider transform active:scale-95 text-xs flex items-center justify-center gap-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Bayar Sekarang &bull; Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </button>
                    </form>
                    <p class="text-[9px] text-brand-gray text-center font-medium mt-2 px-4 leading-normal">
                        *Anda akan dialihkan ke gerbang pembayaran aman Xendit (Qris, E-Wallet, Virtual Account, Retail Outlet).
                    </p>
                </div>
            @endif

            <!-- CTA 2: Unduh Kuitansi Digital -->
            <a id="btn-kuitansi"
               href="{{ route('konsumen.pesanan.kuitansi', $pesanan->no_pesanan) }}"
               class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-6 rounded-2xl transition-all shadow-md hover:shadow-lg uppercase tracking-wider transform active:scale-95 text-xs cursor-pointer {{ $pesanan->status_pembayaran !== 'lunas' ? 'hidden' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Unduh Kuitansi Digital
            </a>
        </div>

        <!-- Catatan Tambahan Layanan -->
        <div class="mt-8 text-center text-[10px] text-brand-gray font-semibold max-w-xs mx-auto leading-relaxed">
            Butuh bantuan lain? Silakan hubungi Waiter kami di area bar.
            <br>
            <span class="text-brand-red mt-1 block">Kohvito Café &copy; {{ date('Y') }}</span>
        </div>

    </main>

    <!-- JS POLLING & REAL-TIME INTERACTION -->
    <script>
    (function () {
        const statusUrl = "{{ route('konsumen.pesanan.status', $pesanan->no_pesanan) }}";
        let isPaid = {{ $pesanan->status_pembayaran === 'lunas' ? 'true' : 'false' }};
        let lastStatus = "{{ $pesanan->status_pesanan }}";

        // SVG Templates
        const iconCheck = `<svg class="w-4 h-4 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
        const iconClock = `<svg class="w-4.5 h-4.5 z-10 animate-spin" style="animation-duration: 4s;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        const iconCooking = `<svg class="w-4 h-4 z-10 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" /></svg>`;
        const iconServe = `<svg class="w-4 h-4 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>`;

        function updateStepUI(stepNumber, state, iconHtml) {
            const circle = document.getElementById(`step-circle-${stepNumber}`);
            const title = document.getElementById(`step-title-${stepNumber}`);
            
            circle.innerHTML = iconHtml;
            circle.className = "absolute -left-[27px] flex items-center justify-center w-8 h-8 rounded-full border-2 transition-all duration-500 z-10 shadow-sm ";
            
            if (state === 'done') {
                circle.classList.add("bg-green-500", "border-green-500", "text-white");
                title.className = "text-xs font-black text-brand-black transition-colors duration-500";
            } else if (state === 'active') {
                circle.classList.add("bg-brand-red", "border-brand-red", "text-white", "ring-pulse");
                title.className = "text-xs font-black text-brand-red transition-colors duration-500";
            } else {
                circle.classList.add("bg-brand-gray-extralight", "border-brand-gray-light", "text-brand-gray");
                title.className = "text-xs font-bold text-brand-gray transition-colors duration-500";
            }
        }

        function renderTimeline(status) {
            const lineProgress = document.getElementById('timeline-progress');
            
            if (status === 'menunggu konfirmasi') {
                lineProgress.style.height = '0%';
                updateStepUI(1, 'active', iconClock);
                updateStepUI(2, 'pending', iconCooking);
                updateStepUI(3, 'pending', iconServe);
            } else if (status === 'diproses') {
                lineProgress.style.height = '50%';
                updateStepUI(1, 'done', iconCheck);
                updateStepUI(2, 'active', iconCooking);
                updateStepUI(3, 'pending', iconServe);
            } else if (status === 'selesai') {
                lineProgress.style.height = '100%';
                updateStepUI(1, 'done', iconCheck);
                updateStepUI(2, 'done', iconCheck);
                updateStepUI(3, 'done', iconCheck);
            }
        }

        function handlePaymentSuccess() {
            isPaid = true;
            
            // Perbarui badge pembayaran
            const badgeBayar = document.getElementById('badge-bayar');
            if (badgeBayar) {
                badgeBayar.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 transition-all duration-500";
                badgeBayar.innerHTML = `<span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Lunas`;
            }

            // Hilangkan tombol Bayar Sekarang
            const paymentCta = document.getElementById('payment-cta');
            if (paymentCta) {
                paymentCta.classList.add("scale-95", "opacity-0");
                setTimeout(() => {
                    paymentCta.style.display = "none";
                }, 500);
            }

            // Tampilkan tombol Unduh Kuitansi
            const btnKuitansi = document.getElementById('btn-kuitansi');
            if (btnKuitansi) {
                btnKuitansi.classList.remove('hidden');
                btnKuitansi.classList.add('flex');
            }

            // Munculkan toast melayang sukses pembayaran
            const toast = document.getElementById('payment-toast');
            if (toast) {
                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.remove('scale-95', 'opacity-0');
                    toast.classList.add('scale-100', 'opacity-100', 'flex');
                }, 100);
            }
        }

        // Render Awal Timeline
        renderTimeline(lastStatus);

        // Polling loop
        function poll() {
            fetch(statusUrl)
                .then(r => r.json())
                .then(data => {
                    // Update Timeline jika status pesanan berubah
                    if (data.status_pesanan !== lastStatus) {
                        lastStatus = data.status_pesanan;
                        renderTimeline(lastStatus);
                    }

                    // Update Pembayaran jika status berubah jadi lunas
                    if (data.status_pembayaran === 'lunas' && !isPaid) {
                        handlePaymentSuccess();
                    }

                    // Lanjutkan polling jika pesanan belum selesai atau belum dibayar
                    if (data.status_pesanan !== 'selesai' || data.status_pembayaran !== 'lunas') {
                        setTimeout(poll, 5000);
                    }
                })
                .catch(() => {
                    setTimeout(poll, 8000);
                });
        }

        // Jalankan Polling
        setTimeout(poll, 5000);
    })();
    </script>
</body>
</html>

