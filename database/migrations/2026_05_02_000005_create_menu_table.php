<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->unsignedBigInteger('id_kategori');
            $table->string('nama_menu');
            $table->text('deskripsi');
            $table->integer('harga');
            $table->string('gambar_menu');
            $table->enum('status_ketersediaan', ['Tersedia', 'Tidak Tersedia'])->default('Tersedia');
            $table->enum('jenis_menu', ['Makanan', 'Minuman']);
            $table->enum('kategori_makanan', ['Pedas', 'Tidak Pedas'])->nullable();
            $table->enum('tipe_minuman', ['Panas', 'Dingin', 'Keduanya'])->nullable();
            $table->text('komposisi')->nullable();
            $table->unsignedInteger('stock')->default(0);

            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_menu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
