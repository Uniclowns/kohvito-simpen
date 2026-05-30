{{-- Beranda Konsumen
    Route: konsumen.beranda (/{noMeja})
    Controller: BerandaKonsumenController@index
    Variables: $meja, $kategoris, $cartCount
--}}
<x-layouts.konsumen :title="'Pesan Menu Anti Ribet - Meja ' . $meja->no_meja . ' - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] font-sans text-brand-black kvt-konsumen-mobile-view">
    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  SPLASH OVERLAY  —  Figma 648-8923 + 792-11529                  ║
         ║  Putih → titik merah → meledak jadi merah → mascot + greeting  ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <div id="splash-overlay" class="hidden fixed inset-0 z-100 overflow-hidden bg-white">
        <div id="splash-dot"
            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-brand-dark">
        </div>
        <div id="splash-content" class="absolute inset-0 flex flex-col items-center justify-center opacity-0 px-6">
            <img src="{{ asset('images/illustration/Splash Screen Kepala Ito.svg') }}" alt="Kohvito"
                class="w-[200px] h-auto drop-shadow-[0_8px_24px_rgba(0,0,0,0.35)]">
            <p class="mt-6 text-white text-3xl font-extrabold tracking-wider" data-typed="Selamat Datang!"
                data-typed-delay="1300">Selamat Datang!</p>
            <p class="mt-2 text-white/80 text-sm font-medium tracking-wide">Pesan Menu Anti Ribet</p>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  HERO SECTION (Top 275px)                                       ║
         ║  Photo background + dark-red header bar + "Pesan Menu" headline ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <header class="relative w-full overflow-hidden">
        <div class="relative h-[275px] w-full sm:h-[300px] lg:h-[320px]">
            <img src="{{ asset('images/bg/kvt-banner.jpg') }}" alt=""
                class="absolute inset-0 w-full h-full object-cover">

            {{-- Dark wash — top-weighted only, so the "Pesan Menu / Anti Ribet"
                 headline stays readable while the bottom of the photo is left
                 clear for the white fade below (Figma 969-22654). --}}
            <div class="absolute inset-0 hero-dark-fade"></div>

            {{-- Bottom fade — photo dissolves into the page bg (#F6F6F6) so the
                 "Cari Menu" search bar can sit centered on the gradient
                 transition, matching the Figma reference. --}}
            <div class="absolute inset-x-0 bottom-0 h-[160px] hero-bottom-fade"></div>
        </div>

        {{-- Top fade (header bar background) --}}
        <div class="absolute inset-x-0 top-0 h-[89px] header-fade pointer-events-none md:hidden"></div>

        {{-- Top bar: Selamat Datang / Mascot / Meja --}}
        <div class="absolute inset-x-0 top-0 safe-top md:hidden">
            <div class="mx-auto flex max-w-md items-center justify-between px-[18px] pb-3 pt-[14px]">
                <p class="flex-1 text-white text-xs font-bold tracking-wide capitalize">
                    Selamat Datang!
                </p>
                <div class="w-6 h-6 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito"
                        class="w-full h-full object-contain">
                </div>
                <p class="flex-1 text-white text-sm font-bold tracking-wide text-right capitalize">
                    Meja {{ $meja->no_meja }}
                </p>
            </div>
        </div>

        <div class="hero-headline absolute inset-x-0 top-[116px] px-[18px] sm:top-[124px] md:top-[104px]">
            <div class="mx-auto max-w-md md:max-w-3xl lg:max-w-5xl">
                <h1
                    class="text-mix-dodge text-[36px] font-bold leading-[40px] tracking-[1.8px] text-white/80 sm:text-[44px] sm:leading-[48px]">
                    Pesan Menu
                </h1>
                <p
                    class="text-mix-dodge text-right text-[36px] font-bold leading-[40px] tracking-[1.8px] text-white/80 sm:text-[44px] sm:leading-[48px]">
                    Anti Ribet
                </p>
            </div>
        </div>
    </header>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  STICKY MINI-HEADER  (appears in scrolled state — Figma 969-22846) ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <div id="sticky-search"
        class="hidden-sticky fixed top-0 inset-x-0 z-30 bg-brand-dark/95 backdrop-blur-md shadow-md">
        <div class="max-w-md md:max-w-3xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl mx-auto px-[18px] pt-3 pb-4 safe-top">
            <div class="flex items-center gap-3 mb-2 max-w-md mx-auto md:max-w-none">
                <span class="flex-1 text-white text-[13px] font-bold tracking-wide capitalize">
                    Selamat Datang!
                </span>
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" class="w-7 h-7 object-contain" alt="">
                <span class="flex-1 text-white text-[13px] font-bold tracking-wide capitalize text-right">
                    Meja {{ $meja->no_meja }}
                </span>
            </div>
            <div class="relative mb-2 max-w-md mx-auto md:max-w-none">
                <input id="sticky-search-input" type="text" placeholder="Cari Menu"
                    class="w-full bg-white/15 border border-white/20 rounded-[9px] p-[10px] pr-10 text-[14px] tracking-[0.7px] text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/40">
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-white/80" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            {{-- NEW: category pills row, mirror dari in-content --}}
            <div id="sticky-category-row"
                class="flex gap-4 overflow-x-auto md:flex-wrap md:justify-center no-scrollbar mx-[-18px] md:mx-0 px-[18px] md:px-0 mt-2">
                <button data-kat="all"
                    class="sticky-cat-btn shrink-0 px-3 py-1.5 rounded-[9px] text-[14px] tracking-[0.7px] bg-brand-dark text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                    Semua
                </button>
                @foreach ($kategoris as $kategori)
                    <button data-kat="{{ $kategori->id_kategori }}"
                        class="sticky-cat-btn shrink-0 px-3 py-1.5 rounded-[9px] text-[14px] tracking-[0.7px] bg-white text-brand-dark shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                        {{ $kategori->nama_kategori }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  MAIN CONTENT — Search + Category + Card Grid                   ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <main
        class="relative z-10 mx-auto mt-[-60px] max-w-md px-[18px] pb-[140px] sm:max-w-xl md:max-w-3xl md:pb-12 lg:max-w-6xl">
        <div class="contents">

            {{-- ╔══════════════════════════════════════════════════════════════════╗
                 ║  TABLET & DESKTOP SIDEBAR CATEGORY — Khusus md:block            ║
                 ║  Sticky sidebar vertikal dengan badge hitungan menu                ║
                 ╚══════════════════════════════════════════════════════════════════╝ --}}
            {{-- Kolom Kanan: Pencarian, Horizontal pills (mobile only) & Grid Menu --}}
            <div class="space-y-0">
                <div class="relative mb-3 max-w-md lg:max-w-lg">
                    <input id="search-input" type="text" placeholder="Cari Menu"
                        class="w-full rounded-[9px] border-0 bg-brand-red/15 p-[10px] pr-10 text-[14px] leading-5 tracking-[0.7px] text-brand-dark placeholder-brand-dark/80 focus:outline-none focus:ring-2 focus:ring-brand-dark/20">
                    <svg class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-dark" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                @if ($kategoris->isNotEmpty())
                    <div>
                        <h2 class="mb-2 mt-6 text-[24px] font-bold leading-[32px] tracking-[1.2px] text-brand-dark">
                            Category</h2>
                        <div class="mx-[-18px] mb-[18px] flex gap-4 overflow-x-auto px-[18px] pb-2 no-scrollbar sm:flex-wrap sm:overflow-visible sm:px-[18px] lg:mx-0 lg:px-0"
                            data-category-row data-anim="stagger">
                            <button onclick="filterCategory('all', this)" data-kat="all" data-anim-item
                                class="category-btn shrink-0 px-3 py-1.5 rounded-[9px] text-[14px] tracking-[0.7px] bg-brand-dark text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                                Semua
                            </button>
                            @foreach ($kategoris as $kategori)
                                <button onclick="filterCategory('{{ $kategori->id_kategori }}', this)"
                                    data-kat="{{ $kategori->id_kategori }}" data-anim-item
                                    class="category-btn shrink-0 px-3 py-1.5 rounded-[9px] text-[14px] tracking-[0.7px] bg-white text-brand-dark shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-all whitespace-nowrap">
                                    {{ $kategori->nama_kategori }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div id="menu-grid"
                    class="grid grid-cols-1 gap-[10px] min-[340px]:grid-cols-2 min-[480px]:gap-4 md:grid-cols-3 lg:grid-cols-4"
                    data-anim="stagger">
                    @forelse ($kategoris as $kategori)
                        @foreach ($kategori->menus as $menu)
                            @php
                                $imgType = $menu->jenis_menu === 'Makanan' ? 'food' : 'drink';
                                $imgSrc = $menu->gambar_menu
                                    ? (str_starts_with($menu->gambar_menu, 'http')
                                        ? $menu->gambar_menu
                                        : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                    : null;
                                $badge = null;
                                if ($menu->jenis_menu === 'Makanan') {
                                    if ($menu->kategori_makanan === 'Pedas') {
                                        $badge = 'Pedas';
                                    } elseif ($menu->kategori_makanan === 'Tidak Pedas') {
                                        $badge = 'Tidak Pedas';
                                    }
                                } elseif ($menu->jenis_menu === 'Minuman') {
                                    if ($menu->tipe_minuman === 'Panas') {
                                        $badge = 'Panas';
                                    } elseif ($menu->tipe_minuman === 'Dingin') {
                                        $badge = 'Dingin';
                                    } elseif ($menu->tipe_minuman === 'Keduanya') {
                                        $badge = 'Panas/Dingin';
                                    }
                                }
                            @endphp
                            <article
                                class="menu-card group relative flex min-w-0 flex-col overflow-hidden rounded-[9px] bg-brand-red shadow-[2px_4px_2px_rgba(0,0,0,0.25)]"
                                data-anim-item data-kategori="{{ $kategori->id_kategori }}"
                                data-nama="{{ strtolower($menu->nama_menu) }}"
                                data-desc="{{ strtolower($menu->deskripsi) }}">

                                {{-- Image area --}}
                                <button type="button" onclick="openMenuSheet({{ $menu->id_menu }});"
                                    class="relative block aspect-177/154 w-full cursor-pointer overflow-hidden bg-brand-light text-left">
                                    @if ($imgSrc)
                                        <img src="{{ $imgSrc }}" alt="{{ $menu->nama_menu }}" loading="lazy"
                                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-brand-gray text-[12px]">No Image</span>
                                        </div>
                                    @endif
                                    <span class="card-photo-bottom-fade absolute inset-0"></span>

                                    @if ($badge)
                                        <div class="absolute left-[10px] top-[10px] z-10">
                                            <span
                                                class="inline-flex items-center justify-center rounded-[9px] bg-brand-dark/55 px-2 py-[3px] text-[10px] font-bold leading-4 tracking-[0.5px] text-white backdrop-blur-sm">
                                                {{ $badge }}
                                            </span>
                                        </div>
                                    @endif
                                </button>

                                {{-- Content area --}}
                                <div
                                    class="card-info-fade relative mt-[-34px] flex flex-1 flex-col gap-[5px] px-[10px] pb-[10px] pt-[6px]">
                                    <div>
                                        <h3
                                            class="line-clamp-2 min-h-[48px] text-[12px] font-bold leading-5 tracking-[0.6px] text-white sm:text-[14px] sm:leading-6">
                                            {{ $menu->nama_menu }}
                                        </h3>
                                    </div>

                                    <div class="mt-auto flex flex-col gap-[5px]">
                                        <span
                                            class="text-right text-[12px] font-bold leading-4 tracking-[0.6px] text-white sm:text-[14px] sm:leading-5">
                                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                        </span>
                                        <button type="button" onclick="openMenuSheet({{ $menu->id_menu }});"
                                            class="w-full rounded-[9px] bg-white px-3 py-[6px] text-[12px] font-bold leading-4 tracking-[0.6px] text-brand-dark transition hover:bg-white/90 active:scale-[0.98] sm:text-[14px] sm:leading-5">
                                            Tambah
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <div
                                class="w-16 h-16 bg-brand-red/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-brand-gray" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-brand-gray text-sm font-bold">Belum ada menu tersedia saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  FOOTER (Dark Red)  — Figma Footer node                         ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <footer class="bg-brand-dark text-white pt-[38px] pb-[140px] px-[18px] md:pb-10">
        <div
            class="max-w-md md:max-w-3xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl mx-auto space-y-8 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-8 md:items-start">
            <div>
                <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="Kohvito"
                    class="h-[73px] w-auto mb-2.5">
                <p class="text-[12px] leading-[16px] tracking-[0.6px] text-white/90 text-justify">
                    A Coffee, Dining &amp; Lifestyle Space Crafted for People Who Love Good Coffee, Cozy Atmosphere,
                    and Meaningful Daily Experiences.
                </p>
                <div class="flex flex-wrap gap-x-5 gap-y-2.5 mt-2.5 text-[12px] leading-[16px] tracking-[0.6px]">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        Jl Johar No. 72 Pontianak
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        kohvitocafe@gmail.com
                    </span>
                </div>
            </div>

            <div>
                <h3 class="text-[20px] leading-[28px] font-bold tracking-[1px] mb-2.5">Navigation</h3>
                <ul class="space-y-2 text-[12px] leading-[16px] tracking-[0.6px]">
                    <li><a href="{{ route('konsumen.beranda', $meja->no_meja) }}" class="hover:underline">Menu</a>
                    </li>
                    <li><a href="{{ route('konsumen.pesanan') }}" class="hover:underline">Pesanan</a></li>
                    <li><a href="{{ route('konsumen.keranjang') }}" class="hover:underline">Keranjang</a></li>
                    <li><a href="{{ route('konsumen.lacak') }}" class="hover:underline">Lacak Pesanan</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-[20px] leading-[28px] font-bold tracking-[1px] mb-2.5">Visit us!</h3>
                <div class="flex flex-wrap gap-x-[17px] gap-y-2.5 text-[12px] leading-[16px] tracking-[0.6px]">
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/Instagram.svg') }}" class="w-4 h-4 invert" alt="">
                        kohvito
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/Facebook.svg') }}" class="w-4 h-4 invert" alt="">
                        kohvito_cafe
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/Threads instagram.svg') }}" class="w-4 h-4 invert"
                            alt="">
                        kohvito
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <img src="{{ asset('images/icons/tiktok.svg') }}" class="w-4 h-4 invert" alt="">
                        kohvito cafe
                    </span>
                </div>
            </div>

            <div>
                <h3 class="text-[20px] leading-[28px] font-bold tracking-[1px] mb-2.5">Reservation?</h3>
                <a href="tel:+6281348922789"
                    class="inline-flex items-center gap-2.5 bg-white text-brand-dark text-[12px] leading-[16px] rounded-[9px] px-3 py-1.5 tracking-[0.6px]">
                    <span class="font-normal">Contact Us!</span>
                    <span class="font-bold capitalize">+62 813-4892-2789</span>
                </a>
            </div>

            <div class="border-t border-white/30 pt-4 md:col-span-2 lg:col-span-4">
                <p class="text-[12px] leading-[16px] text-center text-[#f6f6f6] tracking-[0.6px]">
                    &#64;{{ date('Y') }} Right Reserved. Developed By Pet &amp; Jenn
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

    <x-konsumen-bottom-nav active="menu" :mejaNo="$meja->no_meja" :cartCount="$cartCount" />

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  SLIDE-UP DETAIL SHEET (AJAX, BODY-ONLY OVERLAY)                ║
         ║                                                                  ║
         ║  Sits BELOW the beranda header bar so the top "Selamat Datang!" ║
         ║  / mascot / Meja XXX strip stays visible. The panel slides up   ║
         ║  from the bottom of the screen into this body area. Close via   ║
         ║  scrim click, in-panel "Kembali" button, or ESC.                ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <div id="menu-sheet" class="fixed inset-x-0 bottom-0 z-60 hidden" style="top: var(--kvt-header-h);"
        aria-hidden="true">
        <div id="menu-sheet-scrim" class="absolute inset-0 bg-black/55 backdrop-blur-[2px]"
            onclick="closeMenuSheet()"></div>

        <div id="menu-sheet-panel"
            class="absolute inset-x-0 bottom-0 top-0 bg-[#F6F6F6] max-w-md mx-auto overflow-y-auto overscroll-contain shadow-[0_-12px_40px_rgba(0,0,0,0.35)]"
            style="-webkit-overflow-scrolling: touch; touch-action: pan-y;">

            {{-- Drag indicator on top (also clickable to close) --}}
            <button type="button" onclick="closeMenuSheet()"
                class="sticky top-0 z-10 w-full flex items-center justify-center pt-2 pb-1 bg-transparent">
                <span class="block w-12 h-1.5 rounded-full bg-brand-gray-light/80"></span>
            </button>

            {{-- Default loading state (replaced by injected partial on success) --}}
            <div id="menu-sheet-loader"
                class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-brand-dark">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                        stroke-linecap="round" stroke-dasharray="40 60" />
                </svg>
                <p class="text-[12px] tracking-wide font-bold text-brand-gray">Memuat detail menu...</p>
            </div>

            {{-- Slot where the fetched partial gets injected --}}
            <div id="menu-sheet-body" class="relative"></div>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════════════╗
         ║  SCRIPT                                                         ║
         ╚══════════════════════════════════════════════════════════════════╝ --}}
    <script>
        // ============ Splash Animation (first visit per session) ============
        (function() {
            const overlay = document.getElementById('splash-overlay');
            if (!overlay) return;
            const KEY = 'kohvito_splash_seen_v1';
            if (sessionStorage.getItem(KEY)) {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
                return;
            }

            let finished = false;

            function finishSplash() {
                if (finished) return;
                finished = true;
                overlay.classList.add('is-leaving');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('is-running', 'is-leaving');
                    document.body.style.overflow = '';
                }, 450);
            }

            const fallbackTimer = setTimeout(finishSplash, 3000);
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => overlay.classList.add('is-running'));

            setTimeout(() => {
                clearTimeout(fallbackTimer);
                finishSplash();
            }, 2400);

            sessionStorage.setItem(KEY, '1');
        })();

        // ============ Category & Search filter ============
        let activeKategori = 'all';

        function filterCategory(id, btn) {
            activeKategori = id;

            document.querySelectorAll('[data-category-row] .category-btn').forEach(b => {
                const isActive = b.dataset.kat === String(id);
                if (isActive) {
                    b.classList.remove('bg-white', 'text-brand-dark');
                    b.classList.add('bg-brand-dark', 'text-white');
                } else {
                    b.classList.remove('bg-brand-dark', 'text-white');
                    b.classList.add('bg-white', 'text-brand-dark');
                }
            });

            // Update all sticky category buttons
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
                filterCategory(kat);
            });
        });

        const searchInput = document.getElementById('search-input');
        const stickyInput = document.getElementById('sticky-search-input');
        if (searchInput) searchInput.addEventListener('input', () => {
            syncSearch(searchInput.value);
            runFiltering();
        });
        if (stickyInput) stickyInput.addEventListener('input', () => {
            syncSearch(stickyInput.value);
            runFiltering();
        });

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
            const isDesktop = window.innerWidth >= 768;
            const scrolled = window.scrollY > SCROLL_THRESHOLD && !isDesktop;
            document.body.classList.toggle('is-scrolled', window.scrollY > SCROLL_THRESHOLD);
            if (stickyEl) {
                if (scrolled) {
                    stickyEl.classList.remove('hidden-sticky');
                } else {
                    stickyEl.classList.add('hidden-sticky');
                }
            }
        }, {
            passive: true
        });

        // ============ Detail Menu Sheet (slide-up, full-screen) ============
        // Loads /menu/{id}/detail?partial=1 via AJAX and injects the HTML
        // fragment into the sheet panel, then animates the panel from
        // translateY(100%) → translateY(0). Injected <script> tags are
        // re-executed by cloning them (innerHTML alone does not run them).

        // Remember pre-sheet scroll so we can restore it on close.
        let __menuSheetReturnScrollY = 0;

        // ─── Robust body scroll lock (iOS Safari–safe) ───
        // `body { overflow: hidden }` alone is bypassed on iOS — body still scrolls
        // and scroll chains from the panel into the catalog beneath. The cure is
        // pinning <body> in place with `position: fixed; top: -<scrollY>px` while
        // the sheet is open, then restoring scroll on close.
        function lockBodyScroll() {
            __menuSheetReturnScrollY = window.scrollY || document.documentElement.scrollTop || 0;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${__menuSheetReturnScrollY}px`;
            document.body.style.left = '0';
            document.body.style.right = '0';
            document.body.style.width = '100%';
            document.body.style.overflow = 'hidden';
        }
        function unlockBodyScroll() {
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            document.body.style.width = '';
            document.body.style.overflow = '';
            if (__menuSheetReturnScrollY > 0) {
                window.scrollTo(0, __menuSheetReturnScrollY);
                __menuSheetReturnScrollY = 0;
            }
        }

        window.openMenuSheet = async function(id) {
            const sheet = document.getElementById('menu-sheet');
            const body = document.getElementById('menu-sheet-body');
            const loader = document.getElementById('menu-sheet-loader');
            if (!sheet || !body) return;

            // Reset & reveal
            body.innerHTML = '';
            if (loader) loader.style.display = '';
            sheet.classList.remove('hidden');
            sheet.setAttribute('aria-hidden', 'false');
            lockBodyScroll();

            // Force the browser to register the initial translateY(100%) state
            // BEFORE we toggle .is-open — otherwise display:none→block in the
            // same frame as the class change makes the browser skip the slide
            // animation and the panel just pops in.
            void sheet.offsetHeight; // sync reflow
            requestAnimationFrame(() => { // first paint of initial state
                requestAnimationFrame(() => { // then trigger transition
                    sheet.classList.add('is-open');
                });
            });

            try {
                const res = await fetch(`/menu/${id}/detail?partial=1`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const html = await res.text();

                body.innerHTML = html;
                if (loader) loader.style.display = 'none';

                // <script> tags inserted via innerHTML do NOT execute — clone them
                body.querySelectorAll('script').forEach(oldScript => {
                    const newScript = document.createElement('script');
                    for (const attr of oldScript.attributes) {
                        newScript.setAttribute(attr.name, attr.value);
                    }
                    newScript.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            } catch (e) {
                body.innerHTML = `
                    <div class="p-8 text-center">
                        <p class="text-brand-gray text-sm mb-4">Gagal memuat detail menu.</p>
                        <button type="button" onclick="closeMenuSheet()" class="bg-brand-dark text-white px-5 py-2.5 rounded-[9px] text-sm font-bold">Tutup</button>
                    </div>`;
                if (loader) loader.style.display = 'none';
            }
        };

        window.closeMenuSheet = function() {
            const sheet = document.getElementById('menu-sheet');
            const body = document.getElementById('menu-sheet-body');
            if (!sheet) return;

            sheet.classList.remove('is-open');
            sheet.setAttribute('aria-hidden', 'true');

            // After the slide-down transition completes, hide the sheet entirely
            // and restore the user's prior scroll position via unlockBodyScroll().
            setTimeout(() => {
                sheet.classList.add('hidden');
                if (body) body.innerHTML = '';
                unlockBodyScroll();
            }, 420);
        };

        // ESC closes the sheet
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const sheet = document.getElementById('menu-sheet');
                if (sheet && sheet.classList.contains('is-open')) {
                    window.closeMenuSheet();
                }
            }
        });
    </script>
</x-layouts.konsumen>
