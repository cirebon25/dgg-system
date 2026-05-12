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
    Schema::table('deployments', function (Blueprint $table) {
        // Cek dulu biar gak duplikat lagi
        if (!Schema::hasColumn('deployments', 'counter_color')) {
            $table->integer('counter_color')->default(0)->after('counter_bw');
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            //
        });
    }
};
