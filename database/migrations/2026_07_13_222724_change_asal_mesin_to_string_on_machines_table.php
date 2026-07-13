<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah dari ENUM kaku ke VARCHAR agar tidak terikat pada
        // daftar tetap yang bisa berbeda dari opsi form Filament.
        // Validasi nilai yang diizinkan sepenuhnya didelegasikan
        // ke Select::make('asal_mesin')->options() di MachineResource.
        Schema::table('machines', function (Blueprint $table) {
            $table->string('asal_mesin', 50)
                ->default('BARU')
                ->nullable(false)
                ->change();
        });

        // Backfill data yang sudah rusak akibat silent-fail ENUM sebelumnya.
        // Baris dengan asal_mesin kosong ('') dianggap seharusnya 'EX LUAR'
        // berdasarkan percobaan edit terakhir yang gagal tersimpan.
        DB::table('machines')
            ->where('asal_mesin', '')
            ->update(['asal_mesin' => 'EX LUAR']);
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->enum('asal_mesin', ['KANIBAL', 'BARU', 'SECOND', 'TRADE_IN'])
                ->default('BARU')
                ->change();
        });
    }
};
