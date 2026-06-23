<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marketings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_marketing');
            $table->string('no_telp')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('kota')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pic_nama')->nullable();
            $table->string('pic_jabatan')->nullable();
            $table->string('pic_telp')->nullable();
            $table->enum('status', ['Baru', 'Proses', 'Closing', 'Gagal'])->default('Baru');
            $table->timestamps();
        });

        Schema::create('prospect_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->foreignId('marketing_id')->constrained('marketings')->cascadeOnDelete();
            $table->date('tanggal_kunjungan');
            $table->string('jenis_mesin_existing')->nullable();
            $table->string('merk_mesin_existing')->nullable();
            $table->enum('hasil_kunjungan', ['Interest', 'Follow Up', 'Closing', 'Gagal'])->default('Follow Up');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_visits');
        Schema::dropIfExists('prospects');
        Schema::dropIfExists('marketings');
    }
};
