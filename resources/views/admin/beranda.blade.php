<x-layouts.admin title="Beranda Admin" page-title="Beranda Admin">

    {{-- ── Header: filter + Toko Buka/Tutup ── --}}
    <x-slot:headerEnd>
        <div class="flex flex-wrap items-center gap-2 sm:gap-4">
            {{-- Filter button --}}
            <button type="button" onclick="openFilterModal()"
                    class="flex items-center justify-center transition-colors hover:opacity-80"
                    aria-label="Buka filter tanggal">
                <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </button>

            {{-- Toko Buka / Toko Tutup toggle button (pakai icon SVG dari user) --}}
            @if ($orderStatus === 'buka')
                {{-- State: OPEN — white bg, icon di kiri, click to close store --}}
                <button type="button"
                        id="btn-toggle-store"
                        data-store-state="buka"
                        onclick="openAppModal('confirm-close-store')"
                        class="toggle-store-btn {{ session('success') ? 'toggle-store-btn--success' : '' }} flex items-center gap-2 bg-white border border-black text-brand-black pl-1.5 pr-5 py-1 rounded-full text-sm font-medium shadow-sm hover:bg-gray-50">
                    <img src="{{ asset('images/icons/Toko Buka.svg') }}"
                         class="w-9 h-9 flex-shrink-0" alt="">
                    Toko Buka
                </button>
            @else
                {{-- State: CLOSED — dark maroon bg, icon di kanan, click to open store --}}
                <button type="button"
                        id="btn-toggle-store"
                        data-store-state="tutup"
                        onclick="openAppModal('confirm-open-store')"
                        class="toggle-store-btn {{ session('success') ? 'toggle-store-btn--success' : '' }} flex items-center gap-2 bg-[#380000] text-white pl-5 pr-1.5 py-1 rounded-full text-sm font-medium shadow-sm hover:bg-[#2A0000]">
                    Toko Tutup
                    <img src="{{ asset('images/icons/Toko Tutup.svg') }}"
                         class="w-9 h-9 flex-shrink-0" alt="">
                </button>
            @endif
        </div>
    </x-slot:headerEnd>

    {{-- Confirmation Modals --}}
    <x-confirm-modal
        id="confirm-close-store"
        title="Apakah anda yakin ingin menutup toko?"
        subtitle="Sistem akan dimatikan dan pelanggan tidak dapat mengakses website pemesanan"
        confirmLabel="Ya, Tutup Toko"
        variant="danger"
        :action="route('admin.toggle-order-status')"
        method="POST"
    />

    <x-confirm-modal
        id="confirm-open-store"
        title="Apakah anda yakin ingin membuka toko?"
        subtitle="Sistem akan diaktifkan dan pelanggan dapat mengakses website pemesanan"
        confirmLabel="Ya, Buka Toko"
        variant="success"
        :action="route('admin.toggle-order-status')"
        method="POST"
    />

    {{-- ─── Filter Modal (date range) ─── --}}
    <div id="filter-modal"
        data-confirm-modal
        class="hidden fixed inset-0 z-[60] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all"
        onclick="if(event.target === this) closeAppModal('filter-modal')">
        <div class="kvt-modal-panel relative w-full max-w-[400px] overflow-y-auto rounded-2xl bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.15)] sm:p-6">
            {{-- Header --}}
            <div class="flex items-start justify-between mb-5">
                <h2 class="text-xl font-bold text-[#380000]">Filter</h2>
                <button type="button"
                    class="text-[#380000] hover:text-[#681F1F] transition-colors"
                    onclick="closeAppModal('filter-modal')"
                    aria-label="Tutup filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="filter-form" method="GET" action="{{ route('admin.beranda') }}">
                {{-- Date inputs --}}
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="filter-dari" class="block text-xs text-brand-gray mb-1.5">Dari Tanggal</label>
                        <input type="date"
                            id="filter-dari"
                            name="tanggal_mulai"
                            value="{{ request('tanggal_mulai') }}"
                            class="w-full border border-[#380000] rounded-lg px-3 py-2 text-sm text-[#380000] focus:outline-none focus:ring-2 focus:ring-[#380000] uppercase">
                    </div>
                    <span class="hidden pb-3 text-brand-gray sm:block">—</span>
                    <div class="flex-1">
                        <label for="filter-sampai" class="block text-xs text-brand-gray mb-1.5">Sampai Tanggal</label>
                        <input type="date"
                            id="filter-sampai"
                            name="tanggal_selesai"
                            value="{{ request('tanggal_selesai') }}"
                            class="w-full border border-[#380000] rounded-lg px-3 py-2 text-sm text-[#380000] focus:outline-none focus:ring-2 focus:ring-[#380000] uppercase">
                    </div>
                </div>

                {{-- Footer buttons --}}
                <div class="kvt-modal-actions flex items-center justify-end gap-2">
                    <button type="button"
                        onclick="closeAppModal('filter-modal')"
                        class="bg-[#D0D0D0] text-[#681F1F] px-4 py-2 rounded-lg text-sm font-medium shadow-[0_3px_6px_rgba(0,0,0,0.18)] hover:bg-[#C4C4C4] transition-colors">
                        Batal
                    </button>
                    <button type="button"
                        onclick="openAppModal('confirm-hapus-filter')"
                        class="bg-[#E52E2D] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-[0_3px_6px_rgba(0,0,0,0.18)] hover:bg-[#C92A2A] transition-colors">
                        Hapus Filter
                    </button>
                    <button type="submit"
                        class="bg-[#380000] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-[0_3px_6px_rgba(0,0,0,0.18)] hover:bg-[#2A0000] transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Confirm Hapus Filter ─── --}}
    <x-confirm-modal
        id="confirm-hapus-filter"
        title="Apakah anda yakin ingin menghapus filter?"
        subtitle="Filter akan dihapus dan tanggal kembali ke hari ini."
        confirmLabel="Ya, Hapus Filter"
        cancelLabel="Batal"
        variant="danger"
        onConfirm="hapusFilterTanggal()" />

    <script>
        function openFilterModal() {
            // Reset form values to current request params (in case user cancelled before)
            const dari = '{{ request('tanggal_mulai') }}';
            const sampai = '{{ request('tanggal_selesai') }}';
            document.getElementById('filter-dari').value = dari;
            document.getElementById('filter-sampai').value = sampai;
            openAppModal('filter-modal');
        }

        function hapusFilterTanggal() {
            closeAppModal('confirm-hapus-filter');
            closeAppModal('filter-modal');
            // Redirect to base /admin without query params (today default)
            window.location.href = '{{ route('admin.beranda') }}';
        }
    </script>

    {{-- ── Flash (suppressed when store-status popup will be shown) ── --}}
    @if (session('success') && !session('store_status_changed_to'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Popup: store status changed (BUKA / TUTUP) ── --}}
    @if (session('store_status_changed_to'))
        <x-popup-store-status :status="session('store_status_changed_to')" />
    @endif

    {{-- ── Confirm: Cetak Laporan Kasir (sebelum download) ── --}}
    <x-confirm-modal
        id="confirm-print-laporan"
        title="Cetak Laporan Kasir"
        subtitle="Laporan Kasir akan dicetak dalam bentuk PDF"
        confirmLabel="Ya, Cetak"
        onConfirm="triggerPrintLaporan()" />

    {{-- ── Popup: Berhasil Mencetak Laporan Kasir (after Ya, Cetak) ── --}}
    <x-popup-success id="popup-print-success" heading="Berhasil Mencetak Laporan Kasir" />

    {{-- ── Loading overlay (shown during toggle store form submit) ── --}}
    <x-loading-overlay />

    <script>
        function triggerPrintLaporan() {
            closeAppModal('confirm-print-laporan');
            const a = document.createElement('a');
            a.href = "{{ route('admin.laporan.cetak') }}";
            a.rel = 'noopener';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => openAppModal('popup-print-success'), 700);
        }
    </script>

    {{-- ── Summary Cards ── --}}
    <div class="mb-6 mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" data-anim="stagger">

        {{-- Total Menu --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/coffee.svg') }}" alt=""
                     class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <h6 class="text-[12px] text-brand-black font-bold uppercase">Total Menu</h6>
            </div>
            <div class="flex items-baseline gap-1.5 mt-2">
                <p class="text-4xl font-bold text-brand-black leading-none" data-count-up="{{ $totalMenu }}">{{ $totalMenu }}</p>
                <p class="text-sm font-bold text-brand-black">Menu</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">30 Minuman &nbsp; 20 Makanan</p>
        </div>

        {{-- Total Pengguna Kasir --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/user-group.svg') }}" alt="" class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <p class="text-[11px] text-brand-black font-bold uppercase">Total Pengguna Kasir</p>
            </div>
            <div class="flex items-baseline gap-1.5 mt-2">
                <p class="text-4xl font-bold text-brand-black leading-none" data-count-up="{{ $totalKasir }}">{{ $totalKasir }}</p>
                <p class="text-sm font-bold text-brand-black">Kasir</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">&nbsp;</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/shopping-cart.svg') }}" alt="" class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <p class="text-[11px] text-brand-black font-bold uppercase">Total Transaksi</p>
            </div>
            <div class="flex items-baseline gap-1.5 mt-2">
                <p class="text-4xl font-bold text-brand-black leading-none" data-count-up="{{ $totalTransaksi }}">{{ $totalTransaksi }}</p>
                <p class="text-sm font-bold text-brand-black">Transaksi</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">&nbsp;</p>
        </div>

        {{-- Total Pendapatan Kotor --}}
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4" data-anim-item>
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('images/icons/currency-dollar.svg') }}" alt="" class="w-4 h-4 [filter:brightness(0)_saturate(1)_invert(15%)_sepia(80%)_saturate(600%)_hue-rotate(320deg)_brightness(70%)] opacity-70">
                <p class="text-[11px] text-brand-black font-bold uppercase">Total Pendapatan Kotor</p>
            </div>
            <div class="flex items-baseline gap-1 mt-2">
                <p class="text-lg font-bold text-brand-black">Rp</p>
                <p class="kvt-break-anywhere text-2xl font-bold leading-none text-brand-black sm:text-3xl" data-count-up="{{ $omzetBulanIni }}">{{ number_format($omzetBulanIni, 0, ',', '.') }}</p>
            </div>
            <p class="text-[10px] text-brand-gray mt-2 font-medium">Rp {{ number_format($omzetBulanIni / 30, 0, ',', '.') }} Rata-Rata Pembelian</p>
        </div>
    </div>

    {{-- ── Terlaris Cards ── --}}
    <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-2">

        {{-- Makanan Terlaris --}}
        <div class="flex min-h-24 overflow-hidden rounded-xl border border-brand-gray-extralight bg-white shadow-sm sm:h-24">
            <div class="w-20 bg-brand-dark flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/icons/Food.svg') }}" alt="" class="w-8 h-8 brightness-0 invert">
            </div>
            <div class="flex min-w-0 flex-1 items-center gap-3 px-3 py-3 sm:gap-4 sm:px-4">
                @if ($makananTerlaris?->gambar_menu)
                    @php
                        $imgSrc = str_starts_with($makananTerlaris->gambar_menu, 'http')
                            ? $makananTerlaris->gambar_menu
                            : asset("images/food/{$makananTerlaris->gambar_menu}");
                    @endphp
                    <img src="{{ $imgSrc }}"
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
                        $imgSrc = str_starts_with($minumanTerlaris->gambar_menu, 'http')
                            ? $minumanTerlaris->gambar_menu
                            : asset("images/drink/{$minumanTerlaris->gambar_menu}");
                    @endphp
                    <img src="{{ $imgSrc }}"
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

    {{-- ── Data Pesanan Hari Ini ── --}}
    <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm mb-6">
        <div class="flex flex-col items-start justify-between gap-3 border-b border-brand-gray-extralight px-4 py-4 sm:flex-row sm:items-center sm:px-6">
            <div class="flex items-center gap-2">
                <p class="font-bold text-brand-black">Data Pesanan Hari Ini</p>
            </div>
            <button type="button"
                onclick="openAppModal('confirm-print-laporan')"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-dark px-4 py-2 text-xs font-bold text-white shadow-sm transition-colors hover:bg-opacity-90 sm:w-auto">
                <img src="{{ asset('images/icons/template.svg') }}" alt="" class="w-4 h-4 brightness-0 invert">
                Cetak Laporan Kasir
            </button>
        </div>

        <div class="kvt-scroll-region overflow-x-auto" tabindex="0" aria-label="Data pesanan hari ini">
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

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
        <x-admin-footer />
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

        @if (session('store_status_changed_to'))
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => window.openAppModal && window.openAppModal('popup-store-status'), 120);
        });
        </script>
        @endif

        <script>
        (function () {
            document.querySelectorAll('#confirm-close-store form, #confirm-open-store form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    const btn = document.getElementById('btn-toggle-store');
                    if (!btn || btn.classList.contains('toggle-store-btn--pressing')) return;

                    e.preventDefault();
                    btn.classList.add('toggle-store-btn--pressing');

                    // Close confirm modals & show loading overlay while form submits + page reloads
                    if (window.closeAppModal) {
                        window.closeAppModal('confirm-close-store');
                        window.closeAppModal('confirm-open-store');
                    }
                    if (window.showLoadingOverlay) window.showLoadingOverlay();

                    setTimeout(() => form.submit(), 380);
                }, { once: true });
            });
        })();
        </script>
    </x-slot:scripts>

</x-layouts.admin>
