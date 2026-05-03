<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pesanan — {{ $pesanan->no_pesanan }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; background: #f5f5f5; color: #333; padding: 1rem; }
        .card { background: #fff; border-radius: 8px; padding: 1.5rem; max-width: 480px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h1 { font-size: 1.2rem; margin-bottom: .25rem; }
        .no-pesanan { font-size: .8rem; color: #888; margin-bottom: 1.5rem; }
        .status-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .badge { padding: .3rem .7rem; border-radius: 20px; font-size: .8rem; font-weight: 600; }
        .badge-menunggu { background: #fff3cd; color: #856404; }
        .badge-diproses { background: #cff4fc; color: #055160; }
        .badge-selesai  { background: #d1e7dd; color: #0a3622; }
        .badge-lunas    { background: #d1e7dd; color: #0a3622; }
        .badge-belum    { background: #f8d7da; color: #58151c; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; margin: 1rem 0; }
        th, td { padding: .5rem .25rem; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; }
        td.right { text-align: right; }
        .total-row td { font-weight: 700; border-top: 2px solid #ddd; border-bottom: none; }
        .btn { display: block; width: 100%; padding: .75rem; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; text-align: center; text-decoration: none; margin-top: .75rem; }
        .btn-primary { background: #0d6efd; color: #fff; }
        .btn.disabled { opacity: .5; pointer-events: none; }
        .info-meja { font-size: .85rem; color: #555; margin-bottom: 1rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>Tracking Pesanan</h1>
    <div class="no-pesanan">{{ $pesanan->no_pesanan }}</div>
    <div class="info-meja">Meja: <strong>{{ $pesanan->meja->no_meja ?? '-' }}</strong> &nbsp;|&nbsp; {{ $pesanan->nama_konsumen }}</div>

    <div id="status-section">
        <div class="status-row">
            <span>Status Pesanan</span>
            <span id="badge-pesanan" class="badge {{ $pesanan->status_pesanan === 'selesai' ? 'badge-selesai' : ($pesanan->status_pesanan === 'diproses' ? 'badge-diproses' : 'badge-menunggu') }}">
                {{ ucfirst($pesanan->status_pesanan) }}
            </span>
        </div>
        <div class="status-row">
            <span>Status Pembayaran</span>
            <span id="badge-bayar" class="badge {{ $pesanan->status_pembayaran === 'lunas' ? 'badge-lunas' : 'badge-belum' }}">
                {{ ucfirst($pesanan->status_pembayaran) }}
            </span>
        </div>
    </div>

    <table>
        <thead>
            <tr><th>Menu</th><th>Qty</th><th class="right">Subtotal</th></tr>
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
                <td>{{ $item->jumlah }}</td>
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

    <a id="btn-kuitansi"
       href="{{ route('konsumen.pesanan.kuitansi', $pesanan->no_pesanan) }}"
       class="btn btn-primary {{ $pesanan->status_pembayaran !== 'lunas' ? 'disabled' : '' }}"
       @if($pesanan->status_pembayaran !== 'lunas') aria-disabled="true" @endif>
        Unduh Kuitansi
    </a>
</div>

<script>
(function () {
    var statusUrl = "{{ route('konsumen.pesanan.status', $pesanan->no_pesanan) }}";
    var isPaid    = {{ $pesanan->status_pembayaran === 'lunas' ? 'true' : 'false' }};

    if (isPaid) return;

    function updateBadge(id, text, classMap) {
        var el = document.getElementById(id);
        el.textContent = text.charAt(0).toUpperCase() + text.slice(1);
        el.className = 'badge';
        el.classList.add(classMap[text] || 'badge-menunggu');
    }

    function poll() {
        fetch(statusUrl)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                updateBadge('badge-pesanan', data.status_pesanan, {
                    'selesai': 'badge-selesai',
                    'diproses': 'badge-diproses',
                    'menunggu konfirmasi': 'badge-menunggu'
                });
                updateBadge('badge-bayar', data.status_pembayaran, {
                    'lunas': 'badge-lunas',
                    'menunggu': 'badge-belum'
                });

                if (data.status_pembayaran === 'lunas') {
                    var btn = document.getElementById('btn-kuitansi');
                    btn.classList.remove('disabled');
                    btn.removeAttribute('aria-disabled');
                    isPaid = true;
                    return;
                }

                if (data.status_pesanan !== 'selesai') {
                    setTimeout(poll, 5000);
                }
            })
            .catch(function () { setTimeout(poll, 10000); });
    }

    setTimeout(poll, 5000);
})();
</script>
</body>
</html>
