<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom `tracking_code` ke tabel `pesanan`.
 *
 * Kolom ini menyimpan kode pelacakan publik singkat (mis. "KV-7F3A9") yang mudah
 * dicatat/difoto konsumen. Berfungsi sebagai jaring pengaman device-independent:
 * konsumen dapat melacak pesanannya kapan saja lewat halaman /lacak tanpa
 * bergantung pada session/cookie browser.
 *
 * Nullable + unique: nullable agar aman terhadap baris pesanan lama yang belum
 * memiliki kode; unique agar tidak ada dua pesanan berbagi kode pelacakan sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('tracking_code', 20)
                ->nullable()
                ->unique()
                ->after('no_pesanan');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropUnique(['tracking_code']);
            $table->dropColumn('tracking_code');
        });
    }
};
