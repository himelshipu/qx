<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('wishlists')->insert([
                    'user_id' => 1,
                    'name' => $faker->name,
                    'is_default' => $faker->boolean,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}