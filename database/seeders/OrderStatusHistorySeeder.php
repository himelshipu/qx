<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('order_status_history')->insert([
                    'order_id' => 1,
                    'old_status' => $faker->word,
                    'new_status' => $faker->word,
                    'changed_by_user_id' => 1,
                    'note' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}