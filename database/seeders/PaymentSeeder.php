<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('payments')->insert([
                    'order_id' => 1,
                    'payment_provider' => 1,
                    'provider_payment_id' => 1,
                    'amount' => $faker->randomFloat(2, 10, 5000),
                    'currency' => $faker->word,
                    'status' => $faker->randomElement(['pending','approved','rejected','completed']),
                    'paid_at' => 1,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}