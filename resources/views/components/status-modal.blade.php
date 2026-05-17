@props([
    'id',
    'title',
    'message',
    'buttonLabel' => 'Tutup',
    'variant' => 'error',
])

@php
    $color = $variant === 'error' ? '#380000' : '#380000';
@endphp

<div id="{{ $id }}"
    data-status-modal
    class="hidden fixed inset-0 z-[70] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
    onclick="if(event.target === this) closeStatusModal('{{ $id }}')">
    <div class="bg-white rounded-lg shadow-[0_8px_24px_rgba(0,0,0,0.18)] w-full max-w-[556px] min-h-[300px] p-8 relative">
        <button type="button"
            class="absolute top-7 right-8 text-[#380000] hover:text-[#681F1F] transition-colors"
            onclick="closeStatusModal('{{ $id }}')"
            aria-label="Tutup popup">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-[24px] font-bold text-[#380000] leading-tight pr-10">
            {{ $title }}
        </h2>

        <div class="mt-5 flex justify-center">
            <div class="w-[102px] h-[102px] rounded-full border-[5px] border-[#380000] flex items-center justify-center">
                <svg class="w-12 h-12 text-[#380000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>

        <p class="mt-5 text-[14px] leading-relaxed text-[#808080] max-w-[470px]">
            {{ $message }}
        </p>

        <div class="mt-4 flex justify-end">
            <button type="button"
                class="bg-[#D0D0D0] text-[#681F1F] px-4 py-2 rounded-lg text-sm font-medium shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#C4C4C4] transition-colors"
                onclick="closeStatusModal('{{ $id }}')">
                {{ $buttonLabel }}
            </button>
        </div>
    </div>
</div>

@once
<script>
    window.openStatusModal = function(id) {
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    window.closeStatusModal = function(id) {
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.classList.add('hidden');

        const openModals = document.querySelectorAll('.fixed:not(.hidden)');
        if (openModals.length === 0) {
            document.body.style.overflow = '';
        }
    }
</script>
@endonce
