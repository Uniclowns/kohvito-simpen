{{-- Simulator Pembayaran Konsumen
    Route: konsumen.bayar.simulator (/bayar/simulator/{noPesanan})
    Controller: BayarController@simulator
    Variables: $pesanan
--}}
<x-layouts.konsumen
    :title="'Simulator Pembayaran - ' . $pesanan->no_pesanan"
    bodyClass="min-h-screen bg-[#F6F6F6] font-sans text-brand-black kvt-konsumen-mobile-view">

    <main class="mx-auto flex max-w-md flex-col gap-6 px-[18px] py-10" data-anim="fade-up">
        <div class="rounded-[9px] border border-yellow-300 bg-yellow-100 px-4 py-3 text-[13px] leading-[18px] text-yellow-900">
            <p class="mb-1 font-bold">MODE SIMULATOR LOKAL</p>
            <p>Halaman ini hanya muncul saat <code>BAYAR_DRIVER=mock</code>. Tidak ada transaksi real terjadi. Untuk integrasi real, lihat <code>PANDUAN-PAYMENT-GATEWAY.md</code>.</p>
        </div>

        <div class="flex flex-col gap-3 rounded-[9px] bg-white p-5 shadow-[2px_4px_4px_rgba(0,0,0,0.18)]">
            <p class="text-[12px] uppercase tracking-[0.6px] text-brand-gray">Nomor Pesanan</p>
            <p class="kvt-break-anywhere font-mono text-[20px] font-bold leading-[28px] tracking-[1px] text-brand-dark">{{ $pesanan->no_pesanan }}</p>

            <div class="mt-1 flex flex-wrap items-center justify-between gap-2 border-t border-brand-gray-light pt-3">
                <span class="text-[14px] tracking-[0.7px] text-brand-gray-dark">Total Tagihan</span>
                <span class="text-[20px] font-bold leading-[28px] tracking-[1px] text-brand-dark">
                    Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <p class="text-center text-[14px] leading-[20px] tracking-[0.7px] text-brand-gray-dark">
                Pilih hasil simulasi pembayaran di bawah ini.
            </p>

            <form method="POST" action="{{ route('konsumen.bayar.simulator.callback', $pesanan->no_pesanan) }}">
                @csrf
                <input type="hidden" name="hasil" value="lunas">
                <button type="submit"
                        class="w-full rounded-[9px] bg-emerald-600 py-3 text-[16px] font-bold leading-6 tracking-[0.8px] text-white shadow-[2px_4px_8px_rgba(16,185,129,0.3)] transition hover:bg-emerald-700 active:scale-[0.98]">
                    Tandai LUNAS
                </button>
            </form>

            <form method="POST" action="{{ route('konsumen.bayar.simulator.callback', $pesanan->no_pesanan) }}">
                @csrf
                <input type="hidden" name="hasil" value="gagal">
                <button type="submit"
                        class="w-full rounded-[9px] bg-[#9C2C2C] py-3 text-[16px] font-bold leading-6 tracking-[0.8px] text-white shadow-[2px_4px_8px_rgba(156,44,44,0.3)] transition hover:bg-[#7a2222] active:scale-[0.98]">
                    Tandai GAGAL
                </button>
            </form>

            <a href="{{ route('konsumen.lacak.detail', $pesanan->no_pesanan) }}"
               class="mt-2 text-center text-[13px] tracking-[0.6px] text-brand-gray hover:underline">
                &larr; Kembali ke halaman pesanan tanpa bayar
            </a>
        </div>
    </main>
</x-layouts.konsumen>
