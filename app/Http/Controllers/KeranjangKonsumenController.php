<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutCartRequest;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Requests\UpdateCartNotesRequest;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class KeranjangKonsumenController
 * 
 * Controller ini mengatur manajemen keranjang belanja (Shopping Cart) berbasis sesi (session-based)
 * dan alur proses checkout hingga finalisasi transaksi bagi konsumen.
 *
 * Menggunakan pendekatan penyimpanan session server agar menghemat I/O database dan mencegah
 * penumpukan data transaksi sampah dari pengguna yang batal melakukan checkout.
 * Menerapkan enkripsi kunci unik (MD5 Cart-Key) untuk kustomisasi pesanan yang sama namun dengan
 * catatan/tambahan harga yang berbeda di dalam satu baris daftar belanjaan.
 *
 * @package App\Http\Controllers
 */
class KeranjangKonsumenController extends Controller
{
    /**
     * Tampilkan isi keranjang belanja konsumen beserta kalkulasi subtotal tagihan.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Ambil data keranjang belanja aktif dari session, default berbentuk array kosong
        $keranjang  = session('keranjang', []);
        
        // 2. Kalkulasi akumulasi total harga belanjaan dari seluruh item di keranjang
        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return view('konsumen.keranjang', compact('keranjang', 'totalHarga'));
    }

    /**
     * Tambah item menu baru ke keranjang belanja session.
     * Menerapkan pembentukan Cart Key berbasis MD5 hash agar kombinasi produk, kustomisasi catatan,
     * dan tambahan biaya opsional dapat tersimpan secara unik (tidak bertabrakan).
     *
     * @param  \App\Http\Requests\StoreCartItemRequest  $request  Request validasi tambah keranjang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTambahKeranjang(StoreCartItemRequest $request): RedirectResponse
    {
        $menu      = Menu::findOrFail($request->id_menu);
        $keranjang = session('keranjang', []);
        
        $idMenu        = (int) $request->id_menu;
        $jumlah        = (int) $request->jumlah;
        $catatan       = $request->catatan;
        $hargaTambahan = (int) $request->input('harga_tambahan', 0);
        
        // 1. Kalkulasi harga total per porsi (harga dasar menu + biaya opsional kustom)
        $hargaSatuan   = (int) $menu->harga + $hargaTambahan;
        
        // 2. Bangkitkan Cart Key Unik berbasis hashing MD5.
        //    Menjamin jika konsumen memesan Nasi Goreng (pedas) dan Nasi Goreng (tidak pedas),
        //    keduanya tersimpan secara terpisah dalam list keranjang.
        $cartKey = $idMenu . ':' . md5(($catatan ?? '') . '|' . $hargaTambahan);

        // 3. Jika item dengan spesifikasi kustom yang sama persis sudah ada di keranjang
        if (isset($keranjang[$cartKey])) {
            // 4. Cukup akumulasikan kuantitas jumlah porsi belanjaan
            $keranjang[$cartKey]['jumlah']   += $jumlah;
            $keranjang[$cartKey]['subtotal']  = $keranjang[$cartKey]['harga'] * $keranjang[$cartKey]['jumlah'];
            if ($catatan) {
                $keranjang[$cartKey]['catatan'] = $catatan;
            }
        } else {
            // 5. Jika merupakan item kustom baru, tambahkan baris array baru ke dalam keranjang session
            $keranjang[$cartKey] = [
                'id_menu'         => $idMenu,
                'nama_menu'       => $menu->nama_menu,
                'harga'           => $hargaSatuan,
                'harga_dasar'     => (int) $menu->harga,
                'harga_tambahan'  => $hargaTambahan,
                'jumlah'          => $jumlah,
                'catatan'         => $catatan ?? null,
                'subtotal'        => $hargaSatuan * $jumlah,
            ];
        }

        // 6. Simpan kembali struktur array keranjang belanja ke session server
        session(['keranjang' => $keranjang]);

        return redirect()->back()->with('success', 'Item ditambahkan ke keranjang');
    }

    /**
     * Memperbarui instruksi/catatan khusus (notes) untuk item tertentu di keranjang belanja.
     *
     * @param  \App\Http\Requests\UpdateCartNotesRequest  $request  Request validasi update notes
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotesPesanan(UpdateCartNotesRequest $request): RedirectResponse
    {
        $keranjang = session('keranjang', []);
        $cartKey   = $request->input('cart_key', (string) $request->id_menu);

        // 1. Validasi eksistensi item di dalam keranjang belanja
        if (! isset($keranjang[$cartKey])) {
            return redirect()->route('konsumen.keranjang')
                ->withErrors(['item' => 'Item tidak ditemukan di keranjang.']);
        }

        // 2. Ubah catatan item
        $keranjang[$cartKey]['catatan'] = $request->catatan;
        
        // 3. Simpan perubahan ke session
        session(['keranjang' => $keranjang]);

        return redirect()->route('konsumen.keranjang')->with('success', 'Catatan disimpan');
    }

    /**
     * Memperbarui kuantitas (jumlah porsi) item di keranjang belanja.
     * Secara otomatis menghapus item dari list keranjang jika jumlah porsi disetel ke angka 0.
     *
     * @param  \App\Http\Requests\UpdateCartItemRequest  $request  Request validasi update kuantitas
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePesanan(UpdateCartItemRequest $request): RedirectResponse
    {
        $keranjang = session('keranjang', []);
        $cartKey   = $request->input('cart_key', (string) $request->id_menu);
        $jumlah    = (int) $request->jumlah;

        // 1. Cek eksistensi item di keranjang
        if (! isset($keranjang[$cartKey])) {
            return redirect()->route('konsumen.keranjang')
                ->withErrors(['item' => 'Item tidak ditemukan di keranjang.']);
        }

        // 2. Jika disetel ke kuantitas 0, lakukan penghapusan baris belanjaan (unset)
        if ($jumlah === 0) {
            unset($keranjang[$cartKey]);
        } else {
            // 3. Jika tidak 0, perbarui kuantitas dan hitung ulang nilai subtotal
            $keranjang[$cartKey]['jumlah']   = $jumlah;
            $keranjang[$cartKey]['subtotal'] = $keranjang[$cartKey]['harga'] * $jumlah;
        }

        // 4. Rekam pembaruan di session
        session(['keranjang' => $keranjang]);

        return redirect()->route('konsumen.keranjang');
    }

    /**
     * Memproses penyelesaian belanja (Checkout): Menulis data pesanan & detil ke database.
     * Menerapkan transaksi database atomik (DB::transaction) agar pendaftaran transaksi aman dari
     * risiko kegagalan parsial (menghindari pesanan terdaftar tetapi item detail kosong).
     *
     * @param  \App\Http\Requests\CheckoutCartRequest  $request  Request validasi nama konsumen
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePesan(CheckoutCartRequest $request): RedirectResponse
    {
        // 1. Validasi sesi meja fisik pemesan wajib aktif (hasil scan QR valid)
        if (! session('id_meja')) {
            return redirect()->back()
                ->withErrors(['id_meja' => 'Sesi meja tidak valid. Silakan scan QR Code kembali.']);
        }

        $keranjang = session('keranjang', []);

        // 2. Cek agar keranjang belanja tidak kosong saat checkout dipicu
        if (empty($keranjang)) {
            return redirect()->route('konsumen.keranjang')
                ->with('error', 'Keranjang kosong. Tambahkan menu terlebih dahulu.');
        }

        // 3. Bangkitkan nomor unik transaksi kustom global (no_pesanan)
        $noPesanan     = 'PS-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
        
        // 4. Perhitungan komponen harga: Subtotal, PPN 11%, dan Total Akhir
        $subtotalHarga = array_sum(array_column($keranjang, 'subtotal'));
        $ppnAmount     = (int) round($subtotalHarga * 0.11); // Skema PPN 11% reguler
        $totalHarga    = $subtotalHarga + $ppnAmount;
        $namaKonsumen  = $request->nama_konsumen;

        try {
            // 5. Eksekusi transaksi database secara aman dan terisolasi
            DB::transaction(function () use ($noPesanan, $totalHarga, $keranjang, $request, $namaKonsumen) {
                // A. Buat baris header pesanan baru
                Pesanan::create([
                    'no_pesanan'        => $noPesanan,
                    'id_user'           => null, // Di-set kosong terlebih dahulu (belum ditangani kasir)
                    'id_meja'           => session('id_meja'),
                    'nama_konsumen'     => $namaKonsumen,
                    'total_harga'       => $totalHarga,
                    'status_pembayaran' => 'menunggu', // State awal: menunggu pelunasan transfer
                    'status_pesanan'    => 'menunggu konfirmasi', // State awal dapur
                    'catatan_pesanan'   => $request->catatan_pesanan,
                ]);

                // B. Buat baris rincian detail makanan/minuman yang dibeli
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
        } catch (\Illuminate\Database\QueryException $e) {
            // 6. Kembalikan peringatan error jika proses query transaksi database gagal di tengah jalan
            return redirect()->back()->withErrors(['order' => 'Gagal membuat pesanan, coba lagi.']);
        }

        // 7. Bersihkan keranjang belanja session karena checkout telah sukses dikomit
        session()->forget('keranjang');
        
        // 8. Kunci nomor pesanan baru tersebut di session pengguna aktif
        session(['no_pesanan_baru' => $noPesanan]);

        // 9. Rekam nomor transaksi ini ke array riwayat transaksi lokal browser konsumen.
        //    Digunakan agar konsumen tetap dapat mengakses/melacak pesanan lamanya
        //    meskipun status session pembelanjaan telah di-forget.
        $riwayat   = session('riwayat_pesanan', []);
        $riwayat[] = $noPesanan;
        session(['riwayat_pesanan' => array_values(array_unique($riwayat))]);

        // 10. Alihkan pembeli secara langsung menuju halaman pembayaran QRIS
        return redirect()->route('konsumen.pembayaran', $noPesanan);
    }
}
