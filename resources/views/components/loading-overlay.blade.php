@props([
    'id' => 'loading-overlay',
    'label' => 'Loading',
    'gif' => 'loading_dots_white.gif',
])

<div id="{{ $id }}"
     class="hidden fixed inset-0 z-[80] bg-black/55 backdrop-blur-sm flex flex-col items-center justify-center gap-3 transition-all"
     aria-hidden="true" role="presentation">

    @if (file_exists(public_path('images/gif/' . $gif)))
        <img src="{{ asset('images/gif/' . $gif) }}" alt="" aria-hidden="true"
             class="h-[120px] w-auto drop-shadow-[2px_4px_8px_rgba(0,0,0,0.35)]">
    @else
        <img src="{{ asset('images/illustration/walking.png') }}" alt=""
             class="h-[150px] w-auto drop-shadow-[2px_4px_4px_rgba(0,0,0,0.35)]">
    @endif

    <span class="font-bold text-white text-[20px] leading-[28px] tracking-[1px]">{{ $label }}</span>
</div>

@once
<script>
    window.showLoadingOverlay = function (id) {
        const el = document.getElementById(id || 'loading-overlay');
        if (el) {
            el.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };
    window.hideLoadingOverlay = function (id) {
        const el = document.getElementById(id || 'loading-overlay');
        if (el) {
            el.classList.add('hidden');
            const other = document.querySelector('.fixed:not(.hidden)');
            if (!other) document.body.style.overflow = '';
        }
    };
</script>
@endonce
