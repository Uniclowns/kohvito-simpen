<x-layouts.kasir title="Kelola Pesanan" page-title="Kelola Pesanan" content-width="1280px">

    <x-slot:headerEnd>
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-[#460001]">
                <img src="{{ asset('images/icons/KVT ICON USER.svg') }}" alt="User Avatar" class="h-12 w-12 object-contain">
            </div>
            <span class="text-[22px] leading-8 tracking-[1px] text-[#460001]">
                {{ auth()->user()?->nama_lengkap ?? (auth()->user()?->name ?? 'Username') }}
            </span>
        </div>
    </x-slot:headerEnd>

    @if (session('success') && !session('order_action'))
        <div class="mb-6 rounded-[9px] border border-green-200 bg-green-50 p-5 text-[16px] text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-[9px] border border-red-200 bg-red-50 p-5 text-[16px] text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($pesanans->isEmpty())
        <div class="rounded-[9px] border border-gray-200 bg-white p-16 text-center shadow-[2px_4px_4px_rgba(0,0,0,0.08)]">
            <p class="text-[18px] text-[#808080]">Tidak ada pesanan aktif saat ini.</p>
        </div>
    @else
        <div class="flex flex-col gap-8" data-order-content>
        <section class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3" data-order-grid>
            @foreach ($pesanans as $pesanan)
                @php
                    $isWaiting = $pesanan->status_pesanan === 'menunggu konfirmasi';
                    $statusLabel = $isWaiting ? 'Waiting' : 'Processing';
                    $statusBg = $isWaiting ? 'bg-[#E52E2D]' : 'bg-[#FFE62F]';
                    $statusText = $isWaiting ? 'text-white' : 'text-[#1A1A1A]';
                    $panelId = 'pesanan-detail-' . md5($pesanan->no_pesanan);
                    $items = $pesanan->detailPesanan;
                @endphp

                <article
                    class="flex h-[360px] min-w-0 flex-col overflow-hidden rounded-[9px] bg-[#681F1F] shadow-[2px_4px_4px_rgba(0,0,0,0.25)]"
                    data-order-card="{{ $panelId }}"
                    data-order-preview-card>
                        <div class="flex h-[82px] items-center gap-4 p-4">
                            <div class="flex h-full shrink-0 items-center justify-center rounded-[9px] bg-[#D9C7C7] px-4">
                                <div class="text-center leading-none">
                                    <p class="whitespace-nowrap text-[14px] font-bold uppercase leading-[18px] tracking-[0.6px] text-[#460001]">
                                        Table {{ $pesanan->meja?->no_meja ?? '-' }}
                                    </p>
                                    <p class="whitespace-nowrap text-[12px] leading-4 tracking-[0.5px] text-[#1A1A1A]">
                                        (indoor Lt 1)
                                    </p>
                                </div>
                            </div>

                            <div class="relative flex min-w-0 flex-1 flex-col justify-center">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="min-w-0 truncate text-[24px] font-bold leading-8 tracking-[1px] text-white">
                                        {{ $pesanan->nama_konsumen ?? '-' }}
                                    </p>
                                    <span class="{{ $statusBg }} {{ $statusText }} mt-0.5 inline-flex shrink-0 items-center justify-center rounded-[6px] px-2.5 py-1 text-[12px] leading-4 tracking-[0.5px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-3 text-[12px] leading-4 tracking-[0.5px] text-white/60">
                                    <p class="w-[90px] truncate">Order #{{ \Illuminate\Support\Str::limit($pesanan->no_pesanan, 8, '') }}</p>
                                    <div class="flex shrink-0 items-start justify-end gap-[3px] whitespace-nowrap">
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

                        <div class="flex min-h-0 flex-1 flex-col rounded-t-[9px] bg-white p-5">
                            <div class="min-h-0 flex-1 overflow-hidden" data-order-preview-area>
                                <div class="flex min-h-0 flex-col gap-2" data-order-preview-list>
                                    @forelse ($items as $detail)
                                        @php
                                            $menu = $detail->menu;
                                            $variant = $menu?->jenis_menu === 'Minuman' ? $menu?->tipe_minuman : $menu?->kategori_makanan;
                                        @endphp
                                        <div class="flex min-h-[20px] min-w-0 items-center gap-2" data-order-preview-item>
                                            <p class="min-w-0 shrink truncate text-[15px] font-bold capitalize leading-5 tracking-[0.6px] text-black">
                                                {{ $detail->jumlah }} {{ $menu?->nama_menu ?? 'Menu' }}@if ($variant)<span class="font-bold italic text-[#460001]">({{ $variant }})</span>@endif
                                            </p>
                                            @if ($detail->catatan)
                                                <p class="ml-auto max-w-[45%] shrink-0 truncate text-right text-[11px] leading-4 tracking-[0.4px] text-[#808080]">
                                                    {{ $detail->catatan }}
                                                </p>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-[14px] leading-5 tracking-[0.6px] text-[#808080]" data-order-preview-empty>Tidak ada item.</p>
                                    @endforelse

                                    <p class="hidden text-[12px] leading-4 tracking-[0.4px] text-[#808080]" data-order-preview-more>
                                        +0 Lainnya
                                    </p>
                                </div>
                            </div>

                            @if ($pesanan->catatan_pesanan)
                                <div class="mt-3 flex shrink-0 flex-col gap-1 rounded-[9px] bg-[#F6F6F6] px-4 py-2.5">
                                    <p class="text-[12px] font-bold capitalize leading-4 tracking-[0.5px] text-[#460001]">
                                        Notes Pemesanan
                                    </p>
                                    <p class="line-clamp-2 text-[11px] leading-4 tracking-[0.4px] text-[#1A1A1A]">
                                        {{ $pesanan->catatan_pesanan }}
                                    </p>
                                </div>
                            @endif

                            <div class="mt-4 flex h-11 shrink-0 gap-3">
                                <button type="button" data-order-panel-target="{{ $panelId }}"
                                    class="flex-1 rounded-[9px] bg-[#CCCCCC] px-4 py-2 text-center text-[16px] leading-6 tracking-[0.7px] text-[#681F1F] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-colors hover:bg-[#BEBEBE]">
                                    Detail
                                </button>

                                @if ($isWaiting)
                                    <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}" class="flex-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="h-full w-full rounded-[9px] bg-[#681F1F] px-4 py-2 text-center text-[16px] leading-6 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition hover:brightness-110">
                                            Terima Pesanan
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}" class="flex-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="h-full w-full rounded-[9px] bg-[#58E52D] px-4 py-2 text-center text-[16px] leading-6 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition hover:brightness-95">
                                            Selesai
                                        </button>
                                    </form>
                                    <button type="button" data-order-print-url="{{ route('kasir.pesanan.cetak', $pesanan->no_pesanan) }}"
                                        class="flex-1 rounded-[9px] bg-[#681F1F] px-4 py-2 text-center text-[16px] leading-6 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition hover:brightness-110">
                                        Cetak Struk
                                    </button>
                                @endif
                            </div>
                        </div>
                </article>
            @endforeach
        </section>

        @foreach ($pesanans as $pesanan)
                    @php
                        $isWaiting = $pesanan->status_pesanan === 'menunggu konfirmasi';
                        $statusLabel = $isWaiting ? 'Waiting' : 'Processing';
                        $statusBg = $isWaiting ? 'bg-[#E52E2D]' : 'bg-[#FFE62F]';
                        $statusText = $isWaiting ? 'text-white' : 'text-[#1A1A1A]';
                        $panelId = 'pesanan-detail-' . md5($pesanan->no_pesanan);
                        $subtotalPesanan = (int) $pesanan->detailPesanan->sum('subtotal');
                        $ppnPesanan = (int) round($subtotalPesanan * 0.11);
                        $totalPesanan = $subtotalPesanan + $ppnPesanan;
                    @endphp

                    <div id="{{ $panelId }}"
                        class="fixed inset-0 z-[80] hidden items-start justify-center bg-black/35 px-4 py-6 xl:static xl:inset-auto xl:z-auto xl:items-start xl:justify-start xl:bg-transparent xl:p-0"
                        data-order-detail-panel>
                        <aside class="relative flex h-[760px] max-h-[calc(100vh-48px)] w-full max-w-[420px] flex-col overflow-hidden rounded-[9px] bg-[rgba(104,31,31,0.12)] py-7 shadow-[2px_4px_4px_rgba(0,0,0,0.25)] xl:max-h-none">
                            <div class="flex items-start justify-between gap-4 px-7">
                                <h2 class="text-[28px] font-bold leading-9 tracking-[1.2px] text-black">Detail Pesanan</h2>
                                <button type="button" class="h-9 w-9 shrink-0 text-[#460001] transition hover:opacity-70" data-panel-close aria-label="Tutup detail pesanan">
                                    <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="px-7 py-4">
                                <div class="flex items-start">
                                    <p class="text-center text-[14px] font-bold uppercase leading-[18px] tracking-[0.6px] text-[#460001]">
                                        Table {{ $pesanan->meja?->no_meja ?? '-' }}
                                    </p>
                                    <p class="text-center text-[12px] leading-4 tracking-[0.5px] text-[#1A1A1A]">
                                        &nbsp;(indoor Lt 1)
                                    </p>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <p class="min-w-0 truncate text-[24px] font-bold leading-8 tracking-[1px] text-[#460001]">
                                        {{ $pesanan->nama_konsumen ?? '-' }}
                                    </p>
                                    <span class="{{ $statusBg }} {{ $statusText }} inline-flex shrink-0 items-center justify-center rounded-[6px] px-2.5 py-1 text-[12px] leading-4 tracking-[0.5px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-4 text-[12px] leading-4 tracking-[0.5px] text-[#681F1F]">
                                    <p class="w-[90px] truncate">Order #{{ \Illuminate\Support\Str::limit($pesanan->no_pesanan, 8, '') }}</p>
                                    <div class="flex shrink-0 items-start justify-end gap-[3px] whitespace-nowrap">
                                        @if ($pesanan->tgl_pembayaran)
                                            <p>{{ $pesanan->tgl_pembayaran->translatedFormat('l, d F Y') }}</p>
                                            <p>{{ $pesanan->tgl_pembayaran->format('H:i') }}</p>
                                        @else
                                            <p>-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="relative min-h-0 flex-1 px-7 pb-3">
                                <div class="kasir-order-detail-scroll h-full overflow-y-auto" data-scroll-panel>
                                    <div class="flex flex-col">
                                        @foreach ($pesanan->detailPesanan as $idx => $detail)
                                            @php
                                                $menu = $detail->menu;
                                                $variant = $menu?->jenis_menu === 'Minuman' ? $menu?->tipe_minuman : $menu?->kategori_makanan;
                                                $imgType = $menu?->jenis_menu === 'Makanan' ? 'food' : 'drink';
                                                $imgSrc = $menu?->gambar_menu
                                                    ? (str_starts_with($menu->gambar_menu, 'http')
                                                        ? $menu->gambar_menu
                                                        : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                                    : asset('images/logo/KOHVITO LOGO ONLY RED.png');
                                                $notes = collect(preg_split('/\r\n|\r|\n|,/', (string) $detail->catatan))
                                                    ->map(fn($note) => trim($note))
                                                    ->filter()
                                                    ->values();
                                            @endphp

                                            @if ($idx > 0)
                                                <div class="h-px bg-[#D9C7C7]"></div>
                                            @endif

                                            <div class="flex items-start gap-3 rounded-[9px] px-3 py-2.5">
                                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-[9px] bg-[#F6F6F6]">
                                                    <img src="{{ $imgSrc }}" alt="{{ $menu?->nama_menu ?? 'Menu' }}" class="h-full w-full object-cover">
                                                </div>

                                                <div class="flex min-w-0 flex-1 items-start justify-between gap-3 py-1.5">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-[14px] font-bold capitalize leading-[18px] tracking-[0.6px] text-black">
                                                            {{ $detail->jumlah }} {{ $menu?->nama_menu ?? 'Menu' }}@if ($variant)<span class="italic text-[#460001]">({{ $variant }})</span>@endif
                                                        </p>
                                                        @foreach ($notes as $note)
                                                            <p class="truncate text-[12px] leading-4 tracking-[0.5px] text-[#808080]">{{ $note }}</p>
                                                        @endforeach
                                                    </div>
                                                    <div class="shrink-0 text-right">
                                                        <p class="whitespace-nowrap text-[14px] font-bold leading-[18px] tracking-[0.6px] text-black">
                                                            {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                        </p>
                                                        @foreach ($notes as $note)
                                                            <p class="whitespace-nowrap text-[12px] leading-4 tracking-[0.5px] text-[#808080]">+2.000</p>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($pesanan->catatan_pesanan)
                                        <div class="mt-3 rounded-[9px] bg-[#D9C7C7] px-4 py-3">
                                            <p class="text-[14px] font-bold capitalize leading-[18px] tracking-[0.6px] text-[#460001]">
                                                Notes Pemesanan
                                            </p>
                                            <p class="mt-2 text-[12px] leading-4 tracking-[0.5px] text-[#1A1A1A]">
                                                {{ $pesanan->catatan_pesanan }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="mt-3 flex flex-col gap-2 text-[14px] font-bold capitalize leading-[18px] tracking-[0.6px]">
                                        <div class="flex items-center gap-4">
                                            <p class="flex-1 text-[#460001]">SubTotal Pemesanan</p>
                                            <p class="whitespace-nowrap text-[#1A1A1A]">{{ number_format($subtotalPesanan, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <p class="flex-1 text-[#460001]">Ppn 11%</p>
                                            <p class="whitespace-nowrap text-[#1A1A1A]">{{ number_format($ppnPesanan, 0, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <div class="my-2 h-px bg-[#D9C7C7]"></div>

                                    <div class="flex items-center gap-4 text-[14px] font-bold capitalize leading-[18px] tracking-[0.6px]">
                                        <p class="flex-1 text-[#460001]">Total Pemesanan</p>
                                        <p class="whitespace-nowrap text-[#1A1A1A]">{{ number_format($totalPesanan, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                            </div>

                            <div class="flex gap-3 px-7 pt-3">
                                <button type="button" data-panel-close
                                    class="min-w-0 flex-1 rounded-[9px] bg-[#CCCCCC] px-3 py-2 text-[15px] leading-6 tracking-[0.7px] text-[#681F1F] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition-colors hover:bg-[#BEBEBE]">
                                    Tutup
                                </button>
                                @if ($isWaiting)
                                    <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}" class="min-w-0 flex-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="w-full rounded-[9px] bg-[#681F1F] px-3 py-2 text-[15px] leading-6 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition hover:brightness-110">
                                            Terima Pesanan
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('kasir.pesanan.update-status', $pesanan->no_pesanan) }}" class="min-w-0 flex-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="w-full rounded-[9px] bg-[#58E52D] px-3 py-2 text-[15px] leading-6 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition hover:brightness-95">
                                            Selesai
                                        </button>
                                    </form>
                                    <button type="button" data-order-print-url="{{ route('kasir.pesanan.cetak', $pesanan->no_pesanan) }}"
                                        class="min-w-0 flex-1 rounded-[9px] bg-[#681F1F] px-3 py-2 text-[15px] leading-6 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] transition hover:brightness-110">
                                        Cetak Struk
                                    </button>
                                @endif
                            </div>
                        </aside>
                    </div>
        @endforeach
        </div>
    @endif

    <div id="kasir-order-success" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/35 px-4"
        data-success-modal data-initial-action="{{ session('order_action') }}">
        <div class="relative w-full max-w-[540px] rounded-[6px] bg-white px-10 pb-9 pt-16 shadow-[2px_4px_4px_rgba(0,0,0,0.35)]">
            <button type="button" class="absolute right-9 top-8 text-[#460001] transition hover:opacity-70" data-success-close
                aria-label="Tutup notifikasi pesanan">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>
            <div class="flex flex-col items-center text-center">
                <img src="{{ asset('images/illustration/print success.svg') }}" alt=""
                    class="mb-7 h-[250px] w-[240px] object-contain">
                <p id="kasir-order-success-title"
                    class="max-w-[440px] text-[26px] font-bold leading-8 tracking-[1.1px] text-[#460001]">
                    Berhasil Menerima Pesanan
                </p>
                <p id="kasir-order-success-subtitle"
                    class="mt-3 hidden max-w-[440px] text-[16px] leading-[22px] tracking-[0.7px] text-[#808080]"></p>
                <div class="mt-8 flex w-full justify-end">
                    <button type="button" data-success-close
                        class="rounded-[9px] bg-[#CCCCCC] px-8 py-2.5 text-[16px] tracking-[0.7px] text-[#681F1F] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] transition-colors hover:bg-[#BEBEBE]">
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
                const content = document.querySelector('[data-order-content]');
                const grid = document.querySelector('[data-order-grid]');
                const panels = Array.from(document.querySelectorAll('[data-order-detail-panel]'));
                const cards = Array.from(document.querySelectorAll('[data-order-card]'));
                const desktopDetailQuery = window.matchMedia('(min-width: 1280px)');

                const getActivePanel = () => panels.find((panel) => !panel.classList.contains('hidden'));

                const fitOrderPreviewCard = (card) => {
                    const area = card.querySelector('[data-order-preview-area]');
                    const list = card.querySelector('[data-order-preview-list]');
                    const more = card.querySelector('[data-order-preview-more]');
                    const items = Array.from(card.querySelectorAll('[data-order-preview-item]'));

                    if (!area || !list || !more || items.length === 0) return;

                    items.forEach((item) => item.classList.remove('hidden'));
                    more.classList.add('hidden');
                    more.textContent = '+0 Lainnya';

                    if (list.scrollHeight <= area.clientHeight + 1) return;

                    let hiddenCount = 0;
                    more.classList.remove('hidden');

                    for (let index = items.length - 1; index >= 0; index -= 1) {
                        items[index].classList.add('hidden');
                        hiddenCount += 1;
                        more.textContent = `+${hiddenCount} Lainnya`;

                        if (list.scrollHeight <= area.clientHeight + 1) break;
                    }
                };

                const syncCardPreviews = () => {
                    document.querySelectorAll('[data-order-preview-card]').forEach(fitOrderPreviewCard);
                };

                const syncDetailLayout = () => {
                    const hasActivePanel = Boolean(getActivePanel());

                    content?.classList.toggle('xl:grid', hasActivePanel);
                    content?.classList.toggle('xl:grid-cols-[828px_420px]', hasActivePanel);
                    content?.classList.toggle('xl:items-start', hasActivePanel);

                    grid?.classList.toggle('xl:grid-cols-2', hasActivePanel);
                    grid?.classList.toggle('xl:grid-cols-3', !hasActivePanel);

                    window.requestAnimationFrame(syncCardPreviews);
                };

                const showBodyLockIfNeeded = () => {
                    const panelOpen = Boolean(getActivePanel()) && !desktopDetailQuery.matches;
                    const successOpen = document.querySelector('[data-success-modal].flex');
                    document.body.classList.toggle('overflow-hidden', Boolean(panelOpen || successOpen));
                };

                const resetPanelScroll = (panel) => {
                    panel?.querySelector('[data-scroll-panel]')?.scrollTo({ top: 0 });
                };

                const activatePanel = (id) => {
                    let activePanel = null;

                    panels.forEach((panel) => {
                        const isTarget = panel.id === id;
                        panel.classList.toggle('hidden', !isTarget);
                        panel.classList.toggle('flex', isTarget);
                        if (isTarget) activePanel = panel;
                    });

                    cards.forEach((card) => {
                        card.classList.toggle('ring-2', card.dataset.orderCard === id);
                        card.classList.toggle('ring-[#D9C7C7]', card.dataset.orderCard === id);
                    });

                    syncDetailLayout();
                    resetPanelScroll(activePanel);
                    showBodyLockIfNeeded();
                };

                const closePanel = (panel) => {
                    if (!panel) return;
                    panel.classList.add('hidden');
                    panel.classList.remove('flex');
                    cards.forEach((card) => {
                        if (getActivePanel()) return;
                        card.classList.remove('ring-2', 'ring-[#D9C7C7]');
                    });
                    syncDetailLayout();
                    showBodyLockIfNeeded();
                };

                document.querySelectorAll('[data-order-panel-target]').forEach((button) => {
                    button.addEventListener('click', () => activatePanel(button.dataset.orderPanelTarget));
                });

                document.querySelectorAll('[data-panel-close]').forEach((button) => {
                    button.addEventListener('click', () => closePanel(button.closest('[data-order-detail-panel]')));
                });

                panels.forEach((panel) => {
                    panel.addEventListener('click', (event) => {
                        if (event.target === panel) closePanel(panel);
                    });
                });

                desktopDetailQuery.addEventListener('change', showBodyLockIfNeeded);
                window.addEventListener('resize', () => window.requestAnimationFrame(syncCardPreviews));
                document.fonts?.ready?.then(syncCardPreviews);

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
                    successModal?.classList.remove('hidden');
                    successModal?.classList.add('flex');
                    showBodyLockIfNeeded();
                };

                const closeSuccess = () => {
                    successModal?.classList.add('hidden');
                    successModal?.classList.remove('flex');
                    showBodyLockIfNeeded();
                };

                document.querySelectorAll('[data-order-print-url]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const printWindow = window.open(button.dataset.orderPrintUrl, '_blank');
                        if (printWindow) printWindow.opener = null;
                        openSuccess('printed');
                    });
                });

                document.querySelectorAll('[data-success-close]').forEach((button) => {
                    button.addEventListener('click', closeSuccess);
                });

                successModal?.addEventListener('click', (event) => {
                    if (event.target === successModal) closeSuccess();
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;
                    panels.forEach(closePanel);
                    closeSuccess();
                });

                if (successModal?.dataset.initialAction) {
                    openSuccess(successModal.dataset.initialAction);
                }

                syncDetailLayout();
                syncCardPreviews();

                // Auto-refresh: polling untuk pesanan baru masuk
                const REFRESH_INTERVAL_MS = 30000;
                const shouldSkipRefresh = () =>
                    document.hidden
                    || Boolean(getActivePanel())
                    || successModal?.classList.contains('flex');

                window.setInterval(() => {
                    if (shouldSkipRefresh()) return;
                    window.location.reload();
                }, REFRESH_INTERVAL_MS);
            })();
        </script>
    </x-slot:scripts>

</x-layouts.kasir>
