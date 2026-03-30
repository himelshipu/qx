<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignTargetCountrySeeder extends Seeder
{
    public function run(): void
    {
        $campaignIds = DB::table('campaigns')->pluck('id');
        $countries = ['US', 'CA', 'GB', 'AU', 'DE', 'FR', 'IN'];

        foreach ($campaignIds as $campaignId) {
            $selected = collect($countries)->shuffle()->take(random_int(2, 4));

            foreach ($selected as $countryCode) {
                DB::table('campaign_target_countries')->updateOrInsert(
                    [
                        'campaign_id' => $campaignId,
                        'country_code' => $countryCode,
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
