<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'section_code'  => 'general',
                'section_title' => 'General Questions',
                'audience_type' => 'all',
                'sort_order'    => 1,
                'is_active'     => true
            ],
            [
                'section_code'  => 'for-brands',
                'section_title' => 'For Brands',
                'audience_type' => 'brand',
                'sort_order'    => 2,
                'is_active'     => true
            ],
            [
                'section_code'  => 'for-creators',
                'section_title' => 'For Creators',
                'audience_type' => 'influencer',
                'sort_order'    => 3,
                'is_active'     => true
            ],
            [
                'section_code'  => 'payments-and-safety',
                'section_title' => 'Payments & Safety',
                'audience_type' => 'all',
                'sort_order'    => 4,
                'is_active'     => true
            ]
        ];

        foreach ($sections as $entry) {
            DB::table('faq_sections')->updateOrInsert(
                ['section_code' => $entry['section_code']],
                [
                    'section_title' => $entry['section_title'],
                    'audience_type' => $entry['audience_type'],
                    'sort_order'    => $entry['sort_order'],
                    'is_active'     => $entry['is_active'],
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]
            );
        }
    }
}
