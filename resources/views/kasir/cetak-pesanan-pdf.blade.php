<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Pesanan</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #000; margin: 0; padding: 20px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; vertical-align: top; }
        .total-row td { font-weight: bold; border-top: 1px solid #000; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="center bold" style="font-size:14px; margin-bottom:4px;">KAFE KOHVITO</div>
    <div class="center" style="font-size:11px; color:#555;">Struk Pesanan Dapur</div>
    <div class="divider"></div>

    <table>
        <tr>
            <td>No Pesanan</td>
            <td class="right">{{ $pesanan->no_pesanan }}</td>
        </tr>
        <tr>
            <td>Meja</td>
            <td class="right">{{ $pesanan->meja?->no_meja ?? '-' }}</td>
        </tr>
        <tr>
            <td>Konsumen</td>
            <td class="right">{{ $pesanan->nama_konsumen }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td class="right">{{ $pesanan->tgl_pembayaran?->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        @foreach ($pesanan->detailPesanan as $detail)
            <tr>
                <td style="width:55%">{{ $detail->menu?->nama_menu ?? 'Menu' }}</td>
                <td style="width:10%" class="center">{{ $detail->jumlah }}x</td>
                <td class="right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if ($detail->catatan)
                <tr>
                    <td colspan="3" style="font-size:11px; color:#555; padding-left:8px;">
                        Catatan: {{ $detail->catatan }}
                    </td>
                </tr>
            @endif
        @endforeach
        <tr class="total-row">
            <td colspan="2" class="bold">Total</td>
            <td class="right bold">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>
    <div class="center" style="font-size:11px; margin-top:4px;">Terima kasih!</div>
</body>
</html>
