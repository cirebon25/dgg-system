<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('tipe', 30);
            $table->string('tipe_kunjungan', 100)->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->foreignId('sparepart_id')->nullable()->constrained('spareparts')->nullOnDelete();
            $table->unsignedInteger('jumlah')->nullable();
            $table->unsignedBigInteger('counter')->nullable();
            $table->unsignedBigInteger('service_log_id')->nullable()->index();
            $table->unsignedBigInteger('service_log_sparepart_id')->nullable()->index();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['machine_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_events');
    }
};