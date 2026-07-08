{{--
    Checker internal (Barista / Kitchen) — cetakan operasional, BUKAN untuk pelanggan.
    Disertakan dari kasir.cetak-pesanan-print dan ikut tercetak bersama struk.

    Variabel yang diterima via @include:
      - $pesanan  : App\Models\Pesanan
      - $type     : 'BARISTA' | 'KITCHEN'        (judul checker)
      - $kategori : 'MINUMAN' | 'MAKANAN'        (label section & total)
      - $items    : Collection<DetailPesanan>    (item kategori ini saja)

    Tidak menampilkan harga, diskon, atau total pembayaran — hanya nama, jumlah,
    varian, dan catatan agar tim operasional cepat membaca. Varian + catatan
    diambil dari field `catatan` yang sudah berisi "Label: Value | Label: Value".
--}}
@php
    $ckTanggal = $pesanan->tgl_pembayaran
        ? $pesanan->tgl_pembayaran->format('d-m-Y H:i')
        : now()->format('d-m-Y H:i');
    $ckTotal = (int) $items->sum('jumlah');
@endphp
<section class="sheet checker">
    <div class="ck-title">CHECKER {{ $type }}</div>

    <div class="divider"></div>

    <table class="info">
        <tr><td class="k">No Order</td><td class="s">:</td><td>{{ $pesanan->no_pesanan }}</td></tr>
        <tr><td class="k">Tanggal</td><td class="s">:</td><td>{{ $ckTanggal }}</td></tr>
        <tr><td class="k">Meja</td><td class="s">:</td><td>{{ $pesanan->meja?->no_meja ?? 'Quick Service' }}</td></tr>
        <tr><td class="k">Customer</td><td class="s">:</td><td>{{ $pesanan->nama_konsumen ?? '-' }}</td></tr>
        <tr><td class="k">Purpose</td><td class="s">:</td><td>DINE IN</td></tr>
    </table>

    <div class="divider"></div>

    <div class="ck-cat">{{ $kategori }}</div>

    @foreach ($items as $item)
        <div class="ck-item">
            <div class="ck-item-name">{{ $item->jumlah }}x {{ $item->menu?->nama_menu ?? 'Menu' }}</div>
            @php
                // Pecah "Label: Value | Label: Value | Catatan: ..." menjadi baris-baris.
                $segments = collect(explode('|', (string) $item->catatan))
                    ->map(fn ($s) => trim($s))
                    ->filter()
                    ->values();
            @endphp
            @if ($segments->isNotEmpty())
                <table class="ck-opts">
                    @foreach ($segments as $seg)
                        @php
                            $pos   = strpos($seg, ':');
                            $label = $pos !== false ? trim(substr($seg, 0, $pos)) : $seg;
                            $value = $pos !== false ? trim(substr($seg, $pos + 1)) : '';
                        @endphp
                        <tr>
                            <td class="k">{{ $label }}</td>
                            <td class="s">@if ($value !== ''):@endif</td>
                            <td class="v">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    @endforeach

    <div class="divider"></div>

    <div class="ck-total">TOTAL ITEM {{ $kategori }}: {{ $ckTotal }}</div>
</section>
