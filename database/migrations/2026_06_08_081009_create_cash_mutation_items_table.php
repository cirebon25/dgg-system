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
   Schema::create('cash_mutation_items', function (Blueprint $table) {
   $table->id();
   $table->foreignId('cash_mutation_id')->constrained('cash_mutations')->cascadeOnDelete();
   $table->string('uraian');
   $table->string('plat_nomor')->nullable(); // Kolom Baru
   $table->integer('km_awal')->nullable(); // Kolom Baru
   $table->integer('km_akhir')->nullable(); // Kolom Baru
   $table->string('kode_perkiraan')->nullable();
   $table->bigInteger('jumlah')->default(0);
   $table->timestamps();
   });
   }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_mutation_items');
    }
};