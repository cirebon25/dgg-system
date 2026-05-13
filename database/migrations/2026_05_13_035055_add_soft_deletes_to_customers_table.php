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
    // Cek dulu, kalau kolom 'deleted_at' BELUM ADA, baru buat.
    if (!Schema::hasColumn('customers', 'deleted_at')) {
        Schema::table('customers', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
