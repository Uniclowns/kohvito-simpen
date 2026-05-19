<x-layouts.kasir title="Kelola Histori" page-title="Kelola Histori">

    <div class="bg-white rounded-[6px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] px-8 py-8 mb-40">
        <div class="flex items-start justify-between gap-4 mb-7">
            <h2 class="text-[#460001] text-[22px] font-bold tracking-[0.9px]">Histori Pesanan</h2>

            @if ($pesanans->isNotEmpty())
                <button type="button"
                        data-print-url="{{ route('kasir.histori.cetak-semua') }}"
                        data-print-success="all"
                        class="inline-flex items-center gap-2 bg-[#681F1F] text-white text-[15px] tracking-[0.6px] px-5 py-2.5 rounded-[6px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z" />
                    </svg>
                    Cetak Semua Histori Pesanan
                </button>
            @endif
        </div>

        <form method="GET" action="{{ route('kasir.histori.index') }}" class="relative mb-7">
            <img src="{{ asset('images/icons/search.svg') }}" alt="" class="absolute left-4 top-1/2 -translate-y-1/2 w-6 h-6 opacity-60">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Cari Histori Pesanan"
                   class="w-full h-14 bg-[#EDE4E4] border-0 rounded-[6px] pl-14 pr-4 text-[18px] text-[#460001] placeholder:text-[#808080] tracking-[0.8px] focus:outline-none focus:ring-2 focus:ring-[#681F1F]/20">
        </form>

        @if ($pesanans->isEmpty())
            <div class="py-20 text-center">
                <p class="text-[#808080] text-[18px] tracking-[0.7px]">Belum ada pesanan selesai hari ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[940px] border-collapse">
                    <thead>
                        <tr class="border-b border-[#E6E6E6]">
                            <th class="text-left text-[#7E4A4A] text-[15px] font-bold tracking-[0.6px] py-4 px-3">ID Pesanan</th>
                            <th class="text-left text-[#7E4A4A] text-[15px] font-bold tracking-[0.6px] py-4 px-3">Nomor Meja</th>
                            <th class="text-left text-[#7E4A4A] text-[15px] font-bold tracking-[0.6px] py-4 px-3">Nama Pesanan</th>
                            <th class="text-left text-[#7E4A4A] text-[15px] font-bold tracking-[0.6px] py-4 px-3">Tanggal/Waktu</th>
                            <th class="text-left text-[#7E4A4A] text-[15px] font-bold tracking-[0.6px] py-4 px-3">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesanans as $pesanan)
                            @php
                                $modalId = 'histori-detail-' . md5($pesanan->no_pesanan);
                            @endphp
                            <tr class="border-b border-[#EDEDED]">
                                <td class="py-4 px-3 text-[#460001] text-[15px] font-bold tracking-[0.6px]">
                                    #{{ Str::limit($pesanan->no_pesanan, 8, '') }}
                                </td>
                                <td class="py-4 px-3 text-[#7E4A4A] text-[15px] tracking-[0.6px]">
                                    {{ $pesanan->meja?->no_meja ?? '-' }} (indoor)
                                </td>
                                <td class="py-4 px-3 text-[#7E4A4A] text-[15px] tracking-[0.6px]">
                                    {{ $pesanan->nama_konsumen }}
                                </td>
                                <td class="py-4 px-3 text-[#7E4A4A] text-[15px] tracking-[0.6px]">
                                    {{ $pesanan->tgl_pembayaran?->translatedFormat('l, d F Y H:i') ?? '-' }}
                                </td>
                                <td class="py-4 px-3">
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                                data-modal-target="{{ $modalId }}"
                                                class="inline-flex items-center gap-2 bg-[#CCCCCC] text-[#681F1F] text-[15px] tracking-[0.6px] px-4 py-2 rounded-[6px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] hover:bg-[#BEBEBE] transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 9h6M9 13h6m-8 8h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            Detail
                                        </button>
                                        <button type="button"
                                                data-print-url="{{ route('kasir.histori.cetak', $pesanan->no_pesanan) }}"
                                                data-print-success="single"
                                                class="inline-flex items-center gap-2 bg-[#681F1F] text-white text-[15px] tracking-[0.6px] px-4 py-2 rounded-[6px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z" />
                                            </svg>
                                            Cetak Histori
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach ($pesanans as $pesanan)
                @php
                    $modalId = 'histori-detail-' . md5($pesanan->no_pesanan);
                    $subtotalPesanan = (int) $pesanan->detailPesanan->sum('subtotal');
                    $ppnPesanan = (int) round($subtotalPesanan * 0.11);
                    $totalPesanan = $subtotalPesanan + $ppnPesanan;
                @endphp
                <div id="{{ $modalId }}"
                     class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/35 px-4 py-8"
                     data-modal>
                    <div class="relative w-full max-w-[540px] max-h-[92vh] bg-white rounded-[6px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] overflow-hidden flex flex-col">
                        <button type="button"
                                class="absolute right-9 top-8 z-10 text-[#460001] hover:opacity-70"
                                data-modal-close
                                aria-label="Tutup detail histori">
                            <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>

                        <div class="px-9 pt-8 pb-5">
                            <h3 class="text-black text-[30px] font-bold tracking-[1.3px] mb-6">Detail Histori Pesanan</h3>
                            <div class="flex items-start gap-5">
                                <div class="bg-[#D9C7C7] rounded-[9px] px-4 py-2 text-center shrink-0">
                                    <p class="text-[#460001] text-[14px] font-bold uppercase leading-[18px] tracking-[0.7px]">
                                        Table {{ $pesanan->meja?->no_meja ?? '-' }}
                                    </p>
                                    <p class="text-[#460001] text-[12px] leading-[14px] tracking-[0.5px]">(indoor Lt 1)</p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[#460001] text-[24px] font-bold leading-[30px] tracking-[1px] truncate">
                                        {{ $pesanan->nama_konsumen }}
                                    </p>
                                    <p class="text-[#681F1F] text-[12px] tracking-[0.5px] mt-1">
                                        Order #{{ Str::limit($pesanan->no_pesanan, 8, '') }}
                                    </p>
                                </div>
                                <div class="pt-1 text-right">
                                    <span class="inline-flex bg-[#58E52D] text-white text-[12px] px-2.5 py-1 rounded-[4px] shadow-[0_0_14px_rgba(88,229,45,0.65)]">
                                        Selesai
                                    </span>
                                    <p class="mt-3 text-[#681F1F] text-[14px] tracking-[0.5px] whitespace-nowrap">
                                        {{ $pesanan->tgl_pembayaran?->translatedFormat('l, d F Y H:i') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="relative min-h-0 flex-1">
                            <div class="px-9 pb-5 overflow-y-auto max-h-[440px] pr-5" data-scroll-panel>
                                <div class="divide-y divide-[#E6E6E6]">
                                    @foreach ($pesanan->detailPesanan as $detail)
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
                                        @endphp
                                        <div class="flex items-center gap-4 py-4">
                                            <div class="w-[62px] h-[62px] rounded-[9px] overflow-hidden shrink-0 bg-[#F6F6F6]">
                                                <img src="{{ $imgSrc }}" alt="{{ $menu?->nama_menu ?? 'Menu' }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-black text-[16px] font-bold leading-[20px]">
                                                    {{ $detail->jumlah }} {{ $menu?->nama_menu ?? 'Menu' }}@if ($variant)<span class="italic text-[#681F1F]">({{ $variant }})</span>@endif
                                                </p>
                                                @if ($detail->catatan)
                                                    <p class="text-[#808080] text-[14px] leading-[17px] mt-1">{{ $detail->catatan }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right shrink-0">
                                                <p class="text-black text-[16px] font-bold">{{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                                                <p class="text-[#808080] text-[13px] mt-3">+0</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($pesanan->catatan_pesanan)
                                    <div class="bg-[#D9C7C7] rounded-[9px] px-4 py-4 mt-3">
                                        <p class="text-[#460001] text-[14px] font-bold tracking-[0.6px] mb-2">Notes Pemesanan</p>
                                        <p class="text-[#1A1A1A] text-[12px] leading-[15px] tracking-[0.5px]">
                                            {{ $pesanan->catatan_pesanan }}
                                        </p>
                                    </div>
                                @endif

                                <div class="pt-4 text-[16px] font-bold tracking-[0.7px] text-[#460001]">
                                    <div class="flex justify-between py-1.5">
                                        <span>SubTotal Pesanan</span>
                                        <span>{{ number_format($subtotalPesanan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span>Ppn 11%</span>
                                        <span>{{ number_format($ppnPesanan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="border-t border-[#E6E6E6] mt-3 pt-4 flex justify-between">
                                        <span>Total Pesanan</span>
                                        <span>{{ number_format($totalPesanan, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if ($pesanan->detailPesanan->count() > 4)
                                <div class="absolute left-0 right-0 bottom-0 h-24 flex items-end justify-center pb-5 pointer-events-none [background:linear-gradient(0deg,#B0ADAD_12.5%,rgba(255,255,255,0)_100%)] backdrop-blur-[4.8px] transition-opacity duration-150"
                                     data-scroll-hint>
                                    <p class="text-[#681F1F] text-[20px] leading-[24px] tracking-[0.9px]">
                                        Scroll Untuk Melihat Menu Lainnya <span class="inline-block translate-y-[-1px]">v</span>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="px-9 py-6 bg-white grid grid-cols-2 gap-3">
                            <button type="button"
                                    data-modal-close
                                    class="bg-[#CCCCCC] text-[#681F1F] text-[16px] tracking-[0.7px] rounded-[9px] py-2.5 shadow-[2px_4px_4px_rgba(0,0,0,0.25)] hover:bg-[#BEBEBE] transition-colors">
                                Kembali
                            </button>
                            <button type="button"
                                    data-print-url="{{ route('kasir.histori.cetak', $pesanan->no_pesanan) }}"
                                    data-print-success="single"
                                    class="bg-[#681F1F] text-white text-[16px] tracking-[0.7px] rounded-[9px] py-2.5 shadow-[2px_4px_4px_rgba(0,0,0,0.25)] hover:brightness-110 transition-all">
                                Cetak Histori
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="histori-print-success"
         class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/35 px-4"
         data-success-modal>
        <div class="relative w-full max-w-[540px] bg-white rounded-[6px] shadow-[2px_4px_4px_rgba(0,0,0,0.35)] px-10 pt-16 pb-9">
            <button type="button"
                    class="absolute right-9 top-8 text-[#460001] hover:opacity-70"
                    data-success-close
                    aria-label="Tutup notifikasi cetak">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
            <div class="flex flex-col items-center text-center">
                <img src="{{ asset('images/illustration/print success.svg') }}" alt="" class="w-[240px] h-[250px] object-contain mb-7">
                <p id="histori-print-success-title" class="text-[#460001] text-[26px] font-bold leading-[32px] tracking-[1.1px] max-w-[440px]">
                    Berhasil Mencetak Histori Pesanan
                </p>
                <div class="w-full flex justify-end mt-8">
                    <button type="button"
                            data-success-close
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
                    button.addEventListener('click', () => {
                        const modal = document.getElementById(button.dataset.modalTarget);
                        if (modal) openModal(modal);
                    });
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
                const successTitle = document.getElementById('histori-print-success-title');
                const successCopy = {
                    single: 'Berhasil Mencetak Histori Pesanan',
                    all: 'Berhasil Mencetak Semua Histori Pesanan',
                };

                document.querySelectorAll('[data-print-url]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const printWindow = window.open(button.dataset.printUrl, '_blank');
                        if (printWindow) printWindow.opener = null;

                        if (successTitle) {
                            successTitle.textContent = successCopy[button.dataset.printSuccess] ?? successCopy.single;
                        }
                        if (successModal) openModal(successModal);
                    });
                });

                document.querySelectorAll('[data-success-close]').forEach((button) => {
                    button.addEventListener('click', () => closeModal(successModal));
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;
                    document.querySelectorAll('[data-modal].flex, [data-success-modal].flex').forEach(closeModal);
                });

                updateScrollHints();
            })();
        </script>
    </x-slot:scripts>

</x-layouts.kasir>
