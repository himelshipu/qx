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
            $code = 'badge_' . $i; // Unique code
            DB::table('badge_definitions')->updateOrInsert(
                ['code' => $code],
                [
                    'name'        => $faker->name,
                    'description' => $faker->paragraph,
                    'is_active'   => $faker->boolean,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]
            );
        }
    }
}
