<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->string('dari_lokasi')->default('Gudang Cirebon');
            $table->string('ke_lokasi')->default('Gudang Bandung');
            $table->date('tanggal_retur');
            $table->string('kondisi_saat_retur');
            $table->text('keterangan_kerusakan')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->string('dikirim_oleh')->nullable();
            $table->enum('status_retur', ['Dikirim', 'Selesai Servis', 'Kembali ke Cirebon'])->default('Dikirim');
            $table->date('tanggal_selesai_servis')->nullable();
            $table->date('tanggal_kembali')->nullable();
            $table->text('hasil_servis')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_returns');
    }
};