<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('po_parts', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('po_part_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_part_id')->constrained('po_parts')->cascadeOnDelete();
            $table->foreignId('sparepart_id')->nullable()->constrained('spareparts')->nullOnDelete();
            $table->string('nama_part');         // manual atau dari dropdown
            $table->string('merk_type')->nullable();
            $table->string('kode_part')->nullable();
            $table->integer('jumlah')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_part_items');
        Schema::dropIfExists('po_parts');
    }
};