<x-layouts.admin title="Beranda Admin" page-title="Beranda Admin">

    {{-- ── Header: filter + Tutup/Buka Order ── --}}
    <x-slot:headerEnd>
        <div class="flex items-center gap-3">
            <button type="button"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-brand-gray-extralight hover:bg-brand-light transition-colors">
                <svg class="w-4 h-4 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </button>
            <form action="{{ route('admin.toggle-order-status') }}" method="POST">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                            {{ $orderStatus === 'buka'
                                ? 'bg-brand-red text-white hover:bg-brand-dark'
                                : 'bg-green-600 text-white hover:bg-green-700' }}">
                    @if ($orderStatus === 'buka')
                        Tutup Toko
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Buka Order
                    @endif
                </button>
            </form>
        </div>
    </x-slot:headerEnd>

    {{-- ── Flash ── --}}
    @if (session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-4 gap-4 mb-5">

        {{-- Total Menu --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-5 py-4">
            <p class="text-xs text-brand-gray font-medium mb-3">Total Menu</p>
            <div class="flex items-end justify-between">
                <p class="text-2xl font-bold text-brand-black">{{ $totalMenu }}</p>
                <span class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center">
                    <img src="{{ asset('images/icons/coffee.svg') }}" alt=""
                         class="w-5 h-5 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)]">
                </span>
            </div>
            <p class="text-xs text-brand-gray mt-1">Menu</p>
        </div>

        {{-- Total Pengguna Kasir --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-5 py-4">
            <p class="text-xs text-brand-gray font-medium mb-3">Total Pengguna Kasir</p>
            <div class="flex items-end justify-between">
                <p class="text-2xl font-bold text-brand-black">{{ $totalKasir }}</p>
                <span class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center">
                    <img src="{{ asset('images/icons/user-group.svg') }}" alt="" class="w-4 h-4">
                </span>
            </div>
            <p class="text-xs text-brand-gray mt-1">Kasir</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-5 py-4">
            <p class="text-xs text-brand-gray font-medium mb-3">Total Transaksi</p>
            <div class="flex items-end justify-between">
                <p class="text-2xl font-bold text-brand-black">{{ $totalTransaksi }}</p>
                <span class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center">
                    <img src="{{ asset('images/icons/shopping-cart.svg') }}" alt="" class="w-4 h-4">
                </span>
            </div>
            <p class="text-xs text-brand-gray mt-1">Transaksi</p>
        </div>

        {{-- Total Pendapatan Bulan Ini --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-5 py-4">
            <p class="text-xs text-brand-gray font-medium mb-3">Total Pendapatan Bulan ini</p>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-xs text-brand-gray">Rp</p>
                    <p class="text-2xl font-bold text-brand-black leading-tight">{{ number_format($omzetBulanIni, 0, ',', '.') }}</p>
                </div>
                <span class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center">
                    <img src="{{ asset('images/icons/currency-dollar.svg') }}" alt="" class="w-4 h-4">
                </span>
            </div>
        </div>
    </div>

    {{-- ── Terlaris Cards ── --}}
    <div class="grid grid-cols-2 gap-4 mb-5">

        {{-- Makanan Terlaris --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-5 py-4 flex items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-8 h-8 rounded-md bg-brand-red flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-5 h-5">
                    </span>
                    <p class="text-xs text-brand-gray font-medium">Makanan Terlaris</p>
                </div>
                <p class="text-base font-bold text-brand-black">{{ $makananTerlaris?->nama_menu ?? '—' }}</p>
                @if ($makananTerlaris)
                    <p class="text-xs text-brand-gray mt-0.5">{{ $makananTerlaris->total_terjual }} porsi terjual</p>
                @endif
            </div>
            @if ($makananTerlaris?->gambar_menu)
                <img src="{{ asset('storage/' . $makananTerlaris->gambar_menu) }}"
                     alt="{{ $makananTerlaris->nama_menu }}"
                     class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
            @else
                <div class="w-20 h-20 rounded-lg bg-brand-red flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-10 h-10">
                </div>
            @endif
        </div>

        {{-- Minuman Terlaris --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-5 py-4 flex items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-8 h-8 rounded-md bg-brand-red flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('images/icons/Drink.svg') }}" alt="" class="w-5 h-5">
                    </span>
                    <p class="text-xs text-brand-gray font-medium">Minuman Terlaris</p>
                </div>
                <p class="text-base font-bold text-brand-black">{{ $minumanTerlaris?->nama_menu ?? '—' }}</p>
                @if ($minumanTerlaris)
                    <p class="text-xs text-brand-gray mt-0.5">{{ $minumanTerlaris->total_terjual }} gelas terjual</p>
                @endif
            </div>
            @if ($minumanTerlaris?->gambar_menu)
                <img src="{{ asset('storage/' . $minumanTerlaris->gambar_menu) }}"
                     alt="{{ $minumanTerlaris->nama_menu }}"
                     class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
            @else
                <div class="w-20 h-20 rounded-lg bg-brand-red flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('images/icons/Drink.svg') }}" alt="" class="w-10 h-10">
                </div>
            @endif
        </div>
    </div>

    {{-- ── Data Pesanan Hari Ini ── --}}
    <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm mb-5">
        <div class="flex items-center justify-between px-6 py-4 border-b border-brand-gray-extralight">
            <p class="font-semibold text-brand-black">Data Pesanan Hari Ini</p>
            <a href="{{ route('admin.laporan.cetak') }}"
               class="flex items-center gap-2 bg-brand-red text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-brand-dark transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Laporan Kasir
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-brand-gray font-medium border-b border-brand-gray-extralight">
                        <th class="px-6 py-3">ID Pesanan</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Meja</th>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-gray-extralight">
                    @forelse ($pesananHariIni as $p)
                    @php
                        $items = $p->detailPesanan->map(fn($d) => $d->jumlah . 'x ' . ($d->menu?->nama_menu ?? '?'))->join(', ');
                        [$statusIcon, $statusBg, $statusLabel] = match($p->status_pesanan) {
                            'selesai'             => ['Icon.svg',   'bg-green-50 text-green-700',  'Selesai'],
                            'diproses'            => ['Wait.svg',   'bg-amber-50 text-amber-700',  'Diproses'],
                            'menunggu konfirmasi' => ['Wait.svg',   'bg-orange-50 text-orange-700','Menunggu'],
                            default               => ['Cancel.svg', 'bg-red-50 text-red-700',      ucfirst($p->status_pesanan)],
                        };
                    @endphp
                    <tr class="hover:bg-brand-light/40 transition-colors">
                        <td class="px-6 py-3 font-mono text-xs text-brand-black">{{ $p->no_pesanan }}</td>
                        <td class="px-4 py-3 text-brand-gray whitespace-nowrap">{{ $p->tgl_pembayaran?->format('H:i') }}</td>
                        <td class="px-4 py-3 text-brand-black">{{ $p->meja?->no_meja ?? '—' }}</td>
                        <td class="px-4 py-3 text-brand-gray max-w-xs truncate" title="{{ $items }}">{{ $items ?: '—' }}</td>
                        <td class="px-4 py-3 text-brand-black">{{ $p->user?->nama_lengkap ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-brand-black whitespace-nowrap">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBg }}">
                                <img src="{{ asset('images/icons/' . $statusIcon) }}" alt="" class="w-3.5 h-3.5 flex-shrink-0">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-brand-gray">
                            Belum ada pesanan hari ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="grid grid-cols-1 gap-5 mb-6">

        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm p-6">
            <p class="font-semibold text-brand-black mb-4">Pesanan Hari Ini</p>
            <div class="relative h-48">
                <canvas id="chartPesanan"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm p-6">
            <p class="font-semibold text-brand-black mb-4">Pendapatan Minggu Ini</p>
            <div class="relative h-48">
                <canvas id="chartPendapatan"></canvas>
            </div>
        </div>

    </div>

    {{-- ── Footer ── --}}
    <x-slot:pageFooter>
        <footer class="bg-brand-dark">
            <div class="px-8 py-10 grid grid-cols-3 gap-10">

                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <img src="{{ asset('images/logo/KOHVITO LOGO ONLY WHITE.png') }}" alt="Kohvito" class="h-7 w-auto">
                    </div>
                    <p class="text-xs text-white/60 leading-relaxed mb-4">
                        A Coffee, Dining &amp; Lifestyle Space Crafted for People Who Love Good Coffee, Good Atmosphere, and Meaningful Daily Experiences.
                    </p>
                    <div class="flex items-start gap-2 mb-1.5">
                        <img src="{{ asset('images/icons/location-marker.svg') }}" alt="" class="w-4 h-4 flex-shrink-0 mt-0.5">
                        <p class="text-xs text-white/70">Jl. Jalan No. 17, Pontianak</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/icons/mail.svg') }}" alt="" class="w-4 h-4 flex-shrink-0">
                        <p class="text-xs text-white/60">kohvito.cafe@gmail.com</p>
                    </div>
                </div>

                {{-- Navigation --}}
                <div>
                    <p class="text-sm font-semibold text-white mb-4">Navigation</p>
                    <ul class="space-y-2">
                        <li><a href="{{ route('admin.beranda') }}" class="text-sm text-white/60 hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('admin.pengguna-kasir.index') }}" class="text-sm text-white/60 hover:text-white transition-colors">Kelola User Kasir</a></li>
                        <li><a href="{{ route('admin.menu.index') }}" class="text-sm text-white/60 hover:text-white transition-colors">Kelola Menu</a></li>
                        <li><a href="{{ route('admin.laporan-keuangan.index') }}" class="text-sm text-white/60 hover:text-white transition-colors">Laporan Keuangan</a></li>
                    </ul>
                </div>

                {{-- Visit Us + Reservation --}}
                <div>
                    <p class="text-sm font-semibold text-white mb-4">Visit Us</p>
                    <ul class="space-y-2.5 mb-6">
                        <li class="flex items-center gap-2.5">
                            <img src="{{ asset('images/icons/Instagram.svg') }}" alt="Instagram" class="w-5 h-5 flex-shrink-0">
                            <span class="text-sm text-white/60">kohvito.cafe</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <img src="{{ asset('images/icons/tiktok.svg') }}" alt="TikTok" class="w-5 h-5 flex-shrink-0">
                            <span class="text-sm text-white/60">kohvito</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <img src="{{ asset('images/icons/Threads instagram.svg') }}" alt="Threads" class="w-5 h-5 flex-shrink-0">
                            <span class="text-sm text-white/60">kohvito.cafe</span>
                        </li>
                    </ul>

                    <p class="text-sm font-semibold text-white mb-3">Reservation</p>
                    <a href="https://wa.me/6281348922766"
                       class="inline-flex items-center gap-2 bg-brand-red text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-brand-red/80 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Contact us : +62 813 4892 2766
                    </a>
                </div>
            </div>

            <div class="border-t border-white/10 px-8 py-4 text-center">
                <p class="text-xs text-white/40">&copy;{{ date('Y') }} All Rights Reserved. Developed By Pet &amp; Jenn</p>
            </div>
        </footer>
    </x-slot:pageFooter>

    {{-- ── Scripts ── --}}
    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const jamLabels      = @json($jamLabels);
            const jamData        = @json($jamData);
            const hariLabels     = @json($hariLabels);
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
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#808080' } },
                        y: { grid: { color: '#E6E6E6' }, beginAtZero: true,
                             ticks: { font: { size: 11 }, color: '#808080', stepSize: 1, precision: 0 } }
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
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#808080' } },
                        y: { grid: { color: '#E6E6E6' }, beginAtZero: true,
                             ticks: { font: { size: 11 }, color: '#808080',
                                      callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'k' } }
                    }
                }
            });
        })();
        </script>
    </x-slot:scripts>

</x-layouts.admin>
