<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->unsignedInteger('pegawai_id')->nullable()->index()->after('id');
            $table->string('jabatan')->nullable();
            $table->string('departemen')->nullable();
            $table->string('kantor')->nullable();
        });

        Schema::create('attendance_shifts', function (Blueprint $table) {
            $table->unsignedInteger('jk_id')->primary();
            $table->string('name');
            $table->time('jam_masuk');
            $table->unsignedSmallInteger('tol_late')->default(0);
            $table->timestamps();
        });

        Schema::create('attendance_daily_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pegawai_id');
            $table->date('tgl_shift');
            $table->unsignedInteger('jk_id')->default(0);
            $table->boolean('libur_umum')->default(false);
            $table->boolean('libur_rutin')->default(false);
            $table->boolean('jk_ot')->default(false);
            $table->dateTime('scan_in')->nullable();
            $table->dateTime('scan_out')->nullable();
            $table->unsignedSmallInteger('late_minute')->default(0);
            $table->unsignedSmallInteger('early_minute')->default(0);
            $table->unsignedSmallInteger('durasi_minute')->default(0);
            $table->unsignedSmallInteger('durasi_eot_minute')->default(0);
            $table->unsignedSmallInteger('break_minute')->default(0);
            $table->unsignedSmallInteger('break_ot_minute')->default(0);
            $table->smallInteger('status_jk')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pegawai_id', 'tgl_shift'], 'daily_results_unique');
            $table->index('tgl_shift');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_daily_results');
        Schema::dropIfExists('attendance_shifts');

        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn(['pegawai_id', 'jabatan', 'departemen', 'kantor']);
        });
    }
};