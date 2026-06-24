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
        Schema::table('machine_replacements', function (Blueprint $table) {
            $table->foreignId('deployment_id')
                ->nullable()
                ->after('new_machine_id')
                ->constrained('deployments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('machine_replacements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deployment_id');
        });
    }
};