<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_users');
            $table->unsignedBigInteger('id_role');
            $table->string('nama_lengkap');
            $table->string('username')->unique();
            $table->string('password');

            $table->foreign('id_role')->references('id_role')->on('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
