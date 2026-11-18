<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->index(['machine_id', 'tanggal'], 'service_logs_machine_tanggal_idx');
        });

        Schema::table('meter_readings', function (Blueprint $table) {
            $table->index(['machine_id', 'tanggal'], 'meter_readings_machine_tanggal_idx');
        });
    }

    public function down(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropIndex('service_logs_machine_tanggal_idx');
        });

        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropIndex('meter_readings_machine_tanggal_idx');
        });
    }
};