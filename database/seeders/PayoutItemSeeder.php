<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutItemSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('payout_items')->insert([
                    'payout_id' => 1,
                    'order_item_id' => 1,
                    'amount' => $faker->randomFloat(2, 10, 5000),
                    'currency' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}