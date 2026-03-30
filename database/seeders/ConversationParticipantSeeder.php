<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('conversation_participants')->insert([
                    'conversation_id' => 1,
                    'user_id' => 1,
                    'participant_role' => 'active',
                    'joined_at' => $faker->word,
                    'left_at' => $faker->word,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}