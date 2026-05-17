<x-layouts.admin title="Laporan Keuangan" page-title="Laporan Keuangan">

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 mt-2 mb-5">
        <h2 class="text-base font-bold text-brand-dark mb-4">Filter Periode Laporan</h2>
        <form method="GET" action="{{ route('admin.laporan-keuangan.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-bold text-brand-dark mb-2">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai"
                    value="{{ $tanggalMulai->format('Y-m-d') }}"
                    class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#380000]">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-bold text-brand-dark mb-2">Sampai Tanggal</label>
                <input type="date" name="tanggal_selesai"
                    value="{{ $tanggalSelesai->format('Y-m-d') }}"
                    class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#380000]">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-[#380000] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-[#2A0000] transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.laporan-keuangan.pdf', request()->only('tanggal_mulai', 'tanggal_selesai')) }}"
                    class="bg-[#E03131] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#C92A2A] transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </a>
                <a href="{{ route('admin.laporan-keuangan.excel', request()->only('tanggal_mulai', 'tanggal_selesai')) }}"
                    class="bg-[#22C55E] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#16A34A] transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 mb-5">
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4">
            <p class="text-[11px] text-brand-dark font-bold uppercase">Total Transaksi</p>
            <div class="flex items-baseline gap-1.5 mt-2">
                <p class="text-3xl font-bold text-brand-black leading-none">{{ $totalTransaksi }}</p>
                <p class="text-sm font-bold text-brand-black">Transaksi</p>
            </div>
        </div>
        <div class="bg-[#681F1F]/12 rounded-xl px-5 py-4">
            <p class="text-[11px] text-brand-dark font-bold uppercase">Total Omzet</p>
            <div class="flex items-baseline gap-1 mt-2">
                <p class="text-lg font-bold text-brand-black">Rp</p>
                <p class="text-3xl font-bold text-brand-black leading-none">
                    {{ number_format($totalOmzet, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6">
        <h2 class="text-lg font-bold text-brand-dark mb-5">
            Ringkasan Transaksi
            <span class="text-xs font-normal text-brand-gray ml-2">
                ({{ $tanggalMulai->translatedFormat('d M Y') }}
                @if ($tanggalMulai->format('Y-m-d') !== $tanggalSelesai->format('Y-m-d'))
                    &mdash; {{ $tanggalSelesai->translatedFormat('d M Y') }}
                @endif)
            </span>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-bold text-brand-dark uppercase tracking-wide border-b border-gray-100">
                        <th class="pb-4 pr-4">No Pesanan</th>
                        <th class="pb-4 pr-4">Konsumen</th>
                        <th class="pb-4 pr-4">Meja</th>
                        <th class="pb-4 pr-4">Waktu Bayar</th>
                        <th class="pb-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanans as $p)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 pr-4 text-sm font-bold text-brand-dark">{{ $p->no_pesanan }}</td>
                            <td class="py-3 pr-4 text-sm text-brand-dark">{{ $p->nama_konsumen ?? '-' }}</td>
                            <td class="py-3 pr-4 text-sm text-brand-dark">{{ $p->meja?->no_meja ?? '-' }}</td>
                            <td class="py-3 pr-4 text-sm text-brand-gray">
                                {{ optional($p->tgl_pembayaran)->translatedFormat('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="py-3 text-right text-sm font-bold text-brand-dark">
                                Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-sm text-brand-gray">
                                Belum ada transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <x-slot:pageFooter>
        <x-admin-footer />
    </x-slot:pageFooter>
</x-layouts.admin>
