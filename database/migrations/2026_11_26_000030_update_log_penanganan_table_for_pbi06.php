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
            Schema::dropIfExists('log_penanganan');

            Schema::create('log_penanganan', function (Blueprint $table) {
                $table->bigIncrements('log_id');
                $table->unsignedBigInteger('penanganan_id')->nullable();
                $table->unsignedBigInteger('laporan_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->string('jenis_log', 20);
                $table->text('aktivitas');
                $table->timestamp('waktu')->useCurrent();
                $table->timestamps();

                $table->foreign('penanganan_id')->references('penanganan_id')->on('penanganan_kasus')->cascadeOnDelete();
                $table->foreign('laporan_id')->references('laporan_id')->on('laporan_kasus')->cascadeOnDelete();
                $table->foreign('user_id')->references('user_id')->on('users')->restrictOnDelete();
                $table->index(['penanganan_id', 'waktu']);
                $table->index(['laporan_id', 'waktu']);
            });

            return;
        }

        Schema::table('log_penanganan', function (Blueprint $table) {
            $table->unsignedBigInteger('laporan_id')->nullable()->change();
            $table->string('jenis_log', 20)->change();
            $table->timestamp('waktu')->useCurrent()->change();

            if (! Schema::hasColumn('log_penanganan', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        DB::table('log_penanganan')->where('jenis_log', 'otomatis_status')->update(['jenis_log' => 'sistem']);
        DB::table('log_penanganan')->where('jenis_log', 'manual_catatan')->update(['jenis_log' => 'manual']);

        Schema::table('log_penanganan', function (Blueprint $table) {
            $table->index(['penanganan_id', 'waktu'], 'log_penanganan_penanganan_waktu_index');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::dropIfExists('log_penanganan');

            Schema::create('log_penanganan', function (Blueprint $table) {
                $table->bigIncrements('log_id');
                $table->unsignedBigInteger('laporan_id');
                $table->unsignedBigInteger('penanganan_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->enum('jenis_log', ['otomatis_status', 'manual_catatan']);
                $table->text('aktivitas');
                $table->dateTime('waktu');
                $table->timestamp('created_at')->nullable();

                $table->foreign('laporan_id')->references('laporan_id')->on('laporan_kasus')->cascadeOnDelete();
                $table->foreign('penanganan_id')->references('penanganan_id')->on('penanganan_kasus')->cascadeOnDelete();
                $table->foreign('user_id')->references('user_id')->on('users')->restrictOnDelete();
                $table->index(['laporan_id', 'waktu']);
            });

            return;
        }

        Schema::table('log_penanganan', function (Blueprint $table) {
            $table->dropIndex('log_penanganan_penanganan_waktu_index');
        });

        DB::table('log_penanganan')->where('jenis_log', 'sistem')->update(['jenis_log' => 'otomatis_status']);
        DB::table('log_penanganan')->where('jenis_log', 'manual')->update(['jenis_log' => 'manual_catatan']);

        Schema::table('log_penanganan', function (Blueprint $table) {
            if (Schema::hasColumn('log_penanganan', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            $table->enum('jenis_log', ['otomatis_status', 'manual_catatan'])->change();
            $table->dateTime('waktu')->change();
        });
    }
};
