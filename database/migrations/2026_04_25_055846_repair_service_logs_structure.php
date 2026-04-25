<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            // 1. Pastikan kolom pemakaian ada
            if (!Schema::hasColumn('service_logs', 'usage_color')) {
                $table->integer('usage_color')->default(0)->nullable();
            }
            if (!Schema::hasColumn('service_logs', 'usage_bw')) {
                $table->integer('usage_bw')->default(0)->nullable();
            }

            // 2. Ubah semua kolom menjadi nullable (Boleh Kosong) agar tidak error default value
            $table->time('jam_mulai')->nullable()->change();
            $table->time('jam_selesai')->nullable()->change();
            $table->text('kerusakan')->nullable()->change();
            $table->text('perbaikan')->nullable()->change();
            $table->integer('counter_color')->nullable()->change();
            $table->integer('counter_bw')->nullable()->change();
            $table->integer('counter_scan')->nullable()->change();
            $table->string('sparepart')->nullable()->change();
        });
    }

    public function down(): void {}
};