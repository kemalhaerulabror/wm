<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $categories = ['Sport', 'Matic', 'Cub', 'Big Bike', 'Trail', 'Touring', 'Naked Bike', 'Classic/Retro'];
        $brands = ['Honda', 'Yamaha', 'Suzuki', 'Kawasaki', 'Vespa', 'Ducati', 'Harley Davidson', 'BMW', 'KTM', 'Triumph', 'TVS', 'Bajaj'];
        
        $motorNames = [
            'Honda' => ['Vario', 'Beat', 'PCX', 'CBR', 'CB', 'CRF', 'Supra', 'ADV', 'Genio', 'Monkey'],
            'Yamaha' => ['NMAX', 'Aerox', 'Mio', 'XSR', 'R15', 'R25', 'MT', 'Vixion', 'Jupiter', 'Lexi'],
            'Suzuki' => ['GSX', 'Satria', 'Smash', 'Address', 'Nex', 'Gixxer', 'Intruder', 'V-Strom'],
            'Kawasaki' => ['Ninja', 'ZX', 'W', 'KLX', 'Versys', 'Z', 'Vulcan', 'D-Tracker'],
            'Vespa' => ['Primavera', 'Sprint', 'GTS', 'LX', 'S', 'PX', 'Elettrica'],
            'Ducati' => ['Panigale', 'Monster', 'Scrambler', 'Diavel', 'Hypermotard', 'Multistrada'],
            'Harley Davidson' => ['Sportster', 'Fat Boy', 'Street', 'Road King', 'Softail', 'Iron'],
            'BMW' => ['R1250', 'F800', 'S1000', 'K1600', 'G310', 'C400X'],
            'KTM' => ['Duke', 'RC', 'Adventure', 'Enduro', 'EXC', 'SX'],
            'Triumph' => ['Bonneville', 'Street Twin', 'Scrambler', 'Tiger', 'Speed Triple', 'Rocket'],
            'TVS' => ['Apache', 'Neo', 'Rockz', 'Max', 'XL'],
            'Bajaj' => ['Pulsar', 'Dominar', 'Avenger', 'Platina']
        ];
        
        $engineSizes = ['110cc', '125cc', '150cc', '155cc', '160cc', '200cc', '250cc', '300cc', '400cc', '500cc', '600cc', '650cc', '800cc', '1000cc', '1250cc'];
        $years = ['2020', '2021', '2022', '2023', '2024'];
        
        // Membuat 50 produk motor dummy
        for ($i = 0; $i < 50; $i++) {
            $brand = $faker->randomElement($brands);
            $motorType = $faker->randomElement($motorNames[$brand]);
            $engineSize = $faker->randomElement($engineSizes);
            $year = $faker->randomElement($years);
            
            $name = $brand . ' ' . $motorType . ' ' . $engineSize . ' ' . $year;
            
            // Harga disesuaikan dengan cc motor (semakin besar cc, semakin mahal)
            $basePrice = str_replace('cc', '', $engineSize);
            $price = $basePrice * 100000 + $faker->numberBetween(5000000, 20000000);
            
            // Batasi harga maksimum
            if ($price > 300000000) {
                $price = $faker->numberBetween(250000000, 300000000);
            }
            
            // Batasi harga minimum
            if ($price < 15000000) {
                $price = $faker->numberBetween(15000000, 25000000);
            }
            
            $stock = $faker->numberBetween(3, 20);
            $sold = $faker->numberBetween(0, 50);
            $rating = $faker->randomFloat(1, 3.5, 5.0);
            $is_featured = $faker->boolean(30); // 30% peluang untuk featured
            
            // Deskripsi spesifik motor
            $description = "Motor " . $name . " merupakan " . $faker->randomElement(['pilihan terbaik', 'kendaraan andal', 'varian terbaru', 'motor premium']) 
                . " dari " . $brand . ". "
                . "Dengan mesin " . $engineSize . ", motor ini mampu menghasilkan "
                . $faker->numberBetween(5, 20) . " HP dan torsi " . $faker->numberBetween(10, 30) . " Nm. "
                . "Fitur unggulan: " . $faker->randomElement(['ABS', 'Traction Control', 'Digital Speedometer', 'LED Headlight', 'USB Charging Port', 'Keyless Ignition'])
                . ", " . $faker->randomElement(['Slipper Clutch', 'Riding Modes', 'Smart Key System', 'Inverted Front Fork', 'Dual Channel ABS', 'Combi Brake System'])
                . ", dan " . $faker->randomElement(['Adjustable Windshield', 'Backrest', 'Large Storage', 'Bluetooth Connectivity', 'Full Digital Display', 'Anti-Theft Alarm'])
                . ". Tersedia dalam warna " . $faker->colorName . ", " . $faker->colorName . ", dan " . $faker->colorName . ".";
            
            Product::create([
                'name' => $name,
                'category' => $faker->randomElement($categories),
                'brand' => $brand,
                'price' => $price,
                'description' => $description,
                'image' => 'motor_' . ($i + 1) . '.jpg',
                'stock' => $stock,
                'sold' => $sold,
                'rating' => $rating,
                'is_featured' => $is_featured,
                'status' => true
            ]);
        }
    }
} 