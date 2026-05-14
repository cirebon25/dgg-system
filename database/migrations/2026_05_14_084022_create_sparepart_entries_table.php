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
    Schema::create('sparepart_entries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sparepart_id')->constrained()->cascadeOnDelete();
        $table->integer('jumlah');
        $table->string('supplier')->nullable();
        $table->string('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_entries');
    }
};
