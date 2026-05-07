<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengecekan kolom harus dilakukan di luar Blueprint closure untuk beberapa versi Laravel
        if (!Schema::hasColumn('deployments', 'volt')) {
            Schema::table('deployments', function (Blueprint $table) {
                // Ganti 'counter_color' menjadi 'no_kontrak' agar tidak error
                // Atau hapus ->after(...) jika ingin diletakkan di urutan paling akhir
                $table->integer('volt')->default(220)->nullable()->after('no_kontrak');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('deployments', 'volt')) {
            Schema::table('deployments', function (Blueprint $table) {
                $table->dropColumn('volt');
            });
        }
    }
};