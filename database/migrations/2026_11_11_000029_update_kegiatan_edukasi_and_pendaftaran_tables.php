<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::dropIfExists('pendaftaran_kegiatan');
            Schema::dropIfExists('kegiatan_edukasi');

            Schema::create('kegiatan_edukasi', function (Blueprint $table) {
                $table->bigIncrements('kegiatan_id');
                $table->string('judul', 255);
                $table->text('deskripsi')->nullable();
                $table->string('narasumber', 255)->nullable();
                $table->date('tanggal_mulai');
                $table->time('waktu_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->time('waktu_selesai')->nullable();
                $table->string('metode', 20);
                $table->text('lokasi_atau_tautan')->nullable();
                $table->unsignedInteger('kuota');
                $table->string('status', 20)->default('draft');
                $table->timestamps();
            });

            Schema::create('pendaftaran_kegiatan', function (Blueprint $table) {
                $table->bigIncrements('pendaftaran_id');
                $table->unsignedBigInteger('kegiatan_id');
                $table->unsignedBigInteger('user_id');
                $table->string('status_pendaftaran', 20)->default('aktif');
                $table->timestamps();

                $table->unique(['kegiatan_id', 'user_id']);
                $table->foreign('kegiatan_id')->references('kegiatan_id')->on('kegiatan_edukasi')->cascadeOnDelete();
                $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            });

            return;
        }

        // MySQL 8 / TiDB Cloud: ubah skema kolom tanpa kehilangan struktur tabel yang sudah ada
        Schema::table('kegiatan_edukasi', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->change();
            $table->string('narasumber', 255)->nullable()->change();
            $table->date('tanggal_selesai')->nullable()->change();
            $table->time('waktu_selesai')->nullable()->change();
            $table->string('metode', 20)->change();
            $table->text('lokasi_atau_tautan')->nullable()->change();
            $table->string('status', 20)->default('draft')->change();
        });

        Schema::table('pendaftaran_kegiatan', function (Blueprint $table) {
            $table->string('status_pendaftaran', 20)->default('aktif')->change();
        });

        // Sinkronkan nilai legacy bila ada sebelum kolom dikonversi
        DB::table('kegiatan_edukasi')->where('status', 'buka')->update(['status' => 'tayang']);
        DB::table('kegiatan_edukasi')->where('status', 'tutup')->update(['status' => 'draft']);
        DB::table('pendaftaran_kegiatan')->where('status_pendaftaran', 'terdaftar')->update(['status_pendaftaran' => 'aktif']);
        DB::table('pendaftaran_kegiatan')->where('status_pendaftaran', 'batal')->update(['status_pendaftaran' => 'dibatalkan']);
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::dropIfExists('pendaftaran_kegiatan');
            Schema::dropIfExists('kegiatan_edukasi');

            Schema::create('kegiatan_edukasi', function (Blueprint $table) {
                $table->bigIncrements('kegiatan_id');
                $table->string('judul', 255);
                $table->text('deskripsi');
                $table->string('narasumber', 200);
                $table->date('tanggal_mulai');
                $table->time('waktu_mulai');
                $table->date('tanggal_selesai');
                $table->time('waktu_selesai');
                $table->enum('metode', ['online', 'offline']);
                $table->string('lokasi_atau_tautan', 500)->nullable();
                $table->unsignedInteger('kuota');
                $table->enum('status', ['buka', 'tutup', 'selesai', 'dibatalkan'])->default('buka');
                $table->timestamps();
            });

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

            return;
        }

        DB::table('kegiatan_edukasi')->where('status', 'tayang')->update(['status' => 'buka']);
        DB::table('kegiatan_edukasi')->where('status', 'draft')->update(['status' => 'tutup']);
        DB::table('pendaftaran_kegiatan')->where('status_pendaftaran', 'aktif')->update(['status_pendaftaran' => 'terdaftar']);
        DB::table('pendaftaran_kegiatan')->where('status_pendaftaran', 'dibatalkan')->update(['status_pendaftaran' => 'batal']);
        DB::table('pendaftaran_kegiatan')->where('status_pendaftaran', 'tidak_hadir')->update(['status_pendaftaran' => 'terdaftar']);

        Schema::table('kegiatan_edukasi', function (Blueprint $table) {
            $table->string('narasumber', 200)->nullable()->change();
            $table->string('lokasi_atau_tautan', 500)->nullable()->change();
            $table->enum('metode', ['online', 'offline'])->change();
            $table->enum('status', ['buka', 'tutup', 'selesai', 'dibatalkan'])->default('buka')->change();
        });

        Schema::table('pendaftaran_kegiatan', function (Blueprint $table) {
            $table->enum('status_pendaftaran', ['terdaftar', 'hadir', 'batal'])->default('terdaftar')->change();
        });
    }
};
