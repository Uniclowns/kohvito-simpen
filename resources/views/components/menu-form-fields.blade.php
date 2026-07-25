@props(['id', 'mode' => 'add', 'menu' => null, 'kategoris' => []])

@php
    $isEdit = $mode === 'edit' && $menu;
    $jenisDefault = $isEdit ? $menu->jenis_menu : 'Makanan';
    $stockDefault = $isEdit ? $menu->stock : 1000;
    $isPedas = $isEdit && $menu->kategori_makanan === 'Pedas';
    $tipeMinumanDefault = $isEdit ? $menu->tipe_minuman : 'Keduanya';
    $idKategoriDefault = $isEdit ? $menu->kategoris->pluck('id_kategori')->toArray() : [];
    // old() is keyed globally by field name, so without this guard a failed submission for
    // one form would leak its toggle state into every other form on the page.
    $isThisFormOld = old('_open_modal') === $id;
    $sugarLevelDefault = $isThisFormOld ? old('sugar_level_enabled', false) : false;
    $specificOptionsDefault = $isThisFormOld ? old('specific_options_enabled', false) : false;
@endphp

            <input type="hidden" name="_open_modal" value="{{ $id }}">
            <input type="hidden" name="jenis_menu" id="{{ $id }}-jenis-input" value="{{ $jenisDefault }}">
            <input type="hidden" name="tipe_minuman" id="{{ $id }}-tipe-minuman-input"
                value="{{ $tipeMinumanDefault }}">
            <input type="hidden" name="sugar_level_enabled" id="{{ $id }}-sugar-input" value="{{ $sugarLevelDefault ? '1' : '0' }}">
            <input type="hidden" name="specific_options_enabled" id="{{ $id }}-specific-input" value="{{ $specificOptionsDefault ? '1' : '0' }}">
            <div id="{{ $id }}-id-kategori-inputs"></div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8">
                {{-- ════════════════════════════════════ --}}
                {{-- KOLOM KIRI                            --}}
                {{-- ════════════════════════════════════ --}}
                <div class="space-y-3.5">
                    {{-- Jenis Menu --}}
                    <div>
                        <label class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Jenis Menu</label>
                        <div class="flex gap-[10px]">
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
                        <label class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Foto Menu</label>
                        @php
                            $imgType = $isEdit && $menu ? ($menu->jenis_menu === 'Makanan' ? 'food' : 'drink') : 'food';
                            $existingSrc = $isEdit && $menu && $menu->gambar_menu
                                ? (str_starts_with($menu->gambar_menu, 'http')
                                    ? $menu->gambar_menu
                                    : asset("images/{$imgType}/{$menu->gambar_menu}"))
                                : '';
                            $existingFilename = $isEdit && $menu && $menu->gambar_menu ? $menu->gambar_menu : '';
                        @endphp
                        <div id="{{ $id }}-dropzone"
                            class="relative h-[139px] cursor-pointer rounded-[10px] border border-brand-gray bg-[rgba(246,246,246,0.96)] p-[15px] transition-all hover:bg-gray-50">
                            <input type="file" name="gambar_menu" id="{{ $id }}-file-input" class="hidden"
                                accept="image/png,image/jpeg,image/webp">

                            {{-- Dashed frame stays put; empty/preview swap inside it. --}}
                            <div class="flex h-full w-full items-center justify-center rounded-[10px] border border-dashed border-brand-gray px-[20px] py-[30px]">
                                {{-- Empty State --}}
                                <div id="{{ $id }}-dropzone-empty"
                                    class="{{ $isEdit && $menu->gambar_menu ? 'hidden' : '' }} flex flex-col items-center justify-center gap-3 text-brand-gray sm:flex-row sm:gap-[12px]">
                                    <div class="text-center sm:text-right">
                                        <p class="text-[14px] font-bold leading-[1.5] tracking-[-0.266px]">Support File</p>
                                        <p class="text-[11px] leading-[1.5] tracking-[-0.209px]">PNG, JPG, WEBP</p>
                                    </div>
                                    <svg class="h-[49px] w-[49px] shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <div class="text-center sm:text-left">
                                        <p class="text-[14px] font-bold leading-[1.5] tracking-[-0.266px]">Landscape Only</p>
                                        <p class="text-[11px] leading-[1.5] tracking-[-0.209px]">854px X 440px (16:9)</p>
                                    </div>
                                </div>

                                {{-- Preview State: Figma's gray file chip. Keeps the thumbnail as the
                                     lightbox trigger, which the design's flat chip has no slot for. --}}
                                <div id="{{ $id }}-dropzone-preview"
                                    class="{{ $isEdit && $menu?->gambar_menu ? '' : 'hidden' }} mr-auto flex items-center gap-[8px] rounded-[8px] bg-brand-gray p-[8px] text-[rgba(246,246,246,0.96)]">
                                    <button type="button" id="{{ $id }}-remove-file"
                                        class="shrink-0 transition-all hover:text-white">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <button type="button"
                                            id="{{ $id }}-thumb-btn"
                                            data-lightbox-trigger="{{ $id }}"
                                            class="group relative h-[22px] w-[22px] shrink-0 overflow-hidden rounded-[4px] bg-white transition-all hover:ring-2 hover:ring-white">
                                        <img id="{{ $id }}-thumb-img" src="{{ $existingSrc }}"
                                             class="h-full w-full object-cover" alt="">
                                        <span class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 transition-opacity group-hover:opacity-100">
                                            <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </span>
                                    </button>

                                    <span class="max-w-[180px] truncate text-center text-[14px] leading-5 tracking-[0.7px]" id="{{ $id }}-filename-label">
                                        {{ $existingFilename ?: 'IMG 123' }}
                                    </span>
                                    <img id="{{ $id }}-img-preview" src="{{ $existingSrc }}" class="hidden">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nama Menu --}}
                    <div>
                        <label for="{{ $id }}-nama" class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Nama
                            Menu</label>
                        <input type="text" name="nama_menu" id="{{ $id }}-nama"
                            value="{{ $isEdit ? old('nama_menu', $menu->nama_menu) : old('nama_menu') }}" placeholder="Masukkan Nama Menu"
                            class="w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-[14px] leading-5 tracking-[0.7px] placeholder:text-brand-gray focus:ring-2 focus:ring-[#681f1f] transition-all"
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
                        <label for="{{ $id }}-harga" class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Harga
                            Menu</label>
                        <input type="number" name="harga" id="{{ $id }}-harga"
                            value="{{ $isEdit ? old('harga', $menu->harga) : old('harga') }}"
                            placeholder="Masukkan Harga Menu (contoh: 20000)"
                            class="w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-[14px] leading-5 tracking-[0.7px] placeholder:text-brand-gray focus:ring-2 focus:ring-[#681f1f] transition-all"
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
                    <div class="flex flex-col items-start gap-[14px] sm:flex-row">
                        {{-- Stock Menu --}}
                        <div class="w-full sm:w-[215px]">
                            <div class="flex items-center gap-[5px] mb-[5px]">
                                <label class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark">Stock Menu</label>
                                <span class="cursor-help text-brand-gray"
                                    title="Jumlah stock menu yang tersedia. Set 0 untuk Tidak Tersedia.">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="flex h-8 w-full items-center bg-[rgba(70,0,1,0.25)] rounded-[9px]">
                                <button type="button" data-stock-step="-1" data-form-id="{{ $id }}"
                                    class="w-8 h-8 flex items-center justify-center text-[#460001] hover:bg-[#DFD4CF] rounded-l-[9px] transition-all text-[14px] font-bold">
                                    &minus;
                                </button>
                                <input type="number" name="stock" id="{{ $id }}-stock"
                                    value="{{ $stockDefault }}"
                                    class="flex-1 w-full text-center bg-transparent border-none text-[14px] leading-5 tracking-[0.7px] text-brand-black focus:ring-0"
                                    min="0" max="999999999">
                                <button type="button" data-stock-step="1" data-form-id="{{ $id }}"
                                    class="w-8 h-8 flex items-center justify-center text-[#460001] hover:bg-[#DFD4CF] rounded-r-[9px] transition-all text-[14px] font-bold">
                                    &#43;
                                </button>
                            </div>
                        </div>

                        {{-- Makanan Pedas? (only when jenis = Makanan) --}}
                        <div id="{{ $id }}-conditional-makanan"
                            class="{{ $jenisDefault === 'Makanan' ? '' : 'hidden' }} w-full sm:w-[215px]">
                            <label class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Makanan Pedas?</label>
                            <label class="flex items-center gap-[7px] cursor-pointer h-8">
                                <input type="checkbox" name="is_pedas" value="1" {{ $isPedas ? 'checked' : '' }}
                                    class="hidden peer">
                                <div
                                    class="w-6 h-6 bg-[rgba(104,31,31,0.12)] rounded-full flex items-center justify-center peer-checked:bg-[#460001] peer-checked:[&>svg]:opacity-100 transition-all">
                                    <svg class="w-3.5 h-3.5 text-white opacity-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-[14px] leading-5 tracking-[0.7px] text-brand-gray peer-checked:text-brand-dark">Ya</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════ --}}
                {{-- KOLOM KANAN                           --}}
                {{-- ════════════════════════════════════ --}}
                <div class="space-y-3.5">
                    {{-- Tipe Minuman (conditional — only when jenis = Minuman) --}}
                    <div id="{{ $id }}-conditional-minuman"
                        class="{{ $jenisDefault === 'Minuman' ? '' : 'hidden' }}">
                        <label class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Tipe Minuman</label>
                        <div class="flex flex-wrap gap-[10px]">
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
                        <label class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Kategori Menu</label>
                        <div class="flex flex-wrap gap-[10px]">
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
                            class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Deskripsi Menu</label>
                        <textarea name="deskripsi" id="{{ $id }}-deskripsi" rows="5" placeholder="Masukkan Deskripsi Menu"
                            class="h-[138px] w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-[14px] leading-5 tracking-[0.7px] placeholder:text-brand-gray focus:ring-2 focus:ring-[#681f1f] transition-all resize-none"
                            required>{{ $isEdit ? $menu->deskripsi : '' }}</textarea>
                    </div>

                    {{-- Komposisi --}}
                    <div>
                        <label for="{{ $id }}-komposisi"
                            class="block capitalize text-[14px] font-bold leading-5 tracking-[0.7px] text-brand-dark mb-[5px]">Komposisi Menu</label>
                        <input type="text" name="komposisi" id="{{ $id }}-komposisi"
                            value="{{ $isEdit ? $menu->komposisi : '' }}" placeholder="Masukkan Komposisi Menu"
                            class="w-full bg-[rgba(104,31,31,0.12)] border-none rounded-[9px] p-[10px] text-[14px] leading-5 tracking-[0.7px] placeholder:text-brand-gray focus:ring-2 focus:ring-[#681f1f] transition-all">
                    </div>

                    {{-- Sugar Level (only when jenis = Minuman) --}}
                    <div id="{{ $id }}-conditional-sugar"
                        class="{{ $jenisDefault === 'Minuman' ? '' : 'hidden' }}">
                        <div class="flex items-start gap-[5px]">
                            <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Sugar Level</p>
                            <button type="button"
                                class="relative h-5 w-[44px] rounded-full shadow-[inset_0px_6px_8px_3px_rgba(0,0,0,0.1)] transition-colors {{ $sugarLevelDefault ? 'bg-state-green' : 'bg-brand-gray-light' }}"
                                data-switch-toggle
                                data-form-id="{{ $id }}"
                                data-target-input="{{ $id }}-sugar-input"
                                aria-pressed="{{ $sugarLevelDefault ? 'true' : 'false' }}">
                                <span class="absolute left-0 top-[2px] h-4 w-4 rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] transition-transform {{ $sugarLevelDefault ? 'translate-x-[26px]' : 'translate-x-[2px]' }}" data-switch-knob></span>
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
                                class="relative h-5 w-[44px] rounded-full shadow-[inset_0px_6px_8px_3px_rgba(0,0,0,0.1)] transition-colors {{ $specificOptionsDefault ? 'bg-state-green' : 'bg-brand-gray-light' }}"
                                data-switch-toggle
                                data-form-id="{{ $id }}"
                                data-target-input="{{ $id }}-specific-input"
                                data-target-section="{{ $id }}-specific-section"
                                aria-pressed="{{ $specificOptionsDefault ? 'true' : 'false' }}">
                                <span class="absolute left-0 top-[2px] h-4 w-4 rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] transition-transform {{ $specificOptionsDefault ? 'translate-x-[26px]' : 'translate-x-[2px]' }}" data-switch-knob></span>
                            </button>
                        </div>
                        <p class="mt-0.5 text-[10px] leading-3 tracking-[0.5px] text-brand-gray">
                            Pilihan spesifik digunakan untuk menambah jenis kategori menu, add on topping, atau tambahan spesifik lainnya yang dapat dikustomisasi pelanggan
                        </p>
                    </div>
                </div>
            </div>

            <section id="{{ $id }}-specific-section" class="{{ $specificOptionsDefault ? '' : 'hidden' }} mt-7 border-t border-brand-gray-light pt-6">
                <div class="flex flex-wrap items-end gap-6" data-specific-builder data-form-id="{{ $id }}">
                    <div class="flex flex-col gap-[5px]">
                        <div class="flex items-center gap-1.5">
                            <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Jenis Pilihan</p>
                            <span class="cursor-help text-brand-gray"
                                title="Pilihan Tambahan: opsi tanpa biaya. Pilihan Tambahan + Harga: opsi dengan biaya tambahan.">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex flex-col overflow-hidden rounded-[9px] border border-brand-red text-[14px] leading-5 tracking-[0.7px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] sm:h-8 sm:flex-row">
                            <button type="button" class="px-2 py-1.5 text-brand-red" data-specific-type="basic" data-form-id="{{ $id }}">Pilihan Tambahan</button>
                            <button type="button" class="border-l border-brand-red px-2 py-1.5 text-brand-red" data-specific-type="priced" data-form-id="{{ $id }}">Pilihan Tambahan + Harga</button>
                        </div>
                    </div>

                    {{-- Per Figma, Jumlah Pilihan & Opsional only appear once a Jenis Pilihan is picked. --}}
                    <div id="{{ $id }}-specific-config" class="hidden flex flex-wrap items-end gap-6">
                        <div class="flex w-full flex-col gap-[5px] sm:w-[145px]">
                            <div class="flex items-center gap-1.5">
                                <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Jumlah Pilihan</p>
                                <span class="cursor-help text-brand-gray"
                                    title="Banyaknya opsi pada jenis pilihan ini (1-6).">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="flex h-8 items-center rounded-[9px] bg-[rgba(70,0,1,0.25)]" data-specific-count data-form-id="{{ $id }}">
                                <button type="button" class="h-8 w-8 text-[14px] font-bold text-brand-dark" data-specific-count-step="-1" data-form-id="{{ $id }}">&minus;</button>
                                <span class="flex-1 text-center text-[14px] leading-5 tracking-[0.7px]" data-specific-count-value>2</span>
                                <button type="button" class="h-8 w-8 text-[14px] font-bold text-brand-dark" data-specific-count-step="1" data-form-id="{{ $id }}">&#43;</button>
                            </div>
                        </div>

                        <div class="flex w-full flex-col gap-[5px] sm:w-[145px]">
                            <div class="flex items-center gap-1.5">
                                <p class="capitalize font-bold leading-5 text-brand-dark text-[14px] tracking-[0.7px]">Opsional</p>
                                <span class="cursor-help text-brand-gray"
                                    title="Aktif: pelanggan boleh melewati pilihan ini. Nonaktif: wajib dipilih.">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <button type="button"
                                class="relative h-5 w-[44px] rounded-full shadow-[inset_0px_6px_8px_3px_rgba(0,0,0,0.1)] bg-state-green transition-colors"
                                data-specific-optional
                                data-form-id="{{ $id }}"
                                aria-pressed="true">
                                <span class="absolute left-0 top-[2px] h-4 w-4 translate-x-[26px] rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] transition-transform" data-switch-knob></span>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="{{ $id }}-specific-groups" class="mt-4 flex flex-col gap-4" data-specific-groups data-next-index="0"></div>

                <button type="button"
                    id="{{ $id }}-specific-add"
                    class="hidden mt-4 flex h-8 w-full items-center justify-center gap-2 rounded-[9px] bg-brand-red px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]"
                    data-specific-add-group
                    data-form-id="{{ $id }}">
                    <span class="font-bold">&#43;</span>
                    Tambah Jenis Pilihan
                </button>
            </section>

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
                    knob.classList.toggle('translate-x-[26px]', active);
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

                // Per Figma the segmented control starts unselected; Jumlah Pilihan, Opsional and
                // "Tambah Jenis Pilihan" stay hidden until a jenis is picked. This also keeps the
                // add-group handler honest — it needs a [data-active="1"] type to build a group.
                const config = document.getElementById(`${formId}-specific-config`);
                if (config) config.classList.remove('hidden');
                const addButton = document.getElementById(`${formId}-specific-add`);
                if (addButton) addButton.classList.remove('hidden');
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
                                <span class="relative h-5 w-[44px] rounded-full shadow-[inset_0px_6px_8px_3px_rgba(0,0,0,0.1)] ${optional ? 'bg-state-green' : 'bg-brand-gray-light'}">
                                    <span class="absolute left-0 top-[2px] h-4 w-4 rounded-full bg-white shadow-[2px_1px_3px_rgba(0,0,0,0.25)] ${optional ? 'translate-x-[26px]' : 'translate-x-[2px]'}"></span>
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

            // No mount-time setSpecificType() here on purpose: per Figma the Jenis Pilihan
            // segmented control starts unselected, and Jumlah Pilihan / Opsional / "Tambah Jenis
            // Pilihan" stay hidden until the user picks one.
        });
    </script>
@endonce
