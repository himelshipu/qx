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
        $creatorIds = DB::table('creators')->pluck('id')->all();
        $statuses = ['invited', 'applied', 'shortlisted', 'approved', 'rejected', 'completed'];

        if (empty($creatorIds)) {
            return;
        }

        foreach ($campaignIds as $campaignId) {
            $selectedCreators = collect($creatorIds)->shuffle()->take(random_int(3, min(8, count($creatorIds))));

            foreach ($selectedCreators as $creatorId) {
                $status = $faker->randomElement($statuses);
                $appliedAt = now()->subDays(random_int(1, 30));

                DB::table('campaign_applications')->updateOrInsert(
                    [
                        'campaign_id' => $campaignId,
                        'creator_id' => $creatorId,
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
