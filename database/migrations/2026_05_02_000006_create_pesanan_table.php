<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->string('no_pesanan')->primary();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_meja');
            $table->string('nama_konsumen');
            $table->integer('total_harga');
            $table->enum('status_pembayaran', ['menunggu', 'lunas']);
            $table->enum('status_pesanan', ['menunggu konfirmasi', 'diproses', 'selesai']);
            $table->dateTime('tgl_pembayaran')->nullable();

            $table->foreign('id_user')->references('id_users')->on('users');
            $table->foreign('id_meja')->references('id_meja')->on('meja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
