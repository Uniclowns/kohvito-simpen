<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kasir Kohvito</title>
    <style>
        body {
            color: #000;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 24px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #380000;
            border: 1px solid #380000;
            color: #fff;
            font-size: 11px;
            padding: 6px 8px;
            text-align: left;
        }

        td {
            border: 1px solid #ddd;
            font-size: 11px;
            padding: 5px 8px;
            vertical-align: top;
        }

        .summary {
            margin-top: 16px;
        }

        .summary td {
            border: none;
            font-size: 12px;
            padding: 4px 0;
        }
    </style>
</head>
<body>
    <div class="center bold" style="font-size: 18px; color: #380000; margin-bottom: 4px;">KOHVITO CAFFEE</div>
    <div class="center" style="font-size: 13px;">Laporan Transaksi Kasir</div>
    <div class="center" style="font-size: 11px; color: #555; margin-bottom: 6px;">
        Periode: {{ $tanggalMulai->translatedFormat('d F Y') }}
        @if ($tanggalMulai->format('Y-m-d') !== $tanggalSelesai->format('Y-m-d'))
            &mdash; {{ $tanggalSelesai->translatedFormat('d F Y') }}
        @endif
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>No Pesanan</th>
                <th>Konsumen</th>
                <th>Kasir</th>
                <th style="width: 50px;">Meja</th>
                <th style="width: 90px;">Waktu</th>
                <th class="right" style="width: 100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pesanan as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->no_pesanan }}</td>
                    <td>{{ $row->nama_konsumen ?? '-' }}</td>
                    <td>{{ $row->user?->nama_lengkap ?? '-' }}</td>
                    <td>{{ $row->meja?->no_meja ?? '-' }}</td>
                    <td>{{ optional($row->tgl_pembayaran)->format('d/m H:i') ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center" style="padding: 24px; color: #888;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="bold">Total Transaksi</td>
            <td class="right">{{ $pesanan->count() }} transaksi</td>
        </tr>
        <tr>
            <td class="bold">Total Pendapatan</td>
            <td class="right bold">Rp {{ number_format($pesanan->sum('total_harga'), 0, ',', '.') }}</td>
        </tr>
    </table>

    <div style="margin-top: 32px; text-align: right; font-size: 11px; color: #555;">
        Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i') }}
    </div>
</body>
</html>
