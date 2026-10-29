<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_artikel', function (Blueprint $table) {
            $table->bigIncrements('kategori_id');
            $table->string('nama_kategori', 100)->unique();
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_artikel');
    }
};
