@auth
<aside class="group w-[72px] hover:w-64 h-screen sticky top-0 bg-brand-dark flex flex-col py-6 flex-shrink-0 transition-all duration-300 z-50 overflow-hidden">

    {{-- Logo --}}
    <div class="mb-8 flex items-center justify-center w-full h-16">
        <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" 
             alt="{{ config('app.name') }}" 
             class="w-8 h-auto object-contain transition-all duration-300 group-hover:scale-[4.5]">
    </div>

    {{-- Nav --}}
    <nav class="flex-1 flex flex-col gap-3 w-full px-3">

        @if (auth()->user()->id_role === 1)
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

            <a href="{{ route('admin.laporan-keuangan.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('admin.laporan-keuangan.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="{{ request()->routeIs('admin.laporan-keuangan.*') ? '' : 'opacity:.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Laporan Keuangan</span>
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

            <a href="{{ route('kasir.pesanan.index') }}"
               class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
                      {{ request()->routeIs('kasir.pesanan.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/menu icon.svg') }}" alt=""
                     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
                     style="{{ request()->routeIs('kasir.pesanan.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
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
@endauth

