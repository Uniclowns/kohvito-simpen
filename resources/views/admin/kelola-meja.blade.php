<x-layouts.admin title="Kelola Meja" pageTitle="Kelola Meja & QR Code">
    <x-slot:headerEnd>
        <div class="flex w-full items-center gap-2 sm:w-auto">
            <a href="{{ route('superadmin.meja.cetak') }}" target="_blank"
               class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-[#D9CDC5] bg-white px-4 py-2.5 text-[0.875rem] font-bold text-[#460001] shadow-sm transition hover:-translate-y-0.5 hover:border-[#460001] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#7A1F1F] focus-visible:ring-offset-2 sm:flex-none">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Semua QR
            </a>
            <button type="button" onclick="openAppModal('form-add-meja')"
                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#7A1F1F] px-4 py-2.5 text-[0.875rem] font-bold text-white shadow-[0_8px_18px_rgba(70,0,1,0.16)] transition hover:-translate-y-0.5 hover:bg-[#460001] active:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#7A1F1F] focus-visible:ring-offset-2 sm:flex-none">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-width="2.2" d="M12 5v14M5 12h14" />
                </svg>
                Tambah Meja
            </button>
        </div>
    </x-slot:headerEnd>

    {{-- ─── Flash Messages ─── --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            @foreach ($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- ─── Ringkasan QR ─── --}}
    <section aria-label="Ringkasan meja"
             class="mb-6 overflow-hidden rounded-[1.25rem] bg-[#35090A] text-white shadow-[0_16px_40px_rgba(53,9,10,0.14)]">
        <div class="grid lg:grid-cols-[15rem_1fr]">
            <div class="border-b border-white/10 p-5 sm:p-6 lg:border-b-0 lg:border-r">
                <p class="text-[0.6875rem] font-bold uppercase tracking-[0.18em] text-white/55">
                    {{ $search ? 'Hasil ditemukan' : 'Meja terdaftar' }}
                </p>
                <div class="mt-2 flex items-end gap-3">
                    <strong class="font-mono text-[2.5rem] leading-none tabular-nums">{{ str_pad($meja->count(), 2, '0', STR_PAD_LEFT) }}</strong>
                    <span class="mb-1 inline-flex items-center gap-1.5 text-[0.75rem] font-medium text-white/70">
                        <span class="h-2 w-2 rounded-full bg-[#B8D8B0] shadow-[0_0_0_4px_rgba(184,216,176,0.12)]"></span>
                        QR aktif
                    </span>
                </div>
            </div>

            <div class="bg-white/[0.04] p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white/80">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 13a5 5 0 007.07.07l2-2a5 5 0 00-7.07-7.07l-1.15 1.15m3.15 5.85a5 5 0 00-7.07-.07l-2 2A5 5 0 0012 20l1.15-1.15" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[0.6875rem] font-bold uppercase tracking-[0.18em] text-white/55">Alamat tujuan QR</p>
                        <code class="mt-1.5 block truncate text-[0.875rem] text-white sm:text-[0.9375rem]">
                            {{ rtrim(config('app.qr_meja_base_url'), '/') }}/<span class="font-bold text-[#E5BDB7]">[nomor-meja]</span>
                        </code>
                        <p class="mt-1.5 text-[0.8125rem] leading-5 text-white/60">Setiap QR langsung membuka menu pemesanan untuk meja yang sesuai.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Main Card ─── --}}
    <section class="rounded-[1.5rem] bg-white p-4 shadow-[0_12px_36px_rgba(70,0,1,0.07)] sm:p-6">
        <header class="mb-6 flex flex-col gap-4 border-b border-[#EEE7E2] pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[0.6875rem] font-bold uppercase tracking-[0.18em] text-[#8B7770]">Manajemen QR</p>
                <h2 class="mt-1 text-[1.375rem] font-bold tracking-[-0.025em] text-[#281D1D]">Daftar meja</h2>
                <p class="mt-1 text-[0.8125rem] text-[#725E5A]">Pilih meja untuk memperbarui nomor atau mencetak ulang QR.</p>
            </div>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('superadmin.meja.index') }}" id="search-form" class="w-full sm:max-w-sm">
                <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-4 w-4 text-[#8B7770]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input id="search-input" type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nomor meja..."
                       class="block w-full rounded-xl border border-[#E4DAD4] bg-[#FAF8F6] py-2.5 pl-11 pr-4 text-[0.875rem] text-[#281D1D] transition placeholder:text-[#9B8C87] focus:border-[#7A1F1F] focus:outline-none focus:ring-2 focus:ring-[#7A1F1F]/15">
                </div>
            </form>
        </header>

        {{-- Grid kartu meja --}}
        @if ($meja->isEmpty())
            <div class="py-16 text-center">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-brand-light">
                    <svg class="h-10 w-10 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 4h18v4H3V4zm0 8h6v8H3v-8zm10 0h8v8h-8v-8z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-brand-dark">
                    @if ($search)
                        Meja "{{ $search }}" tidak ditemukan
                    @else
                        Belum ada meja terdaftar
                    @endif
                </h3>
                <p class="mt-1 text-sm text-brand-gray">
                    @if ($search)
                        Coba kata kunci lain atau tambah meja baru.
                    @else
                        Klik tombol <strong class="text-brand-dark">Tambah Meja</strong> di pojok kanan atas untuk mulai.
                    @endif
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($meja as $m)
                    <article class="group flex flex-col overflow-hidden rounded-[1.25rem] border border-[#E8DED8] bg-[#FCFAF8] transition duration-200 hover:-translate-y-1 hover:border-[#D8C5BD] hover:shadow-[0_14px_30px_rgba(70,0,1,0.10)]">
                        <header class="flex items-start justify-between px-5 pb-0 pt-5">
                            <div>
                                <p class="text-[0.625rem] font-bold uppercase tracking-[0.18em] text-[#8B7770]">Nomor meja</p>
                                <h3 class="mt-1 font-mono text-[1.75rem] font-bold leading-none tracking-[-0.04em] text-[#35090A]">{{ $m->no_meja }}</h3>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#EDF3EA] px-2.5 py-1.5 text-[0.6875rem] font-bold text-[#476141]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6F9466]"></span>
                                Siap dipindai
                            </span>
                        </header>

                        {{-- QR Body --}}
                        <div class="flex flex-1 flex-col px-5 pb-5 pt-4">
                            <div role="img" aria-label="QR Code Meja {{ $m->no_meja }}"
                                 class="mx-auto aspect-square w-full max-w-[10.5rem] overflow-hidden rounded-2xl bg-white p-3 shadow-[0_8px_24px_rgba(53,9,10,0.08)] ring-1 ring-[#E5DAD3] [&>svg]:block [&>svg]:h-full [&>svg]:w-full">
                                {!! $m->qr_svg !!}
                            </div>

                            <div class="mt-4 rounded-xl border border-[#EAE1DC] bg-white px-3 py-2.5">
                                <p class="text-[0.625rem] font-bold uppercase tracking-[0.14em] text-[#9B8C87]">Tujuan pemesanan</p>
                                <p class="mt-1 truncate font-mono text-[0.75rem] text-[#5F4A47]" title="{{ $m->scan_url }}">{{ $m->scan_url }}</p>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <footer class="grid grid-cols-2 gap-2 border-t border-[#E8DED8] bg-white p-3">
                            <button type="button"
                                    onclick="openAppModal('form-edit-meja-{{ $m->id_meja }}')"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#460001] px-3 py-2 text-[0.75rem] font-bold text-white transition hover:bg-[#2A0000] active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#7A1F1F] focus-visible:ring-offset-2">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                            <button type="button"
                                    onclick="confirmHapusMeja('{{ route('superadmin.meja.destroy', $m->id_meja) }}', '{{ $m->no_meja }}')"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-[#E7C8C5] bg-[#FFF7F6] px-3 py-2 text-[0.75rem] font-bold text-[#A3342E] transition hover:border-[#A3342E] hover:bg-[#A3342E] hover:text-white active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#A3342E] focus-visible:ring-offset-2">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                </svg>
                                Hapus
                            </button>
                        </footer>
                    </article>

                    {{-- Modal Edit per meja --}}
                    <div id="form-edit-meja-{{ $m->id_meja }}"
                         class="hidden fixed inset-0 z-[60] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
                         onclick="if(event.target === this) closeAppModal('form-edit-meja-{{ $m->id_meja }}')">
                        <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,0.18)] sm:p-8">
                            <button type="button"
                                    onclick="closeAppModal('form-edit-meja-{{ $m->id_meja }}')"
                                    class="absolute right-5 top-5 text-[#380000] hover:text-[#681F1F]"
                                    aria-label="Tutup">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <h2 class="pr-10 text-[20px] font-bold leading-tight text-[#380000] sm:text-[24px]">
                                Edit Meja {{ $m->no_meja }}
                            </h2>
                            <p class="mt-2 text-sm text-[#808080]">Ubah nomor meja. QR Code akan otomatis ter-generate ulang.</p>

                            <form method="POST" action="{{ route('superadmin.meja.update', $m->id_meja) }}" class="mt-6">
                                @csrf @method('PUT')
                                <label class="mb-2 block text-sm font-medium text-brand-dark">Nomor Meja</label>
                                <input type="text" name="no_meja" value="{{ $m->no_meja }}" required maxlength="10"
                                       class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm transition-all focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">

                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button"
                                            onclick="closeAppModal('form-edit-meja-{{ $m->id_meja }}')"
                                            class="rounded-lg bg-[#D0D0D0] px-4 py-2 text-sm font-medium text-[#681F1F] shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#C4C4C4]">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            class="rounded-lg bg-[#7A1F1F] px-4 py-2 text-sm font-bold text-white shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#681F1F]">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ─── Modal Tambah Meja ─── --}}
    <div id="form-add-meja"
         class="hidden fixed inset-0 z-[60] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
         onclick="if(event.target === this) closeAppModal('form-add-meja')">
        <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,0.18)] sm:p-8">
            <button type="button" onclick="closeAppModal('form-add-meja')"
                    class="absolute right-5 top-5 text-[#380000] hover:text-[#681F1F]" aria-label="Tutup">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h2 class="pr-10 text-[20px] font-bold leading-tight text-[#380000] sm:text-[24px]">
                Tambah Meja Baru
            </h2>
            <p class="mt-2 text-sm text-[#808080]">
                Beri nomor unik untuk meja fisik di café Anda.
            </p>

            <form method="POST" action="{{ route('superadmin.meja.store') }}" class="mt-6">
                @csrf
                <label class="mb-2 block text-sm font-medium text-brand-dark">Nomor Meja</label>
                <input type="text" name="no_meja" required maxlength="10"
                       placeholder="contoh: 01, A1, VIP-3" autofocus
                       class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm transition-all focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">

                <div class="mt-3 rounded-md bg-brand-light p-3 text-xs text-brand-dark">
                    <span class="font-medium">URL QR Code akan:</span>
                    <div class="mt-1 break-all font-mono">
                        {{ rtrim(config('app.qr_meja_base_url'), '/') }}/<span class="font-bold text-brand-red">[nomor]</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeAppModal('form-add-meja')"
                            class="rounded-lg bg-[#D0D0D0] px-4 py-2 text-sm font-medium text-[#681F1F] shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#C4C4C4]">
                        Batal
                    </button>
                    <button type="submit"
                            class="rounded-lg bg-[#7A1F1F] px-4 py-2 text-sm font-bold text-white shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#681F1F]">
                        Tambah Meja
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Modal Konfirmasi Hapus (reusable confirm-modal component) ─── --}}
    <x-confirm-modal id="confirm-hapus-meja"
                     title="Hapus meja ini?"
                     subtitle="Meja akan dihapus permanen. Pesanan historis yang merujuk meja ini bisa kehilangan referensi."
                     confirmLabel="Ya, Hapus"
                     cancelLabel="Batal"
                     variant="danger"
                     action="#"
                     method="DELETE" />

    <x-slot:scripts>
        <script>
            // ── Confirm delete: inject action URL ke form modal global ──
            function confirmHapusMeja(actionUrl, noMeja) {
                const modal = document.getElementById('confirm-hapus-meja');
                const form  = modal.querySelector('form');
                form.action = actionUrl;
                modal.querySelector('h2').textContent = `Hapus Meja ${noMeja}?`;
                openAppModal('confirm-hapus-meja');
            }

            // ── Live search dengan debounce ──
            const searchInput = document.getElementById('search-input');
            const searchForm  = document.getElementById('search-form');
            let debounceTimer;

            searchInput?.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => searchForm.submit(), 350);
            });

            // Kembalikan cursor ke akhir teks (lebih nyaman saat refresh dengan keyword)
            if (searchInput?.value) {
                const v = searchInput.value;
                searchInput.value = '';
                searchInput.value = v;
                searchInput.focus();
            }
        </script>
    </x-slot:scripts>
</x-layouts.admin>
