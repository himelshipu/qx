<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('payouts')->insert([
                    'creator_id' => 1,
                    'payout_account_id' => $faker->numberBetween(1, 1000),
                    'amount' => $faker->randomFloat(2, 10, 5000),
                    'currency' => $faker->word,
                    'status' => $faker->randomElement(['pending','approved','rejected','completed']),
                    'external_payout_id' => 1,
                    'paid_at' => 1,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}