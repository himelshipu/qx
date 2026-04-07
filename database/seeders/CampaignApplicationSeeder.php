<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $campaignIds = DB::table('campaigns')->pluck('id');
        $influencerIds = DB::table('influencers')->pluck('id')->all();
        $statuses = ['invited', 'applied', 'shortlisted', 'approved', 'rejected', 'completed'];

        if (empty($influencerIds)) {
            return;
        }

        foreach ($campaignIds as $campaignId) {
            $selectedCreators = collect($influencerIds)->shuffle()->take(random_int(3, min(8, count($influencerIds))));

            foreach ($selectedCreators as $influencerId) {
                $status = $faker->randomElement($statuses);
                $appliedAt = now()->subDays(random_int(1, 30));

                DB::table('campaign_applications')->updateOrInsert(
                    [
                        'campaign_id' => $campaignId,
                        'influencer_id' => $influencerId,
                    ],
                    [
                        'status' => $status,
                        'pitch_message' => 'I can deliver platform-native content aligned with campaign goals and deadlines.',
                        'proposed_rate' => random_int(150, 1500),
                        'agreed_rate' => in_array($status, ['approved', 'completed'], true) ? random_int(180, 1800) : null,
                        'applied_at' => $appliedAt,
                        'decided_at' => in_array($status, ['approved', 'rejected', 'completed'], true) ? $appliedAt->copy()->addDays(random_int(1, 7)) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
