<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDeliverableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('order_deliverables')->insert([
                    'order_item_id' => 1,
                    'uploaded_by_user_id' => 1,
                    'deliverable_type' => 'active',
                    'file_path' => $faker->word,
                    'external_url' => $faker->url,
                    'notes' => $faker->word,
                    'status' => $faker->randomElement(['pending','approved','rejected','completed']),

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}