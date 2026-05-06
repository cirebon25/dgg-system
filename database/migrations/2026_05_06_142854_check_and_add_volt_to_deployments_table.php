<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            // ✨ MANTRA KEBAL EROR: Cek dulu, jika kolom 'volt' BELUM ADA, baru buat baru
            if (!Schema::hasColumn('deployments', 'volt')) {
                $table->integer('volt')->default(220)->nullable()->after('counter_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            if (Schema::hasColumn('deployments', 'volt')) {
                $table->dropColumn('volt');
            }
        });
    }
};