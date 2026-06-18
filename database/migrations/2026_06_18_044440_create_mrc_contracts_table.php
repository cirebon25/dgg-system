<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mrc_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('harga_sewa', 15, 0)->default(0);      // harga sewa bulanan
            $table->integer('free_bw')->default(0);                 // kuota free lembar BW
            $table->decimal('harga_bw', 10, 0)->default(0);         // harga per lembar BW jika melebihi
            $table->integer('free_color')->default(0);              // kuota free lembar Color
            $table->decimal('harga_color', 10, 0)->default(0);      // harga per lembar Color jika melebihi
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mrc_contracts');
    }
};