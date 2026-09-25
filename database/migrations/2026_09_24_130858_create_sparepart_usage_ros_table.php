<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sparepart_usage_ros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_air_ro_id')->constrained('machine_air_ros')->cascadeOnDelete();
            $table->foreignId('sparepart_ro_id')->constrained('sparepart_ros')->cascadeOnDelete();
            $table->integer('jumlah_pakai');
            $table->date('tanggal_pakai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sparepart_usage_ros');
    }
};
