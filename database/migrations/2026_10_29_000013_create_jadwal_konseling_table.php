<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_konseling', function (Blueprint $table) {
            $table->bigIncrements('jadwal_id');
            $table->unsignedBigInteger('konselor_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('metode', ['online', 'offline']);
            $table->string('lokasi_atau_tautan', 500)->nullable();
            $table->enum('status', ['tersedia', 'dipesan', 'selesai', 'dibatalkan'])->default('tersedia');
            $table->timestamps();

            $table->unique(['konselor_id', 'tanggal', 'waktu_mulai']);
            $table->foreign('konselor_id')->references('konselor_id')->on('konselor')->cascadeOnDelete();
            $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_konseling');
    }
};
