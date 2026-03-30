<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('sessions')->insert([
                    'user_id' => 1,
                    'ip_address' => $faker->address,
                    'user_agent' => $faker->word,
                    'payload' => $faker->word,
                    'last_activity' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}