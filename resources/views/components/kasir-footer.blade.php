{{-- Footer brand untuk halaman kasir dengan navigasi khusus kasir. --}}
<footer class="kasir-footer-texture min-h-[360px] bg-[#460001] px-14 py-12 text-white">
    <div class="mx-auto flex max-w-[1180px] flex-col gap-9">
        <div class="grid grid-cols-1 items-start justify-between gap-12 lg:grid-cols-[500px_1fr_460px]">
            <div class="flex min-h-[190px] flex-col justify-between">
                <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="Kohvito" class="h-[84px] w-[194px] object-contain">
                <p class="w-full text-justify text-[16px] leading-6 tracking-[0.7px] text-white">
                    A Coffee, Dining &amp; Lifestyle Space Crafted for People Who Love Good Coffee, Cozy Atmosphere, and Meaningful Daily Experiences.
                </p>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-[16px] leading-6 tracking-[0.7px] text-white">
                    <div class="flex items-center gap-2">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10.5h.01"/>
                        </svg>
                        <span>Jl Johar No. 72 Pontianak</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 7.5h16m-16 0A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5m-16 0v9A2.5 2.5 0 0 0 6.5 19h11a2.5 2.5 0 0 0 2.5-2.5v-9M5 7l7 5 7-5"/>
                        </svg>
                        <span>kohvitocafe@gmail.com</span>
                    </div>
                </div>
            </div>

            <div class="flex h-[190px] flex-col gap-5 whitespace-nowrap">
                <p class="text-[22px] font-bold leading-8 tracking-[1px]">Navigation</p>
                <nav class="flex flex-1 flex-col justify-between text-[16px] leading-6 tracking-[0.7px] text-white">
                    <a href="{{ route('kasir.beranda') }}" class="hover:text-white/80">Beranda Kasir</a>
                    <a href="{{ route('kasir.pesanan.index') }}" class="hover:text-white/80">Kelola Pesanan</a>
                    <a href="{{ route('kasir.histori.index') }}" class="hover:text-white/80">Histori Pesanan</a>
                </nav>
            </div>

            <div class="flex min-h-[190px] flex-col justify-between">
                <div class="flex flex-col gap-5">
                    <p class="text-[22px] font-bold leading-8 tracking-[1px]">Visit us!</p>
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-3 text-[16px] leading-6 tracking-[0.7px] text-white">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/icons/Instagram.svg') }}" alt="" class="h-6 w-6 brightness-0 invert">
                            <span>kohvito</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/icons/Facebook.svg') }}" alt="" class="h-6 w-6 brightness-0 invert">
                            <span>kohvito_cafe</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/icons/Threads instagram.svg') }}" alt="" class="h-6 w-6 brightness-0 invert">
                            <span>kohvito</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/icons/tiktok.svg') }}" alt="" class="h-6 w-6 brightness-0 invert">
                            <span>kohvito cafe</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-5">
                    <p class="text-[22px] font-bold leading-8 tracking-[1px]">Reservation?</p>
                    <a href="https://wa.me/6281348922789"
                       class="inline-flex w-fit items-center justify-center gap-3 rounded-[9px] bg-white px-4 py-2 text-[16px] leading-6 tracking-[0.7px] text-[#460001] hover:bg-[#F6F6F6]">
                        <span>Contact Us!</span>
                        <span class="font-bold">+62 813-4892-2789</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="h-px bg-white/35"></div>

        <p class="text-center text-[16px] leading-6 tracking-[0.7px] text-[#F6F6F6]">
            @2026 Right Reserved. Developed By Pet &amp; Jenn
        </p>
    </div>
</footer>
