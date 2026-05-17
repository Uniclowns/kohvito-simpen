<x-layouts.admin title="Kelola Menu" page-title="Kelola Menu">
    <x-slot:headerEnd>
        <div class="flex gap-2">
            <button
                onclick="openConfirmModal('form-add-menu')"
                class="bg-brand-red hover:bg-brand-dark text-white p-2 rounded-md text-[14px] transition-colors flex items-center gap-1 font-medium shadow-sm">
                <img src="{{ asset('images/icons/plus.svg') }}" class="w-3 h-3 invert"
                    style="filter: brightness(0) invert(1)" alt="Tambah">
                Tambah Menu
            </button>
        </div>
    </x-slot:headerEnd>

    <!-- Search Bar -->
    <form id="search-form" method="GET" action="{{ route('admin.menu.index') }}" class="mb-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <img src="{{ asset('images/icons/search.svg') }}" class="w-4 h-4 opacity-50" alt="Search">
            </div>
            <input id="search-input" type="text" name="search" value="{{ $search }}" placeholder="Cari Menu"
                autocomplete="off"
                class="w-full bg-[#EBE4E0] border-none rounded-md pl-9 pr-4 py-4 text-KG text-brand-black placeholder-brand-gray-dark focus:outline-none focus:ring-1 focus:ring-brand-red shadow-inner">
        </div>
    </form>

    <!-- Grid Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 2xl:grid-cols-6 gap-6 pb-4 justify-items-center">
        @forelse ($menus as $menu)
            @php
                $badgeIcon = null;
                if ($menu->jenis_menu === 'Makanan') {
                    if ($menu->kategori_makanan === 'Pedas') {
                        $badgeIcon = 'ket-pedas.svg';
                    }
                } elseif ($menu->jenis_menu === 'Minuman') {
                    if ($menu->tipe_minuman === 'Dingin') {
                        $badgeIcon = 'ket-dingin.svg';
                    } elseif ($menu->tipe_minuman === 'Panas') {
                        $badgeIcon = 'ket-panas.svg';
                    } elseif ($menu->tipe_minuman === 'Keduanya') {
                        $badgeIcon = 'ket-keduanya.svg';
                    }
                }
            @endphp
            <div
                onclick="openConfirmModal('detail-menu-{{ $menu->id_menu }}')"
                class="bg-white w-full max-w-[220px] rounded-xl shadow-[0_8px_24px_rgba(0,0,0,0.08)] overflow-hidden flex flex-col relative border border-gray-100/50 p-4 pt-0 cursor-pointer hover:shadow-lg transition-all">
                {{-- Badge pojok kanan atas --}}
                @if ($badgeIcon)
                    <div class="absolute top-1 right-1 flex items-center justify-center z-10">
                        <img src="{{ asset('images/icons/' . $badgeIcon) }}" class="w-14 translate-x-1 -translate-y-1"
                            alt="">
                    </div>
                @endif

                {{-- Gambar (lewat compressor on-the-fly) --}}
                <div class="relative w-full aspect-square flex items-center justify-center mt-4 mb-2 z-0">
                    @if ($menu->gambar_menu)
                        @php
                            $imgType = $menu->jenis_menu === 'Makanan' ? 'food' : 'drink';
                            $imgSrc = str_starts_with($menu->gambar_menu, 'http')
                                ? $menu->gambar_menu
                                : asset("images/{$imgType}/{$menu->gambar_menu}");
                        @endphp
                        <img src="{{ $imgSrc }}" loading="lazy" decoding="async"
                            class="w-45 h-45 object-contain drop-shadow-[0_12px_12px_rgba(0,0,0,0.2)]"
                            alt="{{ $menu->nama_menu }}">
                    @else
                        <div class="w-45 h-45 bg-[#EBE4E0] rounded-xl flex items-center justify-center">
                            <img src="{{ asset('images/icons/Food.svg') }}" class="w-10 h-10 opacity-25"
                                alt="">
                        </div>
                    @endif
                </div>

                {{-- Konten --}}
                <div class="flex-1 flex flex-col px-1 pb-1">
                    <h3
                        class="font-bold text-black text-[20px] leading-[1.2] tracking-wide line-clamp-2 min-h-[48px]">
                        {{ $menu->nama_menu }}
                    </h3>
                    <h3 class="mt-2 font-bold text-black text-[20px] leading-[1.2] tracking-wide">
                        Rp{{ number_format($menu->harga, 0, ',', '.') }}
                    </h3>
                    <p class="text-[12px] text-[#7A5252] leading-[1.4] mt-3 line-clamp-2 min-h-[34px]">
                        {{ $menu->komposisi ?? '-' }}
                    </p>

                    <div class="mt-auto pt-4 flex gap-2">
                        <button
                            onclick="event.stopPropagation(); openConfirmModal('form-edit-menu-{{ $menu->id_menu }}')"
                            class="flex-1 bg-[#D4C4C0] text-black py-2 rounded-xl text-[13px] font-medium hover:bg-[#C2B2AE] transition-colors">Edit</button>

                        <button type="button"
                            onclick="event.stopPropagation(); confirmDelete('{{ route('admin.menu.destroy', $menu->id_menu) }}')"
                            class="w-10 bg-[#E03131] flex items-center justify-center rounded-xl hover:bg-[#C92A2A] transition-colors shrink-0">
                            <img src="{{ asset('images/icons/trash min.svg') }}" class="w-4 h-4" alt="Delete">
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal Detail Menu --}}
            <x-menu-detail-modal
                id="detail-menu-{{ $menu->id_menu }}"
                :menu="$menu"
            />

            {{-- Modal Edit Menu --}}
            <x-menu-form-modal
                id="form-edit-menu-{{ $menu->id_menu }}"
                mode="edit"
                :menu="$menu"
                :kategoris="$kategoris"
                :submitUrl="route('admin.menu.update', $menu->id_menu)"
                submitMethod="PUT"
            />
        @empty
            <div class="col-span-full text-center py-12 text-brand-gray text-sm">
                Tidak ada menu ditemukan.
            </div>
        @endforelse
    </div>

    @if ($menus->hasPages())
        <div class="mt-8 mb-6">
            {{ $menus->links('vendor.pagination.kohvito') }}
        </div>
    @endif

    {{-- Modal Tambah Menu --}}
    <x-menu-form-modal
        id="form-add-menu"
        mode="add"
        :kategoris="$kategoris"
        :submitUrl="route('admin.menu.store')"
        submitMethod="POST"
    />

    {{-- Modal #5: Confirm Delete --}}
    <x-confirm-modal
        id="confirm-delete"
        title="Apakah anda yakin ingin menghapus menu Ini?"
        subtitle="Menu yang dihapus tidak dapat dikembalikan"
        confirmLabel="Ya, Hapus"
        variant="danger"
        action="#"
        method="DELETE"
    />

    {{-- Popup: Success setelah action menu (add / edit / delete) --}}
    @php
        $menuActionHeadings = [
            'add'    => 'Berhasil Menambah Menu',
            'edit'   => 'Berhasil Mengedit Menu',
            'delete' => 'Berhasil Menghapus Menu',
        ];
        $menuErrorHeadings = [
            'add'    => 'Gagal Menambah Menu',
            'edit'   => 'Gagal Mengedit Menu',
            'delete' => 'Gagal Menghapus Menu',
        ];
        $menuActionKey = session('menu_action_success');
        $menuErrorKey  = session('menu_action_error');
    @endphp
    @if ($menuActionKey && isset($menuActionHeadings[$menuActionKey]))
        <x-popup-success id="popup-menu-action" :heading="$menuActionHeadings[$menuActionKey]" />
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => window.openConfirmModal && window.openConfirmModal('popup-menu-action'), 120);
            });
        </script>
    @endif
    @if ($menuErrorKey && isset($menuErrorHeadings[$menuErrorKey]))
        <x-popup-success
            id="popup-menu-error"
            :heading="$menuErrorHeadings[$menuErrorKey]"
            description="Terjadi gangguan pada sistem. Silahkan coba beberapa saat lagi."
            illustration="error.svg" />
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => window.openConfirmModal && window.openConfirmModal('popup-menu-error'), 120);
            });
        </script>
    @endif

    <script>
        function confirmDelete(actionUrl) {
            const modal = document.getElementById('confirm-delete');
            const form = modal.querySelector('form');
            form.action = actionUrl;
            openConfirmModal('confirm-delete');
        }

        // Live search — auto-submit saat user mengetik (debounce 350ms)
        (function() {
            const input = document.getElementById('search-input');
            const form = document.getElementById('search-form');
            let timer;
            if (input) {
                input.addEventListener('input', function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        form.submit();
                    }, 350);
                });
            }
        })();
    </script>

    {{-- ── Footer ── --}}
    <x-slot:pageFooter>
        <x-admin-footer />
    </x-slot:pageFooter>

</x-layouts.admin>
