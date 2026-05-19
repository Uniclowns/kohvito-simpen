<x-layouts.kasir title="Beranda Kasir" page-title="Beranda Kasir">

    <x-slot:headerEnd>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/icons/KVT ICON USER.svg') }}" alt="User Avatar"
                    class="w-12 h-12 object-contain">
            </div>
            <span
                class="text-[#460001] text-[22px] tracking-[1px]">{{ auth()->user()?->nama_lengkap ?? (auth()->user()?->name ?? 'Kasir') }}</span>
        </div>
    </x-slot:headerEnd>

    @if (session('success'))
        <div class="mb-6 p-5 bg-green-50 border border-green-200 text-green-800 rounded-lg text-[16px]">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── 4 Stat Cards ── --}}
    <div class="grid grid-cols-4 gap-7 mb-10">
        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-7 py-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Jumlah Pesanan Selesai</h6>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-[42px] font-bold text-black leading-[46px]">{{ $selesai }}</p>
                <p class="text-[26px] font-bold text-black leading-[34px]">Pesanan</p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">
                {{ $selesaiMinuman }} Minuman &nbsp; {{ $selesaiMakanan }} Makanan &nbsp; Berhasil Terjual
            </p>
        </div>

        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-7 py-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Jumlah Pesanan Aktif</h6>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-[42px] font-bold text-black leading-[46px]">{{ $pesananAktif }}</p>
                <p class="text-[26px] font-bold text-black leading-[34px]">Pesanan</p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">&nbsp;</p>
        </div>

        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-7 py-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Total Transaksi</h6>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-[42px] font-bold text-black leading-[46px]">{{ $totalTransaksi }}</p>
                <p class="text-[26px] font-bold text-black leading-[34px]">Transaksi</p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">&nbsp;</p>
        </div>

        <div class="bg-[rgba(104,31,31,0.12)] rounded-[9px] px-7 py-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-[#681F1F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h6 class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px]">Total Pendapatan Kotor</h6>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-[26px] font-bold text-black leading-[34px]">Rp</p>
                <p class="text-[42px] font-bold text-black leading-[46px]">{{ number_format($omzetTotal, 0, ',', '.') }}
                </p>
            </div>
            <p class="text-[14px] text-[rgba(70,0,1,0.5)] mt-2">
                Rata-Rata Pembelian Sebesar Rp {{ number_format($rataPembelian, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- ── 2 Terlaris Cards ── --}}
    <div class="grid grid-cols-2 gap-8 mb-10">
        <div
            class="bg-white rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.18)] flex items-center overflow-hidden h-[120px]">
            <div class="bg-[#681F1F] h-full flex items-center justify-center px-6 flex-shrink-0">
                <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-10 h-10 brightness-0 invert">
            </div>
            @if ($makananTerlaris?->gambar_menu)
                @php
                    $imgSrcMk = str_starts_with($makananTerlaris->gambar_menu, 'http')
                        ? $makananTerlaris->gambar_menu
                        : asset('images/food/' . $makananTerlaris->gambar_menu);
                @endphp
                <div class="w-[120px] h-[120px] flex-shrink-0 overflow-hidden">
                    <img src="{{ $imgSrcMk }}" alt="" class="w-full h-full object-cover">
                </div>
            @endif
            <div class="flex-1 px-6 py-3">
                <p class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px] mb-2">Makanan Terlaris</p>
                <p class="text-[24px] font-bold text-black leading-[32px] mb-2">
                    {{ $makananTerlaris?->nama_menu ?? '—' }}
                </p>
                <p class="text-[14px] text-[rgba(70,0,1,0.5)] font-bold">
                    {{ $makananTerlaris->total_terjual ?? 0 }} Terjual Hari ini
                </p>
            </div>
        </div>

        <div
            class="bg-white rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.18)] flex items-center overflow-hidden h-[120px]">
            <div class="bg-[#681F1F] h-full flex items-center justify-center px-6 flex-shrink-0">
                <img src="{{ asset('images/icons/Drink.svg') }}" alt="" class="w-10 h-10 brightness-0 invert">
            </div>
            @if ($minumanTerlaris?->gambar_menu)
                @php
                    $imgSrcMn = str_starts_with($minumanTerlaris->gambar_menu, 'http')
                        ? $minumanTerlaris->gambar_menu
                        : asset('images/drink/' . $minumanTerlaris->gambar_menu);
                @endphp
                <div class="w-[120px] h-[120px] flex-shrink-0 overflow-hidden">
                    <img src="{{ $imgSrcMn }}" alt="" class="w-full h-full object-cover">
                </div>
            @endif
            <div class="flex-1 px-6 py-3">
                <p class="text-[14px] text-[#681F1F] font-bold uppercase tracking-[0.6px] mb-2">Minuman Terlaris</p>
                <p class="text-[24px] font-bold text-black leading-[32px] mb-2">
                    {{ $minumanTerlaris?->nama_menu ?? '—' }}
                </p>
                <p class="text-[14px] text-[rgba(70,0,1,0.5)] font-bold">
                    {{ $minumanTerlaris->total_terjual ?? 0 }} Terjual Hari ini
                </p>
            </div>
        </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="grid grid-cols-1 gap-8 mb-8">
        <div class="bg-white rounded-[9px] shadow-sm px-8 pt-7 pb-6">
            <p class="text-[24px] font-bold text-[#460001] tracking-[1px] mb-6">Pesanan Hari Ini</p>
            <div class="relative h-[340px]">
                <canvas id="chartPesanan"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-[9px] shadow-sm px-8 pt-7 pb-6">
            <p class="text-[24px] font-bold text-[#460001] tracking-[1px] mb-6">Pendapatan Minggu Ini</p>
            <div class="relative h-[340px]">
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
