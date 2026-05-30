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
        Schema::create('part_replacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained('machines');
            $table->foreignId('sparepart_id')->constrained('spareparts');
            $table->foreignId('service_log_id')->constrained('service_logs');
            $table->integer('counter_saat_ganti')->default(0);
            $table->integer('counter_sebelumnya')->default(0);
            $table->integer('selisih')->default(0);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_replacements');
    }
};
