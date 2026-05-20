    <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#460001">
    <title>Pesan Menu Anti Ribet — Meja {{ $meja->no_meja }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* ===== Splash sequence (Figma 648-8923 → 792-11529) ===== */
        @keyframes splash-dot-expand {
            0%   { transform: scale(1); }
            55%  { transform: scale(1); }
            100% { transform: scale(70); }
        }
        @keyframes splash-content-in {
            0%   { opacity: 0; transform: translateY(8px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes splash-content-out {
            0%   { opacity: 1; }
            100% { opacity: 0; }
        }
        #splash-overlay.is-running #splash-dot {
            animation: splash-dot-expand 1.5s cubic-bezier(0.7, 0, 0.3, 1) forwards;
        }
        #splash-overlay.is-running #splash-content {
            animation: splash-content-in 600ms ease-out 1.2s forwards;
        }
        #splash-overlay.is-leaving {
            animation: splash-content-out 400ms ease-in forwards;
            pointer-events: none;
        }

        /* ===== Card overlay gradients (Figma cards) ===== */
        .card-photo-bottom-fade {
            background-image: linear-gradient(180.96deg, rgba(104,31,31,0) 38.92%, #681F1F 83.41%);
        }
        .card-info-fade {
            background-image: linear-gradient(178.9deg, rgba(104,31,31,0) 1.37%, #681F1F 22.99%);
        }

        /* ===== Header gradient (top dark-red wash that fades down) ===== */
        .header-fade {
            background: linear-gradient(to bottom, #460001 44%, rgba(70,0,1,0) 66.85%);
        }

        /* ===== Sticky bar appears when scrolled ===== */
        #sticky-search {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        #sticky-search.hidden-sticky {
            opacity: 0;
            transform: translateY(-12px);
            pointer-events: none;
        }

        .text-mix-dodge { mix-blend-mode: color-dodge; }

        /* Hide hero when in scrolled state */
        body.is-scrolled .hero-headline {
            opacity: 0;
            transform: translateY(-6px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Nav active styling */
        .nav-item.is-active { background: rgba(255,255,255,0.5); border-radius: 9px; }
        .nav-item.is-active .nav-label { color: #460001; }
        .nav-item.is-active .nav-icon {
            filter: brightness(0) saturate(100%) invert(7%) sepia(86%) saturate(5485%) hue-rotate(348deg) brightness(64%) contrast(112%);
        }

        .nav-glass {
            background-color: #460001;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        }
        .nav-safe-bottom { padding-bottom: max(10px, env(safe-area-inset-bottom)); }
        .safe-top { padding-top: max(0px, env(safe-area-inset-top)); }
    </style>
</head>
<body class="bg-[#F6F6F6] min-h-screen text-brand-black font-sans">

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  SPLASH OVERLAY  —  Figma 648-8923 + 792-11529                  ║
         ║  Putih → titik merah → meledak jadi merah → mascot + greeting  ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <div id="splash-overlay" class="hidden fixed inset-0 z-[100] overflow-hidden bg-white">
        <div id="splash-dot" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-brand-dark"></div>
        <div id="splash-content" class="absolute inset-0 flex flex-col items-center justify-center opacity-0">
            <img src="{{ asset('images/logo/KOHVITO LOGO ONLY WHITE.png') }}"
                 alt="Kohvito"
                 class="w-[142px] h-auto drop-shadow-[0_4px_16px_rgba(0,0,0,0.25)]">
            <p class="mt-6 text-white text-2xl font-bold tracking-[0.06em]">Selamat Datang!</p>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  HERO SECTION (Top 275px)                                       ║
         ║  Photo background + dark-red header bar + "Pesan Menu" headline ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    @php
        $heroMenu = null;
        foreach ($kategoris as $k) {
            foreach ($k->menus as $m) {
                if ($m->gambar_menu) { $heroMenu = $m; break 2; }
            }
        }
    @endphp
    <header class="relative w-full overflow-hidden">
        <div class="relative h-[275px] w-full">
                <img src="{{ asset('images/bg/kvt-banner.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">  

            {{-- Dark wash so text is readable --}}
            <div class="absolute inset-0 bg-brand-dark/55"></div>

            {{-- Bottom fade into page bg --}}
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-b from-transparent to-[#F6F6F6]"></div>
        </div>

        {{-- Top fade (header bar background) --}}
        <div class="absolute inset-x-0 top-0 h-[89px] header-fade pointer-events-none"></div>

        {{-- Top bar: Selamat Datang / Mascot / Meja --}}
        <div class="absolute inset-x-0 top-0 safe-top">
            <div class="flex items-center justify-between px-[18px] pt-[14px] py-3">
                <p class="flex-1 text-white text-xs font-bold tracking-wide capitalize">
                    Selamat Datang!
                </p>
                <div class="w-6 h-6 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}"
                         alt="Kohvito" class="w-full h-full object-contain">
                </div>
                <p class="flex-1 text-white text-sm font-bold tracking-wide text-right capitalize">
                    Meja {{ $meja->no_meja }}
                </p>
            </div>
        </div>

        {{-- Hero headline --}}
        <div class="hero-headline absolute left-[18px] right-[18px] top-[117px]">
            <p class="text-mix-dodge text-white/75 text-[36px] leading-[40px] font-bold tracking-[0.05em]">
                Pesan Menu
            </p>
            <p class="text-mix-dodge text-white/75 text-[36px] leading-[40px] font-bold tracking-[0.05em] text-right">
                Anti Ribet
            </p>
        </div>
    </header>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  STICKY MINI-HEADER  (appears in scrolled state — Figma 969-22846) ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <div id="sticky-search"
         class="hidden-sticky fixed top-0 inset-x-0 z-30 bg-brand-dark/95 backdrop-blur-md shadow-md">
        <div class="max-w-md mx-auto px-[18px] pt-3 pb-3 safe-top">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex-1 text-white text-[13px] font-bold tracking-wide capitalize">
                    Selamat Datang!
                </span>
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" class="w-7 h-7 object-contain" alt="">
                <span class="flex-1 text-white text-[13px] font-bold tracking-wide capitalize text-right">
                    Meja {{ $meja->no_meja }}
                </span>
            </div>
            <div class="relative mb-2">
                <input id="sticky-search-input" type="text"
                       placeholder="Cari Menu"
                       class="w-full bg-white/15 border border-white/20 rounded-[9px] py-3 pl-3 pr-9 text-[16px] text-white placeholder-white/70 font-medium focus:outline-none focus:ring-2 focus:ring-white/40">
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            {{-- NEW: category pills row, mirror dari in-content --}}
            <div id="sticky-category-row" class="flex gap-5 overflow-x-auto no-scrollbar -mx-[18px] px-[18px] mt-3">
                <button data-kat="all"
                        class="sticky-cat-btn shrink-0 px-3 py-2.5 rounded-[9px] text-[16px] font-medium bg-brand-dark text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                    Semua
                </button>
                @foreach ($kategoris as $kategori)
                    <button data-kat="{{ $kategori->id_kategori }}"
                            class="sticky-cat-btn shrink-0 px-3 py-2.5 rounded-[9px] text-[16px] font-medium bg-white text-brand-dark shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                        {{ $kategori->nama_kategori }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  MAIN CONTENT — Search + Category + Card Grid                   ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <main class="relative z-10 max-w-md mx-auto px-[18px] -mt-[34px] pb-[140px]">
        <div class="relative mb-3">
            <input id="search-input" type="text"
                   placeholder="Cari Menu"
                   class="w-full bg-brand-red/[0.12] border-0 rounded-[9px] py-3 pl-3 pr-9 text-[16px] text-brand-gray-dark placeholder-brand-gray font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/40">
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        @if ($kategoris->isNotEmpty())
            <div class="flex gap-5 overflow-x-auto pb-2 mb-6 no-scrollbar -mx-[18px] px-[18px] mt-4">
                <button onclick="filterCategory('all', this)"
                        data-kat="all"
                        class="category-btn shrink-0 px-3 py-2.5 rounded-[9px] text-[16px] font-medium tracking-wide bg-brand-dark text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                    Semua
                </button>
                @foreach ($kategoris as $kategori)
                    <button onclick="filterCategory('{{ $kategori->id_kategori }}', this)"
                            data-kat="{{ $kategori->id_kategori }}"
                            class="category-btn shrink-0 px-3 py-2.5 rounded-[9px] text-[16px] font-medium tracking-wide bg-white text-brand-dark shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                        {{ $kategori->nama_kategori }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Menu Grid (Figma 969-22654 cards) --}}
        <div id="menu-grid" class="grid grid-cols-2 gap-5">
            @forelse ($kategoris as $kategori)
                @foreach ($kategori->menus as $menu)
                    @php
                        $imgType = $menu->jenis_menu === 'Makanan' ? 'food' : 'drink';
                        $imgSrc = $menu->gambar_menu
                            ? (str_starts_with($menu->gambar_menu, 'http') ? $menu->gambar_menu : asset("images/{$imgType}/{$menu->gambar_menu}"))
                            : null;
                        $badge = null;
                        if ($menu->jenis_menu === 'Makanan') {
                            if ($menu->kategori_makanan === 'Pedas') $badge = 'Pedas';
                            elseif ($menu->kategori_makanan === 'Tidak Pedas') $badge = 'Tidak Pedas';
                        } elseif ($menu->jenis_menu === 'Minuman') {
                            if ($menu->tipe_minuman === 'Panas') $badge = 'Panas';
                            elseif ($menu->tipe_minuman === 'Dingin') $badge = 'Dingin';
                            elseif ($menu->tipe_minuman === 'Keduanya') $badge = 'Panas/Dingin';
                        }
                    @endphp
                    <div class="menu-card bg-white rounded-[9px] overflow-hidden shadow-[2px_4px_4px_rgba(0,0,0,0.25)] flex flex-col"
                         data-kategori="{{ $kategori->id_kategori }}"
                         data-nama="{{ strtolower($menu->nama_menu) }}"
                         data-desc="{{ strtolower($menu->deskripsi) }}">

                         <div class="relative w-full aspect-square overflow-hidden cursor-pointer"
                              onclick="openDetailModal({{ $menu->id_menu }})">
                            @if ($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $menu->nama_menu }}"
                                     loading="lazy"
                                     class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 bg-brand-light flex items-center justify-center">
                                    <span class="text-brand-gray text-[12px]">No Image</span>
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 h-[60%] card-photo-bottom-fade pointer-events-none"></div>
                            @if ($badge)
                                <div class="absolute top-2 left-2 z-10">
                                    <span class="inline-flex items-center justify-center bg-brand-dark/25 backdrop-blur-sm text-white text-[14px] leading-4 px-2 py-[4px] rounded-[4.5px] font-normal tracking-wide">
                                        {{ $badge }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="card-info-fade -mt-[34px] relative px-3.5 pt-3.5 pb-3.5 flex flex-col gap-2.5">
                            <p class="text-white text-[16px] leading-5 font-normal tracking-wide line-clamp-2 min-h-[48px]">
                                {{ $menu->nama_menu }}
                            </p>
                            <p class="text-white text-[16px] leading-5 font-bold capitalize tracking-wide text-right">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </p>
                            <button type="button"
                                    onclick="openDetailModal({{ $menu->id_menu }})"
                                    class="mt-1 bg-white text-brand-dark text-[16px] leading-5 font-normal tracking-wide rounded-[9px] py-2.5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-[0.97] transition">
                                Tambah
                            </button>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="col-span-2 text-center py-16">
                    <div class="w-16 h-16 bg-brand-red/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <p class="text-brand-gray text-sm font-bold">Belum ada menu tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </main>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  FOOTER (Dark Red)  — Figma Footer node                         ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <footer class="bg-brand-dark text-white pt-12 pb-[140px] px-[18px]">
        <div class="max-w-md mx-auto space-y-10">
            <div>
                <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}"
                     alt="Kohvito" class="h-[60px] w-auto mb-3">
                <p class="text-[14px] leading-4 tracking-wide text-white/90 text-justify">
                    A Coffee, Dining &amp; Lifestyle Space Crafted for People Who Love Good Coffee, Cozy Atmosphere, and Meaningful Daily Experiences.
                </p>
                <div class="flex flex-wrap gap-x-5 gap-y-2 mt-4 text-[14px]">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Jl Johar No. 72 Pontianak
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        kohvitocafe@gmail.com
                    </span>
                </div>
            </div>

            <div>
                <h3 class="text-[24px] font-bold tracking-wide mb-2">Navigation</h3>
                <ul class="space-y-2 text-[14px]">
                    <li><a href="{{ route('konsumen.beranda', $meja->no_meja) }}" class="hover:underline">Menu</a></li>
                    {{-- TODO: pisahkan "Pesanan" jika histori konsumen diimplementasi --}}
                    <li><a href="{{ route('konsumen.keranjang') }}" class="hover:underline">Pesanan</a></li>
                    <li><a href="{{ route('konsumen.keranjang') }}" class="hover:underline">Keranjang</a></li>
                    <li>
                        @php($noPesanan = session('no_pesanan_baru'))
                        <a href="{{ $noPesanan ? route('konsumen.pesanan', $noPesanan) : route('konsumen.keranjang') }}"
                           class="hover:underline {{ $noPesanan ? '' : 'opacity-70' }}"
                           @if(!$noPesanan) title="Pesanan belum dibuat — buka keranjang dulu" @endif>
                            Lacak Pesanan
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-[24px] font-bold tracking-wide mb-3">Visit us!</h3>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-[14px]">
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/Instagram.svg') }}" class="w-4 h-4 invert" alt="">
                        kohvito
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/Facebook.svg') }}" class="w-4 h-4 invert" alt="">
                        kohvito_cafe
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/Threads instagram.svg') }}" class="w-4 h-4 invert" alt="">
                        kohvito
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/tiktok.svg') }}" class="w-4 h-4 invert" alt="">
                        kohvito cafe
                    </span>
                </div>
            </div>

            <div>
                <h3 class="text-[24px] font-bold tracking-wide mb-3">Reservation?</h3>
                <a href="tel:+6281348922789"
                   class="inline-flex items-center gap-2 bg-white text-brand-dark text-[14px] rounded-[9px] px-3 py-2.5 tracking-wide">
                    <span class="font-normal">Contact Us!</span>
                    <span class="font-bold capitalize">+62 813-4892-2789</span>
                </a>
            </div>

            <div class="border-t border-white/30 pt-4">
                <p class="text-[14px] text-center text-white/90 tracking-wide">
                    &copy; {{ date('Y') }} Right Reserved. Developed By Pet &amp; Jenn
                </p>
            </div>
        </div>
    </footer>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  BOTTOM NAVIGATION  (floating, fixed)                           ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    @php
        $keranjang = session('keranjang', []);
        $cartCount = array_sum(array_column($keranjang, 'jumlah'));
        $hasOrder = session('no_pesanan_baru');
    @endphp

    <nav class="fixed bottom-0 inset-x-0 z-40 pointer-events-none">
        <div class="max-w-md mx-auto px-[10px] pt-2 nav-safe-bottom pointer-events-auto">
            <div class="nav-glass rounded-[18px] p-2.5 flex items-center justify-between">
                <div class="nav-item is-active flex items-center justify-center w-[78px] h-[58px] p-1.5">
                    <div class="flex flex-col items-center justify-center gap-px">
                        <img src="{{ asset('images/icons/menu_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
                        <span class="nav-label text-[12px] leading-3 font-bold tracking-wide capitalize">Menu</span>
                    </div>
                </div>

                <a href="{{ route('konsumen.keranjang') }}" class="nav-item flex items-center justify-center w-[78px] h-[58px] p-1.5">
                    <div class="flex flex-col items-center justify-center gap-px">
                        <img src="{{ asset('images/icons/pesanan_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
                        <span class="nav-label text-[12px] leading-3 font-bold text-white tracking-wide capitalize">Pesanan</span>
                    </div>
                </a>

                <a href="{{ route('konsumen.keranjang') }}" class="nav-item relative flex items-center justify-center w-[78px] h-[58px] p-1.5">
                    <div class="flex flex-col items-center justify-center gap-px">
                        <img src="{{ asset('images/icons/keranjang_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
                        <span class="nav-label text-[12px] leading-3 font-bold text-white tracking-wide capitalize">Keranjang</span>
                    </div>
                    @if ($cartCount > 0)
                        <span class="absolute top-1 right-3 bg-white text-brand-dark text-[9px] font-black min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center shadow">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                @if ($hasOrder)
                    <a href="{{ route('konsumen.pesanan', $hasOrder) }}" class="nav-item flex items-center justify-center w-[78px] h-[58px] p-1.5">
                        <div class="flex flex-col items-center justify-center gap-px">
                            <img src="{{ asset('images/icons/lacak_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
                            <span class="nav-label text-[12px] leading-3 font-bold text-white tracking-wide capitalize">Lacak</span>
                        </div>
                    </a>
                @else
                    <div class="nav-item flex items-center justify-center w-[78px] h-[58px] p-1.5 opacity-60">
                        <div class="flex flex-col items-center justify-center gap-px">
                            <img src="{{ asset('images/icons/lacak_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
                            <span class="nav-label text-[12px] leading-3 font-bold text-white tracking-wide capitalize">Lacak</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  SLIDE-UP DETAIL MODAL (AJAX)                                   ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <div id="detail-modal" class="fixed inset-0 z-50 hidden transition-all duration-300">
        <div class="absolute inset-0 bg-brand-black/60 backdrop-blur-sm" onclick="closeDetailModal()"></div>

        <div class="absolute bottom-0 inset-x-0 bg-[#F6F6F6] rounded-t-[32px] max-w-md mx-auto shadow-2xl overflow-hidden transform translate-y-full transition-transform duration-300 flex flex-col" style="max-height: 88vh;">
            <div class="w-12 h-1.5 bg-brand-gray-light rounded-full mx-auto my-3 shrink-0 cursor-pointer" onclick="closeDetailModal()"></div>
            <div class="overflow-y-auto pb-10">
                <div class="relative">
                    <img id="modal-img" src="" class="w-full aspect-[4/3] object-cover" alt="Detail Menu">
                    <button onclick="closeDetailModal()" class="absolute top-4 right-4 bg-brand-black/45 hover:bg-brand-black/70 text-white rounded-full p-2.5 backdrop-blur-sm transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div id="modal-badges" class="flex flex-wrap gap-1.5 mb-2.5"></div>
                    <h2 id="modal-title" class="text-xl font-bold text-brand-black leading-tight mb-1">Nama Menu</h2>
                    <p id="modal-price" class="text-lg font-bold text-brand-red mb-5">Rp 0</p>
                    <div class="h-px bg-brand-gray-extralight mb-5"></div>

                    <div class="mb-6">
                        <h4 class="text-[12px] font-bold text-brand-gray uppercase tracking-widest mb-1.5">Deskripsi Rasa</h4>
                        <p id="modal-desc" class="text-sm text-brand-gray-dark leading-relaxed font-medium">Deskripsi menu...</p>
                    </div>

                    <form action="{{ route('konsumen.keranjang.tambah') }}" method="POST" id="modal-form">
                        @csrf
                        <input type="hidden" name="id_menu" id="modal-id-menu" value="">
                        <div class="mb-6">
                            <label for="modal-catatan" class="block text-[12px] font-bold text-brand-gray uppercase tracking-widest mb-2">Catatan khusus (opsional)</label>
                            <input type="text" name="catatan" id="modal-catatan" placeholder="Contoh: tidak pedas, es sedikit..."
                                   class="w-full py-3.5 px-4 border border-brand-gray-extralight rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-transparent transition-all shadow-sm placeholder-brand-gray/50 font-medium bg-white">
                        </div>

                        <div class="flex items-center justify-between gap-4 mt-8">
                            <div class="flex items-center bg-white border border-brand-gray-extralight rounded-2xl px-2 py-2 shadow-sm">
                                <button type="button" onclick="adjustModalQty(-1)" class="w-8 h-8 flex items-center justify-center rounded-xl bg-[#F6F6F6] hover:bg-brand-red/15 text-brand-dark font-bold transition-colors text-sm">&minus;</button>
                                <input type="number" name="jumlah" id="modal-qty" value="1" min="1" max="99" class="w-10 text-center text-sm font-bold bg-transparent border-none focus:outline-none" readonly>
                                <button type="button" onclick="adjustModalQty(1)" class="w-8 h-8 flex items-center justify-center rounded-xl bg-[#F6F6F6] hover:bg-brand-red/15 text-brand-dark font-bold transition-colors text-sm">&#43;</button>
                            </div>
                            <button type="submit" class="flex-1 bg-brand-dark hover:bg-brand-red text-white py-4 px-6 rounded-2xl font-bold text-sm transition-all shadow-lg uppercase tracking-wider active:scale-95">
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  SCRIPT                                                         ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <script>
        // ============ Splash Animation (first visit per session) ============
        (function () {
            const overlay = document.getElementById('splash-overlay');
            if (!overlay) return;
            const KEY = 'kohvito_splash_seen_v1';
            if (sessionStorage.getItem(KEY)) return;

            overlay.classList.remove('hidden');
            requestAnimationFrame(() => overlay.classList.add('is-running'));

            setTimeout(() => {
                overlay.classList.add('is-leaving');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('is-running', 'is-leaving');
                }, 450);
            }, 2400);

            sessionStorage.setItem(KEY, '1');
        })();

        // ============ Category & Search filter ============
        let activeKategori = 'all';

        function filterCategory(id, btn) {
            activeKategori = id;
            document.querySelectorAll('.category-btn').forEach(b => {
                b.classList.remove('bg-brand-dark', 'text-white');
                b.classList.add('bg-white', 'text-brand-dark');
            });
            btn.classList.remove('bg-white', 'text-brand-dark');
            btn.classList.add('bg-brand-dark', 'text-white');
            document.querySelectorAll('.sticky-cat-btn').forEach(b => {
                const isActive = b.dataset.kat === String(id);
                b.classList.toggle('bg-brand-dark', isActive);
                b.classList.toggle('text-white', isActive);
                b.classList.toggle('bg-white', !isActive);
                b.classList.toggle('text-brand-dark', !isActive);
            });
            runFiltering();
        }

        // Sticky category pills mirror
        document.querySelectorAll('.sticky-cat-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const kat = btn.dataset.kat;
                const target = document.querySelector(`.category-btn[data-kat="${kat}"]`);
                if (target) target.click();
                document.querySelectorAll('.sticky-cat-btn').forEach(b => {
                    b.classList.remove('bg-brand-dark', 'text-white');
                    b.classList.add('bg-white', 'text-brand-dark');
                });
                btn.classList.remove('bg-white', 'text-brand-dark');
                btn.classList.add('bg-brand-dark', 'text-white');
            });
        });

        const searchInput = document.getElementById('search-input');
        const stickyInput = document.getElementById('sticky-search-input');
        if (searchInput) searchInput.addEventListener('input', () => { syncSearch(searchInput.value); runFiltering(); });
        if (stickyInput) stickyInput.addEventListener('input', () => { syncSearch(stickyInput.value); runFiltering(); });

        function syncSearch(val) {
            if (searchInput && searchInput.value !== val) searchInput.value = val;
            if (stickyInput && stickyInput.value !== val) stickyInput.value = val;
        }

        function runFiltering() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            document.querySelectorAll('.menu-card').forEach(card => {
                const cardKat = card.getAttribute('data-kategori');
                const cardNama = card.getAttribute('data-nama');
                const cardDesc = card.getAttribute('data-desc') || '';
                const matchesKategori = (activeKategori === 'all' || cardKat === activeKategori);
                const matchesSearch = (query === '' || cardNama.includes(query) || cardDesc.includes(query));
                card.classList.toggle('hidden', !(matchesKategori && matchesSearch));
            });
        }

        // ============ Sticky header on scroll ============
        const stickyEl = document.getElementById('sticky-search');
        const SCROLL_THRESHOLD = 220;
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY > SCROLL_THRESHOLD;
            document.body.classList.toggle('is-scrolled', scrolled);
            if (stickyEl) stickyEl.classList.toggle('hidden-sticky', !scrolled);
        }, { passive: true });

        // ============ Detail Modal (AJAX) ============
        function openDetailModal(id) {
            const modal = document.getElementById('detail-modal');
            const panel = modal.querySelector('.absolute.bottom-0');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            fetch(`/menu/${id}/detail`)
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(menu => {
                    document.getElementById('modal-id-menu').value = menu.id_menu;
                    document.getElementById('modal-title').textContent = menu.nama_menu;
                    document.getElementById('modal-desc').textContent = menu.deskripsi || 'Nikmati kelezatan racikan khas Kohvito Café yang memanjakan lidah.';
                    document.getElementById('modal-price').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(menu.harga);

                    const imgType = menu.jenis_menu === 'Makanan' ? 'food' : 'drink';
                    const imgSrc = menu.gambar_menu && menu.gambar_menu.startsWith('http')
                        ? menu.gambar_menu
                        : `/images/${imgType}/${menu.gambar_menu}`;
                    document.getElementById('modal-img').src = imgSrc;
                    document.getElementById('modal-qty').value = 1;
                    document.getElementById('modal-catatan').value = '';

                    const badgesContainer = document.getElementById('modal-badges');
                    badgesContainer.innerHTML = '';
                    const parentCard = document.querySelector(`.menu-card[data-nama="${menu.nama_menu.toLowerCase()}"]`);
                    if (parentCard) {
                        const badgeEl = parentCard.querySelector('.absolute.top-2.left-2');
                        if (badgeEl) {
                            const cloned = badgeEl.cloneNode(true);
                            cloned.className = 'flex gap-1.5';
                            badgesContainer.appendChild(cloned);
                        }
                    }

                    setTimeout(() => panel.classList.remove('translate-y-full'), 50);
                })
                .catch(() => closeDetailModal());
        }

        function closeDetailModal() {
            const modal = document.getElementById('detail-modal');
            const panel = modal.querySelector('.absolute.bottom-0');
            panel.classList.add('translate-y-full');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        function adjustModalQty(val) {
            const input = document.getElementById('modal-qty');
            let num = parseInt(input.value) || 1;
            num = Math.max(1, Math.min(99, num + val));
            input.value = num;
        }
    </script>
</body>
</html>
