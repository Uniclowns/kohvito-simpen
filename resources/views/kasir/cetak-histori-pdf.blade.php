<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Histori Harian</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #000; margin: 0; padding: 20px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px solid #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 5px 6px; background: #f0f0f0; border: 1px solid #ccc; font-size: 11px; }
        td { padding: 4px 6px; border: 1px solid #ddd; font-size: 11px; vertical-align: top; }
        .summary td { border: none; padding: 3px 0; }
        .summary .bold { font-size: 12px; }
    </style>
</head>
<body>
    <div class="center bold" style="font-size:16px; margin-bottom:4px;">KAFE KOHVITO</div>
    <div class="center" style="font-size:12px;">Rekap Histori Pesanan Harian</div>
    <div class="center" style="font-size:11px; color:#555; margin-bottom:8px;">{{ $today->translatedFormat('d F Y') }}</div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>No Pesanan</th>
                <th>Konsumen</th>
                <th>Meja</th>
                <th>Waktu</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pesanans as $pesanan)
                <tr>
                    <td>{{ $pesanan->no_pesanan }}</td>
                    <td>{{ $pesanan->nama_konsumen }}</td>
                    <td>{{ $pesanan->meja?->no_meja ?? '-' }}</td>
                    <td>{{ $pesanan->tgl_pembayaran?->translatedFormat('H:i') ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px;">
        <table class="summary">
            <tr>
                <td class="bold">Total Transaksi</td>
                <td class="right bold">{{ $pesanans->count() }} pesanan</td>
            </tr>
            <tr>
                <td class="bold">Total Omzet</td>
                <td class="right bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top:20px; font-size:11px; color:#555;">
        Dicetak: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
