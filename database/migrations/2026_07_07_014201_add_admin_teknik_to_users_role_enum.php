<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','teknisi','keuangan','manager','admin_teknik') NOT NULL DEFAULT 'teknisi'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','teknisi','keuangan','manager') NOT NULL DEFAULT 'teknisi'");
    }
};
