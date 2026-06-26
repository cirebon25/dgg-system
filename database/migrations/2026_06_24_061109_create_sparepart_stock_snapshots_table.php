<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sparepart_stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained('spareparts')->cascadeOnDelete();
            $table->integer('bulan'); // 1-12
            $table->integer('tahun');
            $table->integer('stok_akhir'); // stok pada saat snapshot diambil
            $table->timestamp('snapshot_at'); // kapan tepatnya diambil
            $table->timestamps();

            $table->unique(['sparepart_id', 'bulan', 'tahun']); // 1 sparepart cuma 1 snapshot per bulan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sparepart_stock_snapshots');
    }
};