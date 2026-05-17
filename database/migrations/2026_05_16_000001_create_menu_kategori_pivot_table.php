<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_kategori', function (Blueprint $table) {
            $table->unsignedBigInteger('id_menu');
            $table->unsignedBigInteger('id_kategori');
            $table->primary(['id_menu', 'id_kategori']);

            $table->foreign('id_menu')->references('id_menu')->on('menu')->cascadeOnDelete();
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_menu')->cascadeOnDelete();
        });

        // Migrate existing single-kategori data to pivot
        DB::statement('
            INSERT INTO menu_kategori (id_menu, id_kategori)
            SELECT id_menu, id_kategori FROM menu WHERE id_kategori IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_kategori');
    }
};
