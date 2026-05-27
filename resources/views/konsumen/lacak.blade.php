{{-- Lacak Pesanan Konsumen — Timeline Persiapan
    Routes: konsumen.lacak (/lacak), konsumen.lacak.detail (/lacak/{noPesanan})
    Controller: PesananController@lacakLatest / PesananController@lacak
    Variables: $pesanan (nullable)
    Figma: 1519-19883 (timeline detail) — empty state 1465-24095.

    The backend only tracks 3 coarse statuses (status_pesanan enum):
    "menunggu konfirmasi" → "diproses" → "selesai". Those 3 states are mapped
    onto the 6 visual preparation steps below; per-step clock times are estimates
    anchored to tgl_pembayaran (or now() when unpaid) — there is no created_at
    column and no fabricated data is written.
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

    <main class="max-w-md md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto px-[18px] pt-4">
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
                // 6 langkah visual Timeline Persiapan (Figma 1519-19883). `offset`
                // adalah perkiraan menit dari waktu mulai untuk label jam tiap langkah.
                $prepSteps = [
                    ['title' => 'Pesanan Diterima',  'desc' => 'Pesanan Anda telah dikonfirmasi',       'offset' => 0,  'icon' => 'check'],
                    ['title' => 'Persiapan Bahan',   'desc' => 'Bahan makanan sedang disiapkan',        'offset' => 5,  'icon' => 'prep'],
                    ['title' => 'Memasak',           'desc' => 'Chef sedang memasak pesanan Anda',      'offset' => 10, 'icon' => 'flame'],
                    ['title' => 'Membuat Minuman',   'desc' => 'Minuman akan segera dibuat',            'offset' => 18, 'icon' => 'cup'],
                    ['title' => 'Penyajian',         'desc' => 'Makanan akan disajikan dengan cantik',  'offset' => 22, 'icon' => 'serve'],
                    ['title' => 'Siap Disajikan',    'desc' => 'Pesanan siap dihidangkan ke meja Anda', 'offset' => 25, 'icon' => 'bell'],
                ];
                $totalSteps = count($prepSteps);

                // Peta 3 status backend → langkah aktif (1-based) di antara 6 langkah visual.
                // menunggu konfirmasi: baru masuk, menunggu kasir → langkah 1 aktif
                // diproses: kasir menerima, sedang dimasak → langkah 3 (Memasak) aktif
                // selesai: keenam langkah selesai
                $stateStep   = ['menunggu konfirmasi' => 1, 'diproses' => 3, 'selesai' => $totalSteps];
                $currentStep = $stateStep[$pesanan->status_pesanan] ?? 1;
                $isDone      = $pesanan->status_pesanan === 'selesai';

                // Anchor waktu: pakai tgl_pembayaran bila ada, jika tidak gunakan now()
                // (tabel pesanan tidak punya created_at). Semua jam adalah estimasi.
                $baseTime = $pesanan->tgl_pembayaran ? $pesanan->tgl_pembayaran->copy() : now();
                $nowTs    = now()->timestamp;
                $finishTs = $baseTime->copy()->addMinutes(end($prepSteps)['offset'])->timestamp;
                $finishTime  = $baseTime->copy()->addMinutes(end($prepSteps)['offset'])->format('H:i');
                $minutesLeft = $isDone ? 0 : max(1, (int) ceil(($finishTs - $nowTs) / 60));

                // Progress keseluruhan: langkah selesai + setengah kredit langkah aktif.
                $progress = $isDone
                    ? 100
                    : (int) round(((($currentStep - 1) + 0.5) / $totalSteps) * 100);

                // Sub-progress kartu "Sedang Berlangsung" (hanya saat langkah Memasak aktif).
                $subProgress = 0;
                if ($currentStep === 3 && ! $isDone) {
                    $startTs = $baseTime->copy()->addMinutes(10)->timestamp;
                    $endTs   = $baseTime->copy()->addMinutes(18)->timestamp;
                    $frac    = $endTs > $startTs ? ($nowTs - $startTs) / ($endTs - $startTs) : 0.7;
                    $subProgress = (int) min(90, max(15, round($frac * 100)));
                }

                $statusChip = [
                    'menunggu konfirmasi' => ['Menunggu Konfirmasi', 'bg-amber-200 text-[#1a1a1a]'],
                    'diproses'            => ['Pesanan Diproses', 'bg-[#FFE62F] text-[#1a1a1a]'],
                    'selesai'             => ['Pesanan Selesai', 'bg-emerald-200 text-[#1a1a1a]'],
                ][$pesanan->status_pesanan] ?? ['-', 'bg-[#CCCCCC] text-[#1a1a1a]'];
            @endphp
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-12 md:items-start md:gap-8">
                <!-- Left Column: Status Summary Info (5 columns) -->
                <div class="md:col-span-5 flex flex-col gap-4">
                    {{-- Order header card --}}
                    <article id="lacak-card" data-current-step="{{ $currentStep }}" data-anim="fade-up"
                             class="bg-white rounded-[16px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] p-[18px] flex flex-col gap-2">
                        <p class="text-[12px] leading-4 tracking-[0.6px] font-bold text-brand-dark">
                            TABLE {{ $pesanan->meja->no_meja ?? '-' }}
                        </p>
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-[20px] leading-7 font-bold tracking-[1px] text-brand-dark truncate">{{ $pesanan->nama_konsumen }}</h2>
                            <span data-status-chip class="shrink-0 inline-flex items-center rounded-[4.5px] px-[6px] py-[3px] text-[10px] leading-3 tracking-[0.5px] shadow-[2px_4px_2px_rgba(0,0,0,0.25)] {{ $statusChip[1] }}">
                                {{ $statusChip[0] }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 text-[10px] leading-3 tracking-[0.5px] text-brand-red min-[390px]:flex-row min-[390px]:items-center min-[390px]:justify-between">
                            <span class="kvt-break-anywhere font-mono">Order #{{ $pesanan->no_pesanan }}</span>
                            <span class="shrink-0">
                                @if ($pesanan->tgl_pembayaran)
                                    {{ $pesanan->tgl_pembayaran->translatedFormat('l, d M Y H:i') }}
                                @else
                                    {{ now()->translatedFormat('l, d M Y H:i') }}
                                @endif
                            </span>
                        </div>

                        {{-- Overall progress bar --}}
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-[#e5e7eb]">
                            <div data-progress-bar data-progress-fill="{{ $progress }}"
                                 class="h-2 rounded-full bg-gradient-to-r from-[#460001] to-[#8b0002] transition-all duration-500"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </article>

                    {{-- Estimasi penyajian banner (Figma 1519-19883) --}}
                    @unless ($isDone)
                        <div class="flex items-center gap-2 rounded-[9px] border border-[#f5e6c3] bg-[#fff9e6] px-3 py-2.5"
                             data-anim="fade-up">
                            <svg class="h-4 w-4 shrink-0 text-[#d4a017]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/>
                            </svg>
                            <p class="text-[12px] leading-[18px] tracking-[0.6px] text-[#6a7282]">
                                Estimasi waktu penyajian: <span class="font-bold text-[#8b7500]">{{ $minutesLeft }} menit lagi</span>
                            </p>
                        </div>
                    @endunless

                    {{-- Estimasi selesai / status card (Figma 1519-19883) --}}
                    @if ($isDone)
                        <div class="rounded-[16px] bg-gradient-to-br from-[#460001] to-[#8b0002] px-5 py-4 text-center" data-anim="fade-up">
                            <p class="text-[14px] leading-5 font-bold tracking-[0.7px] text-white">Pesanan Telah Selesai</p>
                            <p class="mt-1 text-[12px] leading-4 tracking-[0.5px] text-white/60">Terima kasih telah memesan di Kohvito</p>
                        </div>
                    @else
                        <div class="flex items-center justify-between rounded-[16px] bg-[#460001] px-5 py-4" data-anim="fade-up">
                            <div class="min-w-0">
                                <p class="text-[12px] leading-4 tracking-[1.4px] uppercase text-white/60">Estimasi Selesai</p>
                                <p class="text-[28px] leading-9 font-bold tracking-[1px] text-white">{{ $finishTime }}</p>
                                <p class="text-[12px] leading-4 tracking-[0.5px] text-white/60">Sekitar {{ $minutesLeft }} menit lagi</p>
                            </div>
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/10">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/>
                                </svg>
                            </div>
                        </div>
                    @endif

                    {{-- CTA --}}
                    <a href="{{ route('konsumen.pesanan') }}"
                       class="flex h-9 w-full items-center justify-center rounded-[9px] bg-[#681F1F] px-3 py-1.5 text-[14px] leading-5 tracking-[0.7px] text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] active:scale-95">
                        Cek Pesanan Anda
                    </a>
                </div>

                <!-- Right Column: Timeline details (7 columns) -->
                <div class="md:col-span-7">
                    {{-- Timeline Persiapan card --}}
                    <section class="rounded-[16px] bg-white p-[18px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)]" data-anim="fade-up">
                        <p class="mb-4 text-[14px] leading-5 font-bold tracking-[1.4px] text-brand-dark">TIMELINE PERSIAPAN</p>

                        <div class="flex flex-col" data-anim="stagger">
                            @foreach ($prepSteps as $i => $step)
                                @php
                                    $stepNo = $i + 1;
                                    $done   = $stepNo < $currentStep || $isDone;
                                    $active = $stepNo === $currentStep && ! $isDone;
                                    $future = ! $done && ! $active;
                                    $stepTime = $baseTime->copy()->addMinutes($step['offset'])->format('H:i');
                                    $isLast = $loop->last;
                                @endphp
                                <div class="flex gap-3" data-anim-item>
                                    {{-- Icon + connector column --}}
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full
                                            @if ($done) bg-[#460001] text-white
                                            @elseif ($active) bg-[#681F1F] text-white ring-4 ring-[#460001]/15 lacak-active-ring
                                            @else bg-[#f3f4f6] text-[#99a1af] @endif">
                                            @switch($step['icon'])
                                                @case('check')
                                                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    @break
                                                @case('prep')
                                                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v18M6 3a3 3 0 0 1 3 3v3a3 3 0 0 1-3 3M17.5 3v18M17.5 3c-1.5 0-2.5 2.2-2.5 5s1 5 2.5 5"/></svg>
                                                    @break
                                                @case('flame')
                                                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047a8.287 8.287 0 0 0 2.96 2.553c.69-1.05 1.51-2.61 1.4-4.6a8.21 8.21 0 0 1 3.964 2.214Z"/></svg>
                                                    @break
                                                @case('cup')
                                                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8h12v6a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V8ZM16 9h2a2 2 0 0 1 0 4h-2M6 3v2M10 3v2M14 3v2"/></svg>
                                                    @break
                                                @case('serve')
                                                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 18h18M4.5 18a7.5 7.5 0 0 1 15 0M12 8V6m-1.25 0h2.5"/></svg>
                                                    @break
                                                @case('bell')
                                                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                    @break
                                            @endswitch
                                        </div>
                                        @unless ($isLast)
                                            <div class="my-1 w-[2px] flex-1 rounded-full {{ $done ? 'bg-[#460001]' : 'bg-[#e5e7eb]' }}"></div>
                                        @endunless
                                    </div>

                                    {{-- Content --}}
                                    <div class="min-w-0 flex-1 {{ $isLast ? 'pb-0' : 'pb-5' }}">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="text-[14px] leading-5 font-bold tracking-[0.6px] {{ $future ? 'text-[#99a1af]' : 'text-[#1a1a1a]' }}">
                                                {{ $step['title'] }}
                                            </p>
                                            <span class="shrink-0 text-[12px] leading-[18px] tracking-[0.5px] {{ $future ? 'text-[#99a1af]' : 'text-[#460001] font-medium' }}">
                                                {{ $isLast ? 'Est. ' : '' }}{{ $stepTime }}
                                            </span>
                                        </div>
                                        <p class="text-[12px] leading-[18px] tracking-[0.5px] {{ $future ? 'text-[#99a1af]' : 'text-[#6a7282]' }}">
                                            {{ $step['desc'] }}
                                        </p>

                                        {{-- Active "Sedang Berlangsung" sub-card (Memasak) --}}
                                        @if ($active && $step['icon'] === 'flame')
                                            <div class="mt-3 rounded-[8px] border border-[#f5e6c3] bg-[#fff9e6] p-3">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="flex items-center gap-1.5 text-[12px] leading-[18px] font-bold text-[#8b7500]">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047a8.287 8.287 0 0 0 2.96 2.553c.69-1.05 1.51-2.61 1.4-4.6a8.21 8.21 0 0 1 3.964 2.214Z"/></svg>
                                                        Sedang Berlangsung
                                                    </span>
                                                    <span class="shrink-0 text-[11px] leading-4 font-bold text-[#8b7500]">{{ $subProgress }}%</span>
                                                </div>
                                                <p class="mt-1 text-[12px] leading-[18px] tracking-[0.5px] text-[#8b7500]">
                                                    Chef kami sedang memasak dengan penuh perhatian untuk menghasilkan cita rasa terbaik.
                                                </p>
                                                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-[#f5e6c3]">
                                                    <div data-progress-bar data-progress-fill="{{ $subProgress }}"
                                                         class="h-1.5 rounded-full bg-[#d4a017] transition-all duration-500"
                                                         style="width: {{ $subProgress }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
            </div>
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
