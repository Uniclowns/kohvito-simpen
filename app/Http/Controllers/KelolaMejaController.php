<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KelolaMejaController extends Controller
{
    public function index()
    {
        $mejas = Meja::all();
        return view('admin.kelola-meja', compact('mejas'));
    }

    public function storeMeja(Request $request)
    {
        // Validate
        $request->validate([
            'no_meja' => 'required|string|max:50|unique:meja,no_meja',
        ]);

        // Create meja first (without qr_code)
        $meja = Meja::create(['no_meja' => $request->no_meja, 'qr_code' => null]);

        // Generate QR code PNG
        $qrUrl = url('/' . $meja->no_meja);
        $qrFilename = 'qrcodes/' . $meja->no_meja . '.png';
        $qrContent = QrCode::format('png')->size(300)->generate($qrUrl);
        Storage::disk('public')->put($qrFilename, $qrContent);

        // Update meja with qr_code path
        $meja->update(['qr_code' => $qrFilename]);

        return redirect()->route('admin.meja.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function updateMeja(Request $request, string $id)
    {
        $meja = Meja::findOrFail($id);

        $request->validate([
            'no_meja' => 'required|string|max:50|unique:meja,no_meja,' . $id . ',id_meja',
        ]);

        // Delete old QR if no_meja changed
        if ($meja->no_meja !== $request->no_meja) {
            if ($meja->qr_code) {
                Storage::disk('public')->delete($meja->qr_code);
            }

            $meja->update(['no_meja' => $request->no_meja, 'qr_code' => null]);

            $qrUrl = url('/' . $meja->no_meja);
            $qrFilename = 'qrcodes/' . $meja->no_meja . '.png';
            $qrContent = QrCode::format('png')->size(300)->generate($qrUrl);
            Storage::disk('public')->put($qrFilename, $qrContent);
            $meja->update(['qr_code' => $qrFilename]);
        }

        return redirect()->route('admin.meja.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroyMeja(string $id)
    {
        $meja = Meja::findOrFail($id);

        if ($meja->qr_code) {
            Storage::disk('public')->delete($meja->qr_code);
        }

        $meja->delete();

        return redirect()->route('admin.meja.index')->with('success', 'Meja berhasil dihapus.');
    }
}
