<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('media_library')->insert([
                    'uploaded_by_user_id' => 1,
                    'disk' => $faker->word,
                    'path' => $faker->word,
                    'file_name' => $faker->name,
                    'mime_type' => $faker->word,
                    'file_size' => $faker->word,
                    'width' => 1,
                    'height' => $faker->word,
                    'alt_text' => $faker->word,
                    'entity_type' => $faker->word,
                    'entity_id' => 1,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}