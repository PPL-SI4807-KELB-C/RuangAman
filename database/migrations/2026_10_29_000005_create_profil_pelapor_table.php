<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_pelapor', function (Blueprint $table) {
            $table->bigIncrements('profil_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('alias', 100);
            $table->string('kontak_opsional', 100)->nullable();
            $table->boolean('preferensi_anonim')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_pelapor');
    }
};
