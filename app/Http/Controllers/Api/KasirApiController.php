<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class KasirApiController
 * 
 * Controller API ini melayani modul antrean dapur dan transaksi Kasir (RESTful API).
 * Mengelompokkan fungsionalitas menjadi 4 pilar utama:
 * 1. **Dashboard & Antrean**: Menghitung kuantitas antrean pesanan aktif harian.
 * 2. **Daftar Pesanan Dapur**: Menarik daftar pesanan aktif ('menunggu konfirmasi', 'diproses').
 * 3. **Kontrol Status**: Pembaruan status progres pengerjaan menu dapur.
 * 4. **Histori & Pencarian**: Riwayat pesanan selesai hari ini beserta detail histori transaksional.
 *
 * @package App\Http\Controllers\Api
 */
class KasirApiController extends Controller
{
    use ApiResponses; // Menyertakan trait untuk standarisasi format respon JSON

    /**
     * Menyediakan data ringkasan kuantitas antrean pesanan hari ini berdasarkan statusnya.
     *
     * @return \Illuminate\Http\JsonResponse Respon sukses berisikan ringkasan data antrean
     */
    public function dashboard(): JsonResponse
    {
        $today = Carbon::today();

        // 1. Hitung jumlah pesanan menunggu konfirmasi hari ini
        $menunggu = Pesanan::whereDate('tgl_pembayaran', $today)
            ->where('status_pesanan', 'menunggu konfirmasi')
            ->count();

        // 2. Hitung jumlah pesanan sedang diproses hari ini
        $diproses = Pesanan::whereDate('tgl_pembayaran', $today)
            ->where('status_pesanan', 'diproses')
            ->count();

        // 3. Hitung jumlah pesanan selesai saji hari ini
        $selesai  = Pesanan::whereDate('tgl_pembayaran', $today)
            ->where('status_pesanan', 'selesai')
            ->count();

        return $this->successResponse([
            'menunggu' => $menunggu,
            'diproses' => $diproses,
            'selesai'  => $selesai,
        ]);
    }

    /**
     * Menampilkan daftar pesanan yang sedang aktif berjalan di antrean (menunggu konfirmasi & diproses).
     *
     * @return \Illuminate\Http\JsonResponse Respon sukses berisi list pesanan aktif
     */
    public function indexPesanan(): JsonResponse
    {
        // 1. Tarik seluruh antrean aktif beserta eager load meja dan detail item menu terikat
        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])
            ->orderBy('no_pesanan', 'desc')
            ->get();

        return $this->successResponse($pesanans);
    }

    /**
     * Tampilkan detail rincian satu transaksi pesanan aktif berdasarkan nomor uniknya.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\JsonResponse Respon berisi data pesanan, atau 404 jika tidak ditemukan
     */
    public function detailPesanan(string $noPesanan): JsonResponse
    {
        // 1. Cari data pesanan terikat
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->first();

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan', 404);
        }

        return $this->successResponse($pesanan);
    }

    /**
     * Memperbarui status progres pengerjaan menu dapur secara dinamis.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa status baru
     * @param  string  $noPesanan  Nomor transaksi pesanan target
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatusPesanan(Request $request, string $noPesanan): JsonResponse
    {
        // 1. Validasi input status wajib dikirimkan dan dibatasi nilainya
        $request->validate([
            'status' => ['required', 'in:diproses,selesai'],
        ]);

        $pesanan = Pesanan::where('no_pesanan', $noPesanan)->first();

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan', 404);
        }

        // 2. Ubah dan simpan status pesanan baru
        $pesanan->status_pesanan = $request->status;
        $pesanan->save();

        return $this->successResponse([
            'status_pesanan' => $pesanan->status_pesanan
        ], 'Status pesanan berhasil diupdate');
    }

    /**
     * Menampilkan riwayat (histori) transaksi yang selesai hari ini beserta kalkulasi omzet.
     * Mendukung pemfilteran dinamis berbasis kata kunci pencarian.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa parameter search
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexHistori(Request $request): JsonResponse
    {
        $today = Carbon::today();

        // 1. Inisialisasi query dasar: pesanan selesai hari ini
        $query = Pesanan::with('meja')
            ->where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today);

        // 2. Tambahkan pencarian kata kunci multi-kolom
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', '%' . $search . '%')
                  ->orWhere('nama_konsumen', 'like', '%' . $search . '%');
            });
        }

        $pesanans   = $query->get();
        $totalOmzet = $pesanans->sum('total_harga');

        return $this->successResponse([
            'pesanans'   => $pesanans,
            'totalOmzet' => (int) $totalOmzet,
        ]);
    }

    /**
     * Tampilkan detail rincian satu transaksi pesanan selesai.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailHistori(string $noPesanan): JsonResponse
    {
        // 1. Cari data pesanan selesai terikat beserta detail item menunya
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->where('status_pesanan', 'selesai')
            ->first();

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan', 404);
        }

        return $this->successResponse($pesanan);
    }
}
