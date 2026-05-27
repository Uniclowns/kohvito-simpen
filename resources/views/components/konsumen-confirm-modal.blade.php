@props([
    'id',
    'title',
    'subtitle' => null,
    'confirmLabel' => 'Ya',
    'cancelLabel' => 'Batal',
    'variant' => 'primary',
    'action' => null,
    'method' => 'POST',
    'form' => null,
])

@php
    $confirmClasses = $variant === 'danger'
        ? 'bg-[#E52E2D] hover:bg-[#C92A2A]'
        : 'bg-[#681F1F] hover:bg-[#460001]';
@endphp

<div id="{{ $id }}"
    data-konsumen-confirm-modal
    class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/55 p-4"
    onclick="if (event.target === this) closeAppModal('{{ $id }}')">
    <div class="kvt-modal-panel w-full max-w-[354px] overflow-y-auto rounded-[9px] bg-white px-5 py-5 shadow-[2px_4px_4px_rgba(0,0,0,0.25)] sm:px-[30px] sm:py-6">
        <div class="flex items-start justify-between gap-2">
            <h2 class="flex-1 text-[20px] font-bold leading-7 tracking-[1px] text-[#460001]">
                {{ $title }}
            </h2>
            <button type="button"
                class="flex h-[30px] w-[30px] shrink-0 items-center justify-center text-[#460001] active:scale-95"
                onclick="closeAppModal('{{ $id }}')"
                aria-label="Tutup popup">
                <svg class="h-[21px] w-[21px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @if ($subtitle)
            <p class="mt-[7px] text-[10px] leading-3 tracking-[0.5px] text-[#E52E2D]">
                {{ $subtitle }}
            </p>
        @endif

        <div class="mt-[15px] flex justify-end gap-[17px]">
            <button type="button"
                class="rounded-[9px] bg-[#CCCCCC] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-[#681F1F] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95"
                onclick="closeAppModal('{{ $id }}')">
                {{ $cancelLabel }}
            </button>

            @if ($form)
                <button type="submit"
                    form="{{ $form }}"
                    class="{{ $confirmClasses }} rounded-[9px] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95">
                    {{ $confirmLabel }}
                </button>
            @elseif ($action)
                <form action="{{ $action }}" method="POST" class="inline">
                    @csrf
                    @method($method)
                    <button type="submit"
                        class="{{ $confirmClasses }} rounded-[9px] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95">
                        {{ $confirmLabel }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
