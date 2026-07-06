<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom `tgl_pesanan` (waktu pesanan dibuat) ke tabel `pesanan`
 * dan memperluas enum `status_pembayaran` dengan nilai 'gagal'.
 *
 * Sebelumnya satu-satunya kolom tanggal adalah `tgl_pembayaran` yang baru
 * terisi saat lunas, sehingga pesanan belum bayar tidak pernah muncul di
 * dashboard admin (filter tanggal tidak pernah cocok dengan NULL).
 *
 * Nilai 'gagal' dibutuhkan oleh command payments:sync-pending yang menandai
 * pesanan expire/cancel dari Midtrans — sebelumnya menulis nilai di luar enum
 * dan gagal dengan error "Data truncated".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dateTime('tgl_pesanan')->nullable()->after('nama_konsumen');
        });

        // Backfill: waktu dibuat terekam pada prefix no_pesanan (PS-YmdHis-XXXX);
        // fallback ke tgl_pembayaran untuk format nomor lama (data seeder).
        // ponytail: loop PHP per baris — tabel kecil; ganti ke UPDATE set-based bila data membengkak
        foreach (DB::table('pesanan')->whereNull('tgl_pesanan')->get(['no_pesanan', 'tgl_pembayaran']) as $row) {
            $tgl = preg_match('/^PS-(\d{14})-/', $row->no_pesanan, $m)
                ? Carbon::createFromFormat('YmdHis', $m[1])
                : $row->tgl_pembayaran;

            if ($tgl) {
                DB::table('pesanan')->where('no_pesanan', $row->no_pesanan)->update(['tgl_pesanan' => $tgl]);
            }
        }

        // ponytail: ALTER enum khusus MySQL; SQLite (test) tetap enum lama karena tidak ada test yang menulis 'gagal'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu', 'lunas', 'gagal') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Kembalikan baris 'gagal' ke 'menunggu' sebelum enum dipersempit
            DB::table('pesanan')->where('status_pembayaran', 'gagal')->update(['status_pembayaran' => 'menunggu']);
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu', 'lunas') NOT NULL");
        }

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('tgl_pesanan');
        });
    }
};
