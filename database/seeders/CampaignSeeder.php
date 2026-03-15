<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\CampaignAsset;
use App\Models\CampaignTargeting;
use App\Models\Category;
use App\Models\Creator;
use App\Models\FollowerRange;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignSeeder extends Seeder
{
    /**
     * Seed global campaign data and creator applications.
     */
    public function run(): void
    {
        $faker = fake();

        $categories     = Category::all();
        $followerRanges = FollowerRange::all();
        $creators       = Creator::all();

        if ($categories->isEmpty() || $followerRanges->isEmpty() || $creators->isEmpty()) {
            return;
        }

        $countryPool = [
            ['US', 'United States'],
            ['GB', 'United Kingdom'],
            ['BD', 'Bangladesh'],
            ['AE', 'United Arab Emirates'],
            ['IN', 'India'],
            ['CA', 'Canada'],
            ['DE', 'Germany'],
            ['AU', 'Australia']
        ];

        for ($i = 1; $i <= 12; $i++) {
            $campaign = Campaign::create([
                'title'         => $faker->randomElement(['Creator Growth', 'Seasonal Launch', 'Awareness Push', 'Conversion Sprint']) . ' #' . $i,
                'campaign_type' => $faker->randomElement(['instagram', 'tiktok', 'ugc', 'youtube', 'twitch', 'other']),
                'description'   => $faker->paragraph(),
                'instructions'  => $faker->paragraphs(2, true),
                'status'        => $faker->randomElement(['draft', 'published', 'published', 'paused']),
                'budget_min'    => $faker->numberBetween(500, 3000),
                'budget_max'    => $faker->numberBetween(4000, 15000),
                'currency'      => 'USD',
                'start_date'    => now()->addDays(rand(2, 30))->toDateString(),
                'end_date'      => now()->addDays(rand(35, 90))->toDateString(),
                'published_at'  => now()->subDays(rand(1, 15)),
                'is_active'     => true
            ]);

            CampaignTargeting::create([
                'campaign_id'      => $campaign->id,
                'influencer_count' => rand(5, 50),
                'target_gender'    => $faker->randomElement(['any', 'male', 'female', 'other']),
                'age_min'          => rand(18, 25),
                'age_max'          => rand(30, 45),
                'notes'            => $faker->sentence()
            ]);

            $campaignCategoryIds = collect($categories->random(rand(2, 4)))->pluck('id')->all();
            $campaign->categories()->sync($campaignCategoryIds);

            $rangeIds = collect($followerRanges->random(rand(2, 3)))->pluck('id')->all();
            $campaign->followerRanges()->sync($rangeIds);

            $countries = collect($countryPool)->shuffle()->take(rand(2, 4));
            foreach ($countries as [$countryCode, $countryName]) {
                DB::table('campaign_target_countries')->insert([
                    'campaign_id'  => $campaign->id,
                    'country_code' => $countryCode,
                    'country_name' => $countryName,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]);
            }

            CampaignAsset::create([
                'campaign_id' => $campaign->id,
                'asset_type'  => 'image',
                'file_path'   => 'images/campaigns/campaign-' . $campaign->id . '-hero.jpg',
                'mime_type'   => 'image/jpeg',
                'file_size'   => rand(120000, 450000),
                'title'       => 'Campaign Hero',
                'sort_order'  => 1
            ]);

            if ($faker->boolean(70)) {
                CampaignAsset::create([
                    'campaign_id' => $campaign->id,
                    'asset_type'  => 'document',
                    'file_path'   => 'docs/campaigns/campaign-' . $campaign->id . '-brief.pdf',
                    'mime_type'   => 'application/pdf',
                    'file_size'   => rand(60000, 200000),
                    'title'       => 'Brief PDF',
                    'sort_order'  => 2
                ]);
            }

            $applicants = $creators->random(min(rand(5, 10), $creators->count()));
            $applicants = $applicants instanceof Creator ? collect([$applicants]) : $applicants;

            foreach ($applicants as $creator) {
                CampaignApplication::create([
                    'campaign_id'   => $campaign->id,
                    'creator_id'    => $creator->id,
                    'status'        => $faker->randomElement(['applied', 'shortlisted', 'approved', 'rejected']),
                    'pitch_message' => $faker->sentence(),
                    'proposed_rate' => rand(100, 1500),
                    'agreed_rate'   => $faker->boolean(40) ? rand(120, 1800) : null,
                    'applied_at'    => now()->subDays(rand(1, 25)),
                    'decided_at'    => $faker->boolean(65) ? now()->subDays(rand(0, 20)) : null
                ]);
            }
        }
    }
}
