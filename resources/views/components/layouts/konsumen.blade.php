@props([
    'title' => config('app.name'),
    'bodyClass' => 'min-h-screen bg-[#F6F6F6] font-sans text-brand-black kvt-konsumen-mobile-view',
    'viewport' => 'width=device-width, initial-scale=1, viewport-fit=cover',
    'themeColor' => '#460001',
])

@php
    // Membaca nomor meja dari session global konsumen
    $mejaNo = session('id_meja_no');

    // Menghitung total porsi item belanjaan yang aktif di keranjang session
    $cartCount = array_sum(array_column(session('keranjang', []), 'jumlah'));

    // Mendeteksi secara cerdas rute aktif saat ini untuk pencahayaan status menu navigasi desktop
    $routeName = request()->route()?->getName();
    $activeTab = '';

    if ($routeName) {
        if (str_contains($routeName, 'beranda') || str_contains($routeName, 'menu')) {
            $activeTab = 'menu';
        } elseif (str_contains($routeName, 'pesanan')) {
            $activeTab = 'pesanan';
        } elseif (str_contains($routeName, 'keranjang')) {
            $activeTab = 'keranjang';
        } elseif (str_contains($routeName, 'lacak') || str_contains($routeName, 'pembayaran') || str_contains($routeName, 'bayar')) {
            $activeTab = 'lacak';
        }
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="{{ $viewport }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $themeColor }}">
    <title>{{ $title }}</title>

    {{-- Memuat aset CSS & JS menggunakan kompiler Vite super cepat --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="{{ $bodyClass }}">

    {{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  PREMIUM DESKTOP TOP HEADER  —  Khusus layar lebar (lg:block)   ║
     ║  Mengganti navigasi bawah melayang agar tidak terkesan memaksa   ║
     ║  Desain Glassmorphism gelap mewah dengan aksen emas/merah        ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
    <header class="fixed top-0 inset-x-0 z-50 hidden md:block bg-brand-dark/95 backdrop-blur-md border-b border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.15)] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">

            {{-- Bagian Kiri: Logo & Nama Kafe --}}
            <a href="{{ $mejaNo ? route('konsumen.beranda', $mejaNo) : '#' }}" class="flex items-center gap-3.5 active:scale-[0.98] transition duration-200">
                <div class="h-12 w-auto flex items-center justify-center p-0.5 bg-white/5 rounded-xl border border-white/10 shadow-md">
                    <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="Kohvito Logo" class="h-10 w-auto object-contain">
                </div>
                <div class="flex flex-col">
                    <span class="text-white text-lg font-bold tracking-wider font-serif uppercase leading-none">Kohvito</span>
                    <span class="text-brand-red-muted text-[10px] tracking-[1.5px] uppercase mt-0.5">Café & Space</span>
                </div>
            </a>

            {{-- Bagian Tengah: Tautan Navigasi Interaktif --}}
            <nav class="flex items-center gap-1.5">
                @if($mejaNo)
                    <a href="{{ route('konsumen.beranda', $mejaNo) }}"
                       class="relative px-5 py-2.5 rounded-xl text-sm font-bold tracking-wide transition-all duration-200 {{ $activeTab === 'menu' ? 'text-[#E52E2D] bg-white/5' : 'text-white/80 hover:text-white hover:bg-white/5' }}">
                        Menu
                        @if($activeTab === 'menu')
                            <span class="absolute bottom-0 inset-x-5 h-0.5 bg-[#E52E2D] rounded-full"></span>
                        @endif
                    </a>
                @endif

                <a href="{{ route('konsumen.pesanan') }}"
                   class="relative px-5 py-2.5 rounded-xl text-sm font-bold tracking-wide transition-all duration-200 {{ $activeTab === 'pesanan' ? 'text-[#E52E2D] bg-white/5' : 'text-white/80 hover:text-white hover:bg-white/5' }}">
                    Pesanan
                    @if($activeTab === 'pesanan')
                        <span class="absolute bottom-0 inset-x-5 h-0.5 bg-[#E52E2D] rounded-full"></span>
                    @endif
                </a>

                <a href="{{ route('konsumen.keranjang') }}"
                   class="relative px-5 py-2.5 rounded-xl text-sm font-bold tracking-wide transition-all duration-200 {{ $activeTab === 'keranjang' ? 'text-[#E52E2D] bg-white/5' : 'text-white/80 hover:text-white hover:bg-white/5' }}">
                    <div class="flex items-center gap-2">
                        <span>Keranjang</span>
                        @if($cartCount > 0)
                            <span class="bg-[#E52E2D] text-white text-[10px] font-bold h-5 min-w-5 px-1.5 rounded-full flex items-center justify-center shadow-md animate-pulse">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </div>
                    @if($activeTab === 'keranjang')
                        <span class="absolute bottom-0 inset-x-5 h-0.5 bg-[#E52E2D] rounded-full"></span>
                    @endif
                </a>

                <a href="{{ route('konsumen.lacak') }}"
                   class="relative px-5 py-2.5 rounded-xl text-sm font-bold tracking-wide transition-all duration-200 {{ $activeTab === 'lacak' ? 'text-[#E52E2D] bg-white/5' : 'text-white/80 hover:text-white hover:bg-white/5' }}">
                    Lacak Pesanan
                    @if($activeTab === 'lacak')
                        <span class="absolute bottom-0 inset-x-5 h-0.5 bg-[#E52E2D] rounded-full"></span>
                    @endif
                </a>
            </nav>

            {{-- Bagian Kanan: Badge VIP Nomor Meja Aktif --}}
            <div class="flex items-center gap-3">
                @if($mejaNo)
                    <div class="bg-brand-red/35 border border-[#E52E2D]/40 rounded-2xl px-4.5 py-2.5 flex items-center gap-2.5 shadow-inner">
                        <div class="w-2.5 h-2.5 rounded-full bg-state-green animate-pulse"></div>
                        <span class="text-white text-xs font-bold tracking-[1px] font-sans uppercase">Meja {{ $mejaNo }}</span>
                    </div>
                @else
                    <div class="bg-white/5 border border-white/10 rounded-2xl px-4.5 py-2.5 flex items-center gap-2 shadow-inner opacity-75">
                        <span class="text-white/85 text-xs font-bold tracking-[1px] font-sans uppercase">Kohvito Guest</span>
                    </div>
                @endif
            </div>
        </div>
    </header>

    {{-- Render slot halaman pembungkus --}}
    {{ $slot }}

    @stack('scripts')
</body>
</html>
