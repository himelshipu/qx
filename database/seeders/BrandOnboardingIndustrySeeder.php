<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandOnboardingIndustrySeeder extends Seeder
{
    public function run(): void
    {
        $profiles = DB::table('brand_onboarding_profiles')->pluck('id');
        $categoryIds = DB::table('categories')->pluck('id')->all();

        if (empty($categoryIds)) {
            return;
        }

        foreach ($profiles as $profileId) {
            $selected = collect($categoryIds)->shuffle()->take(random_int(2, min(5, count($categoryIds))));

            foreach ($selected as $categoryId) {
                DB::table('brand_onboarding_industries')->updateOrInsert(
                    [
                        'brand_onboarding_profile_id' => $profileId,
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
