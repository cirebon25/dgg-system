<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('cash_receipt_id')->nullable()->after('cash_mutation_id');
        });
    }

    public function down(): void
    {
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->dropColumn('cash_receipt_id');
        });
    }
};
