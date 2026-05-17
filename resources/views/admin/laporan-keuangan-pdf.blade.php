<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Kohvito</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #000; margin: 0; padding: 24px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px solid #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 6px 8px; background: #380000; color: #fff; border: 1px solid #380000; font-size: 11px; }
        td { padding: 5px 8px; border: 1px solid #ddd; font-size: 11px; vertical-align: top; }
        .summary { margin-top: 16px; }
        .summary td { border: none; padding: 4px 0; font-size: 12px; }
    </style>
</head>
<body>
    <div class="center bold" style="font-size:18px; color:#380000; margin-bottom:4px;">KAFE KOHVITO</div>
    <div class="center" style="font-size:13px;">Laporan Keuangan</div>
    <div class="center" style="font-size:11px; color:#555; margin-bottom:6px;">
        Periode: {{ $tanggalMulai->translatedFormat('d F Y') }}
        @if ($tanggalMulai->format('Y-m-d') !== $tanggalSelesai->format('Y-m-d'))
            &mdash; {{ $tanggalSelesai->translatedFormat('d F Y') }}
        @endif
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>No Pesanan</th>
                <th>Konsumen</th>
                <th style="width:50px;">Meja</th>
                <th style="width:110px;">Waktu Bayar</th>
                <th class="right" style="width:110px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pesanans as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->no_pesanan }}</td>
                    <td>{{ $row->nama_konsumen ?? '-' }}</td>
                    <td>{{ $row->meja?->no_meja ?? '-' }}</td>
                    <td>{{ optional($row->tgl_pembayaran)->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="center" style="padding:24px; color:#888;">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="bold">Total Transaksi</td>
            <td class="right">{{ $totalTransaksi }} transaksi</td>
        </tr>
        <tr>
            <td class="bold">Total Omzet</td>
            <td class="right bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div style="margin-top: 32px; text-align:right; font-size:11px; color:#555;">
        Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i') }}
    </div>
</body>
</html>
