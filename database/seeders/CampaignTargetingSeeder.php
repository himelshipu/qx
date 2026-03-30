<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignTargetingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $campaigns = DB::table('campaigns')->pluck('id');

        foreach ($campaigns as $campaignId) {
            $ageMin = random_int(18, 28);
            $ageMax = random_int($ageMin + 4, 45);

            DB::table('campaign_targeting')->updateOrInsert(
                ['campaign_id' => $campaignId],
                [
                    'influencer_count' => random_int(5, 50),
                    'target_gender' => $faker->randomElement(['any', 'male', 'female', 'other']),
                    'age_min' => $ageMin,
                    'age_max' => $ageMax,
                    'notes' => 'Prefer creators with consistent posting cadence and strong audience authenticity.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
