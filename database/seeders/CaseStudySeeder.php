<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $caseStudies = [
            [
                'title' => 'D2C Skincare Brand Scaled UGC Creatives in 6 Weeks',
                'summary' => 'A skincare startup sourced creator-led short videos across TikTok and Reels, reducing CAC by 24% while increasing conversion rate on paid social.',
                'cover_image_path' => 'images/case-studies/skincare-ugc.webp',
                'external_url' => 'https://qx.local/case-studies/skincare-ugc-scale',
                'is_published' => true,
                'sort_order' => 1,
                'published_at' => now()->subDays(45),
            ],
            [
                'title' => 'Fintech App Boosted Qualified Leads with Creator Education',
                'summary' => 'By partnering with personal finance creators, the campaign improved sign-up quality and increased funded-account conversions by 31%.',
                'cover_image_path' => 'images/case-studies/fintech-education.webp',
                'external_url' => 'https://qx.local/case-studies/fintech-creator-education',
                'is_published' => true,
                'sort_order' => 2,
                'published_at' => now()->subDays(28),
            ],
            [
                'title' => 'Travel Marketplace Increased Off-Season Bookings',
                'summary' => 'Destination-focused creator bundles were launched in low-demand periods and drove a measurable rise in off-season reservations.',
                'cover_image_path' => 'images/case-studies/travel-offseason.webp',
                'external_url' => 'https://qx.local/case-studies/travel-offseason-growth',
                'is_published' => true,
                'sort_order' => 3,
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'Athleisure Launch with Regional Creator Mix',
                'summary' => 'A region-first creator rollout produced stronger engagement in Tier-2 markets and improved first-month repeat purchase.',
                'cover_image_path' => 'images/case-studies/athleisure-launch.webp',
                'external_url' => 'https://qx.local/case-studies/athleisure-regional-launch',
                'is_published' => false,
                'sort_order' => 4,
                'published_at' => null,
            ],
        ];

        foreach ($caseStudies as $entry) {
            $slug = Str::slug($entry['title']);

            DB::table('case_studies')->updateOrInsert(
                ['slug' => $slug],
                [
                    'title' => $entry['title'],
                    'slug' => $slug,
                    'summary' => $entry['summary'],
                    'cover_image_path' => $entry['cover_image_path'],
                    'external_url' => $entry['external_url'],
                    'is_published' => $entry['is_published'],
                    'sort_order' => $entry['sort_order'],
                    'published_at' => $entry['published_at'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
