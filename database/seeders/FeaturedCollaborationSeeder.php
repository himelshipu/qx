<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturedCollaborationSeeder extends Seeder
{
    public function run(): void
    {
        $collaborations = [
            [
                'brand_name' => 'GlowNest Beauty',
                'asset_type' => 'image',
                'image_path' => 'images/featured-collaborations/glownest-spring.webp',
                'video_path' => null,
                'thumbnail_path' => null,
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'brand_name' => 'TrailPeak Travel',
                'asset_type' => 'video',
                'image_path' => null,
                'video_path' => 'videos/featured-collaborations/trailpeak-launch.mp4',
                'thumbnail_path' => 'images/featured-collaborations/trailpeak-thumb.webp',
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'brand_name' => 'FitMode Apparel',
                'asset_type' => 'image',
                'image_path' => 'images/featured-collaborations/fitmode-ugc.webp',
                'video_path' => null,
                'thumbnail_path' => null,
                'sort_order' => 3,
                'is_published' => true,
            ],
            [
                'brand_name' => 'FinCraft',
                'asset_type' => 'video',
                'image_path' => null,
                'video_path' => 'videos/featured-collaborations/fincraft-explainer.mp4',
                'thumbnail_path' => 'images/featured-collaborations/fincraft-thumb.webp',
                'sort_order' => 4,
                'is_published' => false,
            ],
        ];

        foreach ($collaborations as $entry) {
            DB::table('featured_collaborations')->updateOrInsert(
                [
                    'brand_name' => $entry['brand_name'],
                    'sort_order' => $entry['sort_order'],
                ],
                [
                    'asset_type' => $entry['asset_type'],
                    'image_path' => $entry['image_path'],
                    'video_path' => $entry['video_path'],
                    'thumbnail_path' => $entry['thumbnail_path'],
                    'is_published' => $entry['is_published'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
