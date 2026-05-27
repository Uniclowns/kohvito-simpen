<x-layouts.admin title="Kelola Kategori Menu" page-title="Kelola Kategori Menu">
    <x-slot:headerEnd>
        <button onclick="openAppModal('form-add-kategori')"
            class="bg-brand-red hover:bg-brand-dark text-white px-4 py-2 rounded-md text-[14px] transition-colors flex items-center gap-2 font-medium shadow-sm">
            <img src="{{ asset('images/icons/plus.svg') }}" class="w-3 h-3 invert"
                style="filter: brightness(0) invert(1)" alt="Tambah">
            Tambah Kategori Menu
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

    @if (session('error'))
        <div
            class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg px-4 py-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if (session('status_modal'))
        @php($statusModal = session('status_modal'))

        <x-status-modal :id="$statusModal['id']" :title="$statusModal['title']" :message="$statusModal['message']" :buttonLabel="$statusModal['buttonLabel']"
            :variant="$statusModal['variant']" />

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                openStatusModal('{{ $statusModal['id'] }}');
            });
        </script>
    @endif

    {{-- Card Container --}}
    <div class="kvt-scroll-region mt-2 overflow-x-auto rounded-2xl bg-white p-4 shadow-[0_4px_24px_rgba(0,0,0,0.06)] sm:p-6" tabindex="0" aria-label="Daftar kategori menu">
        <h2 class="text-lg font-bold text-brand-dark mb-5">List Kategori Menu</h2>

        <table class="w-full min-w-[680px]">
            <thead>
                <tr class="text-left text-xs font-bold text-brand-dark uppercase tracking-wide">
                    <th class="pb-4 pr-4 w-[180px]">ID Kategori</th>
                    <th class="pb-4 pr-4">Nama Kategori Menu</th>
                    <th class="pb-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kategoris as $kat)
                    <tr class="border-t border-gray-100">
                        <td class="py-4 pr-4 text-sm font-bold text-brand-dark">
                            #{{ str_pad($kat->id_kategori, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-4 pr-4 text-sm text-brand-dark">
                            {{ $kat->nama_kategori }}
                        </td>
                        <td class="py-4 text-right">
                            <div class="inline-flex gap-2">
                                <button type="button"
                                    onclick="openEditKategori({{ $kat->id_kategori }}, '{{ addslashes($kat->nama_kategori) }}')"
                                    class="bg-[#380000] hover:bg-[#2A0000] text-white px-4 py-1.5 rounded-md text-xs font-bold transition-colors flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                                <button type="button"
                                    onclick="confirmDeleteKategori('{{ route('admin.kategori.destroy', $kat->id_kategori) }}')"
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
                @empty
                    <tr class="border-t border-gray-100">
                        <td colspan="3" class="py-12 text-center text-sm text-brand-gray">
                            Belum ada kategori menu. Klik "Tambah Kategori Menu" untuk membuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ─── Modal: Tambah Kategori ─── --}}
    <div id="form-add-kategori" data-form-modal
        class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
        onclick="if(event.target === this) closeAppModal('form-add-kategori')">
        <div class="kvt-modal-panel relative w-full max-w-[440px] overflow-y-auto rounded-2xl bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.15)] sm:p-6">
            <button type="button"
                class="absolute top-5 right-5 text-brand-gray hover:text-brand-black"
                onclick="closeAppModal('form-add-kategori')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-base font-bold text-brand-dark mb-4">Tambah Kategori Menu</h3>
            <form id="form-add-kategori-form" method="POST" action="{{ route('admin.kategori.store') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-bold text-brand-dark mb-2">Nama Kategori</label>
                    <input type="text" name="nama_kategori" placeholder="Masukkan nama kategori"
                        class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#380000]"
                        required>
                </div>
                <div class="kvt-modal-actions flex justify-end gap-3">
                    <button type="button"
                        onclick="closeAppModal('form-add-kategori')"
                        class="bg-[#EBE4E0] text-[#380000] px-5 py-2 rounded-xl text-sm font-bold hover:bg-[#DFD4CF]">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-[#380000] text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-[#2A0000]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Modal: Edit Kategori ─── --}}
    <div id="form-edit-kategori" data-form-modal
        class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
        onclick="if(event.target === this) closeAppModal('form-edit-kategori')">
        <div class="kvt-modal-panel relative w-full max-w-[440px] overflow-y-auto rounded-2xl bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.15)] sm:p-6">
            <button type="button"
                class="absolute top-5 right-5 text-brand-gray hover:text-brand-black"
                onclick="closeAppModal('form-edit-kategori')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-base font-bold text-brand-dark mb-4">Edit Kategori Menu</h3>
            <form id="form-edit-kategori-form" method="POST" action="" onsubmit="return confirmSubmitEditKategori(event)">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label class="block text-xs font-bold text-brand-dark mb-2">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="edit-nama-kategori" placeholder="Masukkan nama kategori"
                        class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#380000]"
                        required>
                </div>
                <div class="kvt-modal-actions flex justify-end gap-3">
                    <button type="button"
                        onclick="closeAppModal('form-edit-kategori')"
                        class="bg-[#EBE4E0] text-[#380000] px-5 py-2 rounded-xl text-sm font-bold hover:bg-[#DFD4CF]">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-[#380000] text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-[#2A0000]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Confirm Delete Modal ─── --}}
    <x-confirm-modal id="confirm-delete-kategori" title="Apakah anda yakin ingin menghapus kategori menu Ini?"
        subtitle="Kategori menu yang dihapus tidak dapat dikembalikan" confirmLabel="Ya, Hapus" cancelLabel="Batal"
        variant="danger" action="#" method="DELETE" />

    {{-- ─── Confirm Save Changes Modal ─── --}}
    <x-confirm-modal id="confirm-save-kategori" title="Apakah anda yakin ingin menyimpan perubahan kategori menu Ini?"
        subtitle="Kategori menu yang diubah tidak dapat dikembalikan" confirmLabel="Ya, Simpan" cancelLabel="Batal"
        variant="primary" onConfirm="submitEditKategoriAfterConfirm()" />

    <script>
        let pendingEditKategoriForm = null;

        function openEditKategori(id, nama) {
            const form = document.getElementById('form-edit-kategori-form');
            form.action = `/admin/kategori/${id}`;
            document.getElementById('edit-nama-kategori').value = nama;
            openAppModal('form-edit-kategori');
        }

        function confirmDeleteKategori(actionUrl) {
            const modal = document.getElementById('confirm-delete-kategori');
            const form = modal.querySelector('form');
            form.action = actionUrl;
            openAppModal('confirm-delete-kategori');
        }

        function confirmSubmitEditKategori(event) {
            event.preventDefault();
            pendingEditKategoriForm = event.target;
            openAppModal('confirm-save-kategori');
            return false;
        }

        function submitEditKategoriAfterConfirm() {
            if (!pendingEditKategoriForm) return;

            closeAppModal('confirm-save-kategori');
            pendingEditKategoriForm.submit();
            pendingEditKategoriForm = null;
        }
    </script>

    {{-- ── Footer ── --}}
    <x-slot:pageFooter>
        <x-admin-footer />
    </x-slot:pageFooter>
</x-layouts.admin>
