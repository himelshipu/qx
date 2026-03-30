<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignTargetFollowerRangeSeeder extends Seeder
{
    public function run(): void
    {
        $campaignIds = DB::table('campaigns')->pluck('id');
        $rangeIds = DB::table('follower_ranges')->pluck('id')->all();

        if (empty($rangeIds)) {
            return;
        }

        foreach ($campaignIds as $campaignId) {
            $selected = collect($rangeIds)->shuffle()->take(random_int(1, min(3, count($rangeIds))));

            foreach ($selected as $rangeId) {
                DB::table('campaign_target_follower_ranges')->updateOrInsert(
                    [
                        'campaign_id' => $campaignId,
                        'follower_range_id' => $rangeId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
