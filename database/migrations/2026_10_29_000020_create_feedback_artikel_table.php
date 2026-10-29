<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_artikel', function (Blueprint $table) {
            $table->bigIncrements('feedback_id');
            $table->unsignedBigInteger('artikel_id');
            $table->unsignedBigInteger('user_id');
            $table->text('isi_feedback');
            $table->enum('status_moderasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamps();

            $table->foreign('artikel_id')->references('artikel_id')->on('artikel')->cascadeOnDelete();
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_artikel');
    }
};
