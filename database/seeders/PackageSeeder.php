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
            ['platform' => 'facebook', 'name' => '1 Facebook Feed Post', 'price' => [90, 420]],
            ['platform' => 'instagram', 'name' => '1 Instagram Reel (30-60 sec)', 'price' => [120, 650]],
            ['platform' => 'tiktok', 'name' => '1 TikTok Video', 'price' => [150, 700]],
            ['platform' => 'linkedin', 'name' => '1 LinkedIn Brand Mention', 'price' => [140, 800]],
            ['platform' => 'x', 'name' => '1 X Thread + Post', 'price' => [80, 450]],
            ['platform' => 'youtube', 'name' => 'YouTube Integration (60 sec)', 'price' => [400, 1800]],
            ['platform' => 'ugc', 'name' => 'UGC Product Demo', 'price' => [100, 550]],
            ['platform' => 'other', 'name' => 'Custom Platform Deliverable', 'price' => [120, 900]],
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
