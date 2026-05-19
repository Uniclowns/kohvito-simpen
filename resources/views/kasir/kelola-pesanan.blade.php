<x-layouts.kasir title="Kelola Pesanan" page-title="Kelola Pesanan">

    <x-slot:headerEnd>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-[#D9C7C7] flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/icons/KVT ICON USER.svg') }}" alt="User Avatar"
                    class="w-12 h-12 object-contain">
            </div>
            <span class="text-[#460001] text-[22px] tracking-[1px]">
                {{ auth()->user()?->nama_lengkap ?? (auth()->user()?->name ?? 'Kasir') }}
            </span>
        </div>
    </x-slot:headerEnd>

    @if (session('success') && !session('order_action'))
        <div class="mb-6 p-5 bg-green-50 border border-green-200 text-green-800 rounded-lg text-[16px]">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-5 bg-red-50 border border-red-200 text-red-800 rounded-lg text-[16px]">
            {{ session('error') }}
        </div>
    @endif

    @if ($pesanans->isEmpty())
        <div class="bg-white rounded-[9px] border border-gray-200 p-16 text-center">
            <p class="text-gray-500 text-[18px]">Tidak ada pesanan aktif saat ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-12 gap-y-12 pb-40">
            @foreach ($pesanans as $pesanan)
                @php
                    $isWaiting = $pesanan->status_pesanan === 'menunggu konfirmasi';
                    $statusLabel = $isWaiting ? 'Waiting' : 'Processing';
                    $statusBg = $isWaiting ? 'bg-[#E52E2D]' : 'bg-[#FFE62F]';
                    $statusText = $isWaiting ? 'text-white' : 'text-[#1A1A1A]';
                    $modalId = 'pesanan-detail-' . md5($pesanan->no_pesanan);
                    $items = $pesanan->detailPesanan;
                    $visibleItems = $items->take(4);
                    $remaining = max(0, $items->count() - 4);
                    $visibleImages = $items->take(4);
                    $imagesRemaining = max(0, $items->count() - 4);
                @endphp

                <div
                    class="bg-[#681F1F] rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] overflow-hidden flex flex-col">
                    <div class="flex items-center gap-3 p-4">
                        <div class="bg-[#D9C7C7] flex items-center justify-center px-4 rounded-[9px] self-stretch">
                            <div class="text-center">
                                <p
                                    class="text-[#460001] text-[14px] font-bold uppercase leading-[18px] tracking-[0.7px]">
                                    Table {{ $pesanan->meja?->no_meja ?? '-' }}
                                </p>
                                <p class="text-[#1A1A1A] text-[12px] leading-[14px] tracking-[0.5px]">(indoor Lt 1)</p>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1 flex flex-col">
                            <div class="flex items-center justify-between gap-2 w-full">
                                <p
                                    class="min-w-0 text-white text-[22px] font-bold leading-[30px] tracking-[1px] truncate">
                                    {{ $pesanan->nama_konsumen ?? '-' }}
                                </p>
                                <span
                                    class="{{ $statusBg }} {{ $statusText }} shrink-0 inline-flex items-center justify-center text-[12px] leading-[14px] tracking-[0.5px] px-2 py-1 rounded-[6px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between w-full text-white/60 text-[12px] leading-[14px] tracking-[0.5px] mt-1">
                                <p class="truncate w-[80px]">Order #{{ Str::limit($pesanan->no_pesanan, 8, '') }}</p>
                                <div class="flex gap-1 items-start justify-end whitespace-nowrap">
                                    @if ($pesanan->tgl_pembayaran)
                                        <p>{{ $pesanan->tgl_pembayaran->translatedFormat('l, d F Y') }}</p>
                                        <p>{{ $pesanan->tgl_pembayaran->format('H:i') }}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-t-[9px] p-4 h-[410px] flex flex-col gap-3">
                        <div class="flex flex-col justify-between flex-1 min-h-0 w-full">
                            <div class="flex flex-col gap-2 h-[170px] max-h-[170px] overflow-hidden w-full">
                                @forelse ($visibleItems as $detail)
                                    @php
                                        $menu = $detail->menu;
                                        $isDrink = $menu?->jenis_menu === 'Minuman';
                                        $variant = $isDrink ? $menu?->tipe_minuman : $menu?->kategori_makanan;
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <p
                                            class="capitalize text-black text-[14px] font-bold leading-[18px] tracking-[0.6px]">
                                            {{ $detail->jumlah }} {{ $menu?->nama_menu ?? 'Menu' }}
                                            @if ($variant)
                                                <span class="italic text-[#460001]">({{ $variant }})</span>
                                            @endif
                                        </p>
                                        @if ($detail->catatan)
                                            <p class="text-[#808080] text-[12px] leading-[14px] tracking-[0.5px]">
                                                {{ $detail->catatan }}
                                            </p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-[#808080] text-[14px] italic">Tidak ada item.</p>
                                @endforelse
                                @if ($remaining > 0)
                                    <p class="text-black text-[12px] leading-[14px] tracking-[0.5px]">
                                        + {{ $remaining }} Lainnya
                                    </p>
                                @endif
                            </div>

                            @if ($pesanan->catatan_pesanan)
                                <div
                                    class="bg-[#F6F6F6] rounded-[9px] px-4 py-3 flex flex-col gap-1.5 justify-center w-full">
                                    <p
                                        class="text-[14px] font-bold text-[#460001] text-left tracking-[0.6px] leading-[18px] capitalize">
                                        Notes Pemesanan
                                    </p>
                                    <p class="text-[14px] text-[#1A1A1A] leading-[16px] tracking-[0.5px]">
                                        {{ $pesanan->catatan_pesanan }}
                                    </p>
                                </div>
                            @endif

                            @if ($visibleImages->isNotEmpty())
                                <div class="flex items-center gap-2 h-[72px] w-full overflow-hidden">
                                    @foreach ($visibleImages as $detail)
                                        @php
                                            $menu = $detail->menu;
                                            $imgType = $menu?->jenis_menu === 'Makanan' ? 'food' : 'drink';
                                            $imgSrc = $menu?->gambar_menu
                                                ? (str_starts_with($menu->gambar_menu, 'http')
                                                    ? $menu->gambar_menu
                                                    : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                                : null;
                                        @endphp
                                        <div
                                            class="w-[72px] h-[72px] rounded-[9px] overflow-hidden bg-[#F6F6F6] shrink-0">
                                            @if ($imgSrc)
                                                <img src="{{ $imgSrc }}" alt="{{ $menu?->nama_menu ?? 'Menu' }}"
                                                    class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                    @endforeach
                                    @if ($imagesRemaining > 0)
                                        <div class="flex-shrink-0 w-[60px] text-center">
                                            <p class="text-black text-[12px] leading-[14px] tracking-[0.5px]">+
                                                {{ $imagesRemaining }}</p>
                                            <p class="text-black text-[12px] leading-[14px] tracking-[0.5px]">Lainnya
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="flex gap-3 w-full">
                                <button type="button" data-modal-target="{{ $modalId }}"
                                    class="flex-1 text-center bg-[#CCCCCC] text-[#681F1F] text-[16px] leading-[22px] tracking-[0.7px] px-4 py-2.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-gray-300 transition-all">
                                    Detail
                                </button>

                                @if ($isWaiting)
                                    <form method="POST"
                                        action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}"
                                        class="flex-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="w-full bg-[#681F1F] text-white text-[16px] leading-[22px] tracking-[0.7px] px-4 py-2.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
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
                                            class="w-full bg-[#58E52D] text-white text-[16px] leading-[22px] tracking-[0.7px] px-4 py-2.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-95 transition-all">
                                            Selesai
                                        </button>
                                    </form>
                                    <button type="button"
                                        data-order-print-url="{{ route('kasir.pesanan.cetak', $pesanan->no_pesanan) }}"
                                        class="flex-1 text-center bg-[#681F1F] text-white text-[16px] leading-[22px] tracking-[0.7px] px-4 py-2.5 rounded-[9px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
                                        Cetak Struk
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @foreach ($pesanans as $pesanan)
            @php
                $isWaiting = $pesanan->status_pesanan === 'menunggu konfirmasi';
                $statusLabel = $isWaiting ? 'Waiting' : 'Processing';
                $statusBg = $isWaiting ? 'bg-[#E52E2D]' : 'bg-[#FFE62F]';
                $statusText = $isWaiting ? 'text-white' : 'text-[#1A1A1A]';
                $modalId = 'pesanan-detail-' . md5($pesanan->no_pesanan);
                $subtotalPesanan = (int) $pesanan->detailPesanan->sum('subtotal');
                $ppnPesanan = (int) round($subtotalPesanan * 0.11);
                $totalPesanan = $subtotalPesanan + $ppnPesanan;
            @endphp
            <div id="{{ $modalId }}"
                class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/35 px-4 py-8" data-modal>
                <div
                    class="relative w-full max-w-[540px] max-h-[92vh] bg-white rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] overflow-hidden flex flex-col py-7">
                    <button type="button" class="absolute right-8 top-7 z-10 text-[#460001] hover:opacity-70"
                        data-modal-close aria-label="Tutup detail pesanan">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                    <div class="px-8">
                        <h3 class="text-black text-[28px] font-bold leading-[34px] tracking-[1.2px] mb-3">Detail
                            Pesanan</h3>
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

                    <div class="relative min-h-0 flex-1 flex">
                        <div class="flex-1 pl-8 pr-3 overflow-y-auto max-h-[460px] flex flex-col gap-3"
                            data-scroll-panel>
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
                                        <div class="border-t border-[#E6E6E6]"></div>
                                    @endif
                                    <div class="bg-white flex items-start gap-3 px-3 py-3 rounded-[9px]">
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
                                                        <p
                                                            class="text-[#808080] text-[12px] leading-[14px] tracking-[0.5px]">
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
                                <div class="bg-[#D9C7C7] rounded-[9px] px-4 py-3 flex flex-col gap-1.5">
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
                                class="flex flex-col gap-1.5 text-[14px] font-bold leading-[18px] tracking-[0.6px] capitalize">
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

                            <div class="border-t border-[#E6E6E6]"></div>

                            <div
                                class="flex items-center gap-4 text-[16px] font-bold leading-[20px] tracking-[0.6px] capitalize">
                                <p class="flex-1 text-[#460001]">Total Pemesanan</p>
                                <p class="text-[#1A1A1A] whitespace-nowrap">
                                    {{ number_format($totalPesanan, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if ($pesanan->detailPesanan->count() > 4)
                            <div class="absolute left-0 right-0 bottom-0 h-[110px] flex items-end justify-center pb-5 pointer-events-none [background:linear-gradient(0deg,#B0ADAD_12.5%,rgba(255,255,255,0)_100%)] backdrop-blur-[4.8px] transition-opacity duration-150"
                                data-scroll-hint>
                                <div class="flex items-center gap-1">
                                    <p class="text-[#460001] text-[20px] leading-[28px] tracking-[0.9px]">
                                        Scroll Untuk Melihat Menu Lainnya
                                    </p>
                                    <svg class="w-5 h-5 text-[#460001]" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 9l6 6 6-6" />
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="px-8 pt-4 flex items-stretch gap-3">
                        <button type="button" data-modal-close
                            class="flex-1 bg-[#CCCCCC] text-[#681F1F] text-[16px] leading-[22px] tracking-[0.7px] rounded-[9px] px-4 py-2.5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-[#BEBEBE] transition-colors">
                            Kembali
                        </button>
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
                            <button type="button"
                                data-order-print-url="{{ route('kasir.pesanan.cetak', $pesanan->no_pesanan) }}"
                                class="flex-1 bg-[#681F1F] text-white text-[16px] leading-[22px] tracking-[0.7px] rounded-[9px] px-4 py-2.5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
                                Cetak Struk
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div id="kasir-order-success" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/35 px-4"
        data-success-modal data-initial-action="{{ session('order_action') }}">
        <div
            class="relative w-full max-w-[540px] bg-white rounded-[6px] shadow-[2px_4px_4px_rgba(0,0,0,0.35)] px-10 pt-16 pb-9">
            <button type="button" class="absolute right-9 top-8 text-[#460001] hover:opacity-70" data-success-close
                aria-label="Tutup notifikasi pesanan">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
            <div class="flex flex-col items-center text-center">
                <img src="{{ asset('images/illustration/print success.svg') }}" alt=""
                    class="w-[240px] h-[250px] object-contain mb-7">
                <p id="kasir-order-success-title"
                    class="text-[#460001] text-[26px] font-bold leading-[32px] tracking-[1.1px] max-w-[440px]">
                    Berhasil Menerima Pesanan
                </p>
                <p id="kasir-order-success-subtitle"
                    class="hidden mt-3 text-[#808080] text-[16px] leading-[22px] tracking-[0.7px] max-w-[440px]"></p>
                <div class="w-full flex justify-end mt-8">
                    <button type="button" data-success-close
                        class="bg-[#CCCCCC] text-[#681F1F] text-[16px] tracking-[0.7px] rounded-[9px] px-8 py-2.5 shadow-[2px_4px_4px_rgba(0,0,0,0.25)] hover:bg-[#BEBEBE] transition-colors">
                        Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-slot:pageFooter>
        <x-kasir-footer />
    </x-slot:pageFooter>

    <x-slot:scripts>
        <script>
            (() => {
                const openModal = (modal) => {
                    if (!modal) return;

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    window.setTimeout(updateScrollHints, 0);
                };

                const closeModal = (modal) => {
                    if (!modal) return;

                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    if (!document.querySelector('[data-modal].flex, [data-success-modal].flex')) {
                        document.body.classList.remove('overflow-hidden');
                    }
                };

                const updateScrollHints = () => {
                    document.querySelectorAll('[data-scroll-panel]').forEach((panel) => {
                        const hint = panel.parentElement?.querySelector('[data-scroll-hint]');
                        if (!hint) return;

                        const hasOverflow = panel.scrollHeight > panel.clientHeight + 4;
                        const shouldShow = hasOverflow && panel.scrollTop < 12;
                        hint.classList.toggle('opacity-0', !shouldShow);
                    });
                };

                document.querySelectorAll('[data-scroll-panel]').forEach((panel) => {
                    panel.addEventListener('scroll', updateScrollHints);
                });
                window.addEventListener('resize', updateScrollHints);

                document.querySelectorAll('[data-modal-target]').forEach((button) => {
                    button.addEventListener('click', () => openModal(document.getElementById(button.dataset
                        .modalTarget)));
                });

                document.querySelectorAll('[data-modal-close]').forEach((button) => {
                    button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
                });

                document.querySelectorAll('[data-modal]').forEach((modal) => {
                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) closeModal(modal);
                    });
                });

                const successModal = document.querySelector('[data-success-modal]');
                const successTitle = document.getElementById('kasir-order-success-title');
                const successSubtitle = document.getElementById('kasir-order-success-subtitle');
                const successCopy = {
                    accepted: {
                        title: 'Berhasil Menerima Pesanan',
                        subtitle: '',
                    },
                    printed: {
                        title: 'Berhasil Mencetak Struk',
                        subtitle: '',
                    },
                    completed: {
                        title: 'Berhasil Menyelesaikan Pesanan',
                        subtitle: 'Pesanan berhasil dipindahkan ke histori pesanan',
                    },
                };

                const openSuccess = (type) => {
                    const copy = successCopy[type] ?? successCopy.accepted;
                    if (successTitle) successTitle.textContent = copy.title;
                    if (successSubtitle) {
                        successSubtitle.textContent = copy.subtitle;
                        successSubtitle.classList.toggle('hidden', !copy.subtitle);
                    }
                    openModal(successModal);
                };

                document.querySelectorAll('[data-order-print-url]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const printWindow = window.open(button.dataset.orderPrintUrl, '_blank');
                        if (printWindow) printWindow.opener = null;
                        openSuccess('printed');
                    });
                });

                document.querySelectorAll('[data-success-close]').forEach((button) => {
                    button.addEventListener('click', () => closeModal(successModal));
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;
                    document.querySelectorAll('[data-modal].flex, [data-success-modal].flex').forEach(closeModal);
                });

                if (successModal?.dataset.initialAction) {
                    openSuccess(successModal.dataset.initialAction);
                }
                updateScrollHints();
            })();
        </script>
    </x-slot:scripts>

</x-layouts.kasir>
