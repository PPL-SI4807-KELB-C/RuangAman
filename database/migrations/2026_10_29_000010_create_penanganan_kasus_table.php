<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penanganan_kasus', function (Blueprint $table) {
            $table->bigIncrements('penanganan_id');
            $table->unsignedBigInteger('laporan_id')->unique();
            $table->unsignedBigInteger('petugas_id');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('laporan_id')->references('laporan_id')->on('laporan_kasus')->cascadeOnDelete();
            $table->foreign('petugas_id')->references('user_id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penanganan_kasus');
    }
};
