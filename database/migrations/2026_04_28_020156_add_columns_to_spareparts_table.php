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
        Schema::table('spareparts', function (Blueprint $table) {
            // Kita hapus baris 'stok' karena sudah ada di database Boss
            $table->string('code_part')->nullable()->after('nama_sparepart');
            $table->string('no_part')->nullable()->after('code_part');
            $table->integer('saldo_masuk')->default(0);
            $table->integer('saldo_keluar')->default(0);
            // Baris stok dihapus dari sini supaya tidak bentrok
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropColumn(['code_part', 'no_part', 'saldo_masuk', 'saldo_keluar']);
        });
    }
};
