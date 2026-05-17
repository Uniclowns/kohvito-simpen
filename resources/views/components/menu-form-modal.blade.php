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
@endphp

<div id="{{ $id }}" data-form-modal
    class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all"
    onclick="if(event.target === this) closeConfirmModal('{{ $id }}')">

    <div
        class="bg-white rounded-2xl shadow-[0_8px_24px_rgba(0,0,0,0.15)] w-full max-w-[1000px] max-h-[95vh] relative flex flex-col">
        {{-- Sticky Header --}}
        <div class="px-8 pt-8 pb-4 flex-shrink-0 relative border-b border-gray-100">
            <button type="button"
                class="absolute top-8 right-8 text-brand-gray hover:text-brand-black transition-colors"
                onclick="closeConfirmModal('{{ $id }}')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-2xl font-bold text-brand-dark">{{ $title }}</h2>
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
            <div id="{{ $id }}-id-kategori-inputs"></div>

            {{-- Scrollable body --}}
            <div class="flex-1 overflow-y-auto px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                {{-- ════════════════════════════════════ --}}
                {{-- KOLOM KIRI                            --}}
                {{-- ════════════════════════════════════ --}}
                <div class="space-y-6">
                    {{-- Jenis Menu --}}
                    <div>
                        <label class="block text-sm font-bold text-brand-dark mb-3">Jenis Menu</label>
                        <div class="flex gap-3">
                            <button type="button" data-jenis-toggle="Makanan" data-form-id="{{ $id }}"
                                class="jenis-pill px-8 py-2.5 rounded-xl text-sm font-bold transition-all {{ $jenisDefault === 'Makanan' ? 'bg-[#380000] text-white' : 'bg-white text-[#380000] border border-[#380000]' }}">
                                Makanan
                            </button>
                            <button type="button" data-jenis-toggle="Minuman" data-form-id="{{ $id }}"
                                class="jenis-pill px-8 py-2.5 rounded-xl text-sm font-bold transition-all {{ $jenisDefault === 'Minuman' ? 'bg-[#380000] text-white' : 'bg-white text-[#380000] border border-[#380000]' }}">
                                Minuman
                            </button>
                        </div>
                    </div>

                    {{-- Foto Menu --}}
                    <div>
                        <label class="block text-sm font-bold text-brand-dark mb-3">Foto Menu</label>
                        <div id="{{ $id }}-dropzone"
                            class="relative border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50 p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 transition-all min-h-[180px]">
                            <input type="file" name="gambar_menu" id="{{ $id }}-file-input" class="hidden"
                                accept="image/png,image/jpeg,image/webp">

                            {{-- Empty State --}}
                            <div id="{{ $id }}-dropzone-empty"
                                class="{{ $isEdit && $menu->gambar_menu ? 'hidden' : '' }} flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-xs font-bold text-brand-dark mb-0.5">Support File</p>
                                    <p class="text-[10px] text-brand-gray">PNG, JPG, WEBP</p>
                                </div>
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <div class="text-left">
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
                                            class="relative w-12 h-12 rounded-md overflow-hidden bg-white flex-shrink-0 hover:ring-2 hover:ring-[#380000] transition-all group">
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
                            class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#380000] transition-all"
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
                            class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#380000] transition-all placeholder:italic"
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
                    <div class="grid grid-cols-2 gap-4 items-end">
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
                            <div class="flex items-center bg-[#EBE4E0]/40 rounded-xl">
                                <button type="button" data-stock-step="-1" data-form-id="{{ $id }}"
                                    class="w-10 h-10 flex items-center justify-center text-[#380000] hover:bg-[#DFD4CF] rounded-l-xl transition-all text-lg font-bold">
                                    &minus;
                                </button>
                                <input type="number" name="stock" id="{{ $id }}-stock"
                                    value="{{ $stockDefault }}"
                                    class="flex-1 w-full text-center bg-transparent border-none text-sm font-medium text-brand-dark focus:ring-0"
                                    min="0" max="999999999">
                                <button type="button" data-stock-step="1" data-form-id="{{ $id }}"
                                    class="w-10 h-10 flex items-center justify-center text-[#380000] hover:bg-[#DFD4CF] rounded-r-xl transition-all text-lg font-bold">
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
                <div class="space-y-6">
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
                                    class="tipe-pill px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all {{ $isActive ? 'bg-[#380000] text-white' : 'bg-white text-[#380000] border border-[#380000]' }}">
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
                                    class="kategori-pill px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $isSelected ? 'bg-[#380000] text-white' : 'bg-white text-[#380000] border border-[#380000]' }}">
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
                            class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#380000] transition-all resize-none"
                            required>{{ $isEdit ? $menu->deskripsi : '' }}</textarea>
                    </div>

                    {{-- Komposisi --}}
                    <div>
                        <label for="{{ $id }}-komposisi"
                            class="block text-sm font-bold text-brand-dark mb-3">Komposisi Menu</label>
                        <input type="text" name="komposisi" id="{{ $id }}-komposisi"
                            value="{{ $isEdit ? $menu->komposisi : '' }}" placeholder="Masukkan Komposisi Menu"
                            class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#380000] transition-all">
                    </div>
                </div>
            </div>

            </div>
            {{-- /Scrollable body --}}

            {{-- Sticky Footer --}}
            <div class="flex-shrink-0 flex justify-end gap-3 px-8 py-4 bg-white border-t border-gray-100 rounded-b-2xl">
                <button type="button" onclick="openConfirmModal('confirm-cancel-{{ $mode }}-{{ $id }}')"
                    class="bg-[#EBE4E0] text-[#380000] px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-[#DFD4CF] transition-colors">
                    Batal
                </button>
                <button type="button" onclick="openConfirmModal('confirm-{{ $mode }}-{{ $id }}')"
                    class="bg-[#380000] text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-[#2A0000] transition-colors">
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
    onConfirm="closeConfirmModal('confirm-cancel-{{ $mode }}-{{ $id }}'); closeConfirmModal('{{ $id }}')" />

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                        b.classList.remove('bg-[#380000]', 'text-white');
                        b.classList.add('bg-white', 'text-[#380000]', 'border', 'border-[#380000]');
                    });
                    btn.classList.add('bg-[#380000]', 'text-white');
                    btn.classList.remove('bg-white', 'text-[#380000]', 'border', 'border-[#380000]');

                    document.getElementById(`${formId}-conditional-makanan`).classList.toggle('hidden', jenis !== 'Makanan');
                    document.getElementById(`${formId}-conditional-minuman`).classList.toggle('hidden', jenis !== 'Minuman');
                }

                // Tipe Minuman pills (single-select)
                if (e.target.closest('[data-tipe-toggle]')) {
                    const btn = e.target.closest('[data-tipe-toggle]');
                    const tipe = btn.dataset.tipeToggle;
                    const formId = btn.dataset.formId;

                    document.getElementById(`${formId}-tipe-minuman-input`).value = tipe;

                    document.querySelectorAll(`[data-tipe-toggle][data-form-id="${formId}"]`).forEach(b => {
                        b.classList.remove('bg-[#380000]', 'text-white');
                        b.classList.add('bg-white', 'text-[#380000]', 'border', 'border-[#380000]');
                    });
                    btn.classList.add('bg-[#380000]', 'text-white');
                    btn.classList.remove('bg-white', 'text-[#380000]', 'border', 'border-[#380000]');
                }

                // Kategori Menu pills (multi-select)
                if (e.target.closest('[data-kategori-toggle]')) {
                    const btn = e.target.closest('[data-kategori-toggle]');
                    const formId = btn.dataset.formId;
                    const isSelected = btn.dataset.selected === '1';

                    btn.dataset.selected = isSelected ? '0' : '1';
                    if (isSelected) {
                        btn.classList.add('bg-white', 'text-[#380000]', 'border', 'border-[#380000]');
                        btn.classList.remove('bg-[#380000]', 'text-white');
                    } else {
                        btn.classList.add('bg-[#380000]', 'text-white');
                        btn.classList.remove('bg-white', 'text-[#380000]', 'border', 'border-[#380000]');
                    }

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
                    dropzone.classList.add('border-[#380000]', 'bg-[#EBE4E0]/20');
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('border-[#380000]', 'bg-[#EBE4E0]/20');
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-[#380000]', 'bg-[#EBE4E0]/20');
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
        });
    </script>
@endonce
