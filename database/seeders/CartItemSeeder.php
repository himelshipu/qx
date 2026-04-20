<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('cart_items')->insert([
                    'cart_id' => 1,
                    'package_id' => 1,
                    'influencer_id' => 1,
                    'campaign_id' => 1,
                    'quantity' => $faker->word,
                    'unit_price' => $faker->randomFloat(2, 10, 1000),
                    'currency' => $faker->word,
                    'notes' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}