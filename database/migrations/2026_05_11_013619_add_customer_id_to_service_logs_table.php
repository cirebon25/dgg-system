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
    Schema::table('service_logs', function (Blueprint $table) {
        // Tambahkan customer_id sebagai penghubung langsung
        $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade')->after('machine_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            //
        });
    }
};
