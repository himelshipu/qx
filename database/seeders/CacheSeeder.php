<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CacheSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('cache')->insert([
                    'key' => $faker->word,
                    'value' => $faker->word,
                    'expiration' => $faker->word,
                    'owner' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}