<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistItemSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('wishlist_items')->insert([
                    'wishlist_id' => 1,
                    'influencer_id' => 1,
                    'notes' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}