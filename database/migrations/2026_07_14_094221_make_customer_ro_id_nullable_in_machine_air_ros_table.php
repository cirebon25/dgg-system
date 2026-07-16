<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_air_ros', function (Blueprint $table) {
            $table->foreignId('customer_ro_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('machine_air_ros', function (Blueprint $table) {
            $table->foreignId('customer_ro_id')->nullable(false)->change();
        });
    }
};
