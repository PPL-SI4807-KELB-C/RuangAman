<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_edukasi', function (Blueprint $table) {
            $table->bigIncrements('kegiatan_id');
            $table->string('judul', 255);
            $table->text('deskripsi');
            $table->string('narasumber', 200);
            $table->date('tanggal_mulai');
            $table->time('waktu_mulai');
            $table->date('tanggal_selesai');
            $table->time('waktu_selesai');
            $table->enum('metode', ['online', 'offline']);
            $table->string('lokasi_atau_tautan', 500)->nullable();
            $table->unsignedInteger('kuota');
            $table->enum('status', ['buka', 'tutup', 'selesai', 'dibatalkan'])->default('buka');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_edukasi');
    }
};
