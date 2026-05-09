@auth
<aside class="w-64 min-h-screen bg-brand-dark flex flex-col flex-shrink-0">

    {{-- Logo --}}
    <div class="px-6 py-6 border-b border-white/10">
        <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="{{ config('app.name') }}" class="h-8 w-auto">
        <p class="text-white/50 text-xs mt-2">
            {{ auth()->user()->id_role === 1 ? 'Panel Admin' : 'Panel Kasir' }}
        </p>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1">

        @if (auth()->user()->id_role === 1)
            {{-- Admin Navigation --}}
            <a href="{{ route('admin.beranda') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.beranda') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <img src="{{ asset('images/icons/template.svg') }}" alt="" class="w-4 h-4 brightness-0 invert opacity-80">
                Beranda
            </a>

            <a href="{{ route('admin.menu.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.menu.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <img src="{{ asset('images/icons/coffee.svg') }}" alt="" class="w-4 h-4">
                Kelola Menu
            </a>

            <a href="{{ route('admin.kategori.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.kategori.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <img src="{{ asset('images/icons/menu icon.svg') }}" alt="" class="w-4 h-4">
                Kelola Kategori
            </a>

            <a href="{{ route('admin.pengguna-kasir.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.pengguna-kasir.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <img src="{{ asset('images/icons/users.svg') }}" alt="" class="w-4 h-4">
                Pengguna Kasir
            </a>

            <a href="{{ route('admin.laporan-keuangan.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.laporan-keuangan.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan Keuangan
            </a>

        @else
            {{-- Kasir Navigation --}}
            <a href="{{ route('kasir.beranda') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('kasir.beranda') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <img src="{{ asset('images/icons/template.svg') }}" alt="" class="w-4 h-4 brightness-0 invert opacity-80">
                Beranda
            </a>

            <a href="{{ route('kasir.pesanan.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('kasir.pesanan.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <img src="{{ asset('images/icons/menu icon.svg') }}" alt="" class="w-4 h-4">
                Kelola Pesanan
            </a>

            <a href="{{ route('kasir.histori.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('kasir.histori.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Histori Pesanan
            </a>
        @endif

    </nav>

    {{-- User & Logout --}}
    <div class="px-4 py-4 border-t border-white/10">
        <p class="text-white/90 text-sm font-medium truncate">{{ auth()->user()->nama_lengkap }}</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-white/50 hover:text-white text-xs transition-colors">
                <img src="{{ asset('images/icons/logout.svg') }}" alt="" class="w-3.5 h-3.5">
                Keluar
            </button>
        </form>
    </div>

</aside>
@endauth
