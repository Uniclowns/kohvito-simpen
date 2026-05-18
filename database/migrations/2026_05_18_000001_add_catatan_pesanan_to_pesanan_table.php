<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pesanan', 'catatan_pesanan')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->text('catatan_pesanan')->nullable()->after('status_pesanan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pesanan', 'catatan_pesanan')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->dropColumn('catatan_pesanan');
            });
        }
    }
};
