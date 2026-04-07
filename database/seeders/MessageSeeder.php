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
                'conversation_id'            => 1,
                'sender_user_id'             => 1,
                'sender_role'                => 'active',
                'on_behalf_of_influencer_id' => 1,
                'message'                    => $faker->text(500),
                'attachment_path'            => null,
                'read_at'                    => null,

                'created_at'                 => now(),
                'updated_at'                 => now()
            ]);
        }
    }
}
