<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_usage_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('technicians')->cascadeOnDelete();
            $table->enum('sumber_stok', ['gudang', 'tas'])->default('tas');
            $table->date('tanggal')->default(now());
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('part_usage_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_usage_header_id')->constrained('part_usage_headers')->cascadeOnDelete();
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            $table->foreignId('sparepart_id')->constrained('spareparts')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_usage_items');
        Schema::dropIfExists('part_usage_headers');
    }
};
