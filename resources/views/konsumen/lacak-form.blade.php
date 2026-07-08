{{--
    Halaman publik: Lacak Pesanan via Kode Pelacakan (tracking_code).

    Berbeda dari halaman /lacak (timeline pesanan sesi aktif), halaman ini
    device-independent: siapa pun yang memegang kode pelacakan (mis. KV-7F3A9)
    dapat memasukkannya untuk melihat status pesanannya — meski session hilang,
    browser ditutup, atau berpindah perangkat.
--}}
<x-layouts.konsumen
    :title="'Lacak Pesanan - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] pb-[140px] lg:pb-0 font-sans text-brand-black kvt-konsumen-mobile-view">

    {{-- Top header bar --}}
    <header class="bg-brand-dark safe-top">
        <div class="max-w-md md:max-w-2xl mx-auto px-[18px] pt-[14px] pb-3 flex items-center justify-between">
            <p class="flex-1 text-white text-[12px] leading-4 font-bold tracking-[0.6px] capitalize">Lacak Pesanan</p>
            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito" class="w-full h-full object-contain">
            </div>
            <p class="flex-1"></p>
        </div>
    </header>

    <main class="max-w-md md:max-w-2xl mx-auto px-[18px] pt-6">
        <div class="rounded-[12px] bg-white p-5 shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
            <h1 class="text-[18px] leading-6 font-bold tracking-[0.5px] text-brand-dark">
                Lacak Pesanan Anda
            </h1>
            <p class="mt-1 text-[12px] leading-4 text-brand-dark/60">
                Masukkan kode pelacakan yang Anda terima setelah memesan (contoh: KV-7F3A9).
            </p>

            @if (session('success'))
                <div class="mt-3 rounded-[6px] bg-green-50 px-3 py-2 text-[12px] leading-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mt-3 rounded-[6px] bg-red-50 px-3 py-2 text-[12px] leading-4 text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            @error('tracking_code')
                <div class="mt-3 rounded-[6px] bg-red-50 px-3 py-2 text-[12px] leading-4 text-red-600">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('konsumen.lacak.cari') }}" class="mt-4 flex flex-col gap-3">
                @csrf

                <label for="tracking_code" class="text-[10px] leading-3 font-bold tracking-[0.5px] text-brand-dark uppercase">
                    Kode Pelacakan
                </label>

                <input
                    type="text"
                    id="tracking_code"
                    name="tracking_code"
                    value="{{ old('tracking_code') }}"
                    placeholder="KV-7F3A9"
                    autocomplete="off"
                    autocapitalize="characters"
                    maxlength="20"
                    required
                    class="w-full rounded-[8px] border border-brand-dark/20 px-4 py-3 font-mono text-[16px] tracking-[1px] uppercase text-brand-dark focus:border-brand-dark focus:outline-none">

                <button
                    type="submit"
                    class="mt-1 w-full rounded-[8px] bg-brand-dark px-4 py-3 text-[14px] font-bold tracking-[0.5px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:translate-y-[1px] transition">
                    Lacak Pesanan
                </button>
            </form>
        </div>

        @if (session('id_meja_no'))
            <div class="mt-4 text-center">
                <a href="{{ route('konsumen.beranda', session('id_meja_no')) }}"
                   class="text-[12px] leading-4 text-brand-dark/60 underline">
                    Kembali ke menu Meja {{ session('id_meja_no') }}
                </a>
            </div>
        @endif
    </main>

</x-layouts.konsumen>
