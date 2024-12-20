<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Faker\Factory as Faker;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Menyisipkan data dummy ke tabel items
        for ($i = 0; $i < 10; $i++) {
            DB::table('items')->insert([
                'item_category_id' => rand(1, 5),  // Anggap ada 5 kategori
                'name' => $faker->word,
                'price' => $faker->numberBetween(1000, 100000),
                'stock' => $faker->numberBetween(1, 100),
                'image' => $faker->imageUrl(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
