<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfluencerPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('influencer_portfolios')->insert([
                'influencer_id' => 1,
                'media_type'    => $faker->randomElement(['image', 'video']),
                'file_path'     => 'public/portfolios/file_' . $i . '.jpg',
                'title'         => $faker->sentence,
                'description'   => $faker->paragraph,
                'sort_order'    => $faker->numberBetween(0, 100),
                'is_active'     => $faker->boolean,

                'created_at'    => now(),
                'updated_at'    => now()
            ]);
        }
    }
}
