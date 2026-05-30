@props(['variant' => 'default'])

@auth
@if ($variant === 'kasir')
<aside data-app-sidebar class="kvt-sidebar fixed left-0 top-0 z-50 flex h-screen w-[98px] -translate-x-full flex-shrink-0 flex-col items-center overflow-hidden bg-[#460001] px-[18px] py-14 lg:sticky lg:translate-x-0">
    <a href="{{ route('kasir.beranda') }}" class="block w-[47px] h-[21px] mb-[43px]" aria-label="{{ config('app.name') }}">
        <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}"
             alt="{{ config('app.name') }}"
             class="w-[47px] h-[21px] object-contain">
    </a>

    <nav class="flex-1 flex flex-col items-center gap-[43px] w-full">
        @php
            $isBerandaActive = request()->routeIs('kasir.beranda');
            $isPesananActive = request()->routeIs('kasir.pesanan.*');
            $isHistoriActive = request()->routeIs('kasir.histori.*');
        @endphp

        <a href="{{ route('kasir.beranda') }}"
           class="w-14 h-12 rounded-[9px] flex items-center justify-center shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-colors {{ $isBerandaActive ? 'bg-white' : 'hover:bg-white/10' }}"
           aria-label="Beranda Kasir">
            <img src="{{ asset('images/icons/template.svg') }}" alt=""
                 class="w-6 h-6 object-contain"
                 style="{{ $isBerandaActive ? 'filter: brightness(0) saturate(100%) invert(8%) sepia(64%) saturate(3137%) hue-rotate(349deg) brightness(82%) contrast(117%);' : 'filter: brightness(0) invert(1);' }}">
        </a>

        <a href="{{ route('kasir.pesanan.index') }}"
           class="w-14 h-12 rounded-[9px] flex items-center justify-center shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-colors {{ $isPesananActive ? 'bg-white' : 'hover:bg-white/10' }}"
           aria-label="Kelola Pesanan">
            <img src="{{ asset('images/icons/' . ($isPesananActive ? 'pesanan icon red.svg' : 'pesanan icon white.svg')) }}"
                 alt="" class="w-6 h-6 object-contain">
        </a>

        <a href="{{ route('kasir.histori.index') }}"
           class="w-14 h-12 rounded-[9px] flex items-center justify-center shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-colors {{ $isHistoriActive ? 'bg-white' : 'hover:bg-white/10' }}"
           aria-label="Histori Pesanan">
            <svg class="w-6 h-6 {{ $isHistoriActive ? 'text-[#460001]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4"
                      d="M12 7.5v5l3.2 2.1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </a>
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="w-full flex justify-center">
        @csrf
        <button type="submit"
                class="w-14 h-12 rounded-[9px] flex items-center justify-center text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-white/10 transition-colors"
                aria-label="Keluar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                      d="M15.75 9V5.75A1.75 1.75 0 0 0 14 4H6.75A1.75 1.75 0 0 0 5 5.75v12.5C5 19.216 5.784 20 6.75 20H14a1.75 1.75 0 0 0 1.75-1.75V15M12 12h8m0 0-3-3m3 3-3 3"/>
            </svg>
        </button>
    </form>
