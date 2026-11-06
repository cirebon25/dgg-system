<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospect_visits', function (Blueprint $table) {
            $table->date('tanggal_followup')->nullable()->after('catatan');
            $table->timestamp('followup_reminded_at')->nullable()->after('tanggal_followup');
            $table->index('tanggal_followup');
        });
    }

    public function down(): void
    {
        Schema::table('prospect_visits', function (Blueprint $table) {
            $table->dropIndex(['tanggal_followup']);
            $table->dropColumn(['tanggal_followup', 'followup_reminded_at']);
        });
    }
};