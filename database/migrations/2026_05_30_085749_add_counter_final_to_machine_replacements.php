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
        Schema::table('machine_replacements', function (Blueprint $table) {
            $table->unsignedBigInteger('counter_bw_final')->default(0)->after('keterangan');
            $table->unsignedBigInteger('counter_color_final')->default(0)->after('counter_bw_final');
        });
    }

    public function down(): void
    {
        Schema::table('machine_replacements', function (Blueprint $table) {
            $table->dropColumn(['counter_bw_final', 'counter_color_final']);
        });
    }
};
