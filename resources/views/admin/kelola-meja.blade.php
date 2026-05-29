<x-layouts.admin title="Kelola Meja" pageTitle="Kelola Meja & QR Code">
    <x-slot:headerEnd>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.meja.cetak') }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-md border border-brand-dark/15 bg-white px-4 py-2 text-[14px] font-medium text-brand-dark shadow-sm transition-colors hover:bg-brand-dark hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Semua QR
            </a>
            <button type="button" onclick="openAppModal('form-add-meja')"
                    class="inline-flex items-center gap-2 rounded-md bg-brand-red px-4 py-2 text-[14px] font-medium text-white shadow-sm transition-colors hover:bg-brand-dark">
                <img src="{{ asset('images/icons/plus.svg') }}" class="h-3 w-3" style="filter: brightness(0) invert(1)" alt="">
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

    {{-- ─── Stats Strip ─── --}}
    <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-[0_4px_24px_rgba(0,0,0,0.06)]">
            <div class="text-xs font-medium uppercase tracking-wide text-brand-gray">Total Meja</div>
            <div class="mt-2 text-3xl font-bold text-brand-dark">{{ $meja->count() }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-[0_4px_24px_rgba(0,0,0,0.06)] sm:col-span-2">
            <div class="text-xs font-medium uppercase tracking-wide text-brand-gray">Base URL QR Code</div>
            <div class="mt-2 truncate font-mono text-sm text-brand-dark">
                {{ rtrim(config('app.qr_meja_base_url'), '/') }}/<span class="text-brand-red">[no_meja]</span>
            </div>
            <p class="mt-1 text-xs text-brand-gray">Customer scan QR → otomatis buka halaman menu meja tersebut.</p>
        </div>
    </div>

    {{-- ─── Main Card ─── --}}
    <div class="rounded-2xl bg-white p-5 shadow-[0_4px_24px_rgba(0,0,0,0.06)] sm:p-6">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-bold text-brand-dark">Daftar Meja</h2>
        </div>

        {{-- Search bar --}}
        <form method="GET" action="{{ route('superadmin.meja.index') }}" id="search-form" class="mb-6">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-5 w-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input id="search-input" type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nomor meja..."
                       class="block w-full rounded-xl border-none bg-[#EBE4E0]/40 py-3 pl-12 pr-4 text-sm transition-all focus:ring-2 focus:ring-[#380000]">
            </div>
        </form>

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
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($meja as $m)
                    <article class="group overflow-hidden rounded-2xl border border-brand-gray-extralight bg-white transition-all hover:-translate-y-0.5 hover:shadow-[0_8px_24px_rgba(70,0,1,0.12)]">

                        {{-- Header: brand strip --}}
                        <header class="flex items-center justify-between bg-brand-dark px-4 py-3 text-white">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 4h18v4H3V4zm0 8h6v8H3v-8zm10 0h8v8h-8v-8z"/>
                                </svg>
                                <span class="text-xs font-medium uppercase tracking-wider opacity-80">Meja</span>
                            </div>
                            <span class="text-lg font-bold leading-none">{{ $m->no_meja }}</span>
                        </header>

                        {{-- QR Body --}}
                        <div class="flex flex-col items-center px-4 pb-4 pt-5">
                            <div class="rounded-xl bg-white p-2 ring-1 ring-brand-gray-extralight"
                                 style="width: 170px; height: 170px;">
                                {!! $m->qr_svg !!}
                            </div>

                            <p class="mt-3 line-clamp-2 break-all text-center text-[10px] leading-tight text-brand-gray font-mono">
                                {{ $m->scan_url }}
                            </p>
                        </div>

                        {{-- Footer Actions --}}
                        <footer class="grid grid-cols-2 gap-2 border-t border-brand-gray-extralight bg-[#FAFAFA] p-3">
                            <button type="button"
                                    onclick="openAppModal('form-edit-meja-{{ $m->id_meja }}')"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-md bg-[#380000] px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-[#2A0000]">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                            <button type="button"
                                    onclick="confirmHapusMeja('{{ route('superadmin.meja.destroy', $m->id_meja) }}', '{{ $m->no_meja }}')"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-md bg-[#E03131] px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-[#C92A2A]">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    </div>

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
