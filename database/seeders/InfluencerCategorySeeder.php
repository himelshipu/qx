<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfluencerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $influencerIds = DB::table('influencers')->pluck('id');
        $categoryIds   = DB::table('categories')->pluck('id')->all();

        if (empty($categoryIds)) {
            return;
        }

        foreach ($influencerIds as $influencerId) {
            $selected = collect($categoryIds)
                ->shuffle()
                ->take(random_int(1, min(4, count($categoryIds))));

            foreach ($selected as $categoryId) {
                DB::table('influencer_categories')->updateOrInsert(
                    [
                        'influencer_id' => $influencerId,
                        'category_id'   => $categoryId
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
    }
}
