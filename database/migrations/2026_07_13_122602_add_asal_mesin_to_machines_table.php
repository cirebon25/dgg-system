<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->enum('asal_mesin', ['KANIBAL', 'BARU', 'SECOND', 'TRADE_IN'])
                ->default('BARU')
                ->after('status')
                ->comment('Sumber/asal perolehan mesin');
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('asal_mesin');
        });
    }
};