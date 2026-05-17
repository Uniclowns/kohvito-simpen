@props([
    'id'           => 'popup-success',
    'heading'      => 'Berhasil',
    'description'  => null, // optional subtitle below heading (e.g. error reason)
    'illustration' => 'print success.svg', // OK-hand by default; pass 'error.svg' for failure variant
])

<div id="{{ $id }}" data-confirm-modal
     class="hidden fixed inset-0 z-[70] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all"
     onclick="if(event.target === this) closeConfirmModal('{{ $id }}')"
     role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-heading">

    <div class="bg-white rounded-[9px] shadow-[2px_4px_8px_rgba(0,0,0,0.25)] w-full max-w-[440px] px-[30px] py-[24px] flex flex-col gap-[15px] relative">

        {{-- Top bar: close X only --}}
        <div class="flex justify-end">
            <button type="button"
                    onclick="closeConfirmModal('{{ $id }}')"
                    class="text-[#460001] hover:text-[#681F1F] transition-colors -mr-1"
                    aria-label="Tutup popup">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Illustration + heading (heading below) --}}
        <div class="flex flex-col items-center justify-center gap-4">
            @if (file_exists(public_path('images/illustration/' . $illustration)))
                <img src="{{ asset('images/illustration/' . $illustration) }}" alt=""
                     class="h-[230px] w-auto object-contain">
            @else
                <div class="h-[230px] w-[232px] flex items-center justify-center bg-gray-100 rounded text-[10px] text-brand-gray uppercase tracking-wide">
                    illustration missing
                </div>
            @endif

            <h2 id="{{ $id }}-heading"
                class="font-bold text-[#460001] text-[20px] leading-[28px] tracking-[1px] text-center">
                {{ $heading }}
            </h2>

            @if ($description)
                <p class="text-[#808080] text-[14px] leading-[20px] tracking-[0.7px] text-center px-4">
                    {{ $description }}
                </p>
            @endif
        </div>

        {{-- Footer: Kembali --}}
        <div class="flex justify-end">
            <button type="button"
                    onclick="closeConfirmModal('{{ $id }}')"
                    class="bg-[#CCCCCC] hover:bg-[#BFBFBF] text-[#681F1F] px-[12px] py-[6px] rounded-[9px] text-[14px] tracking-[0.7px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] transition-colors">
                Kembali
            </button>
        </div>
    </div>
</div>
