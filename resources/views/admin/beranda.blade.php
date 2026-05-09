@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Alert --}}
@if (session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-5 mb-6">

    {{-- Total Pesanan Hari Ini --}}
    <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-6 py-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-brand-gray font-medium">Total Pesanan Hari Ini</p>
            <span class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
        </div>
        <p class="text-3xl font-bold text-brand-black">{{ $totalPesananHariIni }}</p>
        <p class="text-xs text-brand-gray mt-1">pesanan lunas hari ini</p>
    </div>

    {{-- Omzet Hari Ini --}}
    <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-6 py-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-brand-gray font-medium">Omzet Hari Ini</p>
            <span class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
        <p class="text-3xl font-bold text-brand-black">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</p>
        <p class="text-xs text-brand-gray mt-1">pendapatan hari ini</p>
    </div>

    {{-- Pesanan Diproses --}}
    <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm px-6 py-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-brand-gray font-medium">Pesanan Diproses</p>
            <span class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
        <p class="text-3xl font-bold text-brand-black">{{ $pesananDiproses }}</p>
        <p class="text-xs text-brand-gray mt-1">sedang dalam antrian</p>
    </div>

</div>

{{-- Chart + Actions Row --}}
<div class="grid grid-cols-3 gap-5">

    {{-- Grafik Omzet --}}
    <div class="col-span-2 bg-white rounded-xl border border-brand-gray-extralight shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="font-semibold text-brand-black">Tren Omzet</p>
                <p class="text-xs text-brand-gray mt-0.5">30 hari terakhir</p>
            </div>
            <p class="text-sm font-semibold text-brand-black">
                Bulan ini: Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}
            </p>
        </div>
        <div class="relative h-56">
            <canvas id="omzetChart"></canvas>
            <p id="omzetChartError" class="hidden text-sm text-brand-gray text-center pt-20">
                Gagal memuat data grafik.
            </p>
        </div>
    </div>

    {{-- Actions Panel --}}
    <div class="flex flex-col gap-5">

        {{-- Cetak Laporan --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm p-5 flex-1">
            <p class="font-semibold text-brand-black mb-1">Unduh Laporan</p>
            <p class="text-xs text-brand-gray mb-4">Download laporan kasir dalam format PDF.</p>
            <form action="{{ route('admin.laporan.cetak') }}" method="GET" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-brand-gray-dark mb-1">Dari tanggal</label>
                    <input type="date" name="tanggal_mulai"
                           value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                           class="w-full text-sm border border-brand-gray-extralight rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red">
                </div>
                <div>
                    <label class="block text-xs font-medium text-brand-gray-dark mb-1">Sampai tanggal</label>
                    <input type="date" name="tanggal_selesai"
                           value="{{ now()->format('Y-m-d') }}"
                           class="w-full text-sm border border-brand-gray-extralight rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red">
                </div>
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-brand-dark text-white text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-brand-red transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh PDF
                </button>
            </form>
        </div>

        {{-- Close Order Toggle --}}
        <div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="font-semibold text-brand-black">Status Pemesanan</p>
                    <p class="text-xs text-brand-gray mt-0.5">Kontrol penerimaan pesanan konsumen.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full
                    {{ $orderStatus === 'buka' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $orderStatus === 'buka' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    {{ $orderStatus === 'buka' ? 'Buka' : 'Tutup' }}
                </span>
            </div>
            <form action="{{ route('admin.toggle-order-status') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 text-sm font-medium rounded-lg px-4 py-2.5 transition-colors
                            {{ $orderStatus === 'buka'
                                ? 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'
                                : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' }}">
                    @if ($orderStatus === 'buka')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Tutup Pemesanan
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Buka Pemesanan
                    @endif
                </button>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const canvas = document.getElementById('omzetChart');
    const errorEl = document.getElementById('omzetChartError');
    const ctx = canvas.getContext('2d');

    fetch('{{ route('admin.beranda.data') }}')
        .then(r => r.json())
        .then(data => {
            const labels = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });
            const values = data.map(d => Number(d.total));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Omzet',
                        data: values,
                        borderColor: '#681F1F',
                        backgroundColor: 'rgba(104, 31, 31, 0.08)',
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
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#808080' },
                        },
                        y: {
                            grid: { color: '#E6E6E6' },
                            ticks: {
                                font: { size: 11 },
                                color: '#808080',
                                callback: v => 'Rp ' + (v / 1000).toLocaleString('id-ID') + 'k',
                            },
                            beginAtZero: true,
                        }
                    }
                }
            });
        })
        .catch(() => {
            canvas.classList.add('hidden');
            errorEl.classList.remove('hidden');
        });
})();
</script>
@endpush
