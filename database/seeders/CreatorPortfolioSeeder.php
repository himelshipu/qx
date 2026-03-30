<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatorPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('creator_portfolios')->insert([
                    'creator_id' => 1,
                    'media_type' => 'active',
                    'file_path' => $faker->word,
                    'title' => $faker->sentence,
                    'description' => $faker->paragraph,
                    'sort_order' => $faker->word,
                    'is_active' => $faker->boolean,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}