{{-- Lacak Pesanan Konsumen — Single-card Timeline
    Routes: konsumen.lacak (/lacak), konsumen.lacak.detail (/lacak/{noPesanan})
    Controller: PesananController@lacakLatest / PesananController@lacak
    Variables: $pesanan (nullable)
    Figma: 1465-22886 (active layout) — empty state 1465-24095.

    Backend hanya menyimpan 3 status enum (status_pesanan):
    "menunggu konfirmasi" → "diproses" → "selesai". Tiga status itu dipetakan
    ke 5 langkah visual berikut (sesuai Figma):
      1. Menunggu Konfirmasi Kasir   (status = menunggu konfirmasi)
      2. Pesanan Diterima             (jeda transisi — kasir baru menekan terima)
      3. Pesanan Disiapkan            (status = diproses)
      4. Siap Disajikan               (jeda penyajian)
      5. Pesanan Telah Diantar        (status = selesai)
    Tidak ada penulisan data baru — semua jam adalah estimasi anchored ke
    `tgl_pembayaran` (atau now() bila belum dibayar).
--}}
<x-layouts.konsumen
    :title="'Lacak Pesanan - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] pb-[140px] lg:pb-0 font-sans text-brand-black kvt-konsumen-mobile-view">

    @php
        $mejaNo = session('id_meja_no');
        $keranjang = session('keranjang', []);
        $cartCount = array_sum(array_column($keranjang, 'jumlah'));
    @endphp

    {{-- Top header bar --}}
    <header class="bg-brand-dark safe-top">
        <div class="max-w-md md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto px-[18px] pt-[14px] pb-3 flex items-center justify-between">
            <p class="flex-1 text-white text-[12px] leading-4 font-bold tracking-[0.6px] capitalize">Lacak Pesanan</p>
            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" alt="Kohvito" class="w-full h-full object-contain">
            </div>
            <p class="flex-1 text-white text-[12px] leading-4 font-bold tracking-[0.6px] capitalize text-right">
                Meja {{ optional($pesanan)->meja->no_meja ?? ($mejaNo ?? '-') }}
            </p>
        </div>
    </header>

    <main class="max-w-md md:max-w-2xl mx-auto px-[18px] pt-4">
        {{-- Back link --}}
        @if ($mejaNo)
            <a href="{{ route('konsumen.beranda', $mejaNo) }}"
               class="inline-flex items-center gap-3 text-brand-dark active:opacity-70 mb-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="text-[20px] leading-8 font-bold tracking-[1px]">Kembali</span>
            </a>
        @endif

        @if (! $pesanan)
            {{-- Empty state — Figma 1465-24095 "Tidak Ada Pesanan Yang Bisa Dilacak" --}}
            <div class="flex min-h-[58vh] flex-col items-center justify-center text-center px-6">
                <img src="{{ asset('images/illustration/empty-sad.svg') }}" alt="" class="w-[150px] h-[150px] mb-5"
                     data-anim="fade-up">
                <p class="text-[20px] leading-7 font-bold tracking-[1px] text-[#CCCCCC] max-w-[240px]">
                    Tidak Ada Pesanan Yang Bisa Dilacak
                </p>
                <p class="mt-2 text-[14px] leading-5 tracking-[0.7px] text-[#CCCCCC] max-w-[240px]">
                    Silahkan Melakukan Pemesanan di Halaman Menu
                </p>
            </div>
        @else
            @php
                // 5 langkah Timeline (Figma 1465-22886). `offset` adalah perkiraan
                // menit dari waktu mulai — hanya dipakai untuk estimasi waktu selesai.
                $prepSteps = [
                    ['title' => 'Menunggu Konfirmasi Kasir', 'desc' => 'Pesanan anda menunggu konfirmasi kasir',              'offset' => 0,  'icon' => 'refresh'],
                    ['title' => 'Pesanan Diterima',          'desc' => 'Pesanan anda telah dikonfirmasi',                     'offset' => 2,  'icon' => 'check'],
                    ['title' => 'Pesanan Disiapkan',         'desc' => 'Pesanan anda sedang disiapkan',                       'offset' => 5,  'icon' => 'pot'],
                    ['title' => 'Siap Disajikan',            'desc' => 'Pesanan anda telah siap dan menunggu untuk diantar',  'offset' => 18, 'icon' => 'serve'],
                    ['title' => 'Pesanan Telah Diantar',     'desc' => 'Pesanan telah diterima dan selesai',                  'offset' => 22, 'icon' => 'bell'],
                ];
                $totalSteps = count($prepSteps);

                // Peta 3 status backend → langkah aktif (1-based) di antara 5 langkah visual.
                // menunggu konfirmasi: pesanan menunggu kasir → step 1
                // diproses: kasir menerima, sedang disiapkan → step 3
                // selesai: pesanan telah diantar → step 5 (semua hijau)
                $stateStep   = ['menunggu konfirmasi' => 1, 'diproses' => 3, 'selesai' => $totalSteps];
                $currentStep = $stateStep[$pesanan->status_pesanan] ?? 1;
                $isDone      = $pesanan->status_pesanan === 'selesai';

                // Anchor waktu: pakai tgl_pembayaran bila ada, jika tidak gunakan now()
                // (tabel pesanan tidak punya created_at). Semua jam adalah estimasi.
                $baseTime    = $pesanan->tgl_pembayaran ? $pesanan->tgl_pembayaran->copy() : now();
                $nowTs       = now()->timestamp;
                $finishTs    = $baseTime->copy()->addMinutes(end($prepSteps)['offset'])->timestamp;
                $finishTime  = $baseTime->copy()->addMinutes(end($prepSteps)['offset'])->format('H.i');
                $minutesLeft = $isDone ? 0 : max(1, (int) ceil(($finishTs - $nowTs) / 60));

                // Progress keseluruhan: langkah selesai + setengah kredit langkah aktif.
                $progress = $isDone
                    ? 100
                    : (int) round(((($currentStep - 1) + 0.5) / $totalSteps) * 100);

                $statusChip = [
                    'menunggu konfirmasi' => ['Menunggu Konfirmasi', 'bg-amber-200 text-brand-black'],
                    'diproses'            => ['Pesanan Diproses',    'bg-[#FFE62F] text-brand-black'],
                    'selesai'             => ['Pesanan Selesai',     'bg-emerald-200 text-brand-black'],
                ][$pesanan->status_pesanan] ?? ['-', 'bg-brand-gray-light text-brand-black'];

                $tanggal = $pesanan->tgl_pembayaran ? $pesanan->tgl_pembayaran : now();
            @endphp

            {{-- ===== KARTU UTAMA — header + timeline + progress + estimasi ===== --}}
            <article id="lacak-card"
                     data-current-step="{{ $currentStep }}"
                     data-anim="fade-up"
                     class="bg-white rounded-[9px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] p-[18px] flex flex-col gap-3">

                {{-- ---------- Header order ---------- --}}
                <header class="flex flex-col gap-1">
                    <p class="text-[12px] leading-4 font-bold tracking-[0.6px] text-brand-dark capitalize">
                        TABLE {{ $pesanan->meja->no_meja ?? '-' }}
                    </p>
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-[20px] leading-7 font-bold tracking-[1px] text-brand-dark truncate">
                            {{ $pesanan->nama_konsumen }}
                        </h2>
                        <span data-status-chip
                              class="shrink-0 inline-flex items-center rounded-[4.5px] px-[6px] py-[3px] text-[10px] leading-3 tracking-[0.5px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] {{ $statusChip[1] }}">
                            {{ $statusChip[0] }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-1 text-[10px] leading-3 tracking-[0.5px] text-brand-red min-[360px]:flex-row min-[360px]:items-center min-[360px]:justify-between">
                        <span class="kvt-break-anywhere font-mono">Order #{{ $pesanan->no_pesanan }}</span>
                        <span class="shrink-0">
                            {{ $tanggal->translatedFormat('l, d M Y H:i') }}
                        </span>
                    </div>
                </header>

                {{-- ---------- Timeline 5 langkah ---------- --}}
                <div class="flex flex-col gap-0" data-anim="stagger">
                    @foreach ($prepSteps as $i => $step)
                        @php
                            $stepNo = $i + 1;
                            $done   = $stepNo < $currentStep || $isDone;
                            $active = $stepNo === $currentStep && ! $isDone;
                            $future = ! $done && ! $active;
                            $isLast = $loop->last;
                        @endphp

                        {{-- step card --}}
                        <div data-anim-item
                             class="relative flex items-center gap-[5px] rounded-[9px] p-[10px] bg-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)]">
                            {{-- ikon lingkaran --}}
                            <div class="relative shrink-0 h-[46px] w-[46px] flex items-center justify-center rounded-full
                                @if ($done) bg-brand-dark text-white
                                @elseif ($active) bg-brand-dark text-white lacak-active-ring
                                @else bg-white text-[#CCCCCC] border border-[#CCCCCC]
                                @endif">
                                @switch($step['icon'])
                                    @case('refresh')
                                        {{-- public/images/icons/Menunggu Konfirmasi Kasir.svg — stroke→currentColor --}}
                                        <svg class="h-[22px] w-[22px]" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.16699 3.77053V7.97577H3.65654M3.65654 7.97577C4.21493 6.59488 5.21576 5.43825 6.50216 4.68716C7.78856 3.93606 9.28784 3.63295 10.765 3.82532C12.2422 4.01769 13.6138 4.69467 14.6649 5.75017C15.716 6.80567 16.3872 8.18003 16.5732 9.65786M3.65654 7.97577H7.37272M16.6253 17.2273V13.022H16.1366M16.1366 13.022C15.5774 14.4021 14.5763 15.5578 13.29 16.3083C12.0038 17.0587 10.5049 17.3614 9.02818 17.1691C7.55143 16.9768 6.18014 16.3003 5.12899 15.2455C4.07784 14.1908 3.40618 12.8172 3.21914 11.34M16.1366 13.022H12.4196" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        @break
                                    @case('check')
                                        {{-- Pesanan Diterima — outline checkmark (Heroicons) --}}
                                        <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        @break
                                    @case('pot')
                                        {{-- public/images/icons/Pesanan Disiapkan.svg — stroke→currentColor --}}
                                        <svg class="h-[22px] w-[22px]" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_lacak_pot_{{ $stepNo }})">
                                                <path d="M1.83301 11H20.1663M18.333 11V18.3333C18.333 18.8196 18.1399 19.2859 17.796 19.6297C17.4522 19.9735 16.9859 20.1667 16.4997 20.1667H5.49967C5.01344 20.1667 4.54713 19.9735 4.20331 19.6297C3.8595 19.2859 3.66634 18.8196 3.66634 18.3333V11M3.66634 7.33333L18.333 3.66666M8.12134 6.21499L7.70884 4.55583C7.64974 4.32229 7.63724 4.07939 7.67207 3.84103C7.70689 3.60266 7.78835 3.37349 7.91179 3.16662C8.03523 2.95975 8.19823 2.77923 8.39148 2.6354C8.58472 2.49156 8.80442 2.38721 9.03801 2.32833L10.8163 1.88833C11.0505 1.82937 11.294 1.81725 11.5328 1.85266C11.7717 1.88807 12.0012 1.97032 12.2082 2.09467C12.4151 2.21903 12.5955 2.38305 12.7389 2.57731C12.8823 2.77157 12.986 2.99224 13.0438 3.22666L13.4563 4.87666" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_lacak_pot_{{ $stepNo }}">
                                                    <rect width="22" height="22" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        @break
                                    @case('serve')
                                        {{-- public/images/icons/Siap Disajikan.svg — fill→currentColor --}}
                                        <svg class="h-[22px] w-[22px]" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.450667 1.28597C0.66 0.485063 1.388 0.00104441 2.15333 0.00104441C2.62533 0.00104441 3.05467 0.178875 3.38133 0.469018C3.7415 0.166057 4.19657 0 4.66667 0C5.13676 0 5.59184 0.166057 5.952 0.469018C6.28997 0.166817 6.72719 0.000197206 7.18 0.00104441C7.94533 0.00104441 8.67333 0.485063 8.88267 1.28597C9.08267 2.05077 9.33333 3.28355 9.33333 4.68078C9.3333 5.46104 9.13872 6.2289 8.7673 6.91452C8.39587 7.60013 7.85939 8.18174 7.20667 8.60642C6.84533 8.84308 6.66667 9.14793 6.66667 9.40866V9.93814C6.66667 9.96844 6.66845 9.99786 6.672 10.0264C6.71733 10.358 6.88133 11.5894 7.03467 12.8663C7.18533 14.1191 7.33333 15.4856 7.33333 16.0459C7.33333 16.7551 7.05238 17.4353 6.55229 17.9368C6.05219 18.4383 5.37391 18.72 4.66667 18.72C3.95942 18.72 3.28115 18.4383 2.78105 17.9368C2.28095 17.4353 2 16.7551 2 16.0459C2 15.4843 2.148 14.1205 2.29867 12.8663C2.452 11.5894 2.616 10.358 2.66133 10.0264L2.66667 9.93814V9.40866C2.66667 9.14793 2.488 8.84308 2.12667 8.60642C1.47394 8.18174 0.937461 7.60013 0.566037 6.91452C0.194613 6.2289 3.32775e-05 5.46104 0 4.68078C0 3.28355 0.250667 2.05077 0.450667 1.28597ZM6.66667 5.34932C6.66667 5.52662 6.59643 5.69667 6.47141 5.82204C6.34638 5.94742 6.17681 6.01785 6 6.01785C5.82319 6.01785 5.65362 5.94742 5.5286 5.82204C5.40357 5.69667 5.33333 5.52662 5.33333 5.34932V2.00665C5.33333 1.82934 5.2631 1.6593 5.13807 1.53392C5.01305 1.40855 4.84348 1.33811 4.66667 1.33811C4.48986 1.33811 4.32029 1.40855 4.19526 1.53392C4.07024 1.6593 4 1.82934 4 2.00665V5.34932C4 5.52662 3.92976 5.69667 3.80474 5.82204C3.67971 5.94742 3.51014 6.01785 3.33333 6.01785C3.15652 6.01785 2.98695 5.94742 2.86193 5.82204C2.7369 5.69667 2.66667 5.52662 2.66667 5.34932V1.85288C2.66667 1.71636 2.61258 1.58542 2.51631 1.48889C2.42005 1.39235 2.28948 1.33811 2.15333 1.33811C1.932 1.33811 1.78 1.47048 1.74 1.62558C1.47724 2.62286 1.34061 3.64931 1.33333 4.68078C1.33327 5.23839 1.47231 5.78715 1.73779 6.2771C2.00327 6.76705 2.38676 7.18262 2.85333 7.48595C3.43867 7.86702 4 8.53689 4 9.40866V9.93814C4 10.0273 3.99422 10.1164 3.98267 10.2056C3.93733 10.5345 3.77467 11.7579 3.62267 13.0268C3.468 14.317 3.33333 15.5859 3.33333 16.0459C3.33333 16.4005 3.47381 16.7406 3.72386 16.9913C3.97391 17.2421 4.31305 17.3829 4.66667 17.3829C5.02029 17.3829 5.35943 17.2421 5.60948 16.9913C5.85952 16.7406 6 16.4005 6 16.0459C6 15.5859 5.86667 14.317 5.71067 13.0254C5.55867 11.7579 5.396 10.5345 5.35067 10.2042C5.34052 10.1163 5.33474 10.028 5.33333 9.93947V9.40999C5.33333 8.53823 5.89467 7.86835 6.48 7.48729C6.94676 7.18383 7.33037 6.76805 7.59586 6.27785C7.86135 5.78765 8.00029 5.23862 8 4.68078C8 3.43464 7.776 2.3222 7.59333 1.62558C7.55333 1.47182 7.4 1.33811 7.18 1.33811C7.04386 1.33811 6.91329 1.39235 6.81702 1.48889C6.72075 1.58542 6.66667 1.71636 6.66667 1.85288V5.34932ZM10.6667 6.01785C10.6667 4.4221 11.2988 2.8917 12.424 1.76333C13.5492 0.634956 15.0754 0.00104441 16.6667 0.00104441C16.8435 0.00104441 17.013 0.071479 17.1381 0.196854C17.2631 0.322228 17.3333 0.492272 17.3333 0.669579V8.65589L17.3587 8.89255C17.4647 9.89045 17.5665 10.8888 17.664 11.8876C17.828 13.5669 18 15.4495 18 16.0459C18 16.7551 17.719 17.4353 17.219 17.9368C16.7189 18.4383 16.0406 18.72 15.3333 18.72C14.6261 18.72 13.9478 18.4383 13.4477 17.9368C12.9476 17.4353 12.6667 16.7551 12.6667 16.0459C12.6667 15.4495 12.8387 13.5669 13.0027 11.8876C13.0867 11.0359 13.1707 10.2203 13.2333 9.6159L13.26 9.36052H12C11.6464 9.36052 11.3072 9.21965 11.0572 8.9689C10.8071 8.71815 10.6667 8.37807 10.6667 8.02345V6.01785ZM14.6627 8.76285L14.6347 9.03294C14.5304 10.0279 14.4291 11.0231 14.3307 12.0186C14.1613 13.7394 14 15.5351 14 16.0459C14 16.4005 14.1405 16.7406 14.3905 16.9913C14.6406 17.2421 14.9797 17.3829 15.3333 17.3829C15.687 17.3829 16.0261 17.2421 16.2761 16.9913C16.5262 16.7406 16.6667 16.4005 16.6667 16.0459C16.6667 15.5338 16.5053 13.7394 16.336 12.0186C16.2382 11.023 16.1369 10.0278 16.032 9.03294L16.004 8.76419L16 8.69199V1.38491C14.8891 1.5457 13.8733 2.10259 13.1386 2.9535C12.4039 3.80441 11.9997 4.89233 12 6.01785V8.02345H14C14.0935 8.02349 14.1859 8.04324 14.2713 8.08143C14.3567 8.11961 14.4331 8.17538 14.4956 8.24511C14.5581 8.31484 14.6053 8.39697 14.634 8.48616C14.6628 8.57536 14.6726 8.66963 14.6627 8.76285Z" fill="currentColor"/>
                                        </svg>
                                        @break
                                    @case('bell')
                                        {{-- public/images/icons/Pesanan Telah Diantar.svg — stroke→currentColor, sw bumped 2→3 untuk skala 46→22 --}}
                                        <svg class="h-[22px] w-[22px]" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.6111 22.4166L21.4815 26.287L29.2222 18.5463M40.8333 22.4166C40.8333 24.7038 40.3828 26.9686 39.5076 29.0817C38.6323 31.1948 37.3494 33.1148 35.7321 34.7321C34.1148 36.3494 32.1948 37.6323 30.0817 38.5075C27.9686 39.3828 25.7039 39.8333 23.4167 39.8333C21.1295 39.8333 18.8647 39.3828 16.7516 38.5075C14.6385 37.6323 12.7185 36.3494 11.1012 34.7321C9.48394 33.1148 8.20103 31.1948 7.32576 29.0817C6.4505 26.9686 6 24.7038 6 22.4166C6 17.7974 7.83496 13.3674 11.1012 10.1012C14.3675 6.83493 18.7975 4.99996 23.4167 4.99996C28.0359 4.99996 32.4658 6.83493 35.7321 10.1012C38.9984 13.3674 40.8333 17.7974 40.8333 22.4166Z" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        @break
                                @endswitch
                            </div>

                            {{-- judul + deskripsi --}}
                            <div class="min-w-0 flex-1 py-[5px] flex flex-col justify-center">
                                <p class="text-[12px] leading-4 font-bold tracking-[0.6px] capitalize truncate
                                    @if ($future) text-[#CCCCCC]
                                    @else text-brand-black
                                    @endif">
                                    {{ $step['title'] }}
                                </p>
                                <p class="text-[10px] leading-3 tracking-[0.5px] mt-1
                                    @if ($future) text-[#CCCCCC]
                                    @else text-brand-gray
                                    @endif">
                                    {{ $step['desc'] }}
                                </p>
                            </div>
                        </div>

                        {{-- konektor vertikal antar step --}}
                        @unless ($isLast)
                            <div class="h-3 ml-[33px] w-[2px] {{ $done ? 'bg-brand-dark' : 'bg-[#E5E7EB]' }}"></div>
                        @endunless
                    @endforeach
                </div>

                {{-- ---------- Progress bar ---------- --}}
                <div class="flex flex-col gap-2 mt-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[12px] leading-[18px] font-bold tracking-[0.6px] text-brand-dark">PROGRESS</p>
                        <p class="text-[12px] leading-[18px] font-bold tracking-[0.6px] text-brand-dark">{{ $progress }}%</p>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-[#E5E7EB]">
                        <div data-progress-bar data-progress-fill="{{ $progress }}"
                             class="h-2 rounded-full bg-gradient-to-r from-[#460001] to-[#8B0002] transition-all duration-500"
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                {{-- ---------- Estimasi Pesanan Selesai (kartu dalam) ---------- --}}
                @if ($isDone)
                    <div class="rounded-[9px] bg-gradient-to-br from-[#460001] to-[#8B0002] px-3 py-2.5 flex flex-col"
                         data-anim="fade-up">
                        <p class="text-[12px] leading-4 font-bold tracking-[0.6px] text-white capitalize">Pesanan Telah Selesai</p>
                        <p class="text-[20px] leading-7 font-bold tracking-[1px] text-white mt-1">Selesai</p>
                        <p class="text-[8px] leading-3 tracking-[0.4px] text-white/50 mt-1">Terima kasih telah memesan di Kohvito</p>
                    </div>
                @else
                    <div class="rounded-[9px] bg-gradient-to-br from-[#460001] to-[#8B0002] px-3 py-2.5 flex flex-col"
                         data-anim="fade-up">
                        <p class="text-[12px] leading-4 font-bold tracking-[0.6px] text-white capitalize">Estimasi Pesanan Selesai</p>
                        <p class="text-[20px] leading-7 font-bold tracking-[1px] text-white mt-1">{{ $finishTime }}</p>
                        <p class="text-[8px] leading-3 tracking-[0.4px] text-white/50 mt-1">Pesanan sekitar {{ $minutesLeft }} menit lagi</p>
                    </div>
                @endif
            </article>

            {{-- ---------- CTA di luar kartu ---------- --}}
            <a href="{{ route('konsumen.pesanan') }}"
               class="mt-3 flex h-8 w-full items-center justify-center rounded-[9px] bg-brand-red px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95">
                Cek Pesanan Anda
            </a>
        @endif
    </main>

    <x-konsumen-bottom-nav active="lacak" :mejaNo="$mejaNo" :cartCount="$cartCount" />

    @if ($pesanan && ! in_array($pesanan->status_pesanan, ['selesai', 'dibatalkan'], true))
        {{-- Polling realtime: reload halaman bila status pesanan berubah --}}
        <script>
        (function () {
            const statusUrl = @json(route('konsumen.pesanan.status', $pesanan->no_pesanan));
            let lastStatus = @json($pesanan->status_pesanan);

            function poll() {
                fetch(statusUrl, { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.status_pesanan && data.status_pesanan !== lastStatus) {
                            window.location.reload();
                            return;
                        }
                        setTimeout(poll, 5000);
                    })
                    .catch(() => setTimeout(poll, 8000));
            }
            setTimeout(poll, 5000);
        })();
        </script>
    @endif
</x-layouts.konsumen>
