@props(['id', 'mode' => 'add', 'menu' => null, 'kategoris' => [], 'submitUrl', 'submitMethod' => 'POST'])

@php
    $isEdit = $mode === 'edit' && $menu;
    $title = $isEdit ? 'Edit Menu' : 'Tambah Menu';
    $needsMethodSpoof = in_array(strtoupper($submitMethod), ['PUT', 'PATCH']);
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


            {{-- Scrollable body --}}
            <div class="flex-1 overflow-y-auto px-4 py-5 sm:px-6">
                <x-menu-form-fields :id="$id" :mode="$mode" :menu="$menu" :kategoris="$kategoris" />
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
