<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('jobs')->insert([
                    'queue' => $faker->word,
                    'payload' => $faker->word,
                    'attempts' => $faker->word,
                    'reserved_at' => $faker->word,
                    'available_at' => $faker->word,
                    'name' => $faker->name,
                    'total_jobs' => $faker->word,
                    'pending_jobs' => $faker->word,
                    'failed_jobs' => $faker->word,
                    'failed_job_ids' => 1,
                    'options' => $faker->word,
                    'cancelled_at' => $faker->word,
                    'finished_at' => $faker->word,
                    'uuid' => 1,
                    'connection' => $faker->word,
                    'exception' => $faker->word,
                    'failed_at' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}