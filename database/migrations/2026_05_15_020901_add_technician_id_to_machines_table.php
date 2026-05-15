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
    Schema::table('machines', function (Blueprint $table) {
        // Tambahkan kolom teknisi yang boleh kosong (nullable)
        // Dan hubungkan (constrained) ke tabel technicians
        $table->foreignId('technician_id')->nullable()->constrained('technicians')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('machines', function (Blueprint $table) {
        $table->dropForeign(['technician_id']);
        $table->dropColumn('technician_id');
    });
}
};
