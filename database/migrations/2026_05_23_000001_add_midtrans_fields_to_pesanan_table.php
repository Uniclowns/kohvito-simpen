<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('midtrans_transaction_id', 100)->nullable()->after('catatan_pesanan');
            $table->string('qr_url', 500)->nullable()->after('midtrans_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['midtrans_transaction_id', 'qr_url']);
        });
    }
};
