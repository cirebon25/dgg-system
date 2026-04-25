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
    Schema::create('service_logs', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal');
        $table->time('jam_mulai');
        $table->time('jam_selesai');
        $table->text('kerusakan');
        $table->text('perbaikan');
        $table->text('sparepart')->nullable();
        $table->string('tipe_kunjungan');
        $table->integer('counter_color')->default(0);
        $table->integer('counter_bw')->default(0);
        $table->integer('counter_scan')->default(0);
        
        // Relasi ke Teknisi & Mesin
        $table->foreignId('technician_id')->constrained('technicians')->cascadeOnDelete();
        $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_logs');
    }
};
