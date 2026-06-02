<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class RealMenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menu = [
            'Camilan' => [
                'Mix Platter' => 20000, 'Tempe Goreng' => 10000, 'Tahu Goreng' => 10000,
                'Bakwan Jagung' => 10000, 'Pisang Crispy' => 12000, 'Kentang Goreng' => 15000,
                'Jamur Crispy' => 12000, 'Otak Otak' => 12000, 'Tahu Bakso' => 12000,
                'Nugget' => 12000, 'Sosis Goreng' => 10000
            ],
            'Ajib Steak' => [
                'Chicken Crispy' => 23000, 'Chicken Crispy Cheese' => 25000,
                'Chicken Katsu' => 25000, 'Sirloin Steak' => 28000, 'Bakso Malang' => 18000
            ],
            'Minuman' => [
                'Es Soda Susu' => 15000, 'Es Jeruk' => 9000, 'Es Lemon Tea' => 9000,
                'Es Teh Manis' => 7000, 'Es Teh Tawar' => 5000, 'Teh Poci' => 15000,
                'Jeruk Panas' => 8000, 'Lemon Tea Panas' => 8000, 'Teh Panas Manis' => 6000,
                'Teh Tawar Panas' => 4000, 'Es Susu' => 10000, 'Susu Panas' => 10000,
                'Air Mineral' => 6000
            ],
            'Juice' => [
                'Juice Alpukat' => 17000, 'Juice Durian' => 17000, 'Juice Mangga' => 15000,
                'Juice Strawberry' => 14000, 'Juice Buah Naga' => 14000, 'Juice Melon' => 14000,
                'Juice Tomat+Wortel' => 13000, 'Juice Sirsak' => 13000, 'Juice Apel' => 14000,
                'Juice Jambu' => 12000, 'Juice Oreo' => 12000, 'Juice Tomat' => 12000, 'Juice Wortel' => 12000
            ],
            'Squash & Milkshake' => [
                'Squash Strawberry' => 17000, 'Squash Melon' => 17000, 'Squash Orange' => 17000,
                'Squash Lychee' => 17000, 'Squash Mangga' => 17000, 'Milkshake Strawberry' => 17000,
                'Milkshake Mangga' => 17000, 'Milkshake Orange' => 17000
            ],
            'Es Buah' => [
                'Es Buah' => 17000, 'Es Campur' => 17000
            ],
            'Spesial Milk Base' => [
                'Ice Red Velvet' => 17000, 'Ice Matcha Latte' => 17000,
                'Ice Taro Latte' => 17000, 'Ice Choco Latte' => 17000
            ],
            'Spesial Kopi' => [
                'Kopi Tubruk Kulu Asri' => 10000, 'Kopi Tubruk Classic' => 10000,
                'Cappucino Hot / Ice' => 17000, 'Es Kopsu Ori / Aren' => 17000,
                'Es Kopsu Vanilla' => 17000, 'Es Kopsu Caramel' => 17000, 'Americano' => 12000
            ],
            'Gurami' => [
                'Gurami Bakar' => 65000, 'Gurami Goreng' => 60000, 'Gurami Cobek' => 65000,
                'Gurami Pecak' => 65000, 'Gurami Sambal Matah' => 65000, 'Gurami Asam Manis' => 65000,
                'Gurami Saus Tiram' => 65000, 'Gurami Lombok Ijo' => 65000, 'Gurami Sop' => 60000,
                'Gurami Pepes' => 60000, 'Gurami Saus Padang' => 65000
            ],
            'Lele' => [
                'Lele Goreng / Bakar' => 25000, 'Lele Asam Manis' => 28000, 'Lele Saus Padang' => 28000,
                'Lele Saus Tiram' => 28000, 'Lele Lombok Ijo' => 28000, 'Lele Cobek' => 28000,
                'Lele Acar' => 28000, 'Lele Penyet' => 28000, 'Lele Sambal Matah' => 28000, 'Lele Asam Pedas' => 28000
            ],
            'Kakap' => [
                'Kakap Goreng' => 40000, 'Kakap Bakar' => 45000, 'Kakap Saus Tiram' => 45000,
                'Kakap Saus Padang' => 45000, 'Kakap Cobek' => 45000, 'Kakap Sambal Matah' => 45000
            ],
            'Cumi' => [
                'Cumi Goreng Tepung' => 35000, 'Cumi Asam Manis' => 40000, 'Cumi Saus Padang' => 40000,
                'Cumi Saus Tiram' => 40000, 'Cumi Lombok Ijo' => 40000
            ],
            'Udang' => [
                'Udang Goreng Tepung' => 35000, 'Udang Asam Manis' => 40000, 'Udang Saus Padang' => 40000,
                'Udang Saus Tiram' => 40000, 'Udang Lombok Ijo' => 40000
            ],
            'Penyet' => [
                'Tempe Penyet' => 8000, 'Tahu Penyet' => 8000
            ],
            'Ayam' => [
                'Ayam Bakar/Goreng' => 30000, 'Ayam Asam Manis' => 35000, 'Ayam Saus Padang' => 35000,
                'Ayam Saus Tiram' => 35000, 'Ayam Pecak' => 35000, 'Ayam Cobek' => 35000,
                'Ayam Lombok Ijo' => 35000, 'Ayam Asam Pedas' => 35000
            ],
            'Bebek' => [
                'Bebek Bakar/Goreng' => 40000, 'Bebek Asam Manis' => 45000, 'Bebek Saus Padang' => 45000,
                'Bebek Saus Tiram' => 45000, 'Bebek Lombok Ijo' => 45000
            ],
            'Nasi' => [
                'Nasi Putih' => 6000, 'Nasi Goreng Kulu Asri' => 17000, 'Nasi Goreng Hijau' => 20000,
                'Nasi Goreng Seafood' => 24000, 'Nasi Goreng Special' => 27000
            ],
            'Sambal' => [
                'Sambal Kulu Asri' => 5000, 'Sambal Mentah' => 5000, 'Sambal Lamongan' => 5000,
                'Sambal Ijo' => 5000, 'Sambal Tomat' => 5000, 'Sambal Taoge' => 7000
            ],
            'Mie & Iga' => [
                'Mie Goreng Telor' => 15000, 'Mie Rebus Telor' => 15000, 'Sop Iga' => 45000, 'Sop Iga Bakar' => 50000
            ],
            'Sayuran' => [
                'Oseng Kangkung' => 15000, 'Oseng Taoge' => 10000, 'Sayur Asem' => 6000,
                'Terancam' => 10000, 'Urapan/Gudangan' => 10000, 'Capcay' => 20000
            ]
        ];

        foreach ($menu as $categoryName => $products) {
            $category = Category::create([
                'name' => $categoryName
            ]);

            foreach ($products as $productName => $price) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $productName,
                    'cost_price' => $price * 0.70,
                    'price' => $price,
                    'stock' => 100
                ]);
            }
        }
    }
}
