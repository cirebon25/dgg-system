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
        // Tambahkan kolom customer_id setelah id
        $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('machines', function (Blueprint $table) {
        $table->dropForeign(['customer_id']);
        $table->dropColumn('customer_id');
    });
}
};
