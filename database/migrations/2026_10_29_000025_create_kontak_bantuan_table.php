<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontak_bantuan', function (Blueprint $table) {
            $table->bigIncrements('kontak_id');
            $table->unsignedBigInteger('instansi_id');
            $table->enum('jenis_kontak', ['telepon', 'whatsapp', 'email', 'hotline']);
            $table->string('nilai_kontak', 150);
            $table->string('keterangan', 200)->nullable();
            $table->timestamps();

            $table->foreign('instansi_id')->references('instansi_id')->on('instansi_bantuan')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontak_bantuan');
    }
};
