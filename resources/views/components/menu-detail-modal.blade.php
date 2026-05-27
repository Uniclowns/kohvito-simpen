@props(['id', 'menu', 'editUrl' => null, 'deleteUrl' => null])

@php
    $imgType = $menu->jenis_menu === 'Makanan' ? 'food' : 'drink';
    $imgSrc = str_starts_with($menu->gambar_menu, 'http')
        ? $menu->gambar_menu
        : asset("images/{$imgType}/{$menu->gambar_menu}");
    $isPedas = $menu->kategori_makanan === 'Pedas';
@endphp

<div id="{{ $id }}" data-confirm-modal
    class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all"
    onclick="if(event.target === this) closeAppModal('{{ $id }}')">

    <div
        class="kvt-modal-panel relative w-full max-w-[840px] overflow-y-auto rounded-2xl bg-white p-4 shadow-[0_8px_24px_rgba(0,0,0,0.15)] sm:p-8">
        {{-- Close Icon (X) --}}
        <button type="button" class="absolute right-4 top-4 text-brand-gray transition-colors hover:text-brand-black sm:right-8 sm:top-8"
            onclick="closeAppModal('{{ $id }}')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Header --}}
        <div class="mb-3">
            <h2 class="text-xl font-bold text-brand-black">Detail Menu</h2>
        </div>

        {{-- Category Badge --}}
        <div class="mb-6">
            <span
                class="inline-block border border-[#380000] text-[#380000] px-3 py-1 rounded-md text-xs font-medium uppercase tracking-wider">
                {{ $menu->jenis_menu }}
            </span>
        </div>

        {{-- 2-Column Body --}}
        <div class="mb-8 grid gap-5 md:grid-cols-2 md:gap-8">
            {{-- Left: Image --}}
            <div class="flex items-center justify-center bg-gray-50/50 rounded-xl p-4">
                <img src="{{ $imgSrc }}" alt="{{ $menu->nama_menu }}"
                    class="w-full h-auto max-h-[300px] object-contain drop-shadow-[0_12px_12px_rgba(0,0,0,0.2)]">
            </div>

            {{-- Right: Info --}}
            <div class="flex flex-col">
                <h3 class="text-2xl font-bold text-brand-black mb-1 leading-tight">{{ $menu->nama_menu }}</h3>
                <p class="text-xl font-bold text-brand-gray mb-4">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>

                <div class="space-y-4 mb-6 flex-1">
                    {{-- Komposisi --}}
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-[#380000] mb-1">Komposisi:</p>
                        <p class="text-sm text-[#380000] italic leading-relaxed">
                            {{ filled($menu->komposisi) ? $menu->komposisi : '-' }}
                        </p>
                    </div>

                    {{-- Deskripsi rasa --}}
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-brand-gray mb-1">Deskripsi Rasa:
                        </p>
                        <p class="text-sm text-brand-gray leading-relaxed">
                            {{ filled($menu->deskripsi) ? $menu->deskripsi : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Badges Row --}}
                <div class="flex items-center gap-3 mb-2">
                    @if ($isPedas)
                        <span
                            class="inline-flex items-center gap-1.5 border border-[#380000] text-[#380000] px-3 py-1 rounded-md text-xs font-bold">
                            <img src="{{ asset('images/icons/Chili No Fill.svg') }}" class="w-3.5 h-3.5"
                                alt="Pedas">
                            Pedas
                        </span>
                    @endif
                </div>

                {{-- Stock Indicator --}}
                <p class="text-sm text-[#E03131] font-bold">999+ Stock</p>
            </div>
        </div>

        {{-- Footer Buttons --}}
        <div class="kvt-modal-actions mt-auto flex flex-wrap justify-end gap-3 border-t border-gray-100 pt-6">
            <button type="button"
                class="bg-[#EBE4E0] text-[#380000] px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-[#DFD4CF] transition-colors"
                onclick="closeAppModal('{{ $id }}')">
                Kembali
            </button>
            <button type="button"
                class="bg-[#380000] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-[#2A0000] transition-colors"
                onclick="closeAppModal('{{ $id }}'); setTimeout(() => openAppModal('form-edit-menu-{{ $menu->id_menu }}'), 50);">
                Edit
            </button>
            <button type="button"
                class="bg-[#E03131] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-[#C92A2A] transition-colors"
                onclick="closeAppModal('{{ $id }}'); setTimeout(() => confirmDelete('{{ route('admin.menu.destroy', $menu->id_menu) }}'), 50);">
                Hapus Menu
            </button>
        </div>
    </div>
</div>
