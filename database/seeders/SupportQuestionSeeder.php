<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('support_questions')->insert([
                    'support_category_id' => 1,
                    'question' => $faker->word,
                    'answer' => $faker->word,
                    'sort_order' => $faker->word,
                    'is_active' => $faker->boolean,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}