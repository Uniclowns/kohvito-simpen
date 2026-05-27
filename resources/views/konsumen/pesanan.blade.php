{{-- Daftar Pesanan Konsumen
    Route: konsumen.pesanan (/pesanan)
    Controller: PesananController@index
    Variables: $pesanans
--}}
<x-layouts.konsumen
    :title="'Pesanan - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] pb-[140px] lg:pb-0 font-sans text-brand-black kvt-konsumen-mobile-view">

    @php
        $mejaNo = session('id_meja_no');
        $keranjang = session('keranjang', []);
        $cartCount = array_sum(array_column($keranjang, 'jumlah'));

        // Chip status pesanan (Figma: pill kuning #FFE62F untuk "Pesanan Diproses").
        $statusPesananChip = [
            'menunggu konfirmasi' => ['Menunggu Konfirmasi', 'bg-amber-200 text-[#1a1a1a]'],
            'diproses'            => ['Pesanan Diproses', 'bg-[#FFE62F] text-[#1a1a1a]'],
            'selesai'             => ['Pesanan Selesai', 'bg-emerald-200 text-[#1a1a1a]'],
            'dibatalkan'          => ['Dibatalkan', 'bg-[#CCCCCC] text-[#1a1a1a]'],
        ];
    @endphp

    {{-- Top header bar --}}
    <header class="bg-brand-dark safe-top">
        <div class="max-w-md md:max-w-3xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl mx-auto px-[18px] pt-[14px] pb-3 flex items-center justify-between">
            <p class="flex-1 text-white text-[12px] leading-4 font-bold tracking-[0.6px] capitalize">Pesanan</p>
            <div class="w-7 h-7 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito" class="w-full h-full object-contain">
            </div>
            <p class="flex-1 text-white text-[12px] font-bold leading-4 tracking-[0.6px] capitalize text-right">
                Meja {{ $mejaNo ?? '-' }}
            </p>
        </div>
    </header>

    <main class="max-w-md md:max-w-3xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl mx-auto px-[18px] pt-4">
        {{-- Back link --}}
        @if ($mejaNo)
            <a href="{{ route('konsumen.beranda', $mejaNo) }}"
               class="inline-flex items-center gap-3 text-brand-dark active:opacity-70 mb-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="text-[20px] leading-8 font-bold tracking-[1px]">Kembali</span>
            </a>
        @endif

        @if ($pesanans->isEmpty())
            {{-- Empty state — Figma 1465-23298 "Tidak Ada Pesanan" --}}
            <div class="flex min-h-[58vh] flex-col items-center justify-center text-center px-6">
                <img src="{{ asset('images/illustration/empty-chef.png') }}" alt=""
                     class="w-[170px] h-auto object-contain opacity-60 mb-4" data-anim="fade-up">
                <p class="text-[20px] leading-7 font-bold tracking-[1px] text-[#CCCCCC]">Tidak Ada Pesanan</p>
                <p class="mt-1 text-[14px] leading-5 tracking-[0.7px] text-[#CCCCCC] max-w-[240px]">
                    Silahkan Melakukan Pemesanan di Halaman Menu
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-[18px] md:grid-cols-2 md:gap-6 lg:grid-cols-3" data-anim="stagger">
                @foreach ($pesanans as $pesanan)
                    @php
                        $items = $pesanan->detailPesanan;
                        $subtotal = $items->sum('subtotal');
                        $ppn = (int) round($subtotal * 0.11);
                        $total = (int) $pesanan->total_harga;
                        $chip = $statusPesananChip[$pesanan->status_pesanan] ?? ['-', 'bg-[#CCCCCC] text-[#1a1a1a]'];
                        $isLunas = $pesanan->status_pembayaran === 'lunas';
                    @endphp

                    {{-- Kartu pesanan — Figma 1432-23620 --}}
                    <article class="bg-white rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] p-[18px] flex flex-col gap-[14px]" data-anim-item>
                        {{-- Header: Table + name + status chip --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[12px] leading-4 tracking-[0.6px] font-bold text-brand-dark">
                                    TABLE {{ $pesanan->meja->no_meja ?? '-' }}
                                </p>
                                <h2 class="text-[20px] leading-7 font-bold tracking-[1px] text-brand-dark truncate">
                                    {{ $pesanan->nama_konsumen }}
                                </h2>
                            </div>
                            <span class="shrink-0 inline-flex items-center rounded-[4.5px] px-[6px] py-[3px] text-[10px] leading-3 tracking-[0.5px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] {{ $chip[1] }}">
                                {{ $chip[0] }}
                            </span>
                        </div>

                        {{-- Order # + date --}}
                        <div class="-mt-2 flex flex-col gap-1 text-[10px] leading-3 tracking-[0.5px] text-brand-red min-[390px]:flex-row min-[390px]:items-center min-[390px]:justify-between">
                            <span class="kvt-break-anywhere font-mono">Order #{{ $pesanan->no_pesanan }}</span>
                            <span class="shrink-0">
                                @if ($pesanan->tgl_pembayaran)
                                    {{ $pesanan->tgl_pembayaran->translatedFormat('l, d M Y H:i') }}
                                @else
                                    {{ now()->translatedFormat('l, d M Y H:i') }}
                                @endif
                            </span>
                        </div>

                        {{-- Item list --}}
                        <div class="flex flex-col">
                            @foreach ($items as $item)
                                @php
                                    $menu = $item->menu;
                                    $imgType = optional($menu)->jenis_menu === 'Makanan' ? 'food' : 'drink';
                                    $imgSrc = $menu && $menu->gambar_menu
                                        ? (str_starts_with($menu->gambar_menu, 'http')
                                            ? $menu->gambar_menu
                                            : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                        : null;
                                    $catatanParts = $item->catatan ? array_map('trim', explode('|', $item->catatan)) : [];
                                    $variant = null; $extras = []; $userNote = null;
                                    foreach ($catatanParts as $part) {
                                        if (preg_match('/^Suhu:\s*(.+)$/', $part, $m)) { $variant = $m[1]; }
                                        elseif (preg_match('/^Catatan:\s*(.+)$/', $part, $m)) { $userNote = $m[1]; }
                                        else { $extras[] = $part; }
                                    }
                                @endphp
                                <div class="flex items-start gap-3 py-3 {{ !$loop->first ? 'border-t border-brand-gray-extralight' : '' }}">
                                    <div class="w-[54px] h-[54px] rounded-[9px] overflow-hidden bg-brand-gray-extralight flex-shrink-0">
                                        @if ($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[12px] leading-4 tracking-[0.6px] font-bold text-brand-black">
                                            {{ $item->jumlah }} {{ optional($menu)->nama_menu ?? '-' }}@if ($variant) <span class="text-brand-red italic">({{ $variant }})</span>@endif
                                        </p>
                                        @foreach ($extras as $extra)
                                            <p class="text-[10px] leading-3 tracking-[0.5px] text-brand-gray">{{ $extra }}</p>
                                        @endforeach
                                        @if ($userNote)
                                            <p class="text-[10px] leading-3 tracking-[0.5px] text-brand-red italic mt-0.5">"{{ $userNote }}"</p>
                                        @endif
                                    </div>
                                    <p class="text-right text-[12px] leading-4 tracking-[0.6px] font-bold text-brand-black flex-shrink-0">
                                        {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Notes Pemesanan --}}
                        @if (!empty($pesanan->catatan_pesanan))
                            <div class="bg-[rgba(70,0,1,0.06)] border border-[rgba(70,0,1,0.12)] rounded-[9px] p-3">
                                <p class="text-[12px] leading-4 tracking-[0.6px] font-bold text-brand-dark mb-1">Notes Pemesanan</p>
                                <p class="text-[10px] leading-3 tracking-[0.5px] text-brand-gray text-justify">{{ $pesanan->catatan_pesanan }}</p>
                            </div>
                        @endif

                        {{-- Totals --}}
                        <div class="border-t border-brand-gray-extralight pt-3 flex flex-col gap-1.5 text-[12px] leading-4 tracking-[0.6px]">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-brand-dark">SubTotal Pemesanan</span>
                                <span class="text-brand-black">{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-brand-dark">Ppn 11%</span>
                                <span class="text-brand-black">{{ number_format($ppn, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-brand-gray-light pt-1.5">
                                <span class="font-bold text-brand-dark">Total Pemesanan</span>
                                <span class="font-bold text-brand-black">{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="font-bold text-brand-gray">Status Pembayaran</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $isLunas ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-brand-red border border-brand-red/20' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isLunas ? 'bg-emerald-500' : 'bg-red-600 animate-ping' }}"></span>
                                    {{ $isLunas ? 'Lunas' : 'Belum Lunas' }}
                                </span>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex flex-col gap-2.5">
                            @if ($isLunas)
                                <a href="{{ route('konsumen.pesanan.kuitansi', $pesanan->no_pesanan) }}"
                                   class="flex h-8 w-full items-center justify-center rounded-[9px] bg-[#CCCCCC] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-[#681F1F] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95">
                                    Cetak Struk Digital
                                </a>
                            @else
                                <a href="{{ route('konsumen.pembayaran', $pesanan->no_pesanan) }}"
                                   class="flex h-8 w-full items-center justify-center rounded-[9px] bg-brand-red px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95">
                                    Bayar Sekarang
                                </a>
                            @endif

                            <a href="{{ route('konsumen.lacak.detail', $pesanan->no_pesanan) }}"
                               class="flex h-8 w-full items-center justify-center rounded-[9px] bg-[#681F1F] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95">
                                Lacak Pesanan
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>

    <x-konsumen-bottom-nav active="pesanan" :mejaNo="$mejaNo" :cartCount="$cartCount" />
</x-layouts.konsumen>
