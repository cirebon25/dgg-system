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
        $table->foreignId('rayon_id'); // atau kolom rayon lainnya
        $table->string('nama_technician');
        $table->string('nomor_hp'); // <-- PASTIKAN BARIS INI ADA
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
