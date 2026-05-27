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
    onclick="if(event.target === this) closeAppModal('{{ $id }}')">

    <div class="kvt-modal-panel relative w-full max-w-[556px] overflow-y-auto rounded-lg bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.18)] sm:p-8">
        {{-- Close Icon (X) --}}
        <button type="button"
            class="absolute right-5 top-5 text-[#380000] transition-colors hover:text-[#681F1F] sm:right-8 sm:top-7"
            onclick="closeAppModal('{{ $id }}')"
            aria-label="Tutup popup">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Content --}}
        <div>
            <h2 class="pr-10 text-[20px] font-bold leading-tight text-[#380000] sm:text-[24px]">
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
        <div class="kvt-modal-actions flex justify-end gap-3">
            {{-- Tombol Kiri --}}
            <button type="button"
                class="bg-[#D0D0D0] text-[#681F1F] px-4 py-2 rounded-lg text-sm font-medium shadow-[0_3px_6px_rgba(0,0,0,0.22)] hover:bg-[#C4C4C4] transition-colors"
                onclick="closeAppModal('{{ $id }}')">
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
