<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \DB::statement("ALTER TABLE machines MODIFY COLUMN status ENUM('Ready','Rented','Refurbish','Returned') NOT NULL DEFAULT 'Ready'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE machines MODIFY COLUMN status ENUM('Ready','Rented','Refurbish') NOT NULL DEFAULT 'Ready'");
    }
};