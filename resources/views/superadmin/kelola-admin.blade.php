<x-layouts.admin title="Kelola Admin" pageTitle="Manajemen admin">
    <x-slot:headerEnd>
        <button type="button" onclick="openAppModal('form-add-admin')"
                class="inline-flex items-center gap-2 rounded-xl bg-[#3A0A0B] px-4 py-2.5 text-sm font-bold text-white shadow-[0_10px_26px_rgba(58,10,11,0.2)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#571719] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#681F1F] focus-visible:ring-offset-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
            </svg>
            Tambah admin
        </button>
    </x-slot:headerEnd>

    @if (session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-2xl border border-[#BCD5C2] bg-[#EEF7F0] px-4 py-3 text-sm text-[#285B35]" role="status">
            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="mb-4 rounded-2xl border border-[#E4BBBB] bg-[#FFF1F1] px-4 py-3 text-sm text-[#7A1F1F]" role="alert">
            @if (session('error'))
                <p>{{ session('error') }}</p>
            @endif
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="relative overflow-hidden rounded-[26px] bg-[#DDD0C6] p-5 sm:p-7">
        <div class="pointer-events-none absolute -right-14 -top-20 h-56 w-56 rounded-full border border-white/35"></div>
        <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#806D68]">Kontrol akses</p>
                <h2 class="mt-2 max-w-2xl text-2xl font-bold tracking-[-0.035em] text-[#2B2020] sm:text-3xl">Akun yang mengelola operasional KOHVITO.</h2>
                <p class="mt-3 max-w-[62ch] text-sm leading-relaxed text-[#725E5A]">Tambah, perbarui, atau hapus administrator. Sistem tetap melindungi akun admin terakhir.</p>
            </div>
            <div class="flex min-w-40 items-end justify-between rounded-2xl border border-white/35 bg-white/40 px-5 py-4">
                <div>
                    <p class="text-xs font-medium text-[#806D68]">Total admin</p>
                    <p class="mt-1 text-3xl font-bold tabular-nums tracking-[-0.04em] text-[#3A0A0B]">{{ str_pad($admins->count(), 2, '0', STR_PAD_LEFT) }}</p>
                </div>
                <span class="mb-1 h-2 w-2 rounded-full bg-[#7A1F1F]"></span>
            </div>
        </div>
    </section>

    <section class="mt-5 overflow-hidden rounded-[24px] border border-[#E3DAD3] bg-white/80" aria-labelledby="admin-list-heading">
        <div class="border-b border-[#E8E0D9] p-4 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 id="admin-list-heading" class="text-lg font-bold tracking-[-0.025em] text-[#2B2020]">Daftar administrator</h2>
                    <p class="mt-1 text-xs text-[#725E5A]">{{ $search ? 'Hasil pencarian akun' : 'Semua akun dengan peran Admin' }}</p>
                </div>

                <form method="GET" action="{{ route('superadmin.admin.index') }}" id="search-form" class="w-full lg:max-w-md">
                    <label for="search-input" class="sr-only">Cari admin</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#725E5A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5-5m2-6a8 8 0 11-16 0 8 8 0 0116 0z"/>
                        </svg>
                        <input id="search-input" type="search" name="search" value="{{ $search ?? '' }}"
                               placeholder="Cari nama atau username"
                               class="w-full rounded-xl border border-[#DED5CD] bg-[#F7F4F1] py-3 pl-11 pr-11 text-sm text-[#342526] outline-none transition placeholder:text-[#A79590] focus:border-[#7A1F1F] focus:bg-white focus:ring-2 focus:ring-[#7A1F1F]/10">
                        @if ($search)
                            <a href="{{ route('superadmin.admin.index') }}" class="absolute right-3 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-lg text-[#806D68] transition hover:bg-[#E9DED5] hover:text-[#3A0A0B]" aria-label="Hapus pencarian">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_auto] gap-4 border-b border-[#E8E0D9] bg-[#F7F4F1]/75 px-6 py-3 text-[10px] font-bold uppercase tracking-[0.16em] text-[#725E5A] md:grid">
            <span>Administrator</span>
            <span>Username</span>
            <span class="pr-1 text-right">Tindakan</span>
        </div>

        <div class="divide-y divide-[#E8E0D9]">
            @forelse ($admins as $admin)
                <article class="grid gap-4 px-4 py-4 transition hover:bg-[#FBF8F5] sm:px-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_auto] md:items-center">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#E9DED5] text-sm font-bold text-[#571719]">
                            {{ strtoupper(mb_substr($admin->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-bold text-[#342526]">{{ $admin->nama_lengkap }}</h3>
                            <p class="mt-0.5 text-xs tabular-nums text-[#725E5A]">ID #{{ str_pad($admin->id_users, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>

                    <div class="min-w-0 pl-14 md:pl-0">
                        <span class="inline-flex max-w-full rounded-lg bg-[#F1ECE7] px-3 py-1.5 font-mono text-xs text-[#5F4A47]">
                            <span class="truncate">{{ $admin->username }}</span>
                        </span>
                    </div>

                    <div class="flex gap-2 pl-14 md:justify-end md:pl-0">
                        <button type="button" onclick="openAppModal('form-edit-admin-{{ $admin->id_users }}')"
                                class="rounded-lg border border-[#D8CCC4] bg-white px-3 py-2 text-xs font-bold text-[#571719] transition hover:border-[#BFAEA3] hover:bg-[#F3ECE6] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#681F1F]">
                            Edit
                        </button>
                        <button type="button"
                                data-action="{{ route('superadmin.admin.destroy', $admin->id_users) }}"
                                data-name="{{ $admin->nama_lengkap }}"
                                onclick="confirmHapusAdmin(this)"
                                class="rounded-lg px-3 py-2 text-xs font-bold text-[#9B3437] transition hover:bg-[#FFF0F0] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#B54447]">
                            Hapus
                        </button>
                    </div>
                </article>

                <div id="form-edit-admin-{{ $admin->id_users }}"
                     class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-[#180405]/55 p-4 backdrop-blur-sm"
                     role="dialog" aria-modal="true" aria-labelledby="edit-admin-title-{{ $admin->id_users }}" data-superadmin-modal tabindex="-1"
                     onclick="if(event.target === this) closeAppModal('form-edit-admin-{{ $admin->id_users }}')">
                    <div class="kvt-modal-panel relative w-full max-w-lg overflow-y-auto rounded-[24px] bg-[#FAF8F5] p-6 shadow-[0_30px_90px_rgba(24,4,5,0.28)] sm:p-8">
                        <button type="button" onclick="closeAppModal('form-edit-admin-{{ $admin->id_users }}')"
                                class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-xl text-[#725E5A] transition hover:bg-[#EDE5DF] hover:text-[#3A0A0B]" aria-label="Tutup">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
                        </button>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#725E5A]">Perbarui akses</p>
                        <h2 id="edit-admin-title-{{ $admin->id_users }}" class="mt-2 pr-12 text-2xl font-bold tracking-[-0.03em] text-[#2B2020]">Edit admin</h2>
                        <p class="mt-2 text-sm text-[#806D68]">Kosongkan password jika tidak ingin mengubahnya.</p>

                        <form method="POST" action="{{ route('superadmin.admin.update', $admin->id_users) }}" class="mt-7 space-y-5">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="edit-name-{{ $admin->id_users }}" class="mb-2 block text-sm font-bold text-[#493634]">Nama lengkap</label>
                                <input id="edit-name-{{ $admin->id_users }}" type="text" name="nama_lengkap" value="{{ $admin->nama_lengkap }}" required maxlength="255" autocomplete="name"
                                       class="w-full rounded-xl border border-[#D8CCC4] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#7A1F1F] focus:ring-2 focus:ring-[#7A1F1F]/10">
                            </div>
                            <div>
                                <label for="edit-username-{{ $admin->id_users }}" class="mb-2 block text-sm font-bold text-[#493634]">Username</label>
                                <input id="edit-username-{{ $admin->id_users }}" type="text" name="username" value="{{ $admin->username }}" required minlength="6" maxlength="255" autocomplete="username"
                                       class="w-full rounded-xl border border-[#D8CCC4] bg-white px-4 py-3 font-mono text-sm outline-none transition focus:border-[#7A1F1F] focus:ring-2 focus:ring-[#7A1F1F]/10">
                            </div>
                            <div>
                                <label for="edit-password-{{ $admin->id_users }}" class="mb-2 block text-sm font-bold text-[#493634]">Password baru <span class="font-normal text-[#725E5A]">(opsional)</span></label>
                                <input id="edit-password-{{ $admin->id_users }}" type="password" name="password" minlength="9" placeholder="Minimal 9 karakter" autocomplete="new-password"
                                       class="w-full rounded-xl border border-[#D8CCC4] bg-white px-4 py-3 text-sm outline-none transition placeholder:text-[#A79590] focus:border-[#7A1F1F] focus:ring-2 focus:ring-[#7A1F1F]/10">
                            </div>
                            <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                                <button type="button" onclick="closeAppModal('form-edit-admin-{{ $admin->id_users }}')" class="rounded-xl px-4 py-2.5 text-sm font-bold text-[#725E5A] transition hover:bg-[#EDE5DF]">Batal</button>
                                <button type="submit" class="rounded-xl bg-[#3A0A0B] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#571719] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#681F1F] focus-visible:ring-offset-2">Simpan perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-5 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#E9DED5] text-[#681F1F]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5-5m2-6a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-[#342526]">{{ $search ? 'Admin tidak ditemukan' : 'Belum ada akun admin' }}</h3>
                    <p class="mt-1 text-sm text-[#725E5A]">{{ $search ? 'Coba kata kunci lain atau hapus pencarian.' : 'Tambahkan akun pertama untuk mulai mengelola operasional.' }}</p>
                </div>
            @endforelse
        </div>
    </section>

    <div id="form-add-admin"
         class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-[#180405]/55 p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true" aria-labelledby="add-admin-title" data-superadmin-modal tabindex="-1"
         onclick="if(event.target === this) closeAppModal('form-add-admin')">
        <div class="kvt-modal-panel relative w-full max-w-lg overflow-y-auto rounded-[24px] bg-[#FAF8F5] p-6 shadow-[0_30px_90px_rgba(24,4,5,0.28)] sm:p-8">
            <button type="button" onclick="closeAppModal('form-add-admin')"
                    class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-xl text-[#725E5A] transition hover:bg-[#EDE5DF] hover:text-[#3A0A0B]" aria-label="Tutup">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#725E5A]">Akun baru</p>
            <h2 id="add-admin-title" class="mt-2 pr-12 text-2xl font-bold tracking-[-0.03em] text-[#2B2020]">Tambah admin</h2>
            <p class="mt-2 text-sm text-[#806D68]">Buat akun administrator untuk mengelola operasional.</p>

            <form method="POST" action="{{ route('superadmin.admin.store') }}" class="mt-7 space-y-5">
                @csrf
                <div>
                    <label for="add-admin-name" class="mb-2 block text-sm font-bold text-[#493634]">Nama lengkap</label>
                    <input id="add-admin-name" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required maxlength="255" autocomplete="name" placeholder="Contoh: Budi Santoso"
                           class="w-full rounded-xl border border-[#D8CCC4] bg-white px-4 py-3 text-sm outline-none transition placeholder:text-[#A79590] focus:border-[#7A1F1F] focus:ring-2 focus:ring-[#7A1F1F]/10">
                </div>
                <div>
                    <label for="add-admin-username" class="mb-2 block text-sm font-bold text-[#493634]">Username</label>
                    <input id="add-admin-username" type="text" name="username" value="{{ old('username') }}" required minlength="6" maxlength="255" autocomplete="username" placeholder="Minimal 6 karakter"
                           class="w-full rounded-xl border border-[#D8CCC4] bg-white px-4 py-3 font-mono text-sm outline-none transition placeholder:text-[#A79590] focus:border-[#7A1F1F] focus:ring-2 focus:ring-[#7A1F1F]/10">
                </div>
                <div>
                    <label for="add-admin-password" class="mb-2 block text-sm font-bold text-[#493634]">Password</label>
                    <input id="add-admin-password" type="password" name="password" required minlength="9" autocomplete="new-password" placeholder="Minimal 9 karakter"
                           class="w-full rounded-xl border border-[#D8CCC4] bg-white px-4 py-3 text-sm outline-none transition placeholder:text-[#A79590] focus:border-[#7A1F1F] focus:ring-2 focus:ring-[#7A1F1F]/10">
                </div>
                <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeAppModal('form-add-admin')" class="rounded-xl px-4 py-2.5 text-sm font-bold text-[#725E5A] transition hover:bg-[#EDE5DF]">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#3A0A0B] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#571719] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#681F1F] focus-visible:ring-offset-2">Tambah admin</button>
                </div>
            </form>
        </div>
    </div>

    <x-confirm-modal id="confirm-hapus-admin"
                     title="Hapus admin ini?"
                     subtitle="Akun admin akan dihapus permanen dan tidak dapat dikembalikan."
                     confirmLabel="Ya, hapus" cancelLabel="Batal"
                     variant="danger" action="#" method="DELETE" />

    <x-slot:scripts>
        <script>
            const deleteModal = document.getElementById('confirm-hapus-admin');
            deleteModal?.setAttribute('data-superadmin-modal', '');
            deleteModal?.setAttribute('tabindex', '-1');

            function confirmHapusAdmin(button) {
                const modal = document.getElementById('confirm-hapus-admin');
                modal.querySelector('form').action = button.dataset.action;
                modal.querySelector('h2').textContent = `Hapus admin "${button.dataset.name}"?`;
                openAppModal('confirm-hapus-admin');
            }

            const searchInput = document.getElementById('search-input');
            const searchForm = document.getElementById('search-form');
            let debounceTimer;

            searchInput?.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => searchForm.submit(), 350);
            });

            if (searchInput?.value) {
                const value = searchInput.value;
                searchInput.value = '';
                searchInput.value = value;
                searchInput.focus();
            }
        </script>
    </x-slot:scripts>
</x-layouts.admin>
