<x-layouts.kasir title="Kelola Pesanan" page-title="Kelola Pesanan">

    <x-slot:headerEnd>
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-full bg-[#D9C7C7] flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/icons/KVT ICON USER.svg') }}" alt="User Avatar" class="w-6 h-6 object-contain">
            </div>
            <span class="text-[#460001] text-[20px] tracking-[1px]">{{ auth()->user()?->nama_lengkap ?? auth()->user()?->name ?? 'Kasir' }}</span>
        </div>
    </x-slot:headerEnd>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($pesanans->isEmpty())
        <div class="bg-white rounded-[9px] border border-gray-200 p-12 text-center">
            <p class="text-gray-500">Tidak ada pesanan aktif saat ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-12 gap-y-10">
            @foreach ($pesanans as $pesanan)
                @php
                    $isWaiting   = $pesanan->status_pesanan === 'menunggu konfirmasi';
                    $statusLabel = $isWaiting ? 'Waiting' : 'Processing';
                    $statusBg    = $isWaiting ? 'bg-[#E52E2D]' : 'bg-[#FFE62F]';
                    $items       = $pesanan->detailPesanan;
                    $visibleItems = $items->take(4);
                    $remaining    = max(0, $items->count() - 4);
                    $visibleImages = $items->take(4);
                    $imagesRemaining = max(0, $items->count() - 4);
                @endphp

                <div class="bg-[#681F1F] rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] overflow-hidden flex flex-col">

                    {{-- Maroon header --}}
                    <div class="flex items-center gap-3 p-3 relative">
                        <div class="bg-[#D9C7C7] flex items-center justify-center px-3 rounded-[9px] self-stretch">
                            <div class="text-center">
                                <p class="text-[#460001] text-[12px] font-bold uppercase leading-[16px] tracking-[0.6px]">
                                    Table {{ $pesanan->meja?->no_meja ?? '—' }}
                                </p>
                                <p class="text-[#460001] text-[10px] leading-[12px] tracking-[0.5px]">
                                    (indoor)
                                </p>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 pr-16">
                            <p class="text-white text-[20px] font-bold leading-[28px] tracking-[1px] truncate">
                                {{ $pesanan->nama_konsumen ?? '—' }}
                            </p>
                            <div class="flex items-center justify-between text-white/50 text-[10px] tracking-[0.5px]">
                                <p class="truncate w-[70px]">Order #{{ Str::limit($pesanan->no_pesanan, 8, '') }}</p>
                                <p class="text-right whitespace-nowrap">
                                    {{ $pesanan->tgl_pembayaran?->translatedFormat('D, d M Y H:i') ?? '—' }}
                                </p>
                            </div>
                        </div>
                        <span class="{{ $statusBg }} absolute top-0 right-0 text-black text-[10px] tracking-[0.5px] px-[6px] py-[3px] rounded-bl-[4.5px] rounded-tr-[9px]">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- White body --}}
                    <div class="bg-white p-3 flex flex-col gap-2 flex-1">
                        {{-- Items list --}}
                        <div class="flex flex-col gap-1">
                            @forelse ($visibleItems as $detail)
                                <div>
                                    <p class="capitalize text-black text-[12px] font-bold leading-[16px] tracking-[0.6px]">
                                        {{ $detail->jumlah }} {{ $detail->menu?->nama_menu ?? 'Menu' }}
                                    </p>
                                    @if ($detail->catatan)
                                        <p class="text-[#808080] text-[10px] leading-[12px] tracking-[0.5px]">
                                            {{ $detail->catatan }}
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-[#808080] text-[12px] italic">Tidak ada item.</p>
                            @endforelse
                            @if ($remaining > 0)
                                <p class="text-black text-[10px] leading-[12px] tracking-[0.5px]">
                                    + {{ $remaining }} Lainnya
                                </p>
                            @endif
                        </div>

                        {{-- Food images strip --}}
                        @if ($visibleImages->isNotEmpty())
                            <div class="flex items-center gap-2 mt-1">
                                @foreach ($visibleImages as $detail)
                                    @php
                                        $menu = $detail->menu;
                                        $imgType = $menu?->jenis_menu === 'Makanan' ? 'food' : 'drink';
                                        $imgSrc  = $menu?->gambar_menu
                                            ? (str_starts_with($menu->gambar_menu, 'http')
                                                ? $menu->gambar_menu
                                                : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                            : null;
                                    @endphp
                                    <div class="flex-1 aspect-square rounded-[9px] overflow-hidden bg-[#F6F6F6]">
                                        @if ($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $menu?->nama_menu }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @endforeach
                                @if ($imagesRemaining > 0)
                                    <div class="flex-shrink-0 w-[52px] text-center">
                                        <p class="text-black text-[10px] leading-[12px] tracking-[0.5px]">+ {{ $imagesRemaining }}</p>
                                        <p class="text-black text-[10px] leading-[12px] tracking-[0.5px]">Lainnya</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Status-aware action buttons --}}
                        <div class="flex gap-2 mt-2">
                            @if ($isWaiting)
                                <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}" class="flex-1">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="w-full bg-[#58E52D] text-white text-[14px] tracking-[0.7px] px-3 py-1.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-95 transition-all">
                                        Terima
                                    </button>
                                </form>
                                <a href="{{ route('kasir.pesanan.detail', $pesanan->no_pesanan) }}"
                                   class="flex-1 text-center bg-[#CCCCCC] text-[#681F1F] text-[14px] tracking-[0.7px] px-3 py-1.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-gray-300 transition-all">
                                    Detail
                                </a>
                            @else
                                <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}" class="flex-1">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="w-full bg-[#E52E2D] text-white text-[14px] tracking-[0.7px] px-3 py-1.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-95 transition-all">
                                        Selesai
                                    </button>
                                </form>
                                <a href="{{ route('kasir.pesanan.detail', $pesanan->no_pesanan) }}"
                                   class="flex-1 text-center bg-[#CCCCCC] text-[#681F1F] text-[14px] tracking-[0.7px] px-3 py-1.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-gray-300 transition-all">
                                    Detail
                                </a>
                                <a href="{{ route('kasir.pesanan.cetak', $pesanan->no_pesanan) }}" target="_blank"
                                   class="flex-1 text-center bg-[#681F1F] text-white text-[14px] tracking-[0.7px] px-3 py-1.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
                                    Cetak Struk
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-layouts.kasir>
