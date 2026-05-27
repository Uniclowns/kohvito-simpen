@props([
    'id'           => 'popup-success',
    'heading'      => 'Berhasil',
    'description'  => null, // optional subtitle below heading (e.g. error reason)
    'illustration' => 'print success.svg', // OK-hand by default; pass 'error.svg' for failure variant
    'gif'          => null, // when provided, uses public/images/gif/{gif} (e.g. 'success.gif', 'failed.gif')
    'lottie'       => null, // optional public/lottie/*.json path; static image remains fallback
    'buttonLabel'  => 'Kembali', // footer button text (Figma success popup uses 'Tutup')
])

@php
    // PRD §9 feature-check: enable Lottie only when its JSON actually exists in
    // public/lottie/. Until the designer JSON is added, the static SVG/GIF
    // (marked data-lottie-fallback below) renders on its own and we skip the
    // fetch that would otherwise 404 in the browser console on every poll.
    $lottieRel = $lottie
        ? ltrim(\Illuminate\Support\Str::after($lottie, url('/')), '/')
        : match ($gif) {
            'success.gif' => 'lottie/payment-success.json',
            'failed.gif'  => 'lottie/payment-failed.json',
            default       => null,
        };
    $lottiePath = ($lottieRel && file_exists(public_path($lottieRel)))
        ? asset($lottieRel)
        : null;
@endphp

<div id="{{ $id }}" data-confirm-modal
     class="hidden fixed inset-0 z-[70] bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all"
     onclick="if(event.target === this) closeAppModal('{{ $id }}')"
     role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-heading">

    <div class="kvt-modal-panel relative flex w-full max-w-[440px] flex-col gap-[15px] overflow-y-auto rounded-[9px] bg-white px-5 py-5 shadow-[2px_4px_8px_rgba(0,0,0,0.25)] sm:px-[30px] sm:py-[24px]">

        {{-- Top bar: close X only --}}
        <div class="flex justify-end">
            <button type="button"
                    onclick="closeAppModal('{{ $id }}')"
                    class="text-[#460001] hover:text-[#681F1F] transition-colors -mr-1"
                    aria-label="Tutup popup">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Illustration + heading (heading below) --}}
        <div class="flex flex-col items-center justify-center gap-4">
            @if ($lottiePath)
                <div class="hidden h-[160px] w-full max-w-[220px] sm:h-[180px]" data-lottie="{{ $lottiePath }}" data-lottie-loop="false"></div>
            @endif

            @if ($gif && file_exists(public_path('images/gif/' . $gif)))
                <img src="{{ asset('images/gif/' . $gif) }}" alt="" aria-hidden="true" data-lottie-fallback
                     class="h-[160px] w-auto object-contain sm:h-[180px]">
            @elseif (file_exists(public_path('images/illustration/' . $illustration)))
                <img src="{{ asset('images/illustration/' . $illustration) }}" alt="" data-lottie-fallback
                     class="h-[180px] w-auto object-contain sm:h-[230px]">
            @else
                <div class="flex h-[180px] w-full max-w-[232px] items-center justify-center rounded bg-gray-100 text-[10px] uppercase tracking-wide text-brand-gray sm:h-[230px]" data-lottie-fallback>
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
                    onclick="closeAppModal('{{ $id }}')"
                    class="bg-[#CCCCCC] hover:bg-[#BFBFBF] text-[#681F1F] px-[12px] py-[6px] rounded-[9px] text-[14px] tracking-[0.7px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] transition-colors">
                {{ $buttonLabel }}
            </button>
        </div>
    </div>
</div>
