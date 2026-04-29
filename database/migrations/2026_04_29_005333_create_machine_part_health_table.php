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
    Schema::create('machine_part_health', function (Blueprint $table) {
        $table->id();
        $table->foreignId('machine_id')->constrained()->onDelete('cascade');
        $table->foreignId('sparepart_id')->constrained()->onDelete('cascade');
        $table->integer('last_replaced_counter')->default(0); // Counter saat part diganti
        $table->date('last_replaced_at'); // Tanggal ganti
        $table->integer('current_usage')->default(0); // Selisih counter sekarang - counter ganti
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_part_health');
    }
};
