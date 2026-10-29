<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lampiran_laporan', function (Blueprint $table) {
            $table->bigIncrements('lampiran_id');
            $table->unsignedBigInteger('laporan_id');
            $table->string('nama_file', 255);
            $table->string('path_privat', 500);
            $table->string('tipe_file', 100);
            $table->unsignedBigInteger('ukuran_file');
            $table->timestamp('created_at')->nullable();

            $table->foreign('laporan_id')->references('laporan_id')->on('laporan_kasus')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampiran_laporan');
    }
};
