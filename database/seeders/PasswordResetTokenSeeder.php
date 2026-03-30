<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasswordResetTokenSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('password_reset_tokens')->insert([
                    'email' => $faker->unique()->safeEmail,
                    'token' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}