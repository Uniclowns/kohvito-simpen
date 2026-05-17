<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KasirApiController extends Controller
{
    /**
     * Ringkasan dashboard kasir: jumlah pesanan per status hari ini.
     */
    public function dashboard(): JsonResponse
    {
        $today = Carbon::today();

        $menunggu = Pesanan::whereDate('tgl_pembayaran', $today)
            ->where('status_pesanan', 'menunggu konfirmasi')
            ->count();

        $diproses = Pesanan::whereDate('tgl_pembayaran', $today)
            ->where('status_pesanan', 'diproses')
            ->count();

        $selesai = Pesanan::whereDate('tgl_pembayaran', $today)
            ->where('status_pesanan', 'selesai')
            ->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'menunggu' => $menunggu,
                'diproses' => $diproses,
                'selesai'  => $selesai,
            ],
        ]);
    }

    /**
     * Daftar pesanan aktif (menunggu konfirmasi & diproses).
     */
    public function indexPesanan(): JsonResponse
    {
        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])
            ->orderBy('no_pesanan', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $pesanans,
        ]);
    }

    /**
     * Detail satu pesanan berdasarkan no_pesanan.
     */
    public function detailPesanan(string $noPesanan): JsonResponse
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->first();

        if (! $pesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $pesanan,
        ]);
    }

    /**
     * Update status pesanan (diproses atau selesai).
     */
    public function updateStatusPesanan(Request $request, string $noPesanan): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:diproses,selesai'],
        ]);

        $pesanan = Pesanan::where('no_pesanan', $noPesanan)->first();

        if (! $pesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        $pesanan->status_pesanan = $request->status;
        $pesanan->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Status pesanan berhasil diupdate',
            'data'    => ['status_pesanan' => $pesanan->status_pesanan],
        ]);
    }

    /**
     * Histori pesanan selesai hari ini, dengan opsional pencarian.
     */
    public function indexHistori(Request $request): JsonResponse
    {
        $today = Carbon::today();

        $query = Pesanan::with('meja')
            ->where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', '%' . $search . '%')
                  ->orWhere('nama_konsumen', 'like', '%' . $search . '%');
            });
        }

        $pesanans   = $query->get();
        $totalOmzet = $pesanans->sum('total_harga');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'pesanans'   => $pesanans,
                'totalOmzet' => (int) $totalOmzet,
            ],
        ]);
    }

    /**
     * Detail histori satu pesanan selesai.
     */
    public function detailHistori(string $noPesanan): JsonResponse
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->where('status_pesanan', 'selesai')
            ->first();

        if (! $pesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $pesanan,
        ]);
    }
}
