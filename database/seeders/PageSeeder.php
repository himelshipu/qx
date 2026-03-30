<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('pages')->insert([
                    'slug' => $faker->word,
                    'title' => $faker->sentence,
                    'meta_title' => $faker->sentence,
                    'meta_description' => $faker->paragraph,
                    'is_published' => $faker->boolean,
                    'published_at' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}