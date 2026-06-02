<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kuluasri.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@kuluasri.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        $catMakanan = Category::create(['name' => 'Makanan']);
        $catMinuman = Category::create(['name' => 'Minuman']);

        Product::create([
            'name' => 'Nasi Goreng Kulu',
            'category_id' => $catMakanan->id,
            'price' => 25000,
            'cost_price' => 15000,
            'stock' => 50,
            'min_stock' => 10,
        ]);

        Product::create([
            'name' => 'Ayam Bakar Madu',
            'category_id' => $catMakanan->id,
            'price' => 30000,
            'cost_price' => 20000,
            'stock' => 40,
            'min_stock' => 5,
        ]);

        Product::create([
            'name' => 'Es Teh Manis',
            'category_id' => $catMinuman->id,
            'price' => 5000,
            'cost_price' => 2000,
            'stock' => 100,
            'min_stock' => 20,
        ]);
        
        Product::create([
            'name' => 'Es Jeruk',
            'category_id' => $catMinuman->id,
            'price' => 8000,
            'cost_price' => 3000,
            'stock' => 80,
            'min_stock' => 15,
        ]);

        $this->call(TableSeeder::class);
    }
}
