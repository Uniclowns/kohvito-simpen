<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Kasir — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900">Dashboard Kasir</h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Keluar</button>
            </form>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-6 py-8">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-xl font-semibold text-gray-800 mb-6">Ringkasan Pesanan Hari Ini</h2>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <p class="text-3xl font-bold text-yellow-500">{{ $menunggu }}</p>
                <p class="text-sm text-gray-500 mt-1">Menunggu Konfirmasi</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <p class="text-3xl font-bold text-blue-500">{{ $diproses }}</p>
                <p class="text-sm text-gray-500 mt-1">Sedang Diproses</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <p class="text-3xl font-bold text-green-500">{{ $selesai }}</p>
                <p class="text-sm text-gray-500 mt-1">Selesai</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('kasir.pesanan.index') }}"
               class="block bg-white rounded-xl border border-gray-200 p-6 hover:border-gray-300 transition-all">
                <h3 class="font-semibold text-gray-800">Kelola Pesanan</h3>
                <p class="text-sm text-gray-500 mt-1">Terima dan proses pesanan masuk</p>
            </a>
            <a href="{{ route('kasir.histori.index') }}"
               class="block bg-white rounded-xl border border-gray-200 p-6 hover:border-gray-300 transition-all">
                <h3 class="font-semibold text-gray-800">Histori Pesanan</h3>
                <p class="text-sm text-gray-500 mt-1">Lihat pesanan selesai hari ini</p>
            </a>
        </div>
    </div>
</body>
</html>
