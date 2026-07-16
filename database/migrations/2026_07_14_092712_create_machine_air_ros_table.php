<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_air_ros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_ro_id')->nullable()->constrained('customer_ros')->onDelete('cascade');
            $table->string('serial_number')->unique();
            $table->string('tipe_mesin');
            $table->string('status')->default('Ready'); // Ready, Perbaikan, Rusak, dll
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_air_ros');
    }
};
