<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('cash_mutation_id')->nullable()->after('id');
            $table->index('cash_mutation_id');
        });
    }

    public function down(): void
    {
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->dropIndex(['cash_mutation_id']);
            $table->dropColumn('cash_mutation_id');
        });
    }
};
