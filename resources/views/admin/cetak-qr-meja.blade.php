<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak QR Code Meja — {{ config('app.name') }}</title>
    <style>
        /* A4 = 210mm x 297mm. Margin 12mm aman untuk printer rumah. */
        @page { size: A4; margin: 12mm; }

        * { box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 0;
            color: #1a1a1a;
        }

        /* Toolbar hanya muncul di layar, tidak ikut tercetak */
        .toolbar {
            background: #fff3e0;
            border-bottom: 1px solid #ffcc80;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .btn-print {
            background: #380000;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-print:hover { background: #2A0000; }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10mm;
            padding: 12mm;
        }

        .card {
            border: 2px dashed #d4a373;
            border-radius: 12px;
            padding: 12mm 8mm;
            text-align: center;
            page-break-inside: avoid;
            background: white;
        }

        .brand {
            font-size: 11pt;
            color: #6b4423;
            letter-spacing: 3px;
            font-weight: 600;
        }
        .meja-label {
            font-size: 11pt;
            color: #999;
            margin: 6mm 0 1mm;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .meja-nomor {
            font-size: 36pt;
            font-weight: 800;
            color: #380000;
            line-height: 1;
            margin-bottom: 4mm;
        }
        .qr {
            width: 55mm;
            height: 55mm;
            margin: 4mm auto;
            padding: 3mm;
            background: white;
        }
        .qr svg { width: 100%; height: 100%; display: block; }

        .hint {
            font-size: 10pt;
            color: #666;
            margin-top: 4mm;
            font-weight: 500;
        }
        .url {
            font-size: 7pt;
            color: #aaa;
            word-break: break-all;
            margin-top: 2mm;
            font-family: "Courier New", monospace;
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        @media print {
            .toolbar { display: none; }
            body { background: white; }
            .grid { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div>
            <strong>{{ $meja->count() }} QR Code</strong> siap dicetak.
            <span style="color: #666; margin-left: 8px;">
                Periksa nomor &amp; URL sebelum cetak.
            </span>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.meja.index') }}"
               style="background:white; color:#380000; border:1px solid #380000; padding:9px 16px; border-radius:8px; text-decoration:none; font-weight:500;">
                ← Kembali
            </a>
            <button class="btn-print" onclick="window.print()">
                🖨️ Cetak Sekarang
            </button>
        </div>
    </div>

    @if ($meja->isEmpty())
        <div class="empty">
            <p>Belum ada meja terdaftar.</p>
            <a href="{{ route('admin.meja.index') }}" style="color:#380000;">Tambah meja dulu →</a>
        </div>
    @else
        <div class="grid">
            @foreach ($meja as $m)
                <div class="card">
                    <div class="brand">{{ strtoupper(config('app.name', 'KOHVITO')) }} CAFÉ</div>
                    <div class="meja-label">Nomor Meja</div>
                    <div class="meja-nomor">{{ $m->no_meja }}</div>
                    <div class="qr">{!! $m->qr_svg !!}</div>
                    <div class="hint">Scan untuk memesan</div>
                    <div class="url">{{ $m->scan_url }}</div>
                </div>
            @endforeach
        </div>
    @endif

</body>
</html>
