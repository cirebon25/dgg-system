<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_borrowing_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('technicians')->cascadeOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_borrowing_headers');
    }
};