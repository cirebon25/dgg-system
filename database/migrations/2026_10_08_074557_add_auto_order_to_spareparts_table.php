<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('spareparts', function (Blueprint $t) {
            $t->boolean('auto_order')->default(true)->after('stok_minimum');
        });
    }

    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $t) {
            $t->dropColumn('auto_order');
        });
    }
};
