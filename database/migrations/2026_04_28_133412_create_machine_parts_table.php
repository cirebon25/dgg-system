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
        Schema::create('machine_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->onDelete('cascade'); // Mesin yang mana
            $table->foreignId('sparepart_id')->constrained()->onDelete('cascade'); // Part apa (Drum/Fuser/dll)
            $table->integer('current_usage')->default(0); // Counter sekarang (Klik)
            $table->integer('limit_usage')->default(100000); // Batas umur part (Klik)
            $table->date('last_replaced_at')->nullable(); // Kapan terakhir ganti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_parts');
    }
};
