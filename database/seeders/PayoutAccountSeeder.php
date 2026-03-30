<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutAccountSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('payout_accounts')->insert([
                    'creator_id' => 1,
                    'provider' => 1,
                    'account_identifier' => $faker->numberBetween(1, 1000),
                    'account_name' => $faker->name,
                    'is_default' => $faker->boolean,
                    'is_active' => $faker->boolean,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}