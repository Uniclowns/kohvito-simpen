@props([
    'id'     => 'popup-store-status',
    'status' => 'buka', // 'buka' | 'tutup'
])

@php
    $isBuka = $status === 'buka';
    $heading = $isBuka ? 'Sistem Pemesanan Diaktifkan' : 'Sistem Pemesanan Dinonaktifkan';
    $illustration = $isBuka ? 'store open.svg' : 'store close.svg';
@endphp

<div id="{{ $id }}" data-confirm-modal
     class="hidden fixed inset-0 z-[70] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all"
     onclick="if(event.target === this) closeConfirmModal('{{ $id }}')"
     role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-heading">

    <div class="bg-white rounded-[9px] shadow-[2px_4px_8px_rgba(0,0,0,0.25)] w-full max-w-[400px] px-[30px] py-[24px] flex flex-col gap-[15px] relative">

        {{-- Header: title + close X --}}
        <div class="flex items-start justify-between gap-[5px]">
            <h2 id="{{ $id }}-heading"
                class="font-bold text-[#460001] text-[20px] leading-[28px] tracking-[1px]">
                {{ $heading }}
            </h2>
            <button type="button"
                    onclick="closeConfirmModal('{{ $id }}')"
                    class="text-[#460001] hover:text-[#681F1F] transition-colors flex-shrink-0 -mr-1"
                    aria-label="Tutup popup">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Illustration --}}
        <div class="flex items-center justify-center py-[12px]">
            @if (file_exists(public_path('images/illustration/' . $illustration)))
                <img src="{{ asset('images/illustration/' . $illustration) }}" alt=""
                     class="h-[120px] w-auto object-contain">
            @else
                {{-- Fallback placeholder until the matching illustration asset is added --}}
                <div class="h-[120px] w-[210px] flex items-center justify-center bg-gray-100 rounded text-[10px] text-brand-gray uppercase tracking-wide">
                    illustration missing
                </div>
            @endif
        </div>

        {{-- Footer: Kembali button --}}
        <div class="flex justify-end">
            <button type="button"
                    onclick="closeConfirmModal('{{ $id }}')"
                    class="bg-[#CCCCCC] hover:bg-[#BFBFBF] text-[#681F1F] px-[12px] py-[6px] rounded-[9px] text-[14px] tracking-[0.7px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] transition-colors">
                Kembali
            </button>
        </div>
    </div>
</div>
