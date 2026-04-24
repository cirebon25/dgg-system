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
    Schema::create('technicians', function (Blueprint $table) {
        $table->id();
        // Menghubungkan Teknisi ke Rayon
        $table->foreignId('rayon_id')->constrained('rayons')->onDelete('cascade');
        $table->string('nama_technician');
        $table->string('nomor_hp')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};
