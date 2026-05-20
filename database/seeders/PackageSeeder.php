<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $influencerIds = DB::table('influencers')->pluck('id');
        $influencerUserByInfluencerId = DB::table('influencers')->pluck('user_id', 'id');
        $managerIds = DB::table('users')->whereIn('user_type', ['admin', 'moderator'])->pluck('id')->all();
        $hasCreatedByColumn = Schema::hasColumn('packages', 'created_by');

       $templates = [
            // Instagram
            ['platform' => 'instagram', 'name' => '1 Instagram Story', 'price' => [50, 200]],
            ['platform' => 'instagram', 'name' => '2 Instagram Stories', 'price' => [90, 350]],
            ['platform' => 'instagram', 'name' => '1 Instagram Reel (60 Seconds)', 'price' => [120, 650]],
            ['platform' => 'instagram', 'name' => '2 Instagram Reels', 'price' => [220, 1200]],
            ['platform' => 'instagram', 'name' => '1 Instagram Photo Feed Post', 'price' => [60, 250]],

            // TikTok
            ['platform' => 'tiktok', 'name' => '1 TikTok Video (30 Seconds)', 'price' => [100, 450]],
            ['platform' => 'tiktok', 'name' => '1 TikTok Video (60 Seconds)', 'price' => [150, 700]],
            ['platform' => 'tiktok', 'name' => '2 TikTok Videos', 'price' => [280, 1300]],
            ['platform' => 'tiktok', 'name' => '3 TikTok Videos', 'price' => [400, 1800]],
            ['platform' => 'tiktok', 'name' => '1 TikTok Stories', 'price' => [60, 250]],
            ['platform' => 'tiktok', 'name' => '1 TikTok Live (30 Minutes)', 'price' => [300, 1200]],

            // YouTube
            ['platform' => 'youtube', 'name' => '1 YouTube Short', 'price' => [150, 600]],
            ['platform' => 'youtube', 'name' => '1 YouTube Video', 'price' => [500, 2500]],
            ['platform' => 'youtube', 'name' => '2 YouTube Videos', 'price' => [950, 4500]],
        ];

        foreach ($influencerIds as $influencerId) {
            foreach ($templates as $index => $template) {
                $price = random_int($template['price'][0], $template['price'][1]);
                $ownerUserId = $influencerUserByInfluencerId[$influencerId] ?? null;
                $createdByUserId = $ownerUserId;

                // Some packages are created by admin/moderator on behalf of influencers.
                if (!empty($managerIds) && random_int(1, 100) <= 35) {
                    $createdByUserId = $managerIds[array_rand($managerIds)];
                }

                $payload = [
                    'platform' => $template['platform'],
                    'description' => 'Includes concept, production, basic editing, and one round of revision.',
                    'base_price' => $price,
                    'currency' => 'USD',
                    'delivery_days' => random_int(4, 10),
                    'revisions_included' => random_int(1, 2),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($hasCreatedByColumn) {
                    $payload['created_by'] = $createdByUserId;
                }

                DB::table('packages')->updateOrInsert(
                    [
                        'influencer_id' => $influencerId,
                        'name' => $template['name'],
                    ],
                    $payload
                );
            }
        }
    }
}
