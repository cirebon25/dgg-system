<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            // Pastikan kolom ini ada dan boleh kosong
            $table->time('jam_mulai')->nullable()->change();
            $table->time('jam_selesai')->nullable()->change();
            $table->text('kerusakan')->nullable()->change();
            $table->text('perbaikan')->nullable()->change();
            $table->integer('counter_color')->nullable()->change();
            $table->integer('counter_bw')->nullable()->change();

            // Tambahkan kolom pemakaian jika belum ada
            if (! Schema::hasColumn('service_logs', 'usage_color')) {
                $table->integer('usage_color')->default(0)->nullable();
            }
            if (! Schema::hasColumn('service_logs', 'usage_bw')) {
                $table->integer('usage_bw')->default(0)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
