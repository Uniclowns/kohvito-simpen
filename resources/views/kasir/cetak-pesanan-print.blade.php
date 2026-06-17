<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $pesanan->no_pesanan }}</title>

    @php
        // Semua nilai berasal dari data transaksi yang sudah ada — tidak ada yang diubah.
        $tx        = $pesanan->tgl_pembayaran;
        $tanggal   = $tx ? $tx->format('d-m-Y H:i') : now()->format('d-m-Y H:i');
        $subtotal  = (int) $pesanan->detailPesanan->sum('subtotal');
        $ppn       = (int) round($subtotal * 0.11);          // skema pajak aplikasi (bukan diskon)
        $grand     = (int) $pesanan->total_harga;
        $itemCount = $pesanan->detailPesanan->count();
        // Format angka ala struk: ribuan pakai koma, tanpa "Rp" — meniru contoh.
        $rp = fn ($n) => number_format((int) $n, 0, '.', ',');

        // Pemisahan item untuk Checker internal berdasarkan kategori menu (jenis_menu).
        // Minuman -> Checker Barista, Makanan -> Checker Kitchen. Pakai data kategori
        // yang sudah ada; tidak ada nama menu yang di-hardcode.
        $drinks = $pesanan->detailPesanan->filter(fn ($d) => optional($d->menu)->jenis_menu === 'Minuman')->values();
        $foods  = $pesanan->detailPesanan->filter(fn ($d) => optional($d->menu)->jenis_menu === 'Makanan')->values();
    @endphp

    <style>
        /* Kertas struk thermal 80mm (ganti ke 58mm bila printer Anda 58mm). */
        @page { size: 80mm auto; margin: 0; }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #fff; }

        body {
            width: 80mm;
            margin: 0 auto;
            padding: 4mm 4mm 6mm;
            background: #fff;
            color: #000;
            font-family: 'Courier New', 'Consolas', 'DejaVu Sans Mono', monospace;
            font-size: 12px;
            line-height: 1.42;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .logo-wrap { text-align: center; margin: 1mm 0 2mm; }
        .logo-wrap img {
            width: 42mm;
            max-width: 72%;
            height: auto;
            /* File logo berwarna PUTIH → paksa jadi hitam agar tampak & tercetak di kertas putih. */
            filter: brightness(0) saturate(100%);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .divider { border-top: 1px dashed #000; margin: 6px 0; }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; }

        /* Info transaksi — titik dua sejajar lewat kolom pemisah tetap. */
        .info td.k { width: 20mm; white-space: nowrap; }
        .info td.s { width: 3mm; }

        /* Daftar item: qty kiri, nama tengah, harga kanan. */
        .items td.qty { width: 7mm; white-space: nowrap; }
        .items td.pr  { text-align: right; white-space: nowrap; padding-left: 6px; }
        .items td.note { padding-left: 0; }

        /* Ringkasan pembayaran. */
        .sum td.cnt { white-space: nowrap; }
        .sum td.lbl { text-align: right; white-space: nowrap; }
        .sum td.val { text-align: right; white-space: nowrap; width: 24mm; padding-left: 6px; }

        .grand td { padding-top: 2px; }
        .grand .lbl { text-align: right; white-space: nowrap; font-size: 17px; font-weight: bold; }
        .grand .val { text-align: right; white-space: nowrap; width: 24mm; font-size: 17px; font-weight: bold; padding-left: 6px; }
        .pay .lbl { text-align: right; white-space: nowrap; }
        .pay .val { text-align: right; white-space: nowrap; width: 24mm; padding-left: 6px; }

        .footer { text-align: center; margin-top: 2px; }
        .footer .ty { font-style: italic; margin-top: 3px; }

        /* Tiap "sheet" (struk pelanggan / checker) tercetak di halaman terpisah
           lewat page-break agar tidak tercampur dalam satu potongan kertas. */
        .sheet { page-break-after: always; break-after: page; }
        .sheet:last-of-type { page-break-after: auto; break-after: auto; }

        /* Checker internal Barista / Kitchen — font sedikit lebih besar, tanpa harga. */
        .ck-title { text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 1px; margin: 1mm 0; }
        .ck-cat { font-weight: bold; font-size: 13px; margin: 5px 0 3px; text-decoration: underline; }
        .ck-item { margin-bottom: 7px; }
        .ck-item-name { font-weight: bold; font-size: 13px; }
        .ck-opts { width: 100%; margin-top: 1px; }
        .ck-opts td { padding: 0; vertical-align: top; }
        .ck-opts td.k { width: 24mm; white-space: nowrap; }
        .ck-opts td.s { width: 3mm; }
        .ck-total { font-weight: bold; font-size: 13px; }

        /* Tampilan layar (fallback bila dibuka langsung): tampak seperti selembar struk. */
        @media screen {
            body { margin: 16px auto; box-shadow: 0 0 0 1px #e5e5e5, 0 8px 24px rgba(0, 0, 0, 0.12); }
        }
    </style>
</head>
<body>
    {{-- ── SHEET 1: Struk Pelanggan (dicetak paling awal) ── --}}
    <section class="sheet">
    <div class="logo-wrap">
        <img src="{{ asset('images/logo/KOHVITO LOGO WHITE.png') }}" alt="Kohvito Coffee &amp; Dining">
    </div>

    <div class="divider"></div>

    <table class="info">
        <tr><td class="k">No</td><td class="s">:</td><td>{{ $pesanan->no_pesanan }}</td></tr>
        <tr><td class="k">Date</td><td class="s">:</td><td>{{ $tanggal }}</td></tr>
        <tr><td class="k">Time In</td><td class="s">:</td><td>{{ $tanggal }}</td></tr>
        <tr><td class="k">Info</td><td class="s">:</td><td>{{ $pesanan->nama_konsumen ?? '-' }}</td></tr>
        <tr><td class="k">Table</td><td class="s">:</td><td>{{ $pesanan->meja?->no_meja ?? 'Quick Service' }}</td></tr>
        <tr><td class="k">Purpose</td><td class="s">:</td><td>DINE IN</td></tr>
    </table>

    <div class="divider"></div>

    <table class="items">
        @foreach ($pesanan->detailPesanan as $detail)
            <tr>
                <td class="qty">{{ $detail->jumlah }}</td>
                <td>{{ strtoupper($detail->menu?->nama_menu ?? 'Menu') }}</td>
                <td class="pr">{{ $rp($detail->subtotal) }}</td>
            </tr>
            @if ($detail->catatan)
                <tr>
                    <td></td>
                    <td class="note">{{ $detail->catatan }}</td>
                    <td class="pr"></td>
                </tr>
            @endif
        @endforeach
    </table>

    <div class="divider"></div>

    <table class="sum">
        <tr>
            <td class="cnt">{{ $itemCount }} Items</td>
            <td class="lbl">Subtotal :</td>
            <td class="val">{{ $rp($subtotal) }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="lbl">PPN 11% :</td>
            <td class="val">{{ $rp($ppn) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="grand">
        <tr><td class="lbl">Grand Total :</td><td class="val">{{ $rp($grand) }}</td></tr>
    </table>
    <table class="pay">
        <tr><td class="lbl">QRIS :</td><td class="val">{{ $rp($grand) }}</td></tr>
    </table>

    <div class="divider"></div>

    <div class="footer">
        <div>WIFI : KOHVITO 5G</div>
        <div>PASS : UBESERIES</div>
        <div class="ty">- Thank You -</div>
    </div>
    </section>

    {{-- ── SHEET 2+: Checker internal (otomatis, di halaman terpisah) ──
         Minuman -> Barista, Makanan -> Kitchen. Hanya muncul bila ada itemnya. --}}
    @if ($drinks->isNotEmpty())
        @include('kasir.cetak-checker', ['type' => 'BARISTA', 'kategori' => 'MINUMAN', 'items' => $drinks])
    @endif
    @if ($foods->isNotEmpty())
        @include('kasir.cetak-checker', ['type' => 'KITCHEN', 'kategori' => 'MAKANAN', 'items' => $foods])
    @endif

    <script>
        // Cetak langsung tanpa unduhan PDF.
        // - Di dalam iframe tersembunyi (alur tombol "Cetak Struk"), JENDELA INDUK
        //   yang memanggil print(); di sini tidak, agar dialog tak muncul dua kali.
        // - Bila dibuka langsung sebagai tab penuh (fallback), halaman mencetak
        //   sendiri lalu menutup diri setelah dialog selesai.
        (function () {
            if (window.self !== window.top) return;

            window.addEventListener('load', function () {
                window.focus();
                window.print();
            });
            window.addEventListener('afterprint', function () {
                window.close();
            });
        })();
    </script>
</body>
</html>
