<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodation_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained()->onDelete('cascade');
            $table->string('wilayah');
            $table->date('dari_tanggal');
            $table->date('sampai_tanggal');
            $table->integer('lama_hari')->storedAs('DATEDIFF(sampai_tanggal, dari_tanggal) + 1');
            $table->decimal('biaya_transportasi', 15, 2)->default(0);
            $table->decimal('konsumsi_karyawan', 15, 2)->default(0);
            $table->decimal('pengeluaran_lain_1', 15, 2)->default(0);
            $table->string('keterangan_lain_1')->nullable();
            $table->decimal('pengeluaran_lain_2', 15, 2)->default(0);
            $table->string('keterangan_lain_2')->nullable();
            $table->decimal('total_biaya', 15, 2)->default(0);
            // Status: Draft -> Diajukan -> Disetujui -> Ditolak
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak'])->default('Draft');
            $table->text('catatan_penolakan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Tabel kunjungan customer di dalam klaim ini
        Schema::create('accommodation_claim_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_claim_id')->constrained()->onDelete('cascade');
            $table->integer('no_urut');
            $table->string('nama_customer');
            $table->string('alamat');
            $table->string('keterangan'); // RM, RN, CM, PENAWARAN, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_claim_visits');
        Schema::dropIfExists('accommodation_claims');
    }
};
