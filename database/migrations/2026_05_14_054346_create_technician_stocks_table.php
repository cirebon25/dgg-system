<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_stocks', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel teknisi
            $table->foreignId('technician_id')->constrained('technicians')->cascadeOnDelete();
            
            // Relasi ke tabel sparepart
            $table->foreignId('sparepart_id')->constrained('spareparts')->cascadeOnDelete();
            
            // KOLOM PALING PENTING
            $table->integer('jumlah')->default(0); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_stocks');
    }
};