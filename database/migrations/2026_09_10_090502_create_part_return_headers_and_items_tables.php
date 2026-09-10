<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_return_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('part_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_return_header_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sparepart_id')->constrained();
            $table->integer('jumlah');
            $table->timestamps();
        });

        // Migrasi data lama: tiap baris part_returns lama -> 1 header + 1 item baru
        // Tabel & data lama TIDAK dihapus/diubah, cuma dibaca (read-only).
        $oldReturns = DB::table('part_returns')->orderBy('id')->get();

        foreach ($oldReturns as $old) {
            $headerId = DB::table('part_return_headers')->insertGetId([
                'technician_id' => $old->technician_id,
                'keterangan'    => null,
                'created_at'    => $old->created_at,
                'updated_at'    => $old->updated_at,
            ]);

            DB::table('part_return_items')->insert([
                'part_return_header_id' => $headerId,
                'sparepart_id'          => $old->sparepart_id,
                'jumlah'                => $old->jumlah,
                'created_at'            => $old->created_at,
                'updated_at'            => $old->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('part_return_items');
        Schema::dropIfExists('part_return_headers');
    }
};