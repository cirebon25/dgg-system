<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_employees', function (Blueprint $table) {
            $table->id();
            $table->string('pin', 32)->unique();
            $table->string('nip', 30)->nullable();
            $table->string('name');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sn', 30);
            $table->string('pin', 32);
            $table->dateTime('scan_at');
            $table->unsignedSmallInteger('verify_mode')->default(0);
            $table->unsignedSmallInteger('in_out_mode')->default(0);
            $table->unsignedInteger('work_code')->default(0);
            $table->string('att_id', 50)->nullable();
            $table->timestamps();

            $table->unique(['sn', 'pin', 'scan_at'], 'attendance_logs_unique');
            $table->index('scan_at');
            $table->index('pin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('attendance_employees');
    }
};