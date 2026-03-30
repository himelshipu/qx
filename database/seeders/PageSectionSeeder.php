<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('page_sections')->insert([
                    'page_id' => 1,
                    'section_key' => $faker->word,
                    'heading' => $faker->word,
                    'subheading' => $faker->word,
                    'content_json' => $faker->word,
                    'sort_order' => $faker->word,
                    'is_active' => $faker->boolean,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}