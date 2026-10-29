<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_penanganan', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->unsignedBigInteger('laporan_id');
            $table->unsignedBigInteger('penanganan_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('jenis_log', ['otomatis_status', 'manual_catatan']);
            $table->text('aktivitas');
            $table->dateTime('waktu');
            $table->timestamp('created_at')->nullable();

            $table->foreign('laporan_id')->references('laporan_id')->on('laporan_kasus')->cascadeOnDelete();
            $table->foreign('penanganan_id')->references('penanganan_id')->on('penanganan_kasus')->cascadeOnDelete();
            $table->foreign('user_id')->references('user_id')->on('users')->restrictOnDelete();
            $table->index(['laporan_id', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_penanganan');
    }
};
