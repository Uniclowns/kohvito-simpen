<x-layouts.kasir title="Beranda Kasir" page-title="Beranda Kasir">

    <x-slot:headerEnd>
        <div class="hidden items-center gap-3 sm:flex">
            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full">
                <img src="{{ asset('images/icons/KVT ICON USER.svg') }}" alt="User Avatar"
                    class="w-12 h-12 object-contain">
            </div>
            <span
                class="max-w-[220px] truncate text-[22px] tracking-[1px] text-[#460001]">{{ auth()->user()?->nama_lengkap ?? (auth()->user()?->name ?? 'Kasir') }}</span>
        </div>
    </x-slot:headerEnd>

    @if (session('success'))
        <div class="mb-6 p-5 bg-green-50 border border-green-200 text-green-800 rounded-lg text-[16px]">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── 4 Stat Cards ── --}}
    <div class="mb-10 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4" data-anim="stagger">
        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-5 py-5 lg:px-7 lg:py-6" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Jumlah Pesanan Selesai</h6>
            </div>
            <div class="mt-2 flex flex-wrap items-baseline gap-2">
                <p class="text-[34px] font-bold leading-[42px] text-black sm:text-[42px] sm:leading-[46px]" data-count-up="{{ $selesai }}">{{ $selesai }}</p>
                <p class="text-[22px] font-bold leading-[30px] text-black sm:text-[26px] sm:leading-[34px]">Pesanan</p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">
                {{ $selesaiMinuman }} Minuman &nbsp; {{ $selesaiMakanan }} Makanan &nbsp; Berhasil Terjual
            </p>
        </div>

        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-5 py-5 lg:px-7 lg:py-6" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Jumlah Pesanan Aktif</h6>
            </div>
            <div class="mt-2 flex flex-wrap items-baseline gap-2">
                <p class="text-[34px] font-bold leading-[42px] text-black sm:text-[42px] sm:leading-[46px]" data-count-up="{{ $pesananAktif }}">{{ $pesananAktif }}</p>
                <p class="text-[22px] font-bold leading-[30px] text-black sm:text-[26px] sm:leading-[34px]">Pesanan</p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">&nbsp;</p>
        </div>

        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-5 py-5 lg:px-7 lg:py-6" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Total Transaksi</h6>
            </div>
            <div class="mt-2 flex flex-wrap items-baseline gap-2">
                <p class="text-[34px] font-bold leading-[42px] text-black sm:text-[42px] sm:leading-[46px]" data-count-up="{{ $totalTransaksi }}">{{ $totalTransaksi }}</p>
                <p class="text-[22px] font-bold leading-[30px] text-black sm:text-[26px] sm:leading-[34px]">Transaksi</p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">&nbsp;</p>
        </div>

        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-5 py-5 lg:px-7 lg:py-6" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Total Pendapatan Kotor</h6>
            </div>
            <div class="mt-2 flex min-w-0 flex-wrap items-baseline gap-2">
                <p class="text-[22px] font-bold leading-[30px] text-black sm:text-[26px] sm:leading-[34px]">Rp</p>
                <p class="kvt-break-anywhere text-[32px] font-bold leading-[40px] text-black sm:text-[42px] sm:leading-[46px]" data-count-up="{{ $omzetTotal }}">{{ number_format($omzetTotal, 0, ',', '.') }}
                </p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">
                Rata-Rata Pembelian Sebesar Rp {{ number_format($rataPembelian, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- ── Terlaris Cards (desain disamakan dengan panel Admin) ── --}}
    <div class="mb-10 grid grid-cols-1 gap-4 lg:grid-cols-2">

        {{-- Makanan Terlaris --}}
        <div class="flex min-h-24 overflow-hidden rounded-xl border border-brand-gray-extralight bg-white shadow-sm sm:h-24">
            <div class="w-20 bg-brand-dark flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-8 h-8 brightness-0 invert">
            </div>
            <div class="flex min-w-0 flex-1 items-center gap-3 px-3 py-3 sm:gap-4 sm:px-4">
                @if ($makananTerlaris?->gambar_menu)
                    @php
                        $imgSrcMk = str_starts_with($makananTerlaris->gambar_menu, 'http')
                            ? $makananTerlaris->gambar_menu
                            : asset("images/food/{$makananTerlaris->gambar_menu}");
                    @endphp
                    <img src="{{ $imgSrcMk }}"
                         alt="{{ $makananTerlaris->nama_menu }}"
                         loading="lazy" decoding="async"
                         class="w-16 h-16 object-cover rounded-full flex-shrink-0 border border-brand-gray-extralight">
                @else
                    <div class="w-16 h-16 rounded-full bg-brand-light flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-8 h-8 opacity-20">
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-[10px] text-brand-red font-bold uppercase tracking-wider mb-1">Makanan Terlaris</p>
                    <p class="mb-1 truncate text-lg font-bold leading-tight text-brand-black">{{ $makananTerlaris?->nama_menu ?? '—' }}</p>
                    @if ($makananTerlaris)
                        <p class="text-[10px] text-brand-gray font-medium">{{ $makananTerlaris->total_terjual }} Terjual Hari Ini</p>
                    @else
                        <p class="text-[10px] text-brand-gray font-medium">Belum ada data</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Minuman Terlaris --}}
        <div class="flex min-h-24 overflow-hidden rounded-xl border border-brand-gray-extralight bg-white shadow-sm sm:h-24">
            <div class="w-20 bg-brand-dark flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/icons/Drink.svg') }}" alt="" class="w-8 h-8 brightness-0 invert">
            </div>
            <div class="flex min-w-0 flex-1 items-center gap-3 px-3 py-3 sm:gap-4 sm:px-4">
                @if ($minumanTerlaris?->gambar_menu)
                    @php
                        $imgSrcMn = str_starts_with($minumanTerlaris->gambar_menu, 'http')
                            ? $minumanTerlaris->gambar_menu
                            : asset("images/drink/{$minumanTerlaris->gambar_menu}");
                    @endphp
                    <img src="{{ $imgSrcMn }}"
                         alt="{{ $minumanTerlaris->nama_menu }}"
                         loading="lazy" decoding="async"
                         class="w-16 h-16 object-cover rounded-full flex-shrink-0 border border-brand-gray-extralight">
                @else
                    <div class="w-16 h-16 rounded-full bg-brand-light flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('images/icons/Drink.svg') }}" alt="" class="w-8 h-8 opacity-20">
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-[10px] text-brand-red font-bold uppercase tracking-wider mb-1">Minuman Terlaris</p>
                    <p class="mb-1 truncate text-lg font-bold leading-tight text-brand-black">{{ $minumanTerlaris?->nama_menu ?? '—' }}</p>
                    @if ($minumanTerlaris)
                        <p class="text-[10px] text-brand-gray font-medium">{{ $minumanTerlaris->total_terjual }} Terjual Hari Ini</p>
                    @else
                        <p class="text-[10px] text-brand-gray font-medium">Belum ada data</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="grid grid-cols-1 gap-8 mb-8">
        <div class="rounded-[9px] bg-white px-4 pb-5 pt-5 shadow-sm sm:px-8 sm:pb-6 sm:pt-7">
            <p class="text-[24px] font-bold text-[#460001] tracking-[1px] mb-6">Pesanan Hari Ini</p>
            <div class="relative h-[260px] sm:h-[340px]">
                <canvas id="chartPesanan"></canvas>
            </div>
        </div>

        <div class="rounded-[9px] bg-white px-4 pb-5 pt-5 shadow-sm sm:px-8 sm:pb-6 sm:pt-7">
            <p class="text-[24px] font-bold text-[#460001] tracking-[1px] mb-6">Pendapatan Minggu Ini</p>
            <div class="relative h-[260px] sm:h-[340px]">
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

                new Chart(document.getElementById('chartPesanan').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: jamLabels,
                        datasets: [{
                            data: jamData,
                            borderColor: '#681F1F',
                            backgroundColor: 'rgba(104,31,31,0.10)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#681F1F',
                            fill: true,
                            tension: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#68121F'
                                }
                            },
                            y: {
                                grid: {
                                    color: '#E6E6E6'
                                },
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#68121F',
                                    stepSize: 25,
                                    precision: 0
                                }
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
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#68121F'
                                }
                            },
                            y: {
                                grid: {
                                    color: '#E6E6E6'
                                },
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#68121F',
                                    callback: v => (v / 1000000).toFixed(1) + 'jt'
                                }
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
