<x-layouts.admin title="Kelola Admin" pageTitle="Kelola Admin">
    <x-slot:headerEnd>
        <button type="button" onclick="openAppModal('form-add-admin')"
                class="inline-flex items-center gap-2 rounded-md bg-brand-red px-4 py-2 text-[14px] font-medium text-white shadow-sm transition-colors hover:bg-brand-dark">
            <img src="{{ asset('images/icons/plus.svg') }}" class="h-3 w-3" style="filter: brightness(0) invert(1)" alt="">
            Tambah Admin
        </button>
    </x-slot:headerEnd>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            @foreach ($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- Card --}}
    <div class="rounded-2xl bg-white p-4 shadow-[0_4px_24px_rgba(0,0,0,0.06)] sm:p-6">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-bold text-brand-dark">List Administrator</h2>
            <span class="text-sm text-brand-gray">{{ $admins->count() }} admin terdaftar</span>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('superadmin.admin.index') }}" id="search-form" class="mb-6">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-5 w-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input id="search-input" type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nama atau username admin..."
                       class="block w-full rounded-xl border-none bg-[#EBE4E0]/40 py-3 pl-12 pr-4 text-sm transition-all focus:ring-2 focus:ring-[#380000]">
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr class="text-left text-xs font-bold uppercase tracking-wide text-brand-dark">
                        <th class="pb-4 pr-4 w-[160px]">ID Admin</th>
                        <th class="pb-4 pr-4">Nama Lengkap</th>
                        <th class="pb-4 pr-4">Username</th>
                        <th class="pb-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr class="border-t border-gray-100">
                            <td class="py-4 pr-4 text-sm font-bold text-brand-dark">
                                #{{ str_pad($admin->id_users, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 pr-4 text-sm text-brand-dark">{{ $admin->nama_lengkap }}</td>
                            <td class="py-4 pr-4 text-sm font-mono text-brand-dark">{{ $admin->username }}</td>
                            <td class="py-4 text-right">
                                <div class="inline-flex gap-2">
                                    <button type="button" onclick="openAppModal('form-edit-admin-{{ $admin->id_users }}')"
                                            class="rounded-md bg-[#380000] px-4 py-1.5 text-xs font-bold text-white transition-colors hover:bg-[#2A0000]">
                                        Edit
                                    </button>
                                    <button type="button"
                                            onclick="confirmHapusAdmin('{{ route('superadmin.admin.destroy', $admin->id_users) }}', '{{ $admin->nama_lengkap }}')"
                                            class="rounded-md bg-[#E03131] px-4 py-1.5 text-xs font-bold text-white transition-colors hover:bg-[#C92A2A]">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit per admin --}}
                        <tr class="hidden"><td>
                        <div id="form-edit-admin-{{ $admin->id_users }}"
                             class="hidden fixed inset-0 z-[60] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
                             onclick="if(event.target === this) closeAppModal('form-edit-admin-{{ $admin->id_users }}')">
                            <div class="relative w-full max-w-md rounded-lg bg-white p-6 text-left shadow-[0_8px_24px_rgba(0,0,0,0.18)] sm:p-8">
                                <button type="button" onclick="closeAppModal('form-edit-admin-{{ $admin->id_users }}')"
                                        class="absolute right-5 top-5 text-[#380000] hover:text-[#681F1F]" aria-label="Tutup">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <h2 class="pr-10 text-[20px] font-bold leading-tight text-[#380000] sm:text-[24px]">Edit Admin</h2>
                                <p class="mt-2 text-sm text-[#808080]">Kosongkan password jika tidak ingin mengubahnya.</p>
                                <form method="POST" action="{{ route('superadmin.admin.update', $admin->id_users) }}" class="mt-6 flex flex-col gap-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-brand-dark">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" value="{{ $admin->nama_lengkap }}" required maxlength="255"
                                               class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-brand-dark">Username</label>
                                        <input type="text" name="username" value="{{ $admin->username }}" required minlength="6" maxlength="255"
                                               class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm font-mono focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-brand-dark">Password Baru <span class="font-normal text-brand-gray">(opsional)</span></label>
                                        <input type="password" name="password" minlength="9" placeholder="Minimal 9 karakter"
                                               class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">
                                    </div>
                                    <div class="mt-2 flex justify-end gap-3">
                                        <button type="button" onclick="closeAppModal('form-edit-admin-{{ $admin->id_users }}')"
                                                class="rounded-lg bg-[#D0D0D0] px-4 py-2 text-sm font-medium text-[#681F1F] shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#C4C4C4]">Batal</button>
                                        <button type="submit"
                                                class="rounded-lg bg-[#7A1F1F] px-4 py-2 text-sm font-bold text-white shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#681F1F]">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        </td></tr>
                    @empty
                        <tr class="border-t border-gray-100">
                            <td colspan="4" class="py-12 text-center text-sm text-brand-gray">
                                @if ($search)
                                    Tidak ada admin dengan kata kunci "{{ $search }}".
                                @else
                                    Belum ada akun admin. Klik "Tambah Admin" untuk membuat.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Admin --}}
    <div id="form-add-admin"
         class="hidden fixed inset-0 z-[60] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
         onclick="if(event.target === this) closeAppModal('form-add-admin')">
        <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,0.18)] sm:p-8">
            <button type="button" onclick="closeAppModal('form-add-admin')"
                    class="absolute right-5 top-5 text-[#380000] hover:text-[#681F1F]" aria-label="Tutup">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h2 class="pr-10 text-[20px] font-bold leading-tight text-[#380000] sm:text-[24px]">Tambah Admin Baru</h2>
            <p class="mt-2 text-sm text-[#808080]">Buat akun administrator baru untuk mengelola sistem.</p>
            <form method="POST" action="{{ route('superadmin.admin.store') }}" class="mt-6 flex flex-col gap-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-brand-dark">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required maxlength="255" placeholder="contoh: Budi Santoso"
                           class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-brand-dark">Username</label>
                    <input type="text" name="username" required minlength="6" maxlength="255" placeholder="minimal 6 karakter"
                           class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm font-mono focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-brand-dark">Password</label>
                    <input type="password" name="password" required minlength="9" placeholder="minimal 9 karakter"
                           class="w-full rounded-lg border border-brand-gray-extralight bg-white px-4 py-2.5 text-sm focus:border-[#380000] focus:ring-2 focus:ring-[#380000]/20">
                </div>
                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" onclick="closeAppModal('form-add-admin')"
                            class="rounded-lg bg-[#D0D0D0] px-4 py-2 text-sm font-medium text-[#681F1F] shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#C4C4C4]">Batal</button>
                    <button type="submit"
                            class="rounded-lg bg-[#7A1F1F] px-4 py-2 text-sm font-bold text-white shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#681F1F]">Tambah Admin</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <x-confirm-modal id="confirm-hapus-admin"
                     title="Hapus admin ini?"
                     subtitle="Akun admin akan dihapus permanen dan tidak dapat dikembalikan."
                     confirmLabel="Ya, Hapus" cancelLabel="Batal"
                     variant="danger" action="#" method="DELETE" />

    <x-slot:scripts>
        <script>
            function confirmHapusAdmin(actionUrl, nama) {
                const modal = document.getElementById('confirm-hapus-admin');
                modal.querySelector('form').action = actionUrl;
                modal.querySelector('h2').textContent = `Hapus admin "${nama}"?`;
                openAppModal('confirm-hapus-admin');
            }

            const searchInput = document.getElementById('search-input');
            const searchForm  = document.getElementById('search-form');
            let debounceTimer;
            searchInput?.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => searchForm.submit(), 350);
            });
            if (searchInput?.value) {
                const v = searchInput.value; searchInput.value = ''; searchInput.value = v; searchInput.focus();
            }
        </script>
    </x-slot:scripts>
</x-layouts.admin>
