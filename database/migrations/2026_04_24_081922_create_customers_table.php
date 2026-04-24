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
    Schema::create('customers', function (Blueprint $table) {
        $table->id();
        // Menghubungkan Customer ke tabel Rayon
        $table->foreignId('rayon_id')->constrained('rayons')->onDelete('cascade');
        
        $table->string('nama_customer');
        $table->string('kota'); // Contoh: Cirebon, Indramayu, Majalengka
        $table->text('alamat')->nullable();
        $table->string('nomor_telp')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
