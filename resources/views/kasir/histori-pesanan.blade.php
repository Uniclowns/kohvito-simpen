<x-layouts.kasir title="Histori Pesanan" page-title="Histori Pesanan">

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <form method="GET" action="{{ route('kasir.histori.index') }}" class="flex-1 flex gap-2">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari no pesanan atau nama konsumen..."
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-300 focus:ring">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-700">Cari</button>
                @if ($search)
                    <a href="{{ route('kasir.histori.index') }}"
                       class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Reset</a>
                @endif
            </form>
            @if ($pesanans->isNotEmpty())
                <a href="{{ route('kasir.histori.cetak-semua') }}" target="_blank"
                   class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 whitespace-nowrap">
                    Cetak Rekap
                </a>
            @endif
        </div>

        @if ($pesanans->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <p class="text-gray-500">Belum ada pesanan selesai hari ini.</p>
            </div>
        @else
            <div class="mb-4 bg-white rounded-xl border border-gray-200 p-4 flex justify-between items-center">
                <span class="text-sm text-gray-600">{{ $pesanans->count() }} pesanan selesai</span>
                <span class="text-sm font-semibold text-gray-800">
                    Total: Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                </span>
            </div>

            <div class="space-y-3">
                @foreach ($pesanans as $pesanan)
                    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="font-semibold text-gray-800">{{ $pesanan->no_pesanan }}</span>
                                <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Selesai</span>
                            </div>
                            <p class="text-sm text-gray-500">
                                Meja {{ $pesanan->meja?->no_meja ?? '-' }} &middot; {{ $pesanan->nama_konsumen }}
                            </p>
                            <p class="text-sm text-gray-400 mt-0.5">
                                {{ $pesanan->tgl_pembayaran?->translatedFormat('H:i') ?? '-' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-gray-800">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('kasir.histori.detail', $pesanan->no_pesanan) }}"
                               class="px-3 py-1.5 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700">
                                Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-slot:pageFooter>
        <x-kasir-footer />
    </x-slot:pageFooter>

</x-layouts.kasir>
