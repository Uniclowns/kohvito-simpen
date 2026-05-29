@props([
    'active' => '',      // menu | pesanan | keranjang | lacak
    'mejaNo' => null,
    'cartCount' => 0,
    'lacakHref' => null,
])

{{-- Floating glassmorphism bottom navigation (Figma 1217:35819) — shared across konsumen pages --}}
<nav class="fixed inset-x-0 bottom-0 z-40 pointer-events-none md:hidden">
    <div class="kvt-nav-safe-bottom pointer-events-auto mx-auto w-full max-w-[374px] px-[10px] pt-2">
        <div class="kvt-nav-glass flex items-center justify-between rounded-[18px] p-2.5">

            {{-- Menu --}}
            @if ($mejaNo)
                <a href="{{ route('konsumen.beranda', $mejaNo) }}"
                    class="kvt-nav-item {{ $active === 'menu' ? 'is-active' : '' }} flex h-[58px] min-w-0 flex-1 items-center justify-center p-1.5 min-[361px]:w-[78px] min-[361px]:flex-none">
                    <div class="flex flex-col items-center justify-center gap-px">
                        <img src="{{ asset('images/icons/menu_konsumen.svg') }}" class="kvt-nav-icon h-[30px] w-[30px]" alt="">
                        <span class="kvt-nav-label text-[10px] font-bold leading-3 tracking-wide text-white capitalize">Menu</span>
                    </div>
                </a>
            @else
                <div class="kvt-nav-item flex h-[58px] min-w-0 flex-1 items-center justify-center p-1.5 opacity-60 min-[361px]:w-[78px] min-[361px]:flex-none">
                    <div class="flex flex-col items-center justify-center gap-px">
                        <img src="{{ asset('images/icons/menu_konsumen.svg') }}" class="kvt-nav-icon h-[30px] w-[30px]" alt="">
                        <span class="kvt-nav-label text-[10px] font-bold leading-3 tracking-wide text-white capitalize">Menu</span>
                    </div>
                </div>
            @endif

            {{-- Pesanan (daftar pesanan) --}}
            <a href="{{ route('konsumen.pesanan') }}"
                class="kvt-nav-item {{ $active === 'pesanan' ? 'is-active' : '' }} flex h-[58px] min-w-0 flex-1 items-center justify-center p-1.5 min-[361px]:w-[78px] min-[361px]:flex-none">
                <div class="flex flex-col items-center justify-center gap-px">
                    <img src="{{ asset('images/icons/pesanan_konsumen.svg') }}" class="kvt-nav-icon h-[30px] w-[30px]" alt="">
                    <span class="kvt-nav-label text-[10px] font-bold leading-3 tracking-wide text-white capitalize">Pesanan</span>
                </div>
            </a>

            {{-- Keranjang --}}
            <a href="{{ route('konsumen.keranjang') }}"
                class="kvt-nav-item {{ $active === 'keranjang' ? 'is-active' : '' }} relative flex h-[58px] min-w-0 flex-1 items-center justify-center p-1.5 min-[361px]:w-[78px] min-[361px]:flex-none">
                <div class="flex flex-col items-center justify-center gap-px">
                    <img src="{{ asset('images/icons/keranjang_konsumen.svg') }}" class="kvt-nav-icon h-[30px] w-[30px]" alt="">
                    <span class="kvt-nav-label text-[10px] font-bold leading-3 tracking-wide text-white capitalize">Keranjang</span>
                </div>
                {{-- Always render badge supaya JS bisa update real-time saat cart change.
                     Hidden saat count=0; toggle via kelas .hidden dari JS (lihat keranjang.blade.php). --}}
                <span class="kvt-nav-badge {{ $cartCount === 0 ? 'hidden' : '' }}"
                      data-cart-count>{{ $cartCount }}</span>
            </a>

            {{-- Lacak (timeline) --}}
            <a href="{{ $lacakHref ?: route('konsumen.lacak') }}"
                class="kvt-nav-item {{ $active === 'lacak' ? 'is-active' : '' }} flex h-[58px] min-w-0 flex-1 items-center justify-center p-1.5 min-[361px]:w-[78px] min-[361px]:flex-none">
                <div class="flex flex-col items-center justify-center gap-px">
                    <img src="{{ asset('images/icons/lacak_konsumen.svg') }}" class="kvt-nav-icon h-[30px] w-[30px]" alt="">
                    <span class="kvt-nav-label text-[10px] font-bold leading-3 tracking-wide text-white capitalize">Lacak</span>
                </div>
            </a>

        </div>
    </div>
</nav>
