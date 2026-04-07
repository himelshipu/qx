<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfluencerBadgeSeeder extends Seeder
{
    public function run(): void
    {
        $faker            = \Faker\Factory::create();
        $badgeDefinitions = DB::table('badge_definitions')->pluck('id')->all();

        if (empty($badgeDefinitions)) {
            return; // Skip if no badge definitions
        }

        $influencerIds = [1, 2, 3, 4, 5]; // Use diverse influencer IDs

        for ($i = 0; $i < 20; $i++) {
            $influencerId = $faker->randomElement($influencerIds);
            $badgeId      = $faker->randomElement($badgeDefinitions);

            DB::table('influencer_badges')->updateOrInsert(
                [
                    'influencer_id'       => $influencerId,
                    'badge_definition_id' => $badgeId
                ],
                [
                    'earned_at'  => $faker->dateTime(),
                    'is_active'  => $faker->boolean,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
