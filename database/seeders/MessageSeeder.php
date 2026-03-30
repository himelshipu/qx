<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('messages')->insert([
                    'conversation_id' => 1,
                    'sender_user_id' => 1,
                    'sender_role' => 'active',
                    'on_behalf_of_creator_id' => 1,
                    'message' => $faker->word,
                    'attachment_path' => $faker->word,
                    'read_at' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}