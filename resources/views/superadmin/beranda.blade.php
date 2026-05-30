<x-layouts.admin title="Super Admin" pageTitle="Dashboard Super Admin">

    {{-- Greeting --}}
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-brand-dark to-[#6B1F1F] p-6 text-white shadow-[0_4px_24px_rgba(70,0,1,0.18)]">
        <h2 class="text-2xl font-bold">Halo, {{ auth()->user()->nama_lengkap }} 👋</h2>
        <p class="mt-1 text-sm text-white/80">
            Anda login sebagai <strong>Super Admin</strong> — akses penuh ke seluruh panel sistem.
        </p>
    </div>

    {{-- Stat cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-[0_4px_24px_rgba(0,0,0,0.06)]">
            <div class="text-xs font-medium uppercase tracking-wide text-brand-gray">Total Admin</div>
            <div class="mt-2 text-3xl font-bold text-brand-dark">{{ $stats['admin'] }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-[0_4px_24px_rgba(0,0,0,0.06)]">
            <div class="text-xs font-medium uppercase tracking-wide text-brand-gray">Total Kasir</div>
            <div class="mt-2 text-3xl font-bold text-brand-dark">{{ $stats['kasir'] }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-[0_4px_24px_rgba(0,0,0,0.06)]">
            <div class="text-xs font-medium uppercase tracking-wide text-brand-gray">Total Meja</div>
            <div class="mt-2 text-3xl font-bold text-brand-dark">{{ $stats['meja'] }}</div>
        </div>
    </div>

    {{-- Navigation hub --}}
    <h3 class="mb-3 text-lg font-bold text-brand-dark">Pusat Kontrol</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

        @php
            // Definisi kartu: [judul, deskripsi, url, warna ikon, path svg, target?]
            $cards = [
                ['Kelola Admin', 'Tambah, edit, hapus akun administrator', route('superadmin.admin.index'), '#7A1F1F', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', null],
                ['Kelola Kasir', 'Manajemen akun staf kasir', route('admin.pengguna-kasir.index'), '#2563EB', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z', null],
                ['Kelola Menu', 'Tambah & atur menu makanan/minuman', route('admin.menu.index'), '#D97706', 'M3 3h18v4H3zM3 10h18M3 14h18M3 18h18', null],
                ['Kelola Kategori', 'Atur kategori menu', route('admin.kategori.index'), '#059669', 'M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 014 9V4a1 1 0 011-1z', null],
                ['Kelola Meja & QR', 'Generate QR Code per meja', route('superadmin.meja.index'), '#460001', 'M3 4h18v4H3V4zm0 8h6v8H3v-8zm10 0h8v8h-8v-8z', null],
                ['Panel Admin', 'Buka dashboard laporan & omzet', route('admin.beranda'), '#7C3AED', 'M3 12l2-2 7-7 7 7 2 2M5 10v10h14V10', null],
                ['Panel Kasir', 'Buka panel pemrosesan pesanan', route('kasir.beranda'), '#DB2777', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', null],
                ['Lihat Konsumen', $firstMeja ? "Buka tampilan pemesanan (Meja {$firstMeja})" : 'Belum ada meja terdaftar', $firstMeja ? url('/' . $firstMeja) : '#', '#0891B2', 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', '_blank'],
            ];
        @endphp

        @foreach ($cards as [$judul, $desc, $url, $warna, $svgPath, $target])
            <a href="{{ $url }}" @if ($target) target="{{ $target }}" @endif
               class="group flex items-start gap-4 rounded-2xl bg-white p-5 shadow-[0_4px_24px_rgba(0,0,0,0.06)] transition-all hover:-translate-y-0.5 hover:shadow-[0_8px_28px_rgba(70,0,1,0.12)]">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                     style="background-color: {{ $warna }}1a;">
                    <svg class="h-6 w-6" fill="none" stroke="{{ $warna }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $svgPath }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="font-bold text-brand-dark group-hover:text-brand-red">{{ $judul }}</div>
                    <div class="mt-0.5 text-sm text-brand-gray">{{ $desc }}</div>
                </div>
            </a>
        @endforeach

    </div>

</x-layouts.admin>
