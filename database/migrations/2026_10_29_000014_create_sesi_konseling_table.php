<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_konseling', function (Blueprint $table) {
            $table->bigIncrements('sesi_id');
            $table->unsignedBigInteger('jadwal_id')->unique();
            $table->text('catatan');
            $table->text('hasil');
            $table->timestamps();

            $table->foreign('jadwal_id')->references('jadwal_id')->on('jadwal_konseling')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_konseling');
    }
};
