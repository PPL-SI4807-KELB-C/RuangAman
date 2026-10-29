<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_kegiatan', function (Blueprint $table) {
            $table->bigIncrements('pendaftaran_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status_pendaftaran', ['terdaftar', 'hadir', 'batal'])->default('terdaftar');
            $table->timestamps();

            $table->unique(['kegiatan_id', 'user_id']);
            $table->foreign('kegiatan_id')->references('kegiatan_id')->on('kegiatan_edukasi')->cascadeOnDelete();
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_kegiatan');
    }
};
