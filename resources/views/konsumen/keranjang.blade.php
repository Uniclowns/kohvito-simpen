<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-light min-h-screen text-brand-black font-sans pb-32">

    <!-- Header Premium -->
    <header class="bg-brand-dark sticky top-0 z-20 shadow-md border-b border-brand-red/10">
        <div class="max-w-md mx-auto px-4 py-4 flex items-center gap-4">
            @if (session('id_meja_no'))
                <a href="{{ route('konsumen.beranda', session('id_meja_no')) }}"
                   class="bg-brand-red hover:bg-brand-red/80 text-white rounded-full p-2.5 transition-all shadow-sm flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif
            <div>
                <h1 class="text-sm font-bold text-brand-white leading-none">Keranjang</h1>
                @if (session('id_meja_no'))
                    <p class="text-[10px] text-brand-red-muted mt-0.5 font-medium">Meja {{ session('id_meja_no') }} &bull; Katalog Menu</p>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto px-4 py-6">

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-xs px-4 py-3 rounded-xl flex items-center gap-2.5 animate-pulse">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3 rounded-xl flex items-center gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             STATE 1: Order just placed — show "Lanjutkan Pembayaran"
             ═══════════════════════════════════════════════════════════ --}}
        @if (empty($keranjang) && session('no_pesanan_baru'))
            <div class="text-center py-10 bg-white border border-brand-gray-extralight rounded-[32px] p-8 shadow-sm">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-50 rounded-full mb-6 border-4 border-green-100 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-lg font-black text-brand-black mb-2">Pesanan Berhasil Dibuat!</h2>
                <p class="text-xs text-brand-gray mb-1">Nomor Transaksi:</p>
                <p class="text-sm font-mono font-black text-brand-red bg-brand-light py-2 px-4 rounded-xl inline-block mb-8 border border-brand-red/10 select-all">{{ session('no_pesanan_baru') }}</p>

                <form method="POST" action="{{ route('konsumen.bayar') }}">
                    @csrf
                    <input type="hidden" name="no_pesanan" value="{{ session('no_pesanan_baru') }}">
                    <button type="submit"
                            class="w-full bg-brand-dark hover:bg-brand-red text-white font-black py-4 px-6 rounded-2xl transition-all shadow-lg uppercase tracking-wider transform active:scale-95 text-xs">
                        Lanjutkan Pembayaran
                    </button>
                </form>
            </div>

        {{-- ═══════════════════════════════════════════════
             STATE 2: Cart is empty, no pending order
             ═══════════════════════════════════════════════ --}}
        @elseif (empty($keranjang))
            <div class="text-center py-16 bg-white border border-brand-gray-extralight rounded-[32px] p-8 shadow-sm">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-light rounded-full mb-6 border-4 border-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-brand-gray" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="text-base font-black text-brand-black mb-2">Keranjang Belanja Kosong</h2>
                <p class="text-xs text-brand-gray mb-8">Pilih hidangan lezat kami di katalog terlebih dahulu.</p>
                @if (session('id_meja_no'))
                    <a href="{{ route('konsumen.beranda', session('id_meja_no')) }}"
                       class="inline-block bg-brand-dark hover:bg-brand-red text-white text-xs font-black px-8 py-3.5 rounded-full transition-all shadow-md uppercase tracking-wider transform active:scale-95">
                        Lihat Katalog Menu
                    </a>
                @endif
            </div>

        {{-- ═══════════════════════════════════════════════
             STATE 3: Cart has items
             ═══════════════════════════════════════════════ --}}
        @else
            <!-- Cart Items List -->
            <div class="space-y-4 mb-6">
                @foreach ($keranjang as $idMenu => $item)
                    @php
                        $menuModel = \App\Models\Menu::find($idMenu);
                    @endphp
                    <div class="bg-white rounded-[24px] border border-brand-gray-extralight overflow-hidden shadow-sm flex flex-col p-4">

                        <!-- Item Header: Name & Image & Price -->
                        <div class="flex gap-4 items-start pb-4 border-b border-brand-gray-extralight/50">
                            <!-- Thumbnail Gambar -->
                            @if ($menuModel && $menuModel->gambar_menu)
                                @php
                                    $imgType = $menuModel->jenis_menu === 'Makanan' ? 'food' : 'drink';
                                    $imgSrc = str_starts_with($menuModel->gambar_menu, 'http')
                                        ? $menuModel->gambar_menu
                                        : asset("images/{$imgType}/{$menuModel->gambar_menu}");
                                @endphp
                                <img src="{{ $imgSrc }}" alt="{{ $item['nama_menu'] }}" class="w-14 h-14 object-cover rounded-xl flex-shrink-0 shadow-sm">
                            @else
                                <div class="w-14 h-14 bg-brand-light flex items-center justify-center rounded-xl flex-shrink-0">
                                    <span class="text-[9px] text-brand-gray font-bold">No Image</span>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="text-xs font-bold text-brand-black leading-snug truncate">
                                    {{ $item['nama_menu'] }}
                                </h3>
                                <p class="text-[10px] text-brand-gray font-semibold mt-0.5">
                                    Rp {{ number_format($item['harga'], 0, ',', '.') }} / porsi
                                </p>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-xs font-black text-brand-red">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Quantity Adjuster & Remove Button -->
                        <div class="flex items-center gap-3 pt-4">
                            <div class="flex items-center bg-brand-light border border-brand-red-muted/20 rounded-xl px-1.5 py-1 shadow-inner">
                                <!-- Decrease -->
                                <form method="POST" action="{{ route('konsumen.keranjang.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                                    <input type="hidden" name="jumlah" value="{{ $item['jumlah'] - 1 }}">
                                    <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-brand-gray-extralight text-brand-dark hover:bg-brand-light font-black transition-colors text-xs">&minus;</button>
                                </form>

                                <span class="w-8 text-center text-xs font-extrabold text-brand-black">
                                    {{ $item['jumlah'] }}
                                </span>

                                <!-- Increase -->
                                <form method="POST" action="{{ route('konsumen.keranjang.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                                    <input type="hidden" name="jumlah" value="{{ $item['jumlah'] + 1 }}">
                                    <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-brand-gray-extralight text-brand-dark hover:bg-brand-light font-black transition-colors text-xs">&#43;</button>
                                </form>
                            </div>

                            <!-- Remove Button -->
                            <form method="POST" action="{{ route('konsumen.keranjang.update') }}" class="ml-auto">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                                <input type="hidden" name="jumlah" value="0">
                                <button type="submit"
                                        class="text-[10px] text-brand-gray hover:text-brand-red font-bold transition-colors px-2 py-1 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>

                        <!-- Notes Form -->
                        <form method="POST" action="{{ route('konsumen.keranjang.notes') }}"
                              class="border-t border-brand-gray-extralight/50 mt-4 pt-3.5">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id_menu" value="{{ $idMenu }}">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-3 h-3 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </span>
                                    <input type="text"
                                           name="catatan"
                                           value="{{ $item['catatan'] ?? '' }}"
                                           maxlength="255"
                                           placeholder="Tambah catatan khusus..."
                                           class="w-full pl-8 pr-3 py-2 bg-brand-light border border-brand-gray-extralight rounded-xl text-[10px] focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-transparent transition-all placeholder-brand-gray/50 font-semibold text-brand-black">
                                </div>
                                <button type="submit"
                                        class="flex-shrink-0 text-[9px] font-black uppercase tracking-wider bg-brand-dark/10 hover:bg-brand-dark hover:text-white text-brand-dark px-3 py-2 rounded-xl transition-all shadow-sm transform active:scale-95">
                                    Simpan
                                </button>
                            </div>
                        </form>

                    </div>
                @endforeach
            </div>

            <!-- Informasi Pemesan Card -->
            <div class="bg-white rounded-[24px] border border-brand-gray-extralight p-5 mb-8 shadow-sm">
                <h2 class="text-xs font-black text-brand-black uppercase tracking-widest mb-4">Informasi Pemesan</h2>
                <form method="POST" action="{{ route('konsumen.keranjang.pesan') }}">
                    @csrf
                    <div>
                        <label for="nama_konsumen" class="block text-[9px] font-black text-brand-gray uppercase tracking-widest mb-1.5">
                            Nama Lengkap <span class="text-brand-red">*</span>
                        </label>
                        <input type="text"
                               id="nama_konsumen"
                               name="nama_konsumen"
                               value="{{ old('nama_konsumen') }}"
                               maxlength="255"
                               required
                               placeholder="Contoh: Budi Santoso"
                               class="w-full text-xs font-semibold border border-brand-gray-extralight rounded-xl px-4 py-3 bg-brand-light focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-transparent transition-all placeholder-brand-gray/40 @error('nama_konsumen') border-red-400 @enderror">
                        @error('nama_konsumen')
                            <p class="text-[10px] text-brand-red font-bold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sticky Bottom Sheet for Checkout Summary & Submit -->
                    <div class="fixed bottom-0 inset-x-0 bg-white border-t border-brand-gray-extralight rounded-t-[32px] shadow-2xl p-5 z-20 max-w-md mx-auto">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <div>
                                <p class="text-[9px] text-brand-gray uppercase tracking-widest font-black">Total Biaya</p>
                                <p class="text-base font-black text-brand-red">
                                    Rp {{ number_format($totalHarga, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-brand-gray uppercase tracking-widest font-black">Total Porsi</p>
                                <p class="text-xs font-black text-brand-black">
                                    {{ $cartCount }} Porsi
                                </p>
                            </div>
                        </div>
                        
                        <button type="submit"
                                class="w-full bg-brand-dark hover:bg-brand-red text-white font-black py-4 px-4 rounded-2xl transition-all shadow-lg uppercase tracking-widest transform active:scale-95 text-xs">
                            Pesan &amp; Kirim Sekarang
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </main>

</body>
</html>
