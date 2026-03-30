<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignAssetSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $campaignIds = DB::table('campaigns')->pluck('id');
        $assetTypes = ['image', 'video', 'document'];

        foreach ($campaignIds as $campaignId) {
            $count = random_int(1, 3);

            for ($i = 1; $i <= $count; $i++) {
                $assetType = $faker->randomElement($assetTypes);
                $ext = $assetType === 'image' ? 'webp' : ($assetType === 'video' ? 'mp4' : 'pdf');
                $mime = $assetType === 'image' ? 'image/webp' : ($assetType === 'video' ? 'video/mp4' : 'application/pdf');

                DB::table('campaign_assets')->updateOrInsert(
                    [
                        'campaign_id' => $campaignId,
                        'title' => ucfirst($assetType) . ' asset ' . $i,
                    ],
                    [
                        'asset_type' => $assetType,
                        'file_path' => "campaigns/{$campaignId}/asset-{$i}.{$ext}",
                        'mime_type' => $mime,
                        'file_size' => random_int(120000, 8500000),
                        'sort_order' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
