<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-4 py-4 sticky top-0 z-10">
        <div class="max-w-lg mx-auto flex items-center gap-3">
            @if (session('id_meja_no'))
                <a href="{{ route('konsumen.beranda', session('id_meja_no')) }}"
                   class="text-gray-500 hover:text-gray-700 transition-colors p-1 -ml-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Keranjang</h1>
                @if (session('id_meja_no'))
                    <p class="text-xs text-gray-500 mt-0.5">Meja {{ session('id_meja_no') }}</p>
                @endif
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-6 pb-32">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             STATE 1: Order just placed — show "Lanjutkan Pembayaran"
             ═══════════════════════════════════════════════════════════ --}}
        @if (empty($keranjang) && session('no_pesanan_baru'))
            <div class="text-center py-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 mb-1">Pesanan Dibuat!</h2>
                <p class="text-sm text-gray-500 mb-1">No. Pesanan:</p>
                <p class="text-base font-mono font-semibold text-amber-700 mb-6">{{ session('no_pesanan_baru') }}</p>

                <form method="POST" action="{{ route('konsumen.bayar') }}">
                    @csrf
                    <input type="hidden" name="no_pesanan" value="{{ session('no_pesanan_baru') }}">
                    <button type="submit"
                            class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                        Lanjutkan Pembayaran
                    </button>
                </form>
            </div>

        {{-- ═══════════════════════════════════════════════
             STATE 2: Cart is empty, no pending order
             ═══════════════════════════════════════════════ --}}
        @elseif (empty($keranjang))
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-700 mb-2">Keranjang kosong</h2>
                <p class="text-sm text-gray-500 mb-6">Belum ada item di keranjang.</p>
                @if (session('id_meja_no'))
                    <a href="{{ route('konsumen.beranda', session('id_meja_no')) }}"
                       class="inline-block bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                        Lihat Menu
                    </a>
                @endif
            </div>

        {{-- ═══════════════════════════════════════════════
             STATE 3: Cart has items
             ═══════════════════════════════════════════════ --}}
        @else
            {{-- Cart Items --}}
            <div class="space-y-3 mb-6">
                @foreach ($keranjang as $idMenu => $item)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                        {{-- Item Header: name + subtotal --}}
                        <div class="px-4 pt-4 pb-3 flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 leading-snug truncate">
                                    {{ $item['nama_menu'] }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Rp {{ number_format($item['harga'], 0, ',', '.') }} / porsi
                                </p>
                            </div>
                            <p class="text-sm font-bold text-amber-700 flex-shrink-0">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Quantity Controls --}}
                        <div class="px-4 pb-3 flex items-center gap-2">

                            {{-- Decrease (jumlah - 1; will remove if reaches 0) --}}
                            <form method="POST" action="{{ route('konsumen.keranjang.update') }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                                <input type="hidden" name="jumlah" value="{{ $item['jumlah'] - 1 }}">
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors text-base font-bold leading-none">
                                    &minus;
                                </button>
                            </form>

                            <span class="w-8 text-center text-sm font-semibold text-gray-800">
                                {{ $item['jumlah'] }}
                            </span>

                            {{-- Increase --}}
                            <form method="POST" action="{{ route('konsumen.keranjang.update') }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                                <input type="hidden" name="jumlah" value="{{ $item['jumlah'] + 1 }}">
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors text-base font-bold leading-none">
                                    &#43;
                                </button>
                            </form>

                            {{-- Remove --}}
                            <form method="POST" action="{{ route('konsumen.keranjang.update') }}" class="ml-auto">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                                <input type="hidden" name="jumlah" value="0">
                                <button type="submit"
                                        class="text-xs text-red-500 hover:text-red-700 transition-colors px-2 py-1">
                                    Hapus
                                </button>
                            </form>
                        </div>

                        {{-- Notes Form --}}
                        <form method="POST" action="{{ route('konsumen.keranjang.notes') }}"
                              class="px-4 pb-4 border-t border-gray-100 pt-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                            <label class="block text-xs text-gray-500 mb-1" for="catatan-{{ $idMenu }}">
                                Catatan (opsional)
                            </label>
                            <div class="flex gap-2">
                                <input type="text"
                                       id="catatan-{{ $idMenu }}"
                                       name="catatan"
                                       value="{{ $item['catatan'] ?? '' }}"
                                       maxlength="255"
                                       placeholder="Contoh: tanpa gula, es sedikit…"
                                       class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent placeholder-gray-400">
                                <button type="submit"
                                        class="flex-shrink-0 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-colors">
                                    Simpan
                                </button>
                            </div>
                        </form>

                    </div>
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="bg-white rounded-xl border border-gray-200 px-4 py-4 mb-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Total</span>
                    <span class="text-base font-bold text-amber-700">
                        Rp {{ number_format($totalHarga, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Checkout Form --}}
            <div class="bg-white rounded-xl border border-gray-200 px-4 py-4">
                <h2 class="text-sm font-semibold text-gray-800 mb-3">Informasi Pemesan</h2>
                <form method="POST" action="{{ route('konsumen.keranjang.pesan') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="nama_konsumen" class="block text-xs text-gray-500 mb-1">
                            Nama Pemesan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nama_konsumen"
                               name="nama_konsumen"
                               value="{{ old('nama_konsumen') }}"
                               maxlength="255"
                               required
                               placeholder="Masukkan nama Anda"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent placeholder-gray-400 @error('nama_konsumen') border-red-400 @enderror">
                        @error('nama_konsumen')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
                        Pesan Sekarang
                    </button>
                </form>
            </div>

        @endif

    </main>

</body>
</html>
