<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instansi_bantuan', function (Blueprint $table) {
            $table->bigIncrements('instansi_id');
            $table->string('nama_instansi', 200);
            $table->text('alamat');
            $table->text('deskripsi')->nullable();
            $table->string('website', 255)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instansi_bantuan');
    }
};
