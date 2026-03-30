<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('badge_definitions')->insert([
                    'code' => $faker->word,
                    'name' => $faker->name,
                    'description' => $faker->paragraph,
                    'is_active' => $faker->boolean,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}