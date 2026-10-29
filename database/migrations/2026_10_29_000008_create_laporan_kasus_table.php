<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kasus', function (Blueprint $table) {
            $table->bigIncrements('laporan_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status_id');
            $table->string('kode_laporan', 50)->unique();
            $table->string('judul', 200);
            $table->string('kategori_kekerasan', 100);
            $table->text('lokasi_kejadian');
            $table->longText('kronologi');
            $table->dateTime('waktu_kejadian');
            $table->enum('tingkat_urgensi', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
            $table->boolean('mode_anonim')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->restrictOnDelete();
            $table->foreign('status_id')->references('status_id')->on('status_kasus')->restrictOnDelete();
            $table->index(['status_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kasus');
    }
};
