<x-layouts.admin title="Superadmin" pageTitle="Pusat kendali">
    @php
        $managementLinks = [
            ['Kelola Admin', 'Atur akun administrator dan hak akses operasional.', route('superadmin.admin.index'), '01', 'M16 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m6.5-10a4 4 0 100-8 4 4 0 000 8zm7-2l2 2 4-4'],
            ['Kelola Kasir', 'Tambah dan perbarui akun petugas kasir.', route('admin.pengguna-kasir.index'), '02', 'M17 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7.5-10a4 4 0 100-8 4 4 0 000 8zm8 1v6m3-3h-6'],
            ['Kelola Menu', 'Perbarui katalog makanan dan minuman.', route('admin.menu.index'), '03', 'M5 4h14l-1 17H6L5 4zm3 4h8m-7 4h6'],
            ['Kelola Kategori', 'Susun kategori agar katalog tetap rapi.', route('admin.kategori.index'), '04', 'M4 5h7v6H4V5zm9 0h7v6h-7V5zM4 13h7v6H4v-6zm9 0h7v6h-7v-6z'],
            ['Kelola Meja & QR', 'Siapkan meja dan cetak kode QR pemesanan.', route('superadmin.meja.index'), '05', 'M4 5h16v5H4V5zm0 9h6v6H4v-6zm10 0h6v6h-6v-6z'],
        ];

        $panelLinks = [
            ['Panel Admin', 'Laporan dan operasional', route('admin.beranda'), false, true],
            ['Panel Kasir', 'Pemrosesan pesanan', route('kasir.beranda'), false, true],
            ['Tampilan Konsumen', $firstMeja ? "Pratinjau meja {$firstMeja}" : 'Belum ada meja terdaftar', $firstMeja ? url('/'.$firstMeja) : '#', true, (bool) $firstMeja],
        ];
    @endphp

    <section class="relative overflow-hidden rounded-[30px] bg-[#2B0708] px-5 pb-5 pt-7 text-white shadow-[0_24px_70px_rgba(43,7,8,0.22)] sm:px-8 sm:pb-7 sm:pt-9 lg:px-10">
        <div class="pointer-events-none absolute -right-20 -top-32 h-80 w-80 rounded-full border border-white/[0.06]"></div>
        <div class="pointer-events-none absolute -right-8 -top-20 h-56 w-56 rounded-full border border-white/[0.08]"></div>
        <img src="{{ asset('images/logo/KOHVITO LOGO ONLY WHITE.png') }}" alt="" class="pointer-events-none absolute -right-5 top-2 hidden w-48 rotate-[-8deg] opacity-[0.035] lg:block">

        <div class="relative grid gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(280px,.65fr)] lg:items-end">
            <div>
                <div class="mb-5 inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.06] px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-white/65">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#C66A6D]"></span>
                    Akses penuh sistem
                </div>
                <h2 class="max-w-3xl text-3xl font-bold leading-[1.05] tracking-[-0.045em] text-balance sm:text-4xl lg:text-5xl">
                    Selamat datang,<br>{{ auth()->user()->nama_lengkap }}.
                </h2>
                <p class="mt-5 max-w-[58ch] text-sm leading-relaxed text-white/58">
                    Pantau struktur operasional KOHVITO dan masuk ke setiap panel dari satu pusat kendali.
                </p>
            </div>

            <a href="{{ route('superadmin.admin.index') }}"
               class="group inline-flex w-full items-center justify-between rounded-2xl border border-white/10 bg-white/[0.07] p-4 transition duration-200 hover:-translate-y-0.5 hover:bg-white/[0.11] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 lg:max-w-sm lg:justify-self-end">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/45">Aksi utama</p>
                    <p class="mt-1 text-sm font-bold text-white">Kelola akun admin</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F3E9E1] text-[#3A0A0B] transition-transform group-hover:translate-x-0.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-5-5l5 5-5 5"/>
                    </svg>
                </span>
            </a>
        </div>

        <div class="relative mt-8 grid grid-cols-1 overflow-hidden rounded-2xl border border-white/10 bg-black/10 sm:grid-cols-3">
            @foreach ([['Admin aktif', $stats['admin']], ['Kasir aktif', $stats['kasir']], ['Meja terdaftar', $stats['meja']]] as [$label, $value])
                <div class="flex items-end justify-between border-white/10 px-5 py-4 sm:border-r sm:last:border-r-0">
                    <p class="text-xs font-medium text-white/48">{{ $label }}</p>
                    <p class="text-3xl font-bold tabular-nums tracking-[-0.04em] text-white">{{ str_pad($value, 2, '0', STR_PAD_LEFT) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="mt-8 grid gap-8 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section aria-labelledby="management-heading">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#725E5A]">Workspace</p>
                    <h3 id="management-heading" class="mt-1 text-xl font-bold tracking-[-0.03em] text-[#2B2020]">Akses utama</h3>
                </div>
                <span class="text-xs text-[#725E5A]">5 modul</span>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($managementLinks as [$title, $description, $url, $number, $path])
                    <a href="{{ $url }}"
                       class="group relative min-h-44 overflow-hidden rounded-2xl border border-[#E3DAD3] p-5 transition duration-200 hover:-translate-y-0.5 hover:border-[#CDBDB3] hover:shadow-[0_18px_45px_rgba(67,34,30,0.09)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#681F1F] focus-visible:ring-offset-2 {{ $loop->first ? 'bg-[#E9DED5] md:col-span-2' : 'bg-white/80' }}">
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-xs font-bold tabular-nums tracking-[0.15em] text-[#725E5A]">{{ $number }}</span>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-[#D8C9BF] bg-white/60 text-[#571719] transition duration-200 group-hover:bg-[#571719] group-hover:text-white">
                                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                                </svg>
                            </span>
                        </div>
                        <div class="mt-7 flex items-end justify-between gap-4">
                            <div>
                                <h4 class="text-lg font-bold tracking-[-0.025em] text-[#2B2020] group-hover:text-[#571719]">{{ $title }}</h4>
                                <p class="mt-1 max-w-[46ch] text-sm leading-relaxed text-[#806D68]">{{ $description }}</p>
                            </div>
                            <svg class="mb-1 h-4 w-4 shrink-0 text-[#806D68] transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-5-5l5 5-5 5"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <aside class="xl:pt-11" aria-labelledby="panel-heading">
            <div class="rounded-[24px] bg-[#D8CBC1] p-5 sm:p-6">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#806D68]">Mode tampilan</p>
                <h3 id="panel-heading" class="mt-1 text-xl font-bold tracking-[-0.03em] text-[#2B2020]">Pindah panel</h3>
                <p class="mt-2 text-sm leading-relaxed text-[#725E5A]">Lihat sistem dari sudut pengguna lain tanpa mengganti akun.</p>

                <div class="mt-6 space-y-2">
                    @foreach ($panelLinks as [$title, $description, $url, $external, $enabled])
                        @if ($enabled)
                            <a href="{{ $url }}" @if ($external) target="_blank" rel="noreferrer" @endif
                               class="group flex items-center justify-between gap-3 rounded-xl bg-white/65 px-4 py-3 transition hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#681F1F]">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-[#342526]">{{ $title }}</p>
                                    <p class="truncate text-xs text-[#725E5A]">{{ $description }}</p>
                                </div>
                                <svg class="h-4 w-4 shrink-0 text-[#725E5A] transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-[#BFAEA3] px-4 py-3 opacity-55" aria-disabled="true">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-[#342526]">{{ $title }}</p>
                                    <p class="truncate text-xs text-[#725E5A]">{{ $description }}</p>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#725E5A]">Nonaktif</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="mt-3 flex items-start gap-3 rounded-2xl border border-[#E0D6CE] bg-white/55 p-4">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#681F1F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l7 3v5c0 4.7-2.9 8-7 10-4.1-2-7-5.3-7-10V6l7-3z"/>
                </svg>
                <p class="text-xs leading-relaxed text-[#806D68]">Perubahan di pusat kendali berdampak pada operasional. Periksa data sebelum menyimpan.</p>
            </div>
        </aside>
    </div>
</x-layouts.admin>
