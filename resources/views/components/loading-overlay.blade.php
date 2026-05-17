@props([
    'id' => 'loading-overlay',
])

<div id="{{ $id }}"
     class="hidden fixed inset-0 z-[80] bg-black/55 backdrop-blur-sm flex flex-col items-center justify-center gap-2 transition-all"
     aria-hidden="true" role="presentation">

    <img src="{{ asset('images/illustration/walking.png') }}" alt=""
         class="h-[150px] w-auto drop-shadow-[2px_4px_4px_rgba(0,0,0,0.35)]">

    <div class="flex items-end gap-1">
        <span class="font-bold text-white text-[24px] leading-[32px] tracking-[1.2px]">Loading</span>
        <span class="flex items-end gap-[3px] pb-[10px]">
            <span class="loading-dot inline-block w-[6px] h-[6px] rounded-full bg-white"></span>
            <span class="loading-dot inline-block w-[6px] h-[6px] rounded-full bg-white" style="animation-delay:0.2s"></span>
            <span class="loading-dot inline-block w-[6px] h-[6px] rounded-full bg-white" style="animation-delay:0.4s"></span>
        </span>
    </div>
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
