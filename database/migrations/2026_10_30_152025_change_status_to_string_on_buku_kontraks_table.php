<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE buku_kontraks MODIFY status VARCHAR(30) NOT NULL DEFAULT 'depo_cirebon'");

        // isi ulang baris yang sempat tersimpan kosong karena enum
        DB::table('buku_kontraks')->where('status', '')->update(['status' => 'selesai']);
    }

    public function down(): void
    {
        //
    }
};
