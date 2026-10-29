<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konselor', function (Blueprint $table) {
            $table->bigIncrements('konselor_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nama', 150);
            $table->string('spesialisasi', 200);
            $table->text('bio')->nullable();
            $table->enum('status', ['aktif', 'cuti', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konselor');
    }
};
