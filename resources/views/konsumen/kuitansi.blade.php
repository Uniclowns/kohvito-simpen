<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi {{ $pesanan->no_pesanan }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 20px; }
        h2 { font-size: 16px; margin-bottom: 4px; }
        .subtitle { font-size: 11px; color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { padding: 6px 4px; border-bottom: 1px solid #ddd; text-align: left; }
        th { font-weight: 700; background: #f0f0f0; }
        .right { text-align: right; }
        .total-row td { font-weight: 700; border-top: 2px solid #aaa; border-bottom: none; }
        .meta td { padding: 3px 4px; border: none; }
        .footer { margin-top: 24px; font-size: 11px; color: #888; text-align: center; }
        hr { border: none; border-top: 1px dashed #aaa; margin: 12px 0; }
    </style>
</head>
<body>

<h2>Kuitansi Pembayaran</h2>
<div class="subtitle">Terima kasih atas pesanan Anda!</div>

<table class="meta">
    <tr><td width="120">No. Pesanan</td><td>: <strong>{{ $pesanan->no_pesanan }}</strong></td></tr>
    <tr><td>Nama</td><td>: {{ $pesanan->nama_konsumen }}</td></tr>
    <tr><td>Meja</td><td>: {{ $pesanan->meja->no_meja ?? '-' }}</td></tr>
    <tr><td>Tgl. Bayar</td><td>: {{ $pesanan->tgl_pembayaran ? $pesanan->tgl_pembayaran->format('d/m/Y H:i') : '-' }}</td></tr>
    <tr><td>Status</td><td>: Lunas</td></tr>
</table>

<hr>

<table>
    <thead>
        <tr>
            <th>Menu</th>
            <th class="right">Qty</th>
            <th class="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pesanan->detailPesanan as $item)
        <tr>
            <td>
                {{ $item->menu->nama_menu ?? '-' }}
                @if($item->catatan)
                    <br><small style="color:#888">{{ $item->catatan }}</small>
                @endif
            </td>
            <td class="right">{{ $item->jumlah }}</td>
            <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="2">Total</td>
            <td class="right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">Dokumen ini digenerate otomatis &mdash; tidak memerlukan tanda tangan.</div>

</body>
</html>
