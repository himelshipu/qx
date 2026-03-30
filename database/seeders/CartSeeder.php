<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('carts')->insert([
                    'user_id' => 1,
                    'status' => $faker->randomElement(['pending','approved','rejected','completed']),
                    'expires_at' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}