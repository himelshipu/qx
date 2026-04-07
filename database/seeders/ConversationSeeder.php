<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('conversations')->insert([
                    'conversation_type' => 'active',
                    'influencer_id' => 1,
                    'brand_user_id' => 1,
                    'handled_by_user_id' => 1,
                    'order_id' => 1,
                    'influencer_direct_message_enabled' => $faker->word,
                    'title' => $faker->sentence,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}