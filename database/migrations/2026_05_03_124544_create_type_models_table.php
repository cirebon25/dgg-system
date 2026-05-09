<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_models', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tipe')->unique(); // Contoh: iR 2525, M 2040, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_models');
    }
};
