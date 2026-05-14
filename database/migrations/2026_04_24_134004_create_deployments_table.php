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
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_instal');
            $table->date('tanggal_tarik')->nullable(); // Diisi nanti kalau sewa selesai
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->integer('counter_bw')->default(0);
            $table->integer('counter_color')->default(0); // PASTIKAN ADA BARIS INI
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
