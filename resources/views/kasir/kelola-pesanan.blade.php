<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pesanan — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('kasir.beranda') }}" class="text-sm text-gray-500 hover:text-gray-700">← Dashboard</a>
            <h1 class="text-lg font-semibold text-gray-900">Kelola Pesanan</h1>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Keluar</button>
        </form>
    </nav>

    <div class="max-w-4xl mx-auto px-6 py-8">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($pesanans->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <p class="text-gray-500">Tidak ada pesanan aktif saat ini.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($pesanans as $pesanan)
                    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="font-semibold text-gray-800">{{ $pesanan->no_pesanan }}</span>
                                @if ($pesanan->status_pesanan === 'menunggu konfirmasi')
                                    <span class="px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Menunggu</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Diproses</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500">
                                Meja {{ $pesanan->meja?->no_meja ?? '-' }} &middot; {{ $pesanan->nama_konsumen }}
                            </p>
                            <p class="text-sm text-gray-400 mt-0.5">
                                {{ $pesanan->tgl_pembayaran?->format('H:i') ?? '-' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-gray-800">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('kasir.pesanan.detail', $pesanan->no_pesanan) }}"
                               class="px-3 py-1.5 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700">
                                Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
