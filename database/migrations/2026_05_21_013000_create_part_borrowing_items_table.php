<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_borrowing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_borrowing_header_id')->constrained('part_borrowing_headers')->cascadeOnDelete();
            $table->foreignId('sparepart_id')->constrained('spareparts')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_borrowing_items');
    }
};