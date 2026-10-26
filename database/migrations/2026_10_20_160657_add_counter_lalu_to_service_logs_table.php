<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->integer('counter_bw_lalu')->nullable()->after('counter_bw');
            $table->integer('counter_color_lalu')->nullable()->after('counter_color');
        });
    }

    public function down(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropColumn(['counter_bw_lalu', 'counter_color_lalu']);
        });
    }
};
