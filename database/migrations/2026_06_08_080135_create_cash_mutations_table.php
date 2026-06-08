<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
   Schema::create('cash_mutations', function (Blueprint $table) {
   $table->id();
   $table->string('no_voucher')->nullable(); // Menyimpan format otomatis: 1/VI/26
   $table->integer('no_urut')->default(1); // Menyimpan angka index urut
   $table->date('tanggal');
   $table->string('terbilang')->nullable();
   $table->string('jenis_pembayaran')->default('Tunai');
   $table->bigInteger('total_jumlah')->default(0);
   $table->string('pembuat')->nullable();
   $table->string('pemeriksa')->nullable();
   $table->string('penerima')->nullable();
   $table->timestamps();
   });
   }

    public function down(): void
    {
        Schema::dropIfExists('cash_mutations');
    }
};