<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('notifications')->insert([
                    'user_id' => 1,
                    'type' => $faker->word,
                    'title' => $faker->sentence,
                    'body' => $faker->word,
                    'data_json' => $faker->word,
                    'is_read' => $faker->boolean,
                    'read_at' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}