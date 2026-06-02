<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiningTable;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing tables if any, optional but good if they want to 'ubah' (change) 
        // We will just truncate or delete all.
        DiningTable::query()->delete();

        $tables = [];

        // Patin 1-14
        for ($i = 1; $i <= 14; $i++) {
            $tables[] = ['name' => 'Patin ' . $i, 'status' => 'available'];
        }

        // Gurami 1-9 (interpreting "gurami 1,9" as 1-9)
        for ($i = 1; $i <= 9; $i++) {
            $tables[] = ['name' => 'Gurami ' . $i, 'status' => 'available'];
        }

        // Bawal 1-12
        for ($i = 1; $i <= 12; $i++) {
            $tables[] = ['name' => 'Bawal ' . $i, 'status' => 'available'];
        }

        // Others
        $tables[] = ['name' => 'Saung Nila', 'status' => 'available'];
        $tables[] = ['name' => 'Saung Bawal', 'status' => 'available'];
        $tables[] = ['name' => 'Greenroom', 'status' => 'available'];

        foreach ($tables as $table) {
            DiningTable::create($table);
        }
    }
}
