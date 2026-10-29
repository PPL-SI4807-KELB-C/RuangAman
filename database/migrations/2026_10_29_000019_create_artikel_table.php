<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->bigIncrements('artikel_id');
            $table->unsignedBigInteger('kategori_id');
            $table->unsignedBigInteger('penulis_id');
            $table->string('judul', 255);
            $table->string('slug', 255)->unique();
            $table->longText('isi');
            $table->enum('status_publikasi', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();

            $table->foreign('kategori_id')->references('kategori_id')->on('kategori_artikel')->restrictOnDelete();
            $table->foreign('penulis_id')->references('user_id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};
