{{--
    Halaman "Buka riwayat di perangkat lain".

    Menampilkan QR code + tautan bertoken (TTL singkat) yang memuat daftar
    tracking_code riwayat browser INI. Pindai/buka di perangkat lain untuk
    memulihkan riwayat pesanan di sana — tanpa login, tanpa akun.
--}}
<x-layouts.konsumen
    :title="'Transfer Riwayat - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] pb-[140px] lg:pb-0 font-sans text-brand-black kvt-konsumen-mobile-view">

    {{-- Top header bar --}}
    <header class="bg-brand-dark safe-top">
        <div class="max-w-md md:max-w-2xl mx-auto px-[18px] pt-[14px] pb-3 flex items-center justify-between">
            <p class="flex-1 text-white text-[12px] leading-4 font-bold tracking-[0.6px] capitalize">Transfer Riwayat</p>
            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito" class="w-full h-full object-contain">
            </div>
            <p class="flex-1"></p>
        </div>
    </header>

    <main class="max-w-md md:max-w-2xl mx-auto px-[18px] pt-6">
        <div class="rounded-[12px] bg-white p-5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
            <h1 class="text-[18px] leading-6 font-bold tracking-[0.5px] text-brand-dark">
                Buka Riwayat di Perangkat Lain
            </h1>
            <p class="mt-1 text-[12px] leading-4 text-brand-dark/60">
                Pindai QR ini (atau buka tautannya) di ponsel/browser lain untuk memulihkan
                {{ $jumlah }} riwayat pesanan Anda di sana. Tautan berlaku {{ $ttlMenit }} menit.
            </p>

            {{-- QR code transfer --}}
            <div class="mt-4 flex justify-center">
                <div class="rounded-[9px] border border-brand-gray-extralight bg-white p-3">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->margin(1)->generate($url) !!}
                </div>
            </div>

            {{-- Tautan manual + tombol salin --}}
            <div class="mt-4 flex items-stretch gap-2">
                <code id="kvt-transfer-url"
                      class="block min-w-0 flex-1 overflow-x-auto rounded-[6px] bg-[#F3F3F3] px-2 py-1.5 font-mono text-[10px] leading-3 text-black/80 whitespace-nowrap">{{ $url }}</code>
                <button type="button"
                        onclick="(function(btn){
                            const url = document.getElementById('kvt-transfer-url').textContent.trim();
                            const done = ()=>{ const o=btn.textContent; btn.textContent='Tersalin'; setTimeout(()=>btn.textContent=o,1400); };
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(url).then(done, ()=>{ window.prompt('Salin tautan ini:', url); });
                            } else {
                                window.prompt('Salin tautan ini:', url);
                            }
                        })(this)"
                        class="shrink-0 rounded-[6px] bg-brand-red px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.8px] text-white active:opacity-80">
                    Salin
                </button>
            </div>

            <p class="mt-3 text-[10px] leading-3 text-brand-gray">
                Jangan bagikan tautan ini ke orang yang tidak Anda kenal — siapa pun yang
                membukanya dapat melihat status pesanan pada riwayat Anda.
            </p>
        </div>

        @if (session('id_meja_no'))
            <div class="mt-4 text-center">
                <a href="{{ route('konsumen.pesanan', session('id_meja_no')) }}"
                   class="text-[12px] leading-4 text-brand-dark/60 underline">
                    Kembali ke riwayat pesanan
                </a>
            </div>
        @endif
    </main>

</x-layouts.konsumen>
