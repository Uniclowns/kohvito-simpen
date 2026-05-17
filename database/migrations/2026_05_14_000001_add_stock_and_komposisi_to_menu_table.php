<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom komposisi (text) dan stock (int) ke tabel menu.
 *
 * Migration ini IDEMPOTENT — aman dijalankan walaupun kolom sudah ada
 * (skip lewat Schema::hasColumn). Tujuannya: source-control hygiene
 * untuk environment yang sudah ditambah kolom manual via Tinker.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('menu', function (Blueprint $table) {
            if (!Schema::hasColumn('menu', 'komposisi')) {
                $table->text('komposisi')->nullable()->after('tipe_minuman');
            }

            if (!Schema::hasColumn('menu', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('komposisi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menu', function (Blueprint $table) {
            if (Schema::hasColumn('menu', 'stock')) {
                $table->dropColumn('stock');
            }

            if (Schema::hasColumn('menu', 'komposisi')) {
                $table->dropColumn('komposisi');
            }
        });
    }
};
