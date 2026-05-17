<x-layouts.kasir title="Detail Pesanan" page-title="Detail Pesanan">

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('kasir.pesanan.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700 mb-4">
            ← Kelola Pesanan
        </a>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $pesanan->no_pesanan }}</h2>
                    @if ($pesanan->status_pesanan === 'menunggu konfirmasi')
                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Menunggu Konfirmasi</span>
                    @else
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Sedang Diproses</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500">
                    Meja {{ $pesanan->meja?->no_meja ?? '-' }} &middot; {{ $pesanan->nama_konsumen }}
                </p>
                <p class="text-sm text-gray-400">
                    {{ $pesanan->tgl_pembayaran?->format('d M Y, H:i') ?? '-' }}
                </p>
            </div>

            <div class="p-6 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Item Pesanan</h3>
                <div class="space-y-3">
                    @foreach ($pesanan->detailPesanan as $detail)
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $detail->menu?->nama_menu ?? 'Menu' }} &times; {{ $detail->jumlah }}
                                </p>
                                @if ($detail->catatan)
                                    <p class="text-xs text-gray-400 mt-0.5">Catatan: {{ $detail->catatan }}</p>
                                @endif
                            </div>
                            <span class="text-sm text-gray-700">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <span class="font-semibold text-gray-800">Total</span>
                <span class="font-bold text-gray-900 text-lg">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>

            <div class="p-6 flex items-center gap-3">
                <a href="{{ route('kasir.pesanan.cetak', $pesanan->no_pesanan) }}"
                   target="_blank"
                   class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cetak Struk
                </a>

                @if ($pesanan->status_pesanan === 'menunggu konfirmasi')
                    <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Terima Pesanan
                        </button>
                    </form>
                @elseif ($pesanan->status_pesanan === 'diproses')
                    <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Tandai Selesai
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</x-layouts.kasir>
