<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deploy_machine_ros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_air_ro_id')->constrained('machine_air_ros')->cascadeOnDelete();
            $table->foreignId('customer_ro_id')->constrained('customer_ros')->cascadeOnDelete();
            $table->date('tanggal_deploy');
            $table->enum('status_deploy', ['Terpasang', 'Ditarik'])->default('Terpasang');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deploy_machine_ros');
    }
};
