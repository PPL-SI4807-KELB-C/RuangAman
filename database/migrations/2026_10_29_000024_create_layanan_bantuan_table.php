<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_bantuan', function (Blueprint $table) {
            $table->bigIncrements('layanan_id');
            $table->unsignedBigInteger('instansi_id');
            $table->string('nama_layanan', 150);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('instansi_id')->references('instansi_id')->on('instansi_bantuan')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_bantuan');
    }
};
