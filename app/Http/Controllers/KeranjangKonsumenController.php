<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KeranjangKonsumenController extends Controller
{
    /**
     * Tampilkan isi keranjang belanja (session-based).
     */
    public function index()
    {
        $keranjang  = session('keranjang', []);
        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return view('konsumen.keranjang', compact('keranjang', 'totalHarga'));
    }

    /**
     * Tambah item menu ke keranjang (session).
     */
    public function storeTambahKeranjang(Request $request)
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
        $catatan   = $request->catatan;

        if (isset($keranjang[$idMenu])) {
            $keranjang[$idMenu]['jumlah']   += $jumlah;
            $keranjang[$idMenu]['subtotal']  = $keranjang[$idMenu]['harga'] * $keranjang[$idMenu]['jumlah'];
            if ($catatan) {
                $keranjang[$idMenu]['catatan'] = $catatan;
            }
        } else {
            $keranjang[$idMenu] = [
                'id_menu'   => $idMenu,
                'nama_menu' => $menu->nama_menu,
                'harga'     => $menu->harga,
                'jumlah'    => $jumlah,
                'catatan'   => $catatan ?? null,
                'subtotal'  => $menu->harga * $jumlah,
            ];
        }

        session(['keranjang' => $keranjang]);

        return redirect()->back()->with('success', 'Item ditambahkan ke keranjang');
    }

    /**
     * Update catatan/notes kustomisasi per item di keranjang.
     */
    public function updateNotesPesanan(Request $request)
    {
        $request->validate([
            'id_menu' => ['required', 'integer'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $keranjang = session('keranjang', []);
        $idMenu    = (int) $request->id_menu;

        if (! isset($keranjang[$idMenu])) {
            return redirect()->route('konsumen.keranjang')->withErrors(['item' => 'Item tidak ditemukan di keranjang.']);
        }

        $keranjang[$idMenu]['catatan'] = $request->catatan;
        session(['keranjang' => $keranjang]);

        return redirect()->route('konsumen.keranjang')->with('success', 'Catatan disimpan');
    }

    /**
     * Update jumlah porsi atau hapus item di keranjang.
     */
    public function updatePesanan(Request $request)
    {
        $request->validate([
            'id_menu' => ['required', 'integer'],
            'jumlah'  => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $keranjang = session('keranjang', []);
        $idMenu    = (int) $request->id_menu;
        $jumlah    = (int) $request->jumlah;

        if (! isset($keranjang[$idMenu])) {
            return redirect()->route('konsumen.keranjang')->withErrors(['item' => 'Item tidak ditemukan di keranjang.']);
        }

        if ($jumlah === 0) {
            unset($keranjang[$idMenu]);
        } else {
            $keranjang[$idMenu]['jumlah']  = $jumlah;
            $keranjang[$idMenu]['subtotal'] = $keranjang[$idMenu]['harga'] * $jumlah;
        }
        session(['keranjang' => $keranjang]);

        return redirect()->route('konsumen.keranjang');
    }

    /**
     * Finalisasi pesanan: input nama konsumen, generate no_pesanan, simpan ke DB.
     */
    public function storePesan(Request $request)
    {
        $request->validate([
            'nama_konsumen' => ['required', 'string', 'max:255'],
        ]);

        if (! session('id_meja')) {
            return redirect()->back()->withErrors(['id_meja' => 'Sesi meja tidak valid. Silakan scan QR Code kembali.']);
        }

        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('konsumen.keranjang')
                ->with('error', 'Keranjang kosong. Tambahkan menu terlebih dahulu.');
        }

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
                    'status_pembayaran' => 'menunggu',
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
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors(['order' => 'Gagal membuat pesanan, coba lagi.']);
        }

        session()->forget('keranjang');
        session(['no_pesanan_baru' => $noPesanan]);

        return redirect()->route('konsumen.keranjang');
    }
}
