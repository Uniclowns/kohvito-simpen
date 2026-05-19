<x-layouts.kasir title="Detail Pesanan" page-title="Detail Pesanan">

    @php
        $isWaiting = $pesanan->status_pesanan === 'menunggu konfirmasi';
        $statusLabel = $isWaiting ? 'Waiting' : 'Processing';
        $statusBg = $isWaiting ? 'bg-[#E52E2D]' : 'bg-[#FFE62F]';
        $statusText = $isWaiting ? 'text-white' : 'text-[#1A1A1A]';
        $subtotalPesanan = (int) $pesanan->detailPesanan->sum('subtotal');
        $ppnPesanan = (int) round($subtotalPesanan * 0.11);
        $totalPesanan = $subtotalPesanan + $ppnPesanan;
    @endphp

    <div class="max-w-[540px] mx-auto mb-40">
        <div
            class="relative bg-[#EDE4E4] rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] overflow-hidden flex flex-col py-7">

            <a href="{{ route('kasir.pesanan.index') }}"
                class="absolute right-8 top-7 z-10 text-[#460001] hover:opacity-70"
                aria-label="Tutup detail pesanan">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 6l12 12M18 6L6 18" />
                </svg>
            </a>

            <div class="px-8">
                <h3 class="text-black text-[28px] font-bold leading-[34px] tracking-[1.2px] mb-3">Detail Pesanan</h3>
            </div>

            <div class="px-8 py-3 flex items-center gap-5">
                <div
                    class="bg-[#D9C7C7] rounded-[9px] px-4 py-2 text-center shrink-0 flex flex-col items-center justify-center">
                    <p
                        class="text-[#460001] text-[14px] font-bold uppercase leading-[18px] tracking-[0.7px] whitespace-nowrap">
                        TABLE {{ $pesanan->meja?->no_meja ?? '-' }}
                    </p>
                    <p class="text-[#1A1A1A] text-[12px] leading-[14px] tracking-[0.5px] whitespace-nowrap">
                        ({{ $pesanan->meja?->lokasi ?? 'indoor Lt 1' }})
                    </p>
                </div>
                <div class="min-w-0 flex-1 flex flex-col">
                    <div class="flex items-center justify-between w-full gap-3">
                        <p class="text-[#460001] text-[24px] font-bold leading-[30px] tracking-[1px] truncate">
                            {{ $pesanan->nama_konsumen ?? '-' }}
                        </p>
                        <span
                            class="{{ $statusBg }} {{ $statusText }} shrink-0 inline-flex items-center justify-center text-[12px] leading-[14px] tracking-[0.5px] px-2 py-1 rounded-[6px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div
                        class="flex items-center justify-between w-full text-[#681F1F] text-[12px] leading-[14px] tracking-[0.5px] mt-1">
                        <p class="w-[80px] truncate">Order #{{ Str::limit($pesanan->no_pesanan, 8, '') }}</p>
                        <p class="whitespace-nowrap">
                            {{ $pesanan->tgl_pembayaran?->translatedFormat('l, d F Y H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="pl-8 pr-3 flex flex-col gap-3">
                <div class="flex flex-col">
                    @foreach ($pesanan->detailPesanan as $idx => $detail)
                        @php
                            $menu = $detail->menu;
                            $isDrink = $menu?->jenis_menu === 'Minuman';
                            $variant = $isDrink ? $menu?->tipe_minuman : $menu?->kategori_makanan;
                            $imgType = $menu?->jenis_menu === 'Makanan' ? 'food' : 'drink';
                            $imgSrc = $menu?->gambar_menu
                                ? (str_starts_with($menu->gambar_menu, 'http')
                                    ? $menu->gambar_menu
                                    : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                : asset('images/logo/KOHVITO LOGO ONLY RED.png');
                            $notes = collect(preg_split('/\r\n|\r|\n|,/', (string) $detail->catatan))
                                ->map(fn($n) => trim($n))
                                ->filter()
                                ->values();
                        @endphp
                        @if ($idx > 0)
                            <div class="border-t border-[#D9C7C7]"></div>
                        @endif
                        <div class="flex items-start gap-3 px-3 py-3 rounded-[9px]">
                            <div
                                class="w-[62px] h-[62px] rounded-[9px] overflow-hidden shrink-0 bg-[#F6F6F6]">
                                <img src="{{ $imgSrc }}" alt="{{ $menu?->nama_menu ?? 'Menu' }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0 flex justify-between gap-3">
                                <div class="flex flex-col gap-0.5 min-w-0">
                                    <p
                                        class="text-[14px] font-bold leading-[18px] tracking-[0.6px] capitalize text-black">
                                        {{ $detail->jumlah }} {{ $menu?->nama_menu ?? 'Menu' }}@if ($variant)
                                            <span class="italic text-[#460001]">({{ $variant }})</span>
                                        @endif
                                    </p>
                                    @if ($notes->isNotEmpty())
                                        @foreach ($notes as $note)
                                            <p class="text-[#808080] text-[12px] leading-[14px] tracking-[0.5px]">
                                                {{ $note }}
                                            </p>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="text-right shrink-0 flex flex-col gap-0.5">
                                    <p
                                        class="text-black text-[14px] font-bold leading-[18px] tracking-[0.6px] whitespace-nowrap">
                                        {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </p>
                                    @if ($notes->isNotEmpty())
                                        @foreach ($notes as $note)
                                            <p
                                                class="text-[#808080] text-[12px] leading-[14px] tracking-[0.5px] whitespace-nowrap">
                                                +2.000
                                            </p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($pesanan->catatan_pesanan)
                    <div class="bg-[#D9C7C7] rounded-[9px] px-4 py-3 flex flex-col gap-1.5 mx-3">
                        <p
                            class="text-[#460001] text-[14px] font-bold leading-[18px] tracking-[0.6px] text-left capitalize">
                            Notes Pemesanan
                        </p>
                        <p class="text-[#1A1A1A] text-[12px] leading-[16px] tracking-[0.5px]">
                            {{ $pesanan->catatan_pesanan }}
                        </p>
                    </div>
                @endif

                <div
                    class="flex flex-col gap-1.5 text-[14px] font-bold leading-[18px] tracking-[0.6px] capitalize px-3">
                    <div class="flex items-center gap-4">
                        <p class="flex-1 text-[#460001]">SubTotal Pemesanan</p>
                        <p class="text-[#1A1A1A] whitespace-nowrap">
                            {{ number_format($subtotalPesanan, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <p class="flex-1 text-[#460001]">Ppn 11%</p>
                        <p class="text-[#1A1A1A] whitespace-nowrap">
                            {{ number_format($ppnPesanan, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="border-t border-[#D9C7C7] mx-3"></div>

                <div
                    class="flex items-center gap-4 text-[16px] font-bold leading-[20px] tracking-[0.6px] capitalize px-3">
                    <p class="flex-1 text-[#460001]">Total Pemesanan</p>
                    <p class="text-[#1A1A1A] whitespace-nowrap">
                        {{ number_format($totalPesanan, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="px-8 pt-5 flex items-stretch gap-3">
                <a href="{{ route('kasir.pesanan.index') }}"
                    class="flex-1 text-center bg-[#CCCCCC] text-[#681F1F] text-[16px] leading-[22px] tracking-[0.7px] rounded-[9px] px-4 py-2.5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-[#BEBEBE] transition-colors">
                    Tutup
                </a>
                @if ($isWaiting)
                    <form method="POST"
                        action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}"
                        class="flex-1">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="w-full bg-[#681F1F] text-white text-[16px] leading-[22px] tracking-[0.7px] rounded-[9px] px-4 py-2.5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
                            Terima Pesanan
                        </button>
                    </form>
                @else
                    <form method="POST"
                        action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}"
                        class="flex-1">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="w-full bg-[#58E52D] text-white text-[16px] leading-[22px] tracking-[0.7px] rounded-[9px] px-4 py-2.5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-95 transition-all">
                            Selesai
                        </button>
                    </form>
                    <a href="{{ route('kasir.pesanan.cetak', $pesanan->no_pesanan) }}" target="_blank"
                        class="flex-1 text-center bg-[#681F1F] text-white text-[16px] leading-[22px] tracking-[0.7px] rounded-[9px] px-4 py-2.5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
                        Cetak Struk
                    </a>
                @endif
            </div>
        </div>
    </div>

    <x-slot:pageFooter>
        <x-kasir-footer />
    </x-slot:pageFooter>

</x-layouts.kasir>
