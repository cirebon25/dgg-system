<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('spareparts', function (Blueprint $t) {
            $t->unsignedInteger('stok_minimum')->default(5)->after('stok');
        });

        Schema::create('order_parts', function (Blueprint $t) {
            $t->id();
            $t->string('no_order')->unique();
            $t->date('tanggal');
            $t->string('status')->default('draft'); // draft, diajukan, dipesan, diterima, batal
            $t->text('catatan')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('order_part_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_part_id')->constrained()->cascadeOnDelete();
            $t->foreignId('sparepart_id')->constrained()->cascadeOnDelete();
            $t->integer('stok_saat_ini')->default(0);
            $t->unsignedInteger('jumlah_order');
            $t->string('keterangan')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_part_items');
        Schema::dropIfExists('order_parts');
        Schema::table('spareparts', fn(Blueprint $t) => $t->dropColumn('stok_minimum'));
    }
};
