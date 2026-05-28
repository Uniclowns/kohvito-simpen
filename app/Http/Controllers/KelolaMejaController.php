<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class KelolaMejaController
 *
 * Controller manajemen meja konsumen dan QR Code di panel Administrator.
 * Setiap meja memiliki nomor unik (no_meja) dan URL scan yang di-encode
 * ke QR Code. Customer scan QR di meja → langsung masuk halaman menu
 * konsumen melalui route /{noMeja}.
 *
 * QR Code di-generate on-the-fly via accessor model Meja::qr_svg
 * sehingga tidak perlu disimpan sebagai file (lebih ringan & gampang
 * di-reprint kalau base URL berubah).
 *
 * @package App\Http\Controllers
 */
class KelolaMejaController extends Controller
{
    /**
     * Tampilkan daftar seluruh meja beserta QR Code thumbnail-nya.
     * Mendukung pencarian berdasarkan nomor meja.
     *
     * @param  \Illuminate\Http\Request  $request  Pembawa parameter ?search=
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $meja = Meja::query()
            ->when($search, fn ($q) => $q->where('no_meja', 'like', "%{$search}%"))
            ->orderBy('no_meja')
            ->get();

        return view('admin.kelola-meja', compact('meja', 'search'));
    }

    /**
     * Halaman cetak A4 berisi grid semua QR Code meja, siap di-print
     * dan ditempel/dilaminasi di setiap meja fisik café.
     *
     * @return \Illuminate\View\View
     */
    public function cetakQr(): View
    {
        $meja = Meja::orderBy('no_meja')->get();

        return view('admin.cetak-qr-meja', compact('meja'));
    }

    /**
     * Validasi dan simpan meja baru. Kolom qr_code diisi otomatis dengan
     * URL scan-nya (cached snapshot, sumber of truth tetap accessor).
     *
     * @param  \Illuminate\Http\Request  $request  Pembawa form no_meja baru
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeMeja(Request $request): RedirectResponse
    {
        $request->validate([
            'no_meja' => 'required|string|max:10|unique:meja,no_meja',
        ], [
            'no_meja.required' => 'Nomor meja wajib diisi',
            'no_meja.max'      => 'Nomor meja maksimal 10 karakter',
            'no_meja.unique'   => 'Nomor meja sudah dipakai, silakan pilih nomor lain',
        ]);

        // 1. Buat instance dulu (belum disimpan) supaya accessor scan_url bisa dipakai
        $meja = new Meja(['no_meja' => $request->no_meja]);
        $meja->qr_code = $meja->scan_url;
        $meja->save();

        return redirect()->route('admin.meja.index')
            ->with('success', "Meja {$meja->no_meja} berhasil ditambahkan.");
    }

    /**
     * Validasi dan perbarui nomor meja. Saat nomor berubah, kolom qr_code
     * ikut di-refresh agar QR baru menunjuk ke URL yang benar.
     *
     * @param  \Illuminate\Http\Request  $request  Pembawa nomor meja baru
     * @param  string  $id  ID Meja target perubahan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMeja(Request $request, string $id): RedirectResponse
    {
        $meja = Meja::findOrFail($id);

        $request->validate([
            // Unique tetapi abaikan record diri sendiri (cek primary key id_meja)
            'no_meja' => 'required|string|max:10|unique:meja,no_meja,' . $id . ',id_meja',
        ], [
            'no_meja.required' => 'Nomor meja wajib diisi',
            'no_meja.max'      => 'Nomor meja maksimal 10 karakter',
            'no_meja.unique'   => 'Nomor meja sudah dipakai, silakan pilih nomor lain',
        ]);

        $meja->no_meja = $request->no_meja;
        $meja->qr_code = $meja->scan_url; // refresh snapshot URL
        $meja->save();

        return redirect()->route('admin.meja.index')
            ->with('success', "Meja {$meja->no_meja} berhasil diperbarui.");
    }

    /**
     * Hapus meja dari database. Pesanan historis tetap menyimpan id_meja
     * sebagai foreign key — pastikan tidak ada constraint blocking sebelum
     * production (atau set onDelete('set null') di migration pesanan).
     *
     * @param  string  $id  ID Meja target penghapusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMeja(string $id): RedirectResponse
    {
        $meja = Meja::findOrFail($id);
        $noMeja = $meja->no_meja;

        $meja->delete();

        return redirect()->route('admin.meja.index')
            ->with('success', "Meja {$noMeja} berhasil dihapus.");
    }
}
