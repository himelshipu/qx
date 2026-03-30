<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportArticleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('support_articles')->insert([
                    'support_category_id' => 1,
                    'title' => $faker->sentence,
                    'slug' => $faker->word,
                    'short_description' => $faker->paragraph,
                    'body' => $faker->word,
                    'is_published' => $faker->boolean,
                    'published_at' => $faker->word,
                    'sort_order' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}