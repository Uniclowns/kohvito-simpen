<x-layouts.kasir title="Beranda Kasir" page-title="Beranda Kasir">

    <x-slot:headerEnd>
        <div class="hidden items-center gap-3 sm:flex">
            <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand-red to-brand-dark ring-1 ring-brand-dark/15">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito"
                    class="h-7 w-7 object-contain">
            </div>
            <span
                class="max-w-[220px] truncate text-[18px] font-bold tracking-[0.5px] text-brand-dark">{{ auth()->user()?->nama_lengkap ?? (auth()->user()?->name ?? 'Username') }}</span>
        </div>
    </x-slot:headerEnd>

    @if (session('success'))
        <div class="mb-6 p-5 bg-green-50 border border-green-200 text-green-800 rounded-lg text-[16px]">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── 4 Stat Cards ── --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:gap-5 md:grid-cols-2 xl:grid-cols-4" data-anim="stagger">
        {{-- Pesanan Selesai --}}
        <div class="group rounded-2xl bg-white p-5 ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.28)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_38px_-18px_rgba(70,0,1,0.34)] lg:p-6" data-anim-item>
            <div class="mb-3.5 flex items-center gap-2 text-brand-red">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h6 class="text-[12px] font-bold uppercase tracking-[1px]">Jumlah Pesanan Selesai</h6>
            </div>
            <div class="flex flex-wrap items-baseline gap-x-2">
                <p class="text-[34px] font-bold leading-[40px] text-brand-black sm:text-[40px] sm:leading-[44px]" data-count-up="{{ $selesai }}">{{ $selesai }}</p>
                <p class="text-[20px] font-bold leading-7 text-brand-gray">Pesanan</p>
            </div>
            <p class="mt-2.5 text-[13px] leading-5 text-brand-gray">
                <span class="font-semibold text-brand-red">{{ $selesaiMinuman }}</span> Minuman
                <span class="px-1 text-brand-red-muted">·</span>
                <span class="font-semibold text-brand-red">{{ $selesaiMakanan }}</span> Makanan Berhasil Terjual
            </p>
        </div>

        {{-- Pesanan Aktif --}}
        <div class="group rounded-2xl bg-white p-5 ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.28)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_38px_-18px_rgba(70,0,1,0.34)] lg:p-6" data-anim-item>
            <div class="mb-3.5 flex items-center gap-2 text-brand-red">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h6 class="text-[12px] font-bold uppercase tracking-[1px]">Jumlah Pesanan Aktif</h6>
            </div>
            <div class="flex flex-wrap items-baseline gap-x-2">
                <p class="text-[34px] font-bold leading-[40px] text-brand-black sm:text-[40px] sm:leading-[44px]" data-count-up="{{ $pesananAktif }}">{{ $pesananAktif }}</p>
                <p class="text-[20px] font-bold leading-7 text-brand-gray">Pesanan</p>
            </div>
            <p class="mt-2.5 text-[13px] leading-5 text-brand-gray">Menunggu &amp; sedang diproses dapur</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="group rounded-2xl bg-white p-5 ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.28)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_38px_-18px_rgba(70,0,1,0.34)] lg:p-6" data-anim-item>
            <div class="mb-3.5 flex items-center gap-2 text-brand-red">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h6 class="text-[12px] font-bold uppercase tracking-[1px]">Total Transaksi</h6>
            </div>
            <div class="flex flex-wrap items-baseline gap-x-2">
                <p class="text-[34px] font-bold leading-[40px] text-brand-black sm:text-[40px] sm:leading-[44px]" data-count-up="{{ $totalTransaksi }}">{{ $totalTransaksi }}</p>
                <p class="text-[20px] font-bold leading-7 text-brand-gray">Transaksi</p>
            </div>
            <p class="mt-2.5 text-[13px] leading-5 text-brand-gray">Transaksi lunas sepanjang waktu</p>
        </div>

        {{-- Total Pendapatan Kotor --}}
        <div class="group rounded-2xl bg-white p-5 ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.28)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_38px_-18px_rgba(70,0,1,0.34)] lg:p-6" data-anim-item>
            <div class="mb-3.5 flex items-center gap-2 text-brand-red">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m4 0h2M6 19h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <h6 class="text-[12px] font-bold uppercase tracking-[1px]">Total Pendapatan Kotor</h6>
            </div>
            <div class="flex min-w-0 flex-wrap items-baseline gap-x-2">
                <p class="text-[22px] font-bold leading-7 text-brand-black sm:text-[24px] sm:leading-8">Rp</p>
                <p class="kvt-break-anywhere text-[30px] font-bold leading-[38px] text-brand-black sm:text-[36px] sm:leading-[42px]" data-count-up="{{ $omzetTotal }}">{{ number_format($omzetTotal, 0, ',', '.') }}</p>
            </div>
            <p class="mt-2.5 text-[13px] leading-5 text-brand-gray">
                Rata-Rata Pembelian Sebesar <span class="font-semibold text-brand-red">Rp {{ number_format($rataPembelian, 0, ',', '.') }}</span>
            </p>
        </div>
    </div>

    {{-- ── 2 Terlaris Cards ── --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:gap-5 xl:grid-cols-2">
        @php
            $imgSrcMk = $makananTerlaris?->gambar_menu
                ? (str_starts_with($makananTerlaris->gambar_menu, 'http')
                    ? $makananTerlaris->gambar_menu
                    : asset('images/food/' . $makananTerlaris->gambar_menu))
                : null;
            $imgSrcMn = $minumanTerlaris?->gambar_menu
                ? (str_starts_with($minumanTerlaris->gambar_menu, 'http')
                    ? $minumanTerlaris->gambar_menu
                    : asset('images/drink/' . $minumanTerlaris->gambar_menu))
                : null;
        @endphp

        <div class="group flex items-stretch overflow-hidden rounded-2xl bg-white ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.28)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_38px_-18px_rgba(70,0,1,0.34)]">
            <div class="flex w-16 shrink-0 items-center justify-center bg-gradient-to-br from-brand-red to-brand-dark sm:w-[76px]">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="" class="h-9 w-9 object-contain sm:h-11 sm:w-11">
            </div>
            @if ($imgSrcMk)
                <div class="h-auto w-[84px] shrink-0 overflow-hidden sm:w-[100px]">
                    <img src="{{ $imgSrcMk }}" alt="" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                </div>
            @endif
            <div class="flex min-w-0 flex-1 flex-col justify-center px-4 py-4 sm:px-6">
                <p class="text-[12px] font-bold uppercase tracking-[1px] text-brand-red">Makanan Terlaris</p>
                <p class="mt-1 truncate text-[20px] font-bold leading-7 text-brand-black sm:text-[24px] sm:leading-8">
                    {{ $makananTerlaris?->nama_menu ?? '—' }}
                </p>
                <p class="mt-1.5 inline-flex items-center gap-1.5 text-[13px] font-semibold text-brand-gray">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-red"></span>
                    {{ $makananTerlaris->total_terjual ?? 0 }} Terjual Hari ini
                </p>
            </div>
        </div>

        <div class="group flex items-stretch overflow-hidden rounded-2xl bg-white ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.28)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_38px_-18px_rgba(70,0,1,0.34)]">
            <div class="flex w-16 shrink-0 items-center justify-center bg-gradient-to-br from-brand-red to-brand-dark sm:w-[76px]">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="" class="h-9 w-9 object-contain sm:h-11 sm:w-11">
            </div>
            @if ($imgSrcMn)
                <div class="h-auto w-[84px] shrink-0 overflow-hidden sm:w-[100px]">
                    <img src="{{ $imgSrcMn }}" alt="" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                </div>
            @endif
            <div class="flex min-w-0 flex-1 flex-col justify-center px-4 py-4 sm:px-6">
                <p class="text-[12px] font-bold uppercase tracking-[1px] text-brand-red">Minuman Terlaris</p>
                <p class="mt-1 truncate text-[20px] font-bold leading-7 text-brand-black sm:text-[24px] sm:leading-8">
                    {{ $minumanTerlaris?->nama_menu ?? '—' }}
                </p>
                <p class="mt-1.5 inline-flex items-center gap-1.5 text-[13px] font-semibold text-brand-gray">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-red"></span>
                    {{ $minumanTerlaris->total_terjual ?? 0 }} Terjual Hari ini
                </p>
            </div>
        </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:gap-5">
        <div class="rounded-2xl bg-white px-4 pb-5 pt-5 ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.26)] sm:px-7 sm:pb-7 sm:pt-7">
            <h2 class="mb-6 text-[22px] font-bold leading-8 tracking-[0.8px] text-brand-dark sm:text-[24px]">Pesanan Hari Ini</h2>
            <div class="relative h-[260px] sm:h-[300px]">
                <canvas id="chartPesanan"></canvas>
            </div>
        </div>

        <div class="rounded-2xl bg-white px-4 pb-5 pt-5 ring-1 ring-brand-dark/[0.06] shadow-[0_2px_4px_rgba(0,0,0,0.05),0_14px_32px_-18px_rgba(70,0,1,0.26)] sm:px-7 sm:pb-7 sm:pt-7">
            <h2 class="mb-6 text-[22px] font-bold leading-8 tracking-[0.8px] text-brand-dark sm:text-[24px]">Pendapatan Minggu Ini</h2>
            <div class="relative h-[260px] sm:h-[300px]">
                <canvas id="chartPendapatan"></canvas>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                const jamLabels = @json($jamLabels);
                const jamData = @json($jamData);
                const hariLabels = @json($hariLabels);
                const pendapatanData = @json($pendapatanData);

                const pesananCtx = document.getElementById('chartPesanan').getContext('2d');
                const areaFill = pesananCtx.createLinearGradient(0, 0, 0, 300);
                areaFill.addColorStop(0, 'rgba(104,31,31,0.18)');
                areaFill.addColorStop(1, 'rgba(104,31,31,0.01)');

                new Chart(pesananCtx, {
                    type: 'line',
                    data: {
                        labels: jamLabels,
                        datasets: [{
                            data: jamData,
                            borderColor: '#681F1F',
                            backgroundColor: areaFill,
                            borderWidth: 2.5,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#681F1F',
                            pointHoverBorderColor: '#FFFFFF',
                            pointHoverBorderWidth: 2,
                            fill: true,
                            tension: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#460001',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: { label: ctx => ctx.parsed.y + ' pesanan' }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, color: '#808080' }
                            },
                            y: {
                                grid: { color: '#EDEDED' },
                                border: { display: false },
                                beginAtZero: true,
                                ticks: { font: { size: 11 }, color: '#808080', stepSize: 25, precision: 0 }
                            }
                        }
                    }
                });

                new Chart(document.getElementById('chartPendapatan').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: hariLabels,
                        datasets: [{
                            data: pendapatanData,
                            backgroundColor: '#681F1F',
                            hoverBackgroundColor: '#460001',
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 56,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#460001',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, color: '#808080' }
                            },
                            y: {
                                grid: { color: '#EDEDED' },
                                border: { display: false },
                                beginAtZero: true,
                                ticks: { font: { size: 11 }, color: '#808080', callback: v => (v / 1000000).toFixed(1) + 'jt' }
                            }
                        }
                    }
                });
            })();
        </script>
    </x-slot:scripts>

    <x-slot:pageFooter>
        <x-kasir-footer />
    </x-slot:pageFooter>

</x-layouts.kasir>
