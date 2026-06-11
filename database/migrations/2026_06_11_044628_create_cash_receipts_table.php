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
   Schema::create('cash_receipts', function (Blueprint $table) {
   $table->id();
   $table->date('tanggal');
   $table->string('no_bukti', 30)->nullable();
   $table->string('sumber_dana', 255);
   $table->bigInteger('jumlah');
   $table->text('keterangan');
   $table->string('dibuat_oleh', 100)->nullable();
   $table->timestamps();
   });
   }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_receipts');
    }
};
