<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('no_pesanan');
            $table->unsignedBigInteger('id_menu');
            $table->integer('jumlah');
            $table->string('catatan')->nullable();
            $table->integer('subtotal');

            $table->foreign('no_pesanan')->references('no_pesanan')->on('pesanan');
            $table->foreign('id_menu')->references('id_menu')->on('menu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
