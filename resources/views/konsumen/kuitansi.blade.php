<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Digital — {{ $pesanan->no_pesanan }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background-color: #ffffff;
            margin: 0;
            padding: 30px 20px;
            line-height: 1.4;
        }
        .receipt-container {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #460001; /* Brand Dark Maroon */
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 5px 0;
            letter-spacing: 2px;
        }
        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 10px;
            font-weight: 500;
        }
        .divider {
            border-top: 1px dashed #808080;
            margin: 15px 0;
            height: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 10px;
            vertical-align: top;
        }
        .meta-label {
            color: #666;
            width: 90px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        .meta-value {
            color: #1a1a1a;
            font-weight: 700;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table th {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #666;
            padding: 6px 0;
            border-bottom: 1px dashed #808080;
            text-align: left;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }
        .item-name {
            font-weight: 700;
            color: #1a1a1a;
            font-size: 11px;
        }
        .item-note {
            font-size: 9px;
            color: #681F1F; /* Brand Red */
            font-style: italic;
            margin-top: 3px;
            font-weight: 500;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 4px 0;
            font-size: 11px;
        }
        .totals-label {
            font-weight: 600;
            color: #666;
        }
        .totals-value {
            font-weight: 700;
            color: #1a1a1a;
        }
        .grand-total-label {
            font-size: 13px;
            font-weight: 800;
            color: #460001; /* Brand Dark Maroon */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-top: 8px;
        }
        .grand-total-value {
            font-size: 15px;
            font-weight: 900;
            color: #460001; /* Brand Dark Maroon */
            padding-top: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
        }
        .footer-thankyou {
            font-family: Georgia, serif;
            font-style: italic;
            font-size: 12px;
            color: #460001;
            margin-bottom: 8px;
        }
        .footer-sub {
            font-size: 9px;
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    
    <!-- Centered Header Brand -->
    <div class="header">
        <h1>KOHVITO CAFÉ</h1>
        <p>Digital Ordering System</p>
        <p style="font-size: 9px; color: #888;">Depok, Jawa Barat, Indonesia</p>
    </div>

    <div class="divider"></div>

    <!-- Metadata Pesanan -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">No. Transaksi</td>
            <td class="meta-value">: {{ $pesanan->no_pesanan }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama Pemesan</td>
            <td class="meta-value">: {{ $pesanan->nama_konsumen }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nomor Meja</td>
            <td class="meta-value">: Meja {{ $pesanan->meja->no_meja ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Waktu Bayar</td>
            <td class="meta-value">: {{ $pesanan->tgl_pembayaran ? $pesanan->tgl_pembayaran->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Status Bayar</td>
            <td class="meta-value" style="color: #2e7d32;">: LUNAS</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Daftar Items Pesanan -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item Menu</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 35%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pesanan->detailPesanan as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item->menu->nama_menu ?? '-' }}</div>
                    @if($item->catatan)
                        <div class="item-note">"{{ $item->catatan }}"</div>
                    @endif
                </td>
                <td class="text-center" style="font-weight: 700;">{{ $item->jumlah }}</td>
                <td class="text-right" style="font-weight: 700; color: #1a1a1a;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Ringkasan Biaya -->
    <table class="totals-table">
        <tr>
            <td class="totals-label">Subtotal Item</td>
            <td class="totals-value text-right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="totals-label">Pajak & Service Charge</td>
            <td class="totals-value text-right">Rp 0</td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0;"><div style="border-top: 1px dashed #e0e0e0; margin: 6px 0;"></div></td>
        </tr>
        <tr>
            <td class="grand-total-label">Total Bayar</td>
            <td class="grand-total-value text-right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Centered Receipt Footer -->
    <div class="footer">
        <div class="footer-thankyou">Terima kasih atas kunjungan Anda!</div>
        <div class="footer-sub">Kohvito Café &bull; Authentic Coffee & Eats</div>
        <p style="font-size: 8px; color: #aaa; margin-top: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Kuitansi Digital Resmi SIMPEN Kohvito</p>
    </div>

</div>

</body>
</html>
