<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Xendit\Invoice;
use Xendit\Xendit;

class KonsumenApiController extends Controller
{
    /**
     * Landing page konsumen setelah scan QR: simpan id_meja ke session,
     * kembalikan data meja + katalog menu per kategori.
     */
    public function beranda(string $noMeja): JsonResponse
    {
        $meja = Meja::where('no_meja', $noMeja)->first();

        if (! $meja) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Meja tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        session(['id_meja' => $meja->id_meja]);

        $kategoris = KategoriMenu::with(['menu' => function ($query) {
            $query->where('status_ketersediaan', 'tersedia');
        }])->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'meja'      => $meja,
                'kategoris' => $kategoris,
            ],
        ]);
    }

    /**
     * Daftar menu tersedia, opsional filter by id_kategori.
     */
    public function getMenu(Request $request): JsonResponse
    {
        $request->validate([
            'id_kategori' => ['sometimes', 'integer', 'exists:kategori_menu,id_kategori'],
        ]);

        $query = Menu::where('status_ketersediaan', 'tersedia')
            ->select('id_menu', 'nama_menu', 'deskripsi', 'harga', 'gambar_menu', 'id_kategori');

        if ($request->filled('id_kategori')) {
            $query->where('id_kategori', $request->id_kategori);
        }

        $menus = $query->get();

        return response()->json([
            'status' => 'success',
            'data'   => $menus,
        ]);
    }

    /**
     * Detail satu item menu.
     */
    public function detailMenu(string $id): JsonResponse
    {
        $menu = Menu::select('id_menu', 'nama_menu', 'deskripsi', 'harga', 'gambar_menu', 'id_kategori')
            ->find($id);

        if (! $menu) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Menu tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $menu,
        ]);
    }

    /**
     * Tampilkan isi keranjang dari session.
     */
    public function keranjang(): JsonResponse
    {
        $keranjang  = session('keranjang', []);
        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return response()->json([
            'status' => 'success',
            'data'   => [
                'keranjang'  => $keranjang,
                'totalHarga' => (int) $totalHarga,
            ],
        ]);
    }

    /**
     * Tambah atau update item di keranjang (session).
     */
    public function tambahKeranjang(Request $request): JsonResponse
    {
        $request->validate([
            'id_menu' => ['required', 'integer', 'exists:menu,id_menu'],
            'jumlah'  => ['required', 'integer', 'min:1', 'max:99'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $menu      = Menu::findOrFail($request->id_menu);
        $keranjang = session('keranjang', []);
        $idMenu    = (int) $request->id_menu;
        $jumlah    = (int) $request->jumlah;

        if (isset($keranjang[$idMenu])) {
            $keranjang[$idMenu]['jumlah']  += $jumlah;
            $keranjang[$idMenu]['subtotal'] = $keranjang[$idMenu]['harga'] * $keranjang[$idMenu]['jumlah'];
        } else {
            $keranjang[$idMenu] = [
                'id_menu'   => $idMenu,
                'nama_menu' => $menu->nama_menu,
                'harga'     => $menu->harga,
                'jumlah'    => $jumlah,
                'catatan'   => $request->catatan,
                'subtotal'  => $menu->harga * $jumlah,
            ];
        }

        session(['keranjang' => $keranjang]);

        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return response()->json([
            'status'  => 'success',
            'message' => 'Item ditambahkan ke keranjang',
            'data'    => [
                'keranjang'  => $keranjang,
                'totalHarga' => (int) $totalHarga,
            ],
        ]);
    }

    /**
     * Update jumlah item di keranjang; jika jumlah = 0, item dihapus.
     */
    public function updateKeranjang(Request $request): JsonResponse
    {
        $request->validate([
            'id_menu' => ['required', 'integer'],
            'jumlah'  => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $keranjang = session('keranjang', []);
        $idMenu    = (int) $request->id_menu;

        if (! isset($keranjang[$idMenu])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Item tidak ditemukan di keranjang',
                'data'    => null,
            ], 404);
        }

        if ((int) $request->jumlah === 0) {
            unset($keranjang[$idMenu]);
        } else {
            $keranjang[$idMenu]['jumlah']  = (int) $request->jumlah;
            $keranjang[$idMenu]['subtotal'] = $keranjang[$idMenu]['harga'] * $keranjang[$idMenu]['jumlah'];
        }

        session(['keranjang' => $keranjang]);

        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return response()->json([
            'status'  => 'success',
            'message' => 'Keranjang berhasil diupdate',
            'data'    => [
                'keranjang'  => $keranjang,
                'totalHarga' => (int) $totalHarga,
            ],
        ]);
    }

    /**
     * Update catatan/notes per item di keranjang.
     */
    public function updateNotes(Request $request): JsonResponse
    {
        $request->validate([
            'id_menu' => ['required', 'integer'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $keranjang = session('keranjang', []);
        $idMenu    = (int) $request->id_menu;

        if (! isset($keranjang[$idMenu])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Item tidak ditemukan di keranjang',
                'data'    => null,
            ], 404);
        }

        $keranjang[$idMenu]['catatan'] = $request->catatan;
        session(['keranjang' => $keranjang]);

        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return response()->json([
            'status'  => 'success',
            'message' => 'Catatan berhasil disimpan',
            'data'    => [
                'keranjang'  => $keranjang,
                'totalHarga' => (int) $totalHarga,
            ],
        ]);
    }

    /**
     * Finalisasi pesanan: buat record Pesanan + DetailPesanan, kosongkan keranjang.
     */
    public function storePesan(Request $request): JsonResponse
    {
        if (! session('id_meja')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi meja tidak valid. Silakan scan QR Code kembali.',
                'data'    => null,
            ], 422);
        }

        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Keranjang kosong. Tambahkan menu terlebih dahulu.',
                'data'    => null,
            ], 422);
        }

        $request->validate([
            'nama_konsumen' => ['required', 'string', 'max:255'],
        ]);

        $noPesanan  = 'PS-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        try {
            DB::transaction(function () use ($noPesanan, $totalHarga, $keranjang, $request) {
                Pesanan::create([
                    'no_pesanan'        => $noPesanan,
                    'id_user'           => null,
                    'id_meja'           => session('id_meja'),
                    'nama_konsumen'     => $request->nama_konsumen,
                    'total_harga'       => $totalHarga,
                    'status_pembayaran' => 'belum bayar',
                    'status_pesanan'    => 'menunggu konfirmasi',
                ]);

                foreach ($keranjang as $item) {
                    DetailPesanan::create([
                        'no_pesanan' => $noPesanan,
                        'id_menu'    => $item['id_menu'],
                        'jumlah'     => $item['jumlah'],
                        'catatan'    => $item['catatan'],
                        'subtotal'   => $item['subtotal'],
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat pesanan, coba lagi.',
                'data'    => null,
            ], 500);
        }

        session()->forget('keranjang');

        return response()->json([
            'status'  => 'success',
            'message' => 'Pesanan berhasil dibuat',
            'data'    => [
                'no_pesanan'  => $noPesanan,
                'total_harga' => (int) $totalHarga,
            ],
        ], 201);
    }

    /**
     * Cek status pesanan berdasarkan no_pesanan.
     */
    public function statusPesanan(string $noPesanan): JsonResponse
    {
        $pesanan = Pesanan::select('no_pesanan', 'status_pesanan', 'status_pembayaran', 'tgl_pembayaran')
            ->find($noPesanan);

        if (! $pesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'no_pesanan'        => $pesanan->no_pesanan,
                'status_pesanan'    => $pesanan->status_pesanan,
                'status_pembayaran' => $pesanan->status_pembayaran,
                'tgl_pembayaran'    => $pesanan->tgl_pembayaran,
            ],
        ]);
    }

    /**
     * Buat invoice Xendit untuk pembayaran pesanan.
     */
    public function bayar(Request $request): JsonResponse
    {
        $request->validate([
            'no_pesanan' => ['required', 'string'],
        ]);

        $pesanan = Pesanan::find($request->no_pesanan);

        if (! $pesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        if ($pesanan->status_pembayaran === 'lunas') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan sudah lunas',
                'data'    => null,
            ], 422);
        }

        try {
            Xendit::setApiKey(config('services.xendit.api_key'));

            $invoice = Invoice::create([
                'external_id'          => $pesanan->no_pesanan,
                'amount'               => $pesanan->total_harga,
                'payer_email'          => 'konsumen@kohvito.com',
                'description'          => 'Pembayaran #' . $pesanan->no_pesanan,
                'invoice_duration'     => 86400,
                'currency'             => 'IDR',
                'customer'             => ['given_names' => $pesanan->nama_konsumen],
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Invoice berhasil dibuat',
                'data'    => [
                    'invoice_url' => $invoice['invoice_url'],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat invoice pembayaran: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}
