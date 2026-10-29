<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halaman_informasi', function (Blueprint $table) {
            $table->bigIncrements('halaman_id');
            $table->string('judul', 200);
            $table->string('slug', 200)->unique();
            $table->longText('isi');
            $table->enum('status_publikasi', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halaman_informasi');
    }
};
