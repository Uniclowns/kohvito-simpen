<x-layouts.admin title="Kelola Pengguna Kasir" page-title="Kelola Pengguna Kasir">
    <x-slot:headerEnd>
        <button onclick="openConfirmModal('form-add-pengguna')"
            class="bg-brand-red hover:bg-brand-dark text-white px-4 py-2 rounded-md text-[14px] transition-colors flex items-center gap-2 font-medium shadow-sm">
            <img src="{{ asset('images/icons/plus.svg') }}" class="w-3 h-3 invert" style="filter: brightness(0) invert(1)"
                alt="Tambah">
            Tambah Pengguna
        </button>
    </x-slot:headerEnd>

    {{-- Flash messages --}}
    @if (session('success'))
        <div
            class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Card Container --}}
    <div class="bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 mt-2">
        <h2 class="text-lg font-bold text-brand-dark mb-5">List Pengguna Kasir</h2>

        {{-- Search Bar --}}
        <div class="mb-6 relative max-w-full">
            <form id="search-form" method="GET" action="{{ route('admin.pengguna-kasir.index') }}">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" id="search-input" value="{{ $search ?? '' }}"
                        placeholder="Cari Pengguna Kasir"
                        class="block w-full bg-[#EBE4E0]/40 border-none rounded-xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-[#380000] transition-all">
                </div>
            </form>
        </div>

        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-bold text-brand-dark uppercase tracking-wide">
                    <th class="pb-4 pr-4 w-[180px]">ID Pengguna</th>
                    <th class="pb-4 pr-4">Nama Lengkap</th>
                    <th class="pb-4 pr-4">Username</th>
                    <th class="pb-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kasirs as $user)
                    <tr class="border-t border-gray-100">
                        <td class="py-4 pr-4 text-sm font-bold text-brand-dark">
                            #{{ str_pad($user->id_users, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-4 pr-4 text-sm text-brand-dark">
                            {{ $user->nama_lengkap }}
                        </td>
                        <td class="py-4 pr-4 text-sm text-brand-dark font-mono">
                            {{ $user->username }}
                        </td>
                        <td class="py-4 text-right">
                            <div class="inline-flex gap-2">
                                <button type="button" onclick="openConfirmModal('form-edit-pengguna-{{ $user->id_users }}')"
                                    class="bg-[#380000] hover:bg-[#2A0000] text-white px-4 py-1.5 rounded-md text-xs font-bold transition-colors flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                                <button type="button"
                                    onclick="confirmDeletePengguna('{{ route('admin.pengguna-kasir.destroy', $user->id_users) }}')"
                                    class="bg-[#E03131] hover:bg-[#C92A2A] text-white px-4 py-1.5 rounded-md text-xs font-bold transition-colors flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Edit Pengguna (per row) --}}
                    <x-pengguna-form-modal id="form-edit-pengguna-{{ $user->id_users }}" mode="edit" :user="$user"
                        :submitUrl="route('admin.pengguna-kasir.update', $user->id_users)" submitMethod="PUT" />
                @empty
                    <tr class="border-t border-gray-100">
                        <td colspan="4" class="py-12 text-center text-sm text-brand-gray">
                            Belum ada akun kasir. Klik "Tambah Pengguna" untuk membuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Pengguna --}}
    <x-pengguna-form-modal id="form-add-pengguna" mode="add" :submitUrl="route('admin.pengguna-kasir.store')" submitMethod="POST" />

    {{-- Modal Konfirmasi Hapus --}}
    <x-confirm-modal id="confirm-hapus-pengguna" title="Apakah anda yakin ingin menghapus pengguna Ini?"
        subtitle="Data pengguna yang dihapus tidak dapat dikembalikan" confirmLabel="Ya, Hapus" cancelLabel="Batal"
        variant="danger" action="#" method="DELETE" />

    <script>
        // Delete confirmation handler
        function confirmDeletePengguna(actionUrl) {
            const modal = document.getElementById('confirm-hapus-pengguna');
            const form = modal.querySelector('form');
            form.action = actionUrl;
            openConfirmModal('confirm-hapus-pengguna');
        }

        // Live Search with Debounce
        const searchInput = document.getElementById('search-input');
        const searchForm = document.getElementById('search-form');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchForm.submit();
            }, 350);
        });

        // Focus cursor to end of text in search input
        if (searchInput.value) {
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
            searchInput.focus();
        }
    </script>

    {{-- Footer --}}
    <x-slot:pageFooter>
        <x-admin-footer />
    </x-slot:pageFooter>
</x-layouts.admin>
