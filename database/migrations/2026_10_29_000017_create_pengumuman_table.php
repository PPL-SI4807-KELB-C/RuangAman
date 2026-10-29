<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->bigIncrements('pengumuman_id');
            $table->unsignedBigInteger('dibuat_oleh');
            $table->string('judul', 200);
            $table->text('isi');
            $table->date('mulai_tayang');
            $table->date('akhir_tayang')->nullable();
            $table->enum('status', ['aktif', 'arsip'])->default('aktif');
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('user_id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
