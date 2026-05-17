{{--
    Custom Kohvito pagination view (Tailwind, brand-aligned).
    Tidak menampilkan teks "Showing X to Y of Z" — hanya tombol Prev/Next + nomor halaman.
    Pakai: {{ $items->links('vendor.pagination.kohvito') }}

    Brand colors:
      - Active   : bg-[#380000] text-white (brand-dark)
      - Inactive : bg-white text-[#380000] border-[#EBE4E0]
      - Hover    : bg-[#EBE4E0]/60 (warm beige tint)
      - Disabled : opacity-40 cursor-not-allowed
--}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
        class="flex justify-center items-center gap-2 select-none">

        {{-- ── Previous Page Link ── --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#EBE4E0] text-[#380000] opacity-40 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                aria-label="{{ __('pagination.previous') }}"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#EBE4E0] text-[#380000] hover:bg-[#EBE4E0]/60 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        {{-- ── Pagination Elements ── --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span aria-disabled="true"
                    class="flex items-center justify-center min-w-9 h-9 px-2 text-sm text-[#380000]/60 font-medium">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                            class="flex items-center justify-center min-w-9 h-9 px-3 rounded-xl bg-[#380000] text-white text-sm font-bold shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                            class="flex items-center justify-center min-w-9 h-9 px-3 rounded-xl bg-white border border-[#EBE4E0] text-[#380000] text-sm font-medium hover:bg-[#EBE4E0]/60 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- ── Next Page Link ── --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                aria-label="{{ __('pagination.next') }}"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#EBE4E0] text-[#380000] hover:bg-[#EBE4E0]/60 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span aria-disabled="true"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#EBE4E0] text-[#380000] opacity-40 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif
    </nav>
@endif
