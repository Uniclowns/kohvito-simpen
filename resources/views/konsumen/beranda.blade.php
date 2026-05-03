<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu — Meja {{ $meja->no_meja }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-4 py-4 sticky top-0 z-10">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">{{ config('app.name') }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">Meja {{ $meja->no_meja }}</p>
            </div>
            <a href="{{ route('konsumen.keranjang') }}"
               class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Keranjang
            </a>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-6">

        {{-- Category Filter Tabs --}}
        @if ($kategoris->isNotEmpty())
            <div class="flex gap-2 overflow-x-auto pb-2 mb-6">
                <a href="#semua"
                   class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium bg-amber-600 text-white transition-colors">
                    Semua
                </a>
                @foreach ($kategoris as $kategori)
                    <a href="#kategori-{{ $kategori->id_kategori }}"
                       class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium bg-white border border-gray-200 text-gray-700 hover:bg-amber-50 hover:border-amber-300 transition-colors">
                        {{ $kategori->nama_kategori }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Menu Sections by Category --}}
        <div id="semua" class="space-y-8">
            @forelse ($kategoris as $kategori)
                @if ($kategori->menu->isNotEmpty())
                    <section id="kategori-{{ $kategori->id_kategori }}">
                        <h2 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-100">
                            {{ $kategori->nama_kategori }}
                        </h2>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($kategori->menu as $menu)
                                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col">
                                    {{-- Gambar Menu --}}
                                    @if ($menu->gambar_menu)
                                        <img src="{{ asset('storage/' . $menu->gambar_menu) }}"
                                             alt="{{ $menu->nama_menu }}"
                                             class="w-full h-32 object-cover">
                                    @else
                                        <div class="w-full h-32 bg-gray-100 flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">Tidak ada gambar</span>
                                        </div>
                                    @endif

                                    {{-- Info Menu --}}
                                    <div class="p-3 flex flex-col flex-1">
                                        <h3 class="text-sm font-semibold text-gray-800 leading-snug">
                                            {{ $menu->nama_menu }}
                                        </h3>
                                        @if ($menu->deskripsi)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 flex-1">
                                                {{ $menu->deskripsi }}
                                            </p>
                                        @else
                                            <div class="flex-1"></div>
                                        @endif
                                        <div class="mt-3 flex items-center justify-between gap-2">
                                            <span class="text-sm font-bold text-amber-700">
                                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                            </span>

                                            {{-- Tambah ke Keranjang --}}
                                            <form method="POST"
                                                  action="{{ route('konsumen.keranjang.tambah') }}">
                                                @csrf
                                                <input type="hidden" name="id_menu" value="{{ $menu->id_menu }}">
                                                <input type="hidden" name="jumlah" value="1">
                                                <button type="submit"
                                                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                                    Tambah
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @empty
                <div class="text-center py-16">
                    <p class="text-gray-500 text-sm">Belum ada menu tersedia saat ini.</p>
                </div>
            @endforelse
        </div>

    </main>

</body>
</html>
