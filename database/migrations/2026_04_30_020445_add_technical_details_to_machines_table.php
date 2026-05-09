<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            if (! Schema::hasColumn('machines', 'volt')) {
                $table->string('volt')->nullable()->after('tipe_model');
                $table->string('finisher')->nullable()->after('volt');
                $table->string('cover')->nullable()->after('finisher');
                $table->string('kaset')->nullable()->after('cover');
            }
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn(['volt', 'finisher', 'cover', 'kaset']);
        });
    }
};
