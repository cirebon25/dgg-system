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
        Schema::create('deployment_sparepart', function (Blueprint $table) {
    $table->id();
    $table->foreignId('deployment_id')->constrained()->cascadeOnDelete();
    $table->foreignId('sparepart_id')->constrained()->cascadeOnDelete();
    $table->integer('jumlah')->default(1);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_sparepart');
    }
};
