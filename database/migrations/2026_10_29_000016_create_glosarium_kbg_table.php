<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glosarium_kbg', function (Blueprint $table) {
            $table->bigIncrements('glosarium_id');
            $table->string('istilah', 150);
            $table->text('definisi');
            $table->string('kategori', 100)->nullable();
            $table->enum('status_publikasi', ['draft', 'published'])->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glosarium_kbg');
    }
};
