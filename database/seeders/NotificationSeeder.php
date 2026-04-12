<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $types = ['order', 'payment', 'campaign', 'message', 'review', 'payout'];

        for ($i = 0; $i < 20; $i++) {
            DB::table('notifications')->insert([
                'user_id' => 1,
                'type' => $faker->randomElement($types),
                'title' => $faker->sentence,
                'body' => $faker->paragraph,
                'data_json' => json_encode([
                    'action_url' => '/dashboard/notifications',
                    'icon_class' => 'bell',
                    'color_class' => 'blue',
                ]),
                'is_read' => $faker->boolean,
                'read_at' => $faker->optional()->dateTimeThisYear()?->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}