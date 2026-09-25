<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sparepart_ros', function (Blueprint $table) {
            $table->id();
            $table->string('no_part');          // Kolom baru
            $table->string('kode_part');        // Kolom baru
            $table->string('nama_sparepart');
            $table->integer('stok')->default(0);
            // Kolom 'harga' sengaja dihilangkan sesuai permintaan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sparepart_ros');
    }
};
