<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_kontraks', function (Blueprint $table) {
            $table->id();
            $table->string('no_kontrak');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tgl_dtg');
            $table->foreignId('technician_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tgl_dibawa')->nullable();
            $table->enum('status', ['depo_cirebon', 'dibawa_teknisi', 'di_customer'])
                ->default('depo_cirebon');
            $table->date('tgl_status');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_kontraks');
    }
};
