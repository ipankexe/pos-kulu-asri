<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('paid', 'void', 'unpaid') DEFAULT 'unpaid'");
            DB::statement("ALTER TABLE transactions MODIFY COLUMN payment DECIMAL(10, 2) DEFAULT 0");
            DB::statement("ALTER TABLE transactions MODIFY COLUMN `change` DECIMAL(10, 2) DEFAULT 0");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('paid', 'void') DEFAULT 'paid'");
            DB::statement("ALTER TABLE transactions MODIFY COLUMN payment DECIMAL(10, 2) NOT NULL");
            DB::statement("ALTER TABLE transactions MODIFY COLUMN `change` DECIMAL(10, 2) NOT NULL");
        }
    }
};
