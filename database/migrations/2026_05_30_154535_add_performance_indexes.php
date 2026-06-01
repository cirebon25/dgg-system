<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index pada service_logs.tanggal
        // Alasan: dipakai defaultSort('tanggal', 'desc') setiap halaman service log dibuka
        Schema::table('service_logs', function (Blueprint $table) {
            $table->index('tanggal', 'idx_service_logs_tanggal');
        });

        // Index pada machines.status
        // Alasan: dipakai WHERE status = 'Ready' setiap form deployment dibuka
        Schema::table('machines', function (Blueprint $table) {
            $table->index('status', 'idx_machines_status');
        });

        // Index pada customers.kota
        // Alasan: dipakai orderBy('kota') di grouping halaman customer
        Schema::table('customers', function (Blueprint $table) {
            $table->index('kota', 'idx_customers_kota');
        });

        // Index pada deployments.tanggal_instal
        // Alasan: kolom sortable di tabel deployment
        Schema::table('deployments', function (Blueprint $table) {
            $table->index('tanggal_instal', 'idx_deployments_tanggal_instal');
        });

        // Composite index pada technician_stocks.(technician_id, sparepart_id)
        // Alasan: query WHERE technician_id = ? AND sparepart_id = ?
        // dipakai setiap validasi stok saat input service log
        // Index individual sudah ada dari FK, tapi composite jauh lebih cepat untuk query dua kolom sekaligus
        Schema::table('technician_stocks', function (Blueprint $table) {
            $table->index(['technician_id', 'sparepart_id'], 'idx_tech_stocks_tech_part');
        });
    }

    public function down(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropIndex('idx_service_logs_tanggal');
        });

        Schema::table('machines', function (Blueprint $table) {
            $table->dropIndex('idx_machines_status');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_kota');
        });

        Schema::table('deployments', function (Blueprint $table) {
            $table->dropIndex('idx_deployments_tanggal_instal');
        });

        Schema::table('technician_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_tech_stocks_tech_part');
        });
    }
};
