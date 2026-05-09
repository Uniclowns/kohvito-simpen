@auth
<aside class="w-[72px] h-screen sticky top-0 bg-brand-dark flex flex-col items-center py-6 flex-shrink-0">

    {{-- Logo --}}
    <div class="mb-8">
        <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="{{ config('app.name') }}" class="w-8 h-auto object-contain">
    </div>

    {{-- Nav --}}
    <nav class="flex-1 flex flex-col items-center gap-3 w-full px-2">

        @if (auth()->user()->id_role === 1)
            {{-- Admin Navigation --}}
            <a href="{{ route('admin.beranda') }}" title="Beranda"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('admin.beranda') ? 'bg-white' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/template.svg') }}" alt="" class="w-5 h-5 {{ request()->routeIs('admin.beranda') ? '' : 'brightness-0 invert opacity-80' }}">
            </a>

            <a href="{{ route('admin.menu.index') }}" title="Kelola Menu"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('admin.menu.*') ? 'bg-white' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/coffee.svg') }}" alt="" class="w-5 h-5 {{ request()->routeIs('admin.menu.*') ? '' : 'brightness-0 invert opacity-80' }}">
            </a>

            <a href="{{ route('admin.kategori.index') }}" title="Kelola Kategori"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('admin.kategori.*') ? 'bg-white' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/menu icon.svg') }}" alt="" class="w-5 h-5 {{ request()->routeIs('admin.kategori.*') ? '' : 'brightness-0 invert opacity-80' }}">
            </a>

            <a href="{{ route('admin.pengguna-kasir.index') }}" title="Pengguna Kasir"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('admin.pengguna-kasir.*') ? 'bg-white' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/users.svg') }}" alt="" class="w-5 h-5 {{ request()->routeIs('admin.pengguna-kasir.*') ? '' : 'brightness-0 invert opacity-80' }}">
            </a>

            <a href="{{ route('admin.laporan-keuangan.index') }}" title="Laporan Keuangan"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('admin.laporan-keuangan.*') ? 'bg-white text-brand-dark' : 'text-white/80 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </a>

        @else
            {{-- Kasir Navigation --}}
            <a href="{{ route('kasir.beranda') }}" title="Beranda"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('kasir.beranda') ? 'bg-white' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/template.svg') }}" alt="" class="w-5 h-5 {{ request()->routeIs('kasir.beranda') ? '' : 'brightness-0 invert opacity-80' }}">
            </a>

            <a href="{{ route('kasir.pesanan.index') }}" title="Kelola Pesanan"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('kasir.pesanan.*') ? 'bg-white' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/icons/menu icon.svg') }}" alt="" class="w-5 h-5 {{ request()->routeIs('kasir.pesanan.*') ? '' : 'brightness-0 invert opacity-80' }}">
            </a>

            <a href="{{ route('kasir.histori.index') }}" title="Histori Pesanan"
               class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors
                      {{ request()->routeIs('kasir.histori.*') ? 'bg-white text-brand-dark' : 'text-white/80 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>
        @endif

    </nav>

    {{-- Logout --}}
    <div class="mt-auto pt-4 border-t border-white/10 w-full flex justify-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Keluar" class="w-10 h-10 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors">
                <img src="{{ asset('images/icons/logout.svg') }}" alt="" class="w-5 h-5 brightness-0 invert opacity-80">
            </button>
        </form>
    </div>

</aside>
@endauth

