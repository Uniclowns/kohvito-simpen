@props([
    'id',
    'title',
    'subtitle' => null,
    'confirmLabel' => 'Simpan',
    'cancelLabel' => 'Batal',
    'variant' => 'primary', // 'primary' | 'danger'
    'action' => null,       // URL for form submit
    'method' => 'POST',     // HTTP method
    'onConfirm' => null,    // JS expression for callback
])

@php
    $primaryClasses = match ($variant) {
        'danger'  => 'bg-[#E52E2D] hover:bg-[#C92A2A]',
        'success' => 'bg-[#22C55E] hover:bg-[#16A34A]',
        default   => 'bg-[#7A1F1F] hover:bg-[#681F1F]',
    };
@endphp

<div id="{{ $id }}"
    data-confirm-modal
    class="hidden fixed inset-0 z-[60] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all"
    onclick="if(event.target === this) closeConfirmModal('{{ $id }}')">

    <div class="bg-white rounded-lg shadow-[0_8px_24px_rgba(0,0,0,0.18)] w-full max-w-[556px] p-8 relative">
        {{-- Close Icon (X) --}}
        <button type="button" 
            class="absolute top-7 right-8 text-[#380000] hover:text-[#681F1F] transition-colors"
            onclick="closeConfirmModal('{{ $id }}')"
            aria-label="Tutup popup">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Content --}}
        <div>
            <h2 class="text-[24px] font-bold text-[#380000] leading-tight pr-10">
                {{ $title }}
            </h2>
            
            @if($subtitle)
                <p class="text-[14px] text-[#808080] leading-relaxed mt-4 mb-8">
                    {{ $subtitle }}
                </p>
            @else
                <div class="mb-8"></div>
            @endif
        </div>

        {{-- Footer Buttons --}}
        <div class="flex justify-end gap-3">
            {{-- Tombol Kiri --}}
            <button type="button"
                class="bg-[#D0D0D0] text-[#681F1F] px-4 py-2 rounded-lg text-sm font-medium shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#C4C4C4] transition-colors"
                onclick="closeConfirmModal('{{ $id }}')">
                {{ $cancelLabel }}
            </button>

            @if($action)
                {{-- Mode A: Form Submit --}}
                <form action="{{ $action }}" method="POST" class="inline" id="form-{{ $id }}">
                    @csrf
                    @method($method)
                    <button type="submit"
                        class="{{ $primaryClasses }} text-white px-4 py-2 rounded-lg text-sm font-bold shadow-[0_3px_6px_rgba(0,0,0,0.22)] transition-colors">
                        {{ $confirmLabel }}
                    </button>
                </form>
            @elseif($onConfirm)
                {{-- Mode B: Custom JS --}}
                <button type="button"
                    onclick="{!! $onConfirm !!}"
                    class="{{ $primaryClasses }} text-white px-4 py-2 rounded-lg text-sm font-bold shadow-[0_3px_6px_rgba(0,0,0,0.22)] transition-colors">
                    {{ $confirmLabel }}
                </button>
            @endif
        </div>
    </div>
</div>

@once
<script>
    window.openConfirmModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    window.closeConfirmModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            // Check if there are other open modals
            const openModals = document.querySelectorAll('.fixed:not(.hidden)');
            if (openModals.length === 0) {
                document.body.style.overflow = '';
            }
        }
    }
</script>
@endonce
