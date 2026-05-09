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
            // Mengubah kolom menjadi nullable (boleh kosong)
            $table->integer('counter_color')->nullable()->change();
            $table->foreignId('sparepart_id')->nullable()->change();
            $table->integer('jumlah_sparepart')->nullable()->change();

            // Pastikan usage juga boleh kosong agar tidak error hitungan
            $table->integer('usage_color')->nullable()->change();
            $table->integer('usage_bw')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
