<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_ledgers', function (Blueprint $table) {
            $table->id();
            $table->integer('no_urut')->default(1);
            $table->string('no_surat')->nullable();        // format: 01/VI/26
            $table->date('tanggal');
            $table->string('keterangan');
            $table->bigInteger('uang_masuk')->default(0);
            $table->bigInteger('uang_keluar')->default(0);
            $table->string('dibuat_oleh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_ledgers');
    }
};
