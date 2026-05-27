@props(['id', 'mode' => 'add', 'menu' => null, 'kategoris' => [], 'submitUrl', 'submitMethod' => 'POST'])

@php
    $isEdit = $mode === 'edit' && $menu;
    $title = $isEdit ? 'Edit Menu' : 'Tambah Menu';
    $jenisDefault = $isEdit ? $menu->jenis_menu : 'Makanan';
    $stockDefault = $isEdit ? $menu->stock : 1000;
    $isPedas = $isEdit && $menu->kategori_makanan === 'Pedas';
    $tipeMinumanDefault = $isEdit ? $menu->tipe_minuman : 'Keduanya';
    $needsMethodSpoof = in_array(strtoupper($submitMethod), ['PUT', 'PATCH']);
    $idKategoriDefault = $isEdit ? $menu->kategoris->pluck('id_kategori')->toArray() : [];
    $sugarLevelDefault = old('sugar_level_enabled', false);
    $specificOptionsDefault = old('specific_options_enabled', false);
@endphp

<div id="{{ $id }}" data-form-modal
    class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-2 sm:p-4 transition-all"
    onclick="if(event.target === this) closeAppModal('{{ $id }}')">

    <div
        class="kvt-modal-panel relative flex w-full max-w-[1120px] flex-col rounded-[9px] bg-white shadow-[2px_4px_4px_rgba(0,0,0,0.25)]">
        {{-- Sticky Header --}}
        <div class="relative flex-shrink-0 border-b border-gray-100 px-4 pb-3 pt-5 sm:px-6 sm:pt-6">
            <button type="button"
                class="absolute left-4 top-6 text-brand-dark transition-colors hover:text-brand-red sm:left-6 sm:top-7"
                onclick="closeAppModal('{{ $id }}')"
                aria-label="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button type="button"
                class="absolute right-4 top-5 text-brand-gray transition-colors hover:text-brand-black sm:right-6 sm:top-6"
                onclick="closeAppModal('{{ $id }}')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="pl-8 pr-8 text-[24px] font-bold leading-8 text-brand-black sm:pl-10 sm:text-[30px] sm:leading-10">{{ $title }}</h2>
        </div>

        <form id="{{ $id }}-form" method="POST" action="{{ $submitUrl }}" enctype="multipart/form-data"
              class="flex-1 flex flex-col min-h-0">
            @csrf
            @if ($needsMethodSpoof)
                @method($submitMethod)
            @endif

            <input type="hidden" name="jenis_menu" id="{{ $id }}-jenis-input" value="{{ $jenisDefault }}">
            <input type="hidden" name="tipe_minuman" id="{{ $id }}-tipe-minuman-input"
                value="{{ $tipeMinumanDefault }}">
            <input type="hidden" name="sugar_level_enabled" id="{{ $id }}-sugar-input" value="{{ $sugarLevelDefault ? '1' : '0' }}">
            <input type="hidden" name="specific_options_enabled" id="{{ $id }}-specific-input" value="{{ $specificOptionsDefault ? '1' : '0' }}">
            <div id="{{ $id }}-id-kategori-inputs"></div>

            {{-- Scrollable body --}}
            <div class="flex-1 overflow-y-auto px-4 py-5 sm:px-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8">
                {{-- ════════════════════════════════════ --}}
                {{-- KOLOM KIRI                            --}}
                {{-- ════════════════════════════════════ --}}
                <div class="space-y-4">
                    {{-- Jenis Menu --}}
                    <div>
                        <label class="block text-sm font-bold text-brand-dark mb-3">Jenis Menu</label>
                        <div class="flex gap-3">
                            <button type="button" data-jenis-toggle="Makanan" data-form-id="{{ $id }}"
                                class="jenis-pill px-3 py-1.5 rounded-[9px] text-[14px] leading-5 tracking-[0.7px] transition-all {{ $jenisDefault === 'Makanan' ? 'bg-[#681f1f] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]' : 'bg-white text-[#460001] border border-[#460001]' }}">
                                Makanan
                            </button>
                            <button type="button" data-jenis-toggle="Minuman" data-form-id="{{ $id }}"
                                class="jenis-pill px-3 py-1.5 rounded-[9px] text-[14px] leading-5 tracking-[0.7px] transition-all {{ $jenisDefault === 'Minuman' ? 'bg-[#681f1f] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]' : 'bg-white text-[#460001] border border-[#460001]' }}">
                                Minuman
                            </button>
                        </div>
                    </div>

                    {{-- Foto Menu --}}
                    <div>
                        <label class="block text-sm font-bold text-brand-dark mb-3">Foto Menu</label>
                        <div id="{{ $id }}-dropzone"
                            class="relative border border-dashed border-gray-400 rounded-[10px] bg-[rgba(246,246,246,0.96)] p-3 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 transition-all min-h-[112px]">
                            <input type="file" name="gambar_menu" id="{{ $id }}-file-input" class="hidden"
                                accept="image/png,image/jpeg,image/webp">

                            {{-- Empty State --}}
                            <div id="{{ $id }}-dropzone-empty"
                                class="{{ $isEdit && $menu->gambar_menu ? 'hidden' : '' }} flex flex-col items-center gap-3 sm:flex-row sm:gap-4">
                                <div class="text-center sm:text-right">
                                    <p class="text-xs font-bold text-brand-dark mb-0.5">Support File</p>
                                    <p class="text-[10px] text-brand-gray">PNG, JPG, WEBP</p>
                                </div>
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <div class="text-center sm:text-left">
                                    <p class="text-xs font-bold text-brand-dark mb-0.5">Landscape Only</p>
                                    <p class="text-[10px] text-brand-gray">854px X 440px (16:9)</p>
                                </div>
                            </div>

                            {{-- Preview State --}}
                            @php
                                $imgType = $isEdit && $menu ? ($menu->jenis_menu === 'Makanan' ? 'food' : 'drink') : 'food';
                                $existingSrc = $isEdit && $menu && $menu->gambar_menu
                                    ? (str_starts_with($menu->gambar_menu, 'http')
                                        ? $menu->gambar_menu
                                        : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                    : '';
                                $existingFilename = $isEdit && $menu && $menu->gambar_menu ? $menu->gambar_menu : '';
                            @endphp
                            <div id="{{ $id }}-dropzone-preview"
                                class="{{ $isEdit && $menu?->gambar_menu ? '' : 'hidden' }} w-full relative">
                                <div class="flex items-center justify-center gap-3 bg-gray-300/60 rounded-lg py-2 px-3 max-w-[280px] mx-auto">
                                    <button type="button" id="{{ $id }}-remove-file"
                                        class="text-brand-dark hover:text-red-600 transition-all flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <button type="button"
                                            id="{{ $id }}-thumb-btn"
                                            data-lightbox-trigger="{{ $id }}"
                                            class="relative w-12 h-12 rounded-md overflow-hidden bg-white flex-shrink-0 hover:ring-2 hover:ring-[#681f1f] transition-all group">
                                        <img id="{{ $id }}-thumb-img" src="{{ $existingSrc }}"
                                             class="w-full h-full object-cover" alt="">
                                        <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </span>
                                    </button>

                                    <span class="text-sm font-medium text-brand-dark truncate" id="{{ $id }}-filename-label">
                                        {{ $existingFilename ?: 'IMG 123' }}
                                    </span>
                                </div>
                                <img id="{{ $id }}-img-preview" src="{{ $existingSrc }}" class="hidden">
                            </div>
                        </div>
                    </div>

                    {{-- Nama Menu --}}
                    <div>
                        <label for="{{ $id }}-nama" class="block text-sm font-bold text-brand-dark mb-3">Nama
                            Menu</label>
                        <input type="text" name="nama_menu" id="{{ $id }}-nama"
                            value="{{ $isEdit ? old('nama_menu', $menu->nama_menu) : old('nama_menu') }}" placeholder="Masukkan Nama Menu"
                            class="w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-sm focus:ring-2 focus:ring-[#681f1f] transition-all"
                            required>
                        @error('nama_menu')
                            <div class="flex items-center gap-[5px] mt-1.5">
                                <svg class="w-3.5 h-3.5 text-[#E52E2D] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-[#E52E2D] text-[10px] tracking-[0.5px]">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    {{-- Harga Menu --}}
                    <div>
                        <label for="{{ $id }}-harga" class="block text-sm font-bold text-brand-dark mb-3">Harga
                            Menu</label>
                        <input type="number" name="harga" id="{{ $id }}-harga"
                            value="{{ $isEdit ? old('harga', $menu->harga) : old('harga') }}"
                            placeholder="Masukkan Harga Menu (contoh: 20000)"
                            class="w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-sm focus:ring-2 focus:ring-[#681f1f] transition-all placeholder:italic"
                            required min="1">
                        @error('harga')
                            <div class="flex items-center gap-[5px] mt-1.5">
                                <svg class="w-3.5 h-3.5 text-[#E52E2D] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-[#E52E2D] text-[10px] tracking-[0.5px]">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    {{-- Stock Menu + Makanan Pedas? (side-by-side) --}}
                    <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2">
                        {{-- Stock Menu --}}
                        <div>
                            <div class="flex items-center gap-1.5 mb-3">
                                <label class="block text-sm font-bold text-brand-dark">Stock Menu</label>
                                <span class="cursor-help text-brand-gray"
                                    title="Jumlah stock menu yang tersedia. Set 0 untuk Tidak Tersedia.">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="flex h-8 w-full max-w-[172px] items-center bg-[rgba(70,0,1,0.25)] rounded-[9px]">
                                <button type="button" data-stock-step="-1" data-form-id="{{ $id }}"
                                    class="w-8 h-8 flex items-center justify-center text-[#460001] hover:bg-[#DFD4CF] rounded-l-[9px] transition-all text-[14px] font-bold">
                                    &minus;
                                </button>
                                <input type="number" name="stock" id="{{ $id }}-stock"
                                    value="{{ $stockDefault }}"
                                    class="flex-1 w-full text-center bg-transparent border-none text-sm font-medium text-brand-dark focus:ring-0"
                                    min="0" max="999999999">
                                <button type="button" data-stock-step="1" data-form-id="{{ $id }}"
                                    class="w-8 h-8 flex items-center justify-center text-[#460001] hover:bg-[#DFD4CF] rounded-r-[9px] transition-all text-[14px] font-bold">
                                    &#43;
                                </button>
                            </div>
                        </div>

                        {{-- Makanan Pedas? (only when jenis = Makanan) --}}
                        <div id="{{ $id }}-conditional-makanan"
                            class="{{ $jenisDefault === 'Makanan' ? '' : 'hidden' }}">
                            <label class="block text-sm font-bold text-brand-dark mb-3">Makanan Pedas?</label>
                            <label class="flex items-center gap-2 cursor-pointer h-10">
                                <input type="checkbox" name="is_pedas" value="1" {{ $isPedas ? 'checked' : '' }}
                                    class="hidden peer">
                                <div
                                    class="w-6 h-6 bg-[rgba(104,31,31,0.12)] rounded-full flex items-center justify-center peer-checked:bg-[#460001] transition-all">
                                    <svg class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-brand-dark">Ya</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════ --}}
                {{-- KOLOM KANAN                           --}}
                {{-- ════════════════════════════════════ --}}
                <div class="space-y-4">
                    {{-- Tipe Minuman (conditional — only when jenis = Minuman) --}}
                    <div id="{{ $id }}-conditional-minuman"
                        class="{{ $jenisDefault === 'Minuman' ? '' : 'hidden' }}">
                        <label class="block text-sm font-bold text-brand-dark mb-3">Tipe Minuman</label>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $tipeOptions = [
                                    'Keduanya' => [
                                        'label' => 'Panas/Dingin',
                                        'icons' => ['hot fill.svg', 'cold fill.svg'],
                                    ],
                                    'Panas' => [
                                        'label' => 'Panas',
                                        'icons' => ['hot fill.svg'],
                                    ],
                                    'Dingin' => [
                                        'label' => 'Dingin',
                                        'icons' => ['cold fill.svg'],
                                    ],
                                ];
                            @endphp
                            @foreach ($tipeOptions as $val => $cfg)
                                @php $isActive = $tipeMinumanDefault === $val; @endphp
                                <button type="button" data-tipe-toggle="{{ $val }}" data-form-id="{{ $id }}"
                                    class="tipe-pill px-3 py-1.5 rounded-[9px] text-[14px] leading-5 tracking-[0.7px] flex items-center gap-1.5 transition-all {{ $isActive ? 'bg-[#681f1f] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]' : 'bg-white text-[#460001] border border-[#460001]' }}">
                                    @foreach ($cfg['icons'] as $icon)
                                        <img src="{{ asset('images/icons/' . $icon) }}"
                                             class="w-3.5 h-3.5 transition-all"
                                             data-tipe-icon
                                             alt="">
                                    @endforeach
                                    {{ $cfg['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Kategori Menu (chip selector — always visible) --}}
                    <div>
                        <label class="block text-sm font-bold text-brand-dark mb-3">Kategori Menu</label>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($kategoris as $kat)
                                @php $isSelected = in_array($kat->id_kategori, $idKategoriDefault); @endphp
                                <button type="button" data-kategori-toggle="{{ $kat->id_kategori }}"
                                    data-form-id="{{ $id }}"
                                    data-selected="{{ $isSelected ? '1' : '0' }}"
                                    class="kategori-pill px-3 py-1.5 rounded-[9px] text-[14px] leading-5 tracking-[0.7px] transition-all {{ $isSelected ? 'bg-[#681f1f] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]' : 'bg-white text-[#460001] border border-[#460001]' }}">
                                    {{ $kat->nama_kategori }}
                                </button>
                            @empty
                                <p class="text-xs text-brand-gray italic">Belum ada kategori. Buat dulu di Kelola
                                    Kategori Menu.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="{{ $id }}-deskripsi"
                            class="block text-sm font-bold text-brand-dark mb-3">Deskripsi Menu</label>
                        <textarea name="deskripsi" id="{{ $id }}-deskripsi" rows="5" placeholder="Masukkan Deskripsi Menu"
                            class="w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-sm focus:ring-2 focus:ring-[#681f1f] transition-all resize-none"
                            required>{{ $isEdit ? $menu->deskripsi : '' }}</textarea>
                    </div>

                    {{-- Komposisi --}}
                    <div>
                        <label for="{{ $id }}-komposisi"
                            class="block text-sm font-bold text-brand-dark mb-3">Komposisi Menu</label>
                        <input type="text" name="komposisi" id="{{ $id }}-komposisi"
                            value="{{ $isEdit ? $menu->komposisi : '' }}" placeholder="Masukkan Komposisi Menu"
                            class="w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-sm focus:ring-2 focus:ring-[#681f1f] transition-all">
                    </div>

                    {{-- Sugar Level (only when jenis = Minuman) --}}
                    <div id="{{ $id }}-conditional-sugar"
                        class="{{ $jenisDefault === 'Minuman' ? '' : 'hidden' }}">
                        <div class="flex items-start gap-[5px]">
                            <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Sugar Level</p>
                            <button type="button"
                                class="relative h-5 w-[44px] rounded-full transition-colors {{ $sugarLevelDefault ? 'bg-state-green' : 'bg-brand-gray-light' }}"
                                data-switch-toggle
                                data-form-id="{{ $id }}"
                                data-target-input="{{ $id }}-sugar-input"
                                aria-pressed="{{ $sugarLevelDefault ? 'true' : 'false' }}">
                                <span class="absolute top-[2px] h-4 w-4 rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] transition-transform {{ $sugarLevelDefault ? 'translate-x-[25px]' : 'translate-x-[2px]' }}" data-switch-knob></span>
                            </button>
                        </div>
                        <p class="mt-0.5 text-[10px] leading-3 tracking-[0.5px] text-brand-gray">
                            Konsumen dapat memilih tingkat kemanisan (Normal/Less Sugar/No Sugar)
                        </p>
                    </div>

                    {{-- Specific options toggle --}}
                    <div>
                        <div class="flex items-start gap-[5px]">
                            <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Tambah Pilihan Spesifik</p>
                            <button type="button"
                                class="relative h-5 w-[44px] rounded-full transition-colors {{ $specificOptionsDefault ? 'bg-state-green' : 'bg-brand-gray-light' }}"
                                data-switch-toggle
                                data-form-id="{{ $id }}"
                                data-target-input="{{ $id }}-specific-input"
                                data-target-section="{{ $id }}-specific-section"
                                aria-pressed="{{ $specificOptionsDefault ? 'true' : 'false' }}">
                                <span class="absolute top-[2px] h-4 w-4 rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] transition-transform {{ $specificOptionsDefault ? 'translate-x-[25px]' : 'translate-x-[2px]' }}" data-switch-knob></span>
                            </button>
                        </div>
                        <p class="mt-0.5 text-[10px] leading-3 tracking-[0.5px] text-brand-gray">
                            Pilihan spesifik digunakan untuk menambah jenis kategori menu, add on topping, atau tambahan spesifik lainnya yang dapat dikustomisasi pelanggan
                        </p>
                    </div>
                </div>
            </div>

            <section id="{{ $id }}-specific-section" class="{{ $specificOptionsDefault ? '' : 'hidden' }} mt-7 border-t border-brand-gray-light pt-6">
                <h3 class="text-[24px] leading-8 font-bold tracking-[1.2px] text-brand-dark">Pilihan Spesifik Menu</h3>

                <div class="mt-4 flex flex-wrap items-end gap-6" data-specific-builder data-form-id="{{ $id }}">
                    <div class="flex flex-col gap-[5px]">
                        <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Jenis Pilihan</p>
                        <div class="flex flex-col overflow-hidden rounded-[9px] border border-brand-red text-[14px] leading-5 tracking-[0.7px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] sm:h-8 sm:flex-row">
                            <button type="button" class="px-2 py-1.5 bg-brand-dark text-white" data-specific-type="basic" data-form-id="{{ $id }}">Pilihan Tambahan</button>
                            <button type="button" class="border-l border-brand-red px-2 py-1.5 text-brand-red" data-specific-type="priced" data-form-id="{{ $id }}">Pilihan Tambahan + Harga</button>
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-[5px] sm:w-[145px]">
                        <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Jumlah Pilihan</p>
                        <div class="flex h-8 items-center rounded-[9px] bg-[rgba(70,0,1,0.25)]" data-specific-count data-form-id="{{ $id }}">
                            <button type="button" class="h-8 w-8 text-[14px] font-bold text-brand-dark" data-specific-count-step="-1" data-form-id="{{ $id }}">&minus;</button>
                            <span class="flex-1 text-center text-[14px] leading-5 tracking-[0.7px]" data-specific-count-value>2</span>
                            <button type="button" class="h-8 w-8 text-[14px] font-bold text-brand-dark" data-specific-count-step="1" data-form-id="{{ $id }}">&#43;</button>
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-[5px] sm:w-[145px]">
                        <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Opsional</p>
                        <button type="button"
                            class="relative h-5 w-[44px] rounded-full bg-state-green transition-colors"
                            data-specific-optional
                            data-form-id="{{ $id }}"
                            aria-pressed="true">
                            <span class="absolute top-[2px] h-4 w-4 translate-x-[25px] rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] transition-transform" data-switch-knob></span>
                        </button>
                    </div>
                </div>

                <div id="{{ $id }}-specific-groups" class="mt-4 flex flex-col gap-4" data-specific-groups data-next-index="0"></div>

                <button type="button"
                    class="mt-4 flex h-8 w-full items-center justify-center gap-2 rounded-[9px] bg-brand-red px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]"
                    data-specific-add-group
                    data-form-id="{{ $id }}">
                    <span class="font-bold">&#43;</span>
                    Tambah Jenis Pilihan
                </button>
            </section>

            </div>
            {{-- /Scrollable body --}}

            {{-- Sticky Footer --}}
            <div class="kvt-modal-actions flex flex-shrink-0 justify-end gap-[17px] rounded-b-[9px] border-t border-gray-100 bg-white px-4 py-4 sm:px-6">
                <button type="button" onclick="openAppModal('confirm-cancel-{{ $mode }}-{{ $id }}')"
                    class="bg-brand-gray-light text-brand-red px-3 py-1.5 rounded-[9px] text-[14px] leading-5 tracking-[0.7px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-[#C4C4C4] transition-colors">
                    Batal
                </button>
                <button type="button" onclick="openAppModal('confirm-{{ $mode }}-{{ $id }}')"
                    class="bg-brand-red text-white px-3 py-1.5 rounded-[9px] text-[14px] leading-5 tracking-[0.7px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] hover:bg-brand-dark transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Confirmation Modals --}}
<x-confirm-modal id="confirm-{{ $mode }}-{{ $id }}"
    title="{{ $isEdit ? 'Apakah anda yakin ingin menyimpan perubahan pada menu ini?' : 'Apakah anda yakin ingin menambah menu ini?' }}"
    subtitle="{{ $isEdit ? 'Perubahan data pada menu akan disimpan secara permanen' : 'Menu akan ditambahkan ke dalam sistem' }}"
    confirmLabel="Simpan" onConfirm="document.getElementById('{{ $id }}-form').submit()" />

<x-confirm-modal id="confirm-cancel-{{ $mode }}-{{ $id }}"
    title="{{ $isEdit ? 'Apakah anda yakin ingin membatalkan perubahan pada menu ini?' : 'Apakah anda yakin ingin membatalkan tambah menu ini?' }}"
    subtitle="{{ $isEdit ? 'Perubahan data pada menu akan dibatalkan' : 'Tambah menu akan dibatalkan' }}"
    confirmLabel="Ya, Batalkan" cancelLabel="Kembali"
    onConfirm="closeAppModal('confirm-cancel-{{ $mode }}-{{ $id }}'); closeAppModal('{{ $id }}')" />

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const PILL_ACTIVE = ['bg-[#681f1f]', 'text-white', 'shadow-[2px_4px_2px_rgba(0,0,0,0.25)]'];
            const PILL_INACTIVE = ['bg-white', 'text-[#460001]', 'border', 'border-[#460001]'];

            function setPillState(button, active) {
                button.classList.remove(...PILL_ACTIVE, ...PILL_INACTIVE);
                button.classList.add(...(active ? PILL_ACTIVE : PILL_INACTIVE));
            }

            function setSwitch(button, active) {
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
                button.classList.toggle('bg-state-green', active);
                button.classList.toggle('bg-brand-gray-light', !active);

                const knob = button.querySelector('[data-switch-knob]');
                if (knob) {
                    knob.classList.toggle('translate-x-[25px]', active);
                    knob.classList.toggle('translate-x-[2px]', !active);
                }

                const input = button.dataset.targetInput ? document.getElementById(button.dataset.targetInput) : null;
                if (input) input.value = active ? '1' : '0';

                const section = button.dataset.targetSection ? document.getElementById(button.dataset.targetSection) : null;
                if (section) section.classList.toggle('hidden', !active);
            }

            function setSpecificType(formId, type) {
                document.querySelectorAll(`[data-specific-type][data-form-id="${formId}"]`).forEach((button) => {
                    const active = button.dataset.specificType === type;
                    button.dataset.active = active ? '1' : '0';
                    button.classList.toggle('bg-brand-dark', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('text-brand-red', !active);
                });
            }

            function fieldError(text) {
                return `
                    <div class="mt-[3px] flex items-center gap-[5px] text-state-red">
                        <span class="flex h-[10px] w-[10px] items-center justify-center rounded-full border border-current text-[8px] leading-none">!</span>
                        <span class="text-[10px] leading-3 tracking-[0.5px]">${text}</span>
                    </div>
                `;
            }

            function textInput(name, placeholder) {
                return `
                    <input type="text" name="${name}" placeholder="${placeholder}"
                        class="w-full rounded-[9px] border-none bg-[rgba(104,31,31,0.12)] p-[10px] text-[14px] leading-5 tracking-[0.7px] focus:ring-2 focus:ring-brand-red/40">
                `;
            }

            function priceInput(name, placeholder) {
                return `
                    <input type="number" min="0" name="${name}" placeholder="${placeholder}"
                        class="w-full rounded-[9px] border-none bg-[rgba(104,31,31,0.12)] p-[10px] text-[14px] leading-5 tracking-[0.7px] focus:ring-2 focus:ring-brand-red/40">
                `;
            }

            function specificGroupTemplate(formId, index, type, count, optional) {
                const priced = type === 'priced';
                let rows = '';

                for (let i = 1; i <= count; i += 1) {
                    if (priced) {
                        rows += `
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">Nama Pilihan Tambahan${i}</label>
                                    <div class="mt-[5px]">
                                        ${textInput(`pilihan_spesifik[${index}][opsi][${i}][nama]`, `Masukkan Nama Pilihan Tambahan (cth: ${i === 1 ? '+1 Shot Espresso' : '+2 Shots Espresso'})`)}
                                        ${fieldError('Nama Pilihan Wajib Di isi')}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">Harga Pilihan Tambahan${i}</label>
                                    <div class="mt-[5px]">
                                        ${priceInput(`pilihan_spesifik[${index}][opsi][${i}][harga]`, 'Masukkan Harga Pilihan Tambahan 1 (cth: 2000)')}
                                        ${fieldError('Harga Pilihan Wajib Di isi')}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        rows += `
                            <div>
                                <label class="block text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">Nama Pilihan Tambahan${i}</label>
                                <div class="mt-[5px]">
                                    ${textInput(`pilihan_spesifik[${index}][opsi][${i}][nama]`, `Masukkan Nama Pilihan Tambahan (cth: ${i === 1 ? 'Matang' : 'Setengah Matang'})`)}
                                    ${fieldError('Nama Pilihan Wajib Di isi')}
                                </div>
                            </div>
                        `;
                    }
                }

                return `
                    <div class="flex flex-col gap-[10px]" data-specific-group>
                        <input type="hidden" name="pilihan_spesifik[${index}][jenis]" value="${type}">
                        <input type="hidden" name="pilihan_spesifik[${index}][jumlah]" value="${count}">
                        <input type="hidden" name="pilihan_spesifik[${index}][opsional]" value="${optional ? 1 : 0}">
                        <div class="flex flex-wrap items-end gap-6">
                            <div class="flex flex-col gap-[5px]">
                                <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Jenis Pilihan</p>
                                <div class="flex flex-col overflow-hidden rounded-[9px] border border-brand-red text-[14px] leading-5 tracking-[0.7px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] sm:h-8 sm:flex-row">
                                    <span class="px-2 py-1.5 ${priced ? 'text-brand-red' : 'bg-brand-dark text-white'}">Pilihan Tambahan</span>
                                    <span class="border-l border-brand-red px-2 py-1.5 ${priced ? 'bg-brand-dark text-white' : 'text-brand-red'}">Pilihan Tambahan + Harga</span>
                                </div>
                            </div>
                            <div class="flex w-full flex-col gap-[5px] sm:w-[145px]">
                                <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Jumlah Pilihan</p>
                                <div class="flex h-8 items-center rounded-[9px] bg-[rgba(70,0,1,0.25)]">
                                    <span class="h-8 w-8 text-center text-[14px] font-bold leading-8 text-brand-dark">&minus;</span>
                                    <span class="flex-1 text-center text-[14px] leading-5 tracking-[0.7px]">${count}</span>
                                    <span class="h-8 w-8 text-center text-[14px] font-bold leading-8 text-brand-dark">&#43;</span>
                                </div>
                            </div>
                            <div class="flex w-full flex-col gap-[5px] sm:w-[145px]">
                                <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Opsional</p>
                                <span class="relative h-5 w-[44px] rounded-full ${optional ? 'bg-state-green' : 'bg-brand-gray-light'}">
                                    <span class="absolute top-[2px] h-4 w-4 rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] ${optional ? 'translate-x-[25px]' : 'translate-x-[2px]'}"></span>
                                </span>
                            </div>
                        </div>
                        <div class="rounded-[9px] border border-brand-gray-light p-[18px]">
                            <div>
                                <label class="block text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">Judul Pilihan Tambahan</label>
                                <div class="mt-[5px]">
                                    ${textInput(`pilihan_spesifik[${index}][judul]`, `Masukkan Nama Pilihan Tambahan (cth: ${priced ? 'Extra Espresso' : 'Tingkat Kematangan Telur'})`)}
                                    ${fieldError('Judul Pilihan Wajib Di isi')}
                                </div>
                            </div>
                            <div class="mt-[21px] flex flex-col gap-[21px]">${rows}</div>
                        </div>
                    </div>
                `;
            }
            // ── Populate hidden kategori inputs on mount (from server-rendered selected pills) ──
            document.querySelectorAll('[data-form-modal]').forEach(modal => {
                const formId = modal.id;
                const container = document.getElementById(`${formId}-id-kategori-inputs`);
                if (!container) return;
                document.querySelectorAll(`[data-kategori-toggle][data-form-id="${formId}"][data-selected="1"]`)
                    .forEach(b => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'id_kategori[]';
                        input.value = b.dataset.kategoriToggle;
                        container.appendChild(input);
                    });
            });

            // ── Delegated click handler ──
            document.body.addEventListener('click', function (e) {
                // Jenis Menu pills (single-select)
                if (e.target.closest('[data-jenis-toggle]')) {
                    const btn = e.target.closest('[data-jenis-toggle]');
                    const jenis = btn.dataset.jenisToggle;
                    const formId = btn.dataset.formId;

                    document.getElementById(`${formId}-jenis-input`).value = jenis;

                    document.querySelectorAll(`[data-jenis-toggle][data-form-id="${formId}"]`).forEach(b => {
                        setPillState(b, false);
                    });
                    setPillState(btn, true);

                    document.getElementById(`${formId}-conditional-makanan`).classList.toggle('hidden', jenis !== 'Makanan');
                    document.getElementById(`${formId}-conditional-minuman`).classList.toggle('hidden', jenis !== 'Minuman');
                    const sugarBlock = document.getElementById(`${formId}-conditional-sugar`);
                    if (sugarBlock) sugarBlock.classList.toggle('hidden', jenis !== 'Minuman');
                }

                // Tipe Minuman pills (single-select)
                if (e.target.closest('[data-tipe-toggle]')) {
                    const btn = e.target.closest('[data-tipe-toggle]');
                    const tipe = btn.dataset.tipeToggle;
                    const formId = btn.dataset.formId;

                    document.getElementById(`${formId}-tipe-minuman-input`).value = tipe;

                    document.querySelectorAll(`[data-tipe-toggle][data-form-id="${formId}"]`).forEach(b => {
                        setPillState(b, false);
                    });
                    setPillState(btn, true);
                }

                // Kategori Menu pills (multi-select)
                if (e.target.closest('[data-kategori-toggle]')) {
                    const btn = e.target.closest('[data-kategori-toggle]');
                    const formId = btn.dataset.formId;
                    const isSelected = btn.dataset.selected === '1';

                    btn.dataset.selected = isSelected ? '0' : '1';
                    setPillState(btn, !isSelected);

                    const container = document.getElementById(`${formId}-id-kategori-inputs`);
                    container.innerHTML = '';
                    document.querySelectorAll(`[data-kategori-toggle][data-form-id="${formId}"][data-selected="1"]`)
                        .forEach(b => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'id_kategori[]';
                            input.value = b.dataset.kategoriToggle;
                            container.appendChild(input);
                        });
                }

                // Stock stepper
                if (e.target.closest('[data-stock-step]')) {
                    const btn = e.target.closest('[data-stock-step]');
                    const step = parseInt(btn.dataset.stockStep);
                    const formId = btn.dataset.formId;
                    const input = document.getElementById(`${formId}-stock`);
                    input.value = Math.max(0, parseInt(input.value || 0) + step);
                }

                if (e.target.closest('[data-switch-toggle]')) {
                    const btn = e.target.closest('[data-switch-toggle]');
                    setSwitch(btn, btn.getAttribute('aria-pressed') !== 'true');
                }

                if (e.target.closest('[data-specific-optional]')) {
                    const btn = e.target.closest('[data-specific-optional]');
                    setSwitch(btn, btn.getAttribute('aria-pressed') !== 'true');
                }

                if (e.target.closest('[data-specific-type]')) {
                    const btn = e.target.closest('[data-specific-type]');
                    setSpecificType(btn.dataset.formId, btn.dataset.specificType);
                }

                if (e.target.closest('[data-specific-count-step]')) {
                    const btn = e.target.closest('[data-specific-count-step]');
                    const formId = btn.dataset.formId;
                    const valueEl = document.querySelector(`[data-specific-count][data-form-id="${formId}"] [data-specific-count-value]`);
                    if (!valueEl) return;
                    const next = Math.max(1, Math.min(6, parseInt(valueEl.textContent || '2') + parseInt(btn.dataset.specificCountStep)));
                    valueEl.textContent = next;
                }

                if (e.target.closest('[data-specific-add-group]')) {
                    const btn = e.target.closest('[data-specific-add-group]');
                    const formId = btn.dataset.formId;
                    const groups = document.getElementById(`${formId}-specific-groups`);
                    const countEl = document.querySelector(`[data-specific-count][data-form-id="${formId}"] [data-specific-count-value]`);
                    const activeType = document.querySelector(`[data-specific-type][data-form-id="${formId}"][data-active="1"]`);
                    const optionalBtn = document.querySelector(`[data-specific-optional][data-form-id="${formId}"]`);
                    if (!groups || !countEl || !activeType) return;
                    const index = parseInt(groups.dataset.nextIndex || '0');
                    const count = parseInt(countEl.textContent || '2');
                    const optional = optionalBtn ? optionalBtn.getAttribute('aria-pressed') === 'true' : true;
                    groups.insertAdjacentHTML('beforeend', specificGroupTemplate(formId, index, activeType.dataset.specificType, count, optional));
                    groups.dataset.nextIndex = String(index + 1);
                }

                // Lightbox trigger (thumbnail click)
                if (e.target.closest('[data-lightbox-trigger]')) {
                    const trigger = e.target.closest('[data-lightbox-trigger]');
                    e.preventDefault();
                    e.stopPropagation();
                    const formId = trigger.dataset.lightboxTrigger;
                    const img = document.getElementById(`${formId}-thumb-img`);
                    if (img && img.src && !img.src.endsWith('#') && img.src !== window.location.href) {
                        window.openImageLightbox(img.src);
                    }
                }
            });

            // ── Dropzone & File Preview ──
            document.querySelectorAll('[data-form-modal]').forEach(modal => {
                const formId = modal.id;
                const dropzone = document.getElementById(`${formId}-dropzone`);
                const fileInput = document.getElementById(`${formId}-file-input`);
                const emptyState = document.getElementById(`${formId}-dropzone-empty`);
                const previewState = document.getElementById(`${formId}-dropzone-preview`);
                const imgPreview = document.getElementById(`${formId}-img-preview`);
                const removeBtn = document.getElementById(`${formId}-remove-file`);

                if (!dropzone) return;

                dropzone.addEventListener('click', (e) => {
                    if (!e.target.closest('[data-lightbox-trigger]') && !e.target.closest(`#${formId}-remove-file`)) {
                        fileInput.click();
                    }
                });

                fileInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (ev) {
                            if (imgPreview) imgPreview.src = ev.target.result;
                            const thumb = document.getElementById(`${formId}-thumb-img`);
                            const label = document.getElementById(`${formId}-filename-label`);
                            if (thumb) thumb.src = ev.target.result;
                            if (label) label.textContent = file.name;
                            emptyState.classList.add('hidden');
                            previewState.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });

                if (removeBtn) {
                    removeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        fileInput.value = '';
                        if (imgPreview) imgPreview.src = '';
                        const thumb = document.getElementById(`${formId}-thumb-img`);
                        if (thumb) thumb.src = '';
                        emptyState.classList.remove('hidden');
                        previewState.classList.add('hidden');
                    });
                }

                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-[#681f1f]', 'bg-[#EBE4E0]/20');
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('border-[#681f1f]', 'bg-[#EBE4E0]/20');
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-[#681f1f]', 'bg-[#EBE4E0]/20');
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        fileInput.files = e.dataTransfer.files;
                        const reader = new FileReader();
                        reader.onload = function (ev) {
                            if (imgPreview) imgPreview.src = ev.target.result;
                            const thumb = document.getElementById(`${formId}-thumb-img`);
                            const label = document.getElementById(`${formId}-filename-label`);
                            if (thumb) thumb.src = ev.target.result;
                            if (label) label.textContent = file.name;
                            emptyState.classList.add('hidden');
                            previewState.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            document.querySelectorAll('[data-specific-builder]').forEach((builder) => {
                setSpecificType(builder.dataset.formId, 'basic');
            });
        });
    </script>
@endonce
