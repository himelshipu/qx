<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignCategorySeeder extends Seeder
{
    public function run(): void
    {
        $campaignIds = DB::table('campaigns')->pluck('id');
        $categoryIds = DB::table('categories')->pluck('id')->all();

        if (empty($categoryIds)) {
            return;
        }

        foreach ($campaignIds as $campaignId) {
            $selected = collect($categoryIds)->shuffle()->take(random_int(1, min(3, count($categoryIds))));

            foreach ($selected as $categoryId) {
                DB::table('campaign_categories')->updateOrInsert(
                    [
                        'campaign_id' => $campaignId,
                        'category_id' => $categoryId,
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
