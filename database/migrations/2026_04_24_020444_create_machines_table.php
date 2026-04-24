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
    Schema::create('machines', function (Blueprint $table) {
        $table->id();
        $table->string('serial_number')->unique()->index(); // Kunci pencarian cepat
        $table->string('tipe_model');
        $table->enum('status', ['Ready', 'Rented', 'Refurbish'])->default('Ready');
        $table->text('keterangan_awal')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
