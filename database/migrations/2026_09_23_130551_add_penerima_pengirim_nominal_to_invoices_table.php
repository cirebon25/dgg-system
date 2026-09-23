<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('nominal', 15, 2)->nullable()->after('tanggal');
            $table->string('nama_pengirim')->nullable()->after('nominal');
            $table->string('nama_penerima')->nullable()->after('nama_pengirim');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['nominal', 'nama_pengirim', 'nama_penerima']);
        });
    }
};
