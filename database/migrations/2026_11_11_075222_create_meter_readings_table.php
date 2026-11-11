<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('machine_id')->index();
            $t->unsignedBigInteger('customer_id')->nullable()->index();
            $t->unsignedBigInteger('technician_id')->nullable()->index();
            $t->date('tanggal')->index();
            $t->bigInteger('counter_bw_lalu')->default(0);
            $t->bigInteger('counter_bw')->default(0);
            $t->bigInteger('usage_bw')->default(0);
            $t->bigInteger('counter_color_lalu')->default(0);
            $t->bigInteger('counter_color')->nullable();
            $t->bigInteger('usage_color')->default(0);
            $t->timestamps();
            $t->softDeletes();
        });

        // Salin data MRC lama (tabel service_logs tidak diubah)
        DB::statement("
            INSERT INTO meter_readings
              (machine_id, customer_id, technician_id, tanggal,
               counter_bw_lalu, counter_bw, usage_bw,
               counter_color_lalu, counter_color, usage_color,
               created_at, updated_at)
            SELECT machine_id, customer_id, technician_id, tanggal,
               COALESCE(counter_bw_lalu,0), COALESCE(counter_bw,0), COALESCE(usage_bw,0),
               COALESCE(counter_color_lalu,0), counter_color, COALESCE(usage_color,0),
               created_at, updated_at
            FROM service_logs
            WHERE is_mrc = 1 AND deleted_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
