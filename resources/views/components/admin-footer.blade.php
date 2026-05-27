{{--
    Component: <x-admin-footer />
    Dipakai di halaman admin (Beranda Admin, Kelola Menu, dll) sebagai slot pageFooter.
    Edit di satu file untuk update semua halaman.
--}}
<footer class="bg-brand-dark px-6 py-10 sm:px-10 sm:py-12 mt-auto">
    {{-- Mobile-first: single column on phones/tablets, restore the 12-col desktop grid at md+ (PRD Workstream B). --}}
    <div class="grid grid-cols-1 gap-8 md:grid-cols-12 border-b border-white/10 pb-8">

        {{-- Brand & Info --}}
        <div class="md:col-span-6 md:pr-8">
            <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="Kohvito" class="h-24 w-auto mb-6">
            <p class="text-[18px] text-white/90 leading-relaxed mb-6 w-full md:w-[75%]">
                A Coffee, Dining &amp; Lifestyle Space Crafted for People Who Love Good Coffee, Cozy Atmosphere, and
                Meaningful Daily Experiences.
            </p>
            <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-[18px] text-white/90">Jl Johar No. 72 Pontianak</p>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="text-[18px] text-white/90">kohvitocafe@gmail.com</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="md:col-span-2">
            <p class="text-base font-bold text-white mb-5 tracking-wide">Navigation</p>
            <ul class="space-y-4">
                <li><a href="#" class="text-[18px] text-white/80 hover:text-white transition-colors">Beranda
                        Admin</a></li>
                <li><a href="#" class="text-[18px] text-white/80 hover:text-white transition-colors">Kelola Pengguna
                        Kasir</a></li>
                <li><a href="#" class="text-[18px] text-white/80 hover:text-white transition-colors">Kelola Menu</a>
                </li>
                <li><a href="#" class="text-[18px] text-white/80 hover:text-white transition-colors">Kelola Kategori
                        Menu</a></li>
            </ul>
        </div>

        {{-- Visit Us & Reservation --}}
        <div class="md:col-span-4">
            <p class="text-base font-bold text-white mb-5 tracking-wide">Visit Us!</p>
            <div class="flex items-center gap-5 mb-8 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <img src="{{ asset('images/icons/Instagram.svg') }}" alt=""
                        class="w-4 h-4 brightness-0 invert">
                    <span class="text-[18px] text-white/90">kohvito</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                    </svg>
                    <span class="text-[18px] text-white/90">kohvito_cafe</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <img src="{{ asset('images/icons/Threads instagram.svg') }}" alt=""
                        class="w-4 h-4 brightness-0 invert">
                    <span class="text-[18px] text-white/90">kohvito</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <img src="{{ asset('images/icons/tiktok.svg') }}" alt=""
                        class="w-4 h-4 brightness-0 invert">
                    <span class="text-[18px] text-white/90">kohvito cafe</span>
                </div>
            </div>

            <p class="text-base font-bold text-white mb-4 tracking-wide">Reservation?</p>
            <a href="https://wa.me/6281348922789"
                class="inline-flex items-center bg-white rounded-xl px-5 py-2.5 hover:bg-gray-100 transition-colors shadow-sm">
                <span class="text-[18px] text-brand-dark">Contact Us! <span class="font-bold ml-1">+62
                        813-4892-2789</span></span>
            </a>
        </div>

    </div>

    <div class="pt-6 text-center">
        <p class="text-[14px] text-white/70">&copy;2026 Right Reserved. Developed By Pet &amp; Jenn</p>
    </div>
</footer>
