<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_events', function (Blueprint $table) {
            if (!Schema::hasColumn('machine_events', 'tipe_kunjungan')) {
                $table->string('tipe_kunjungan', 100)->nullable()->after('tipe');
            }
            if (!Schema::hasColumn('machine_events', 'teknisi_2')) {
                $table->string('teknisi_2', 100)->nullable()->after('technician_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('machine_events', function (Blueprint $table) {
            foreach (['tipe_kunjungan', 'teknisi_2'] as $col) {
                if (Schema::hasColumn('machine_events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};