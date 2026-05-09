<x-layouts.admin title="Beranda Admin" page-title="Beranda Admin">

    {{-- ── Header: filter + Tutup/Buka Order ── --}}
    <x-slot:headerEnd>
        <div class="flex items-center gap-5">
            <button type="button" class="flex items-center justify-center transition-colors">
                <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </button>
            <form action="{{ route('admin.toggle-order-status') }}" method="POST">
                @csrf
                <button type="submit"
                        class="flex items-center justify-center px-6 py-2 rounded-xl text-sm font-bold transition-colors shadow-sm
                            {{ $orderStatus === 'buka'
                                ? 'bg-state-red text-white hover:opacity-90'
                                : 'bg-state-green text-white hover:opacity-90' }}">
                    @if ($orderStatus === 'buka')
                        Tutup Toko
                    @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Buka Toko
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
    <div class="grid grid-cols-4 gap-4 mb-6 mt-2">

        {{-- Total Menu --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4">
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/coffee.svg') }}" alt=""
                     class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <h6 class="text-[12px] text-brand-black font-bold uppercase">Total Menu</h6>
            </div>
            <div class="flex items-baseline gap-1.5 mt-2">
                <p class="text-4xl font-bold text-brand-black leading-none">{{ $totalMenu }}</p>
                <p class="text-sm font-bold text-brand-black">Menu</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">30 Minuman &nbsp; 20 Makanan</p>
        </div>

        {{-- Total Pengguna Kasir --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4">
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/user-group.svg') }}" alt="" class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <p class="text-[11px] text-brand-black font-bold uppercase">Total Pengguna Kasir</p>
            </div>
            <div class="flex items-baseline gap-1.5 mt-2">
                <p class="text-4xl font-bold text-brand-black leading-none">{{ $totalKasir }}</p>
                <p class="text-sm font-bold text-brand-black">Kasir</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">&nbsp;</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4">
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/shopping-cart.svg') }}" alt="" class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <p class="text-[11px] text-brand-black font-bold uppercase">Total Transaksi</p>
            </div>
            <div class="flex items-baseline gap-1.5 mt-2">
                <p class="text-4xl font-bold text-brand-black leading-none">{{ $totalTransaksi }}</p>
                <p class="text-sm font-bold text-brand-black">Transaksi</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">&nbsp;</p>
        </div>

        {{-- Total Pendapatan Kotor --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4">
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/currency-dollar.svg') }}" alt="" class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <p class="text-[11px] text-brand-black font-bold uppercase">Total Pendapatan Kotor</p>
            </div>
            <div class="flex items-baseline gap-1 mt-2">
                <p class="text-lg font-bold text-brand-black">Rp</p>
                <p class="text-3xl font-bold text-brand-black leading-none">{{ number_format($omzetBulanIni, 0, ',', '.') }}</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">Rp {{ number_format($omzetBulanIni / 30, 0, ',', '.') }} Rata-Rata Pembelian</p>
        </div>
    </div>

    {{-- ── Terlaris Cards ── --}}
    <div class="grid grid-cols-2 gap-4 mb-6">

        {{-- Makanan Terlaris --}}
        <div class="bg-white rounded-xl shadow-sm border border-brand-gray-extralight flex overflow-hidden h-24">
            <div class="w-20 bg-brand-dark flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-8 h-8 brightness-0 invert">
            </div>
            <div class="flex-1 py-3 px-4 flex items-center gap-4">
                @if ($makananTerlaris?->gambar_menu)
                    <img src="{{ asset('storage/' . $makananTerlaris->gambar_menu) }}"
                         alt="{{ $makananTerlaris->nama_menu }}"
                         class="w-16 h-16 object-cover rounded-full flex-shrink-0 border border-brand-gray-extralight">
                @else
                    <div class="w-16 h-16 rounded-full bg-brand-light flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-8 h-8 opacity-20">
                    </div>
                @endif
                <div>
                    <p class="text-[10px] text-brand-red font-bold uppercase tracking-wider mb-1">Makanan Terlaris</p>
                    <p class="text-lg font-bold text-brand-black leading-tight mb-1">{{ $makananTerlaris?->nama_menu ?? '—' }}</p>
                    @if ($makananTerlaris)
                        <p class="text-[10px] text-brand-gray font-medium">{{ $makananTerlaris->total_terjual }} Terjual Hari Ini</p>
                    @else
                        <p class="text-[10px] text-brand-gray font-medium">Belum ada data</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Minuman Terlaris --}}
        <div class="bg-white rounded-xl shadow-sm border border-brand-gray-extralight flex overflow-hidden h-24">
            <div class="w-20 bg-brand-dark flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/icons/Drink.svg') }}" alt="" class="w-8 h-8 brightness-0 invert">
            </div>
            <div class="flex-1 py-3 px-4 flex items-center gap-4">
                @if ($minumanTerlaris?->gambar_menu)
                    <img src="{{ asset('storage/' . $minumanTerlaris->gambar_menu) }}"
                         alt="{{ $minumanTerlaris->nama_menu }}"
                         class="w-16 h-16 object-cover rounded-full flex-shrink-0 border border-brand-gray-extralight">
                @else
                    <div class="w-16 h-16 rounded-full bg-brand-light flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('images/icons/Drink.svg') }}" alt="" class="w-8 h-8 opacity-20">
                    </div>
                @endif
                <div>
                    <p class="text-[10px] text-brand-red font-bold uppercase tracking-wider mb-1">Minuman Terlaris</p>
                    <p class="text-lg font-bold text-brand-black leading-tight mb-1">{{ $minumanTerlaris?->nama_menu ?? '—' }}</p>
                    @if ($minumanTerlaris)
                        <p class="text-[10px] text-brand-gray font-medium">{{ $minumanTerlaris->total_terjual }} Terjual Hari Ini</p>
                    @else
                        <p class="text-[10px] text-brand-gray font-medium">Belum ada data</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Data Pesanan Hari Ini ── --}}
    <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-brand-gray-extralight">
            <div class="flex items-center gap-2">
                <p class="font-bold text-brand-black">Data Pesanan Hari Ini</p>
            </div>
            <a href="{{ route('admin.laporan.cetak') }}"
               class="flex items-center justify-center gap-2 bg-brand-dark text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-opacity-90 transition-colors shadow-sm">
                <img src="{{ asset('images/icons/template.svg') }}" alt="" class="w-4 h-4 brightness-0 invert">
                Cetak Laporan Kasir
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] text-brand-dark font-bold border-b border-brand-gray-extralight">
                        <th class="px-6 py-4">ID Pesanan</th>
                        <th class="px-4 py-4">Waktu</th>
                        <th class="px-4 py-4">Meja</th>
                        <th class="px-4 py-4">Item</th>
                        <th class="px-4 py-4">Kasir</th>
                        <th class="px-4 py-4">Total</th>
                        <th class="px-4 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-gray-extralight">
                    @forelse ($pesananHariIni as $p)
                    @php
                        $items = $p->detailPesanan->map(fn($d) => $d->jumlah . 'x ' . ($d->menu?->nama_menu ?? '?'))->join(', ');
                        [$statusIcon, $statusBg, $statusLabel] = match($p->status_pesanan) {
                            'selesai'             => ['Icon.svg',   'bg-state-green/15 text-state-green',  'Selesai'],
                            'diproses'            => ['Wait.svg',   'bg-state-yellow/20 text-state-yellow',  'Diproses'],
                            'menunggu konfirmasi' => ['Wait.svg',   'bg-state-yellow/20 text-state-yellow','Menunggu'],
                            default               => ['Cancel.svg', 'bg-state-red/15 text-state-red',      ucfirst($p->status_pesanan)],
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
    <div class="grid grid-cols-1 gap-6 mb-6">

        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm p-6">
            <p class="font-bold text-brand-dark text-sm mb-4 tracking-wide">Pesanan Hari Ini</p>
            <div class="relative h-64">
                <canvas id="chartPesanan"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm p-6">
            <p class="font-bold text-brand-dark text-sm mb-4 tracking-wide">Pendapatan Minggu Ini</p>
            <div class="relative h-64">
                <canvas id="chartPendapatan"></canvas>
            </div>
        </div>

    </div>

    {{-- ── Footer ── --}}
    <x-slot:pageFooter>
        <footer class="bg-brand-dark px-10 py-12 mt-auto">
            <div class="grid grid-cols-12 gap-8 border-b border-white/10 pb-8">

                {{-- Brand & Info --}}
                <div class="col-span-6 pr-8">
                    <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="Kohvito" class="h-12 w-auto mb-6">
                    <p class="text-[13px] text-white/90 leading-relaxed mb-6">
                        A Coffee, Dining &amp; Lifestyle Space Crafted for People Who Love Good Coffee, Cozy Atmosphere, and Meaningful Daily Experiences.
                    </p>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="text-[13px] text-white/90">Jl Johar No. 72 Pontianak</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <p class="text-[13px] text-white/90">kohvitocafe@gmail.com</p>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="col-span-2">
                    <p class="text-base font-bold text-white mb-5 tracking-wide">Navigation</p>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-[13px] text-white/80 hover:text-white transition-colors">Beranda Admin</a></li>
                        <li><a href="#" class="text-[13px] text-white/80 hover:text-white transition-colors">Kelola Pengguna Kasir</a></li>
                        <li><a href="#" class="text-[13px] text-white/80 hover:text-white transition-colors">Kelola Menu</a></li>
                        <li><a href="#" class="text-[13px] text-white/80 hover:text-white transition-colors">Kelola Kategori Menu</a></li>
                    </ul>
                </div>

                {{-- Visit Us & Reservation --}}
                <div class="col-span-4">
                    <p class="text-base font-bold text-white mb-5 tracking-wide">Visit Us!</p>
                    <div class="flex items-center gap-5 mb-8 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <img src="{{ asset('images/icons/Instagram.svg') }}" alt="" class="w-4 h-4 brightness-0 invert">
                            <span class="text-[13px] text-white/90">kohvito</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            <span class="text-[13px] text-white/90">kohvito_cafe</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <img src="{{ asset('images/icons/Threads instagram.svg') }}" alt="" class="w-4 h-4 brightness-0 invert">
                            <span class="text-[13px] text-white/90">kohvito</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <img src="{{ asset('images/icons/tiktok.svg') }}" alt="" class="w-4 h-4 brightness-0 invert">
                            <span class="text-[13px] text-white/90">kohvito cafe</span>
                        </div>
                    </div>

                    <p class="text-base font-bold text-white mb-4 tracking-wide">Reservation?</p>
                    <a href="https://wa.me/6281348922789" class="inline-flex items-center bg-white rounded-xl px-5 py-2.5 hover:bg-gray-100 transition-colors shadow-sm">
                        <span class="text-[13px] text-brand-dark">Contact Us! <span class="font-bold ml-1">+62 813-4892-2789</span></span>
                    </a>
                </div>

            </div>

            <div class="pt-6 text-center">
                <p class="text-[11px] text-white/70">@2026 Right Reserved. Developed By Pet &amp; Jenn</p>
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