</aside>
@else
<aside data-app-sidebar class="kvt-sidebar group fixed left-0 top-0 z-50 flex h-screen w-[72px] -translate-x-full flex-shrink-0 flex-col overflow-hidden bg-brand-dark py-6 transition-all duration-300 hover:w-64 lg:sticky lg:translate-x-0">

    {{-- Logo --}}
    <div class="mb-8 flex items-center justify-center w-full h-16">
        <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" 
             alt="{{ config('app.name') }}" 
             class="w-8 h-auto object-contain transition-all duration-300 group-hover:scale-[4.5]">
    </div>

    {{-- Nav --}}
    <nav class="flex-1 flex flex-col gap-3 w-full px-3">

        @php
            $userRoleId = auth()->user()->id_role;
            $isSuperadmin = $userRoleId === 3; // Super Admin (god mode)
            $isAdmin      = $userRoleId === 1; // Admin biasa
            // Target tombol "Lihat Konsumen" untuk Super Admin (meja pertama).
            $firstMejaNo  = $isSuperadmin ? optional(\App\Models\Meja::orderBy('no_meja')->first())->no_meja : null;
        @endphp

        @if ($isSuperadmin)
            {{-- ══ Super Admin Navigation (god-mode hub) ══ --}}
            <a href="{{ route('superadmin.beranda') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('superadmin.beranda') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="{{ request()->routeIs('superadmin.beranda') ? '' : 'opacity:.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
            </a>

            <a href="{{ route('superadmin.admin.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('superadmin.admin.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="{{ request()->routeIs('superadmin.admin.*') ? '' : 'opacity:.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Admin</span>
            </a>

            <a href="{{ route('admin.pengguna-kasir.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.pengguna-kasir.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/users.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.pengguna-kasir.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Kasir</span>
            </a>

            <a href="{{ route('admin.menu.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.menu.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/coffee.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.menu.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Menu</span>
            </a>

            <a href="{{ route('admin.kategori.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.kategori.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/menu icon.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.kategori.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Kategori</span>
            </a>

            <a href="{{ route('superadmin.meja.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('superadmin.meja.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="{{ request()->routeIs('superadmin.meja.*') ? '' : 'opacity:.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4h18v4H3V4zm0 8h6v8H3v-8zm10 0h8v8h-8v-8z"/>
                </svg>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Meja</span>
            </a>

            {{-- Divider --}}
            <div class="my-1 border-t border-white/10"></div>

            <a href="{{ route('admin.beranda') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.beranda') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/template.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.beranda') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Panel Admin</span>
            </a>

            <a href="{{ route('kasir.beranda') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('kasir.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="{{ request()->routeIs('kasir.*') ? '' : 'opacity:.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Panel Kasir</span>
            </a>

            <a href="{{ $firstMejaNo ? url('/' . $firstMejaNo) : '#' }}" target="_blank"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden text-white hover:bg-white/10 {{ $firstMejaNo ? '' : 'opacity-40 pointer-events-none' }}">
                <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.8">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Lihat Konsumen</span>
            </a>

        @elseif ($isAdmin)
            {{-- Admin Navigation --}}
            <a href="{{ route('admin.beranda') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.beranda') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/template.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.beranda') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Beranda Admin</span>
            </a>

            <a href="{{ route('admin.menu.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.menu.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/coffee.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.menu.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Menu</span>
            </a>

            <a href="{{ route('admin.kategori.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.kategori.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/menu icon.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.kategori.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Kategori</span>
            </a>

            <a href="{{ route('admin.pengguna-kasir.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.pengguna-kasir.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/users.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('admin.pengguna-kasir.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Pengguna Kasir</span>
            </a>

        @else
            {{-- Kasir Navigation --}}
            <a href="{{ route('kasir.beranda') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('kasir.beranda') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/template.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('kasir.beranda') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Beranda Kasir</span>
            </a>

            @php $isPesananActive = request()->routeIs('kasir.pesanan.*'); @endphp
            <a href="{{ route('kasir.pesanan.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ $isPesananActive ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/' . ($isPesananActive ? 'pesanan icon red.svg' : 'pesanan icon white.svg')) }}"
                     alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Pesanan</span>
            </a>

            <a href="{{ route('kasir.histori.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('kasir.histori.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="{{ request()->routeIs('kasir.histori.*') ? '' : 'opacity:.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Histori Pesanan</span>
            </a>
        @endif

    </nav>

    {{-- Logout --}}
    <div class="mt-auto pt-4 border-t border-white/10 w-full flex justify-center px-3 group-hover:px-4 transition-all duration-300">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full h-10 flex items-center px-3 text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors overflow-hidden">
                <img src="{{ asset('images/icons/logout.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="filter:brightness(0) invert(1);opacity:.8">
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Keluar</span>
            </button>
        </form>
    </div>

</aside>
@endif
@endauth
