<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
class ExtraSeeder extends Seeder {
    public function run(): void {
        $c1 = Category::firstOrCreate(['name' => 'Snack']);
        $c2 = Category::firstOrCreate(['name' => 'Sambal']);
        Product::firstOrCreate(['name' => 'Kerupuk Udang'], ['category_id' => $c1->id, 'price' => 5000, 'cost_price' => 2000, 'stock' => 100]);
        Product::firstOrCreate(['name' => 'Sambal Bawang'], ['category_id' => $c2->id, 'price' => 3000, 'cost_price' => 1000, 'stock' => 50]);
    }
}
