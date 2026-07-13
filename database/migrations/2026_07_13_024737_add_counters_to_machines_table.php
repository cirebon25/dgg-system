<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->bigInteger('counter_bw')->nullable()->default(0)->after('double_scan');
            $table->bigInteger('counter_color')->nullable()->default(0)->after('counter_bw');
            $table->timestamp('last_rolled_at')->nullable()->after('counter_color');
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn(['counter_bw', 'counter_color', 'last_rolled_at']);
        });
    }
};
