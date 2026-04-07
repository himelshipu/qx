<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'author_name' => 'Maya Rodriguez',
                'author_role' => 'Growth Lead',
                'company_name' => 'GlowNest Beauty',
                'quote' => 'ROCKIES helped us find influencers who actually match our customer profile. We went from one-off posts to a repeatable influencer pipeline in under a month.',
                'rating' => 5,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'author_name' => 'Ethan Cole',
                'author_role' => 'Performance Marketing Manager',
                'company_name' => 'TrailPeak Travel',
                'quote' => 'The collaboration flow is simple and transparent. Our campaign turnaround time improved, and the content quality has been consistently strong.',
                'rating' => 5,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'author_name' => 'Nadia Khan',
                'author_role' => 'Brand Strategist',
                'company_name' => 'FitMode Apparel',
                'quote' => 'What stood out is how quickly we could launch multiple influencer briefs. Reporting and communication are much smoother than our old workflow.',
                'rating' => 4,
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'author_name' => 'Liam Foster',
                'author_role' => 'Head of Marketing',
                'company_name' => 'FinCraft',
                'quote' => 'We were able to test educational influencer angles with low friction. The first campaign already beat our benchmark CPA.',
                'rating' => 4,
                'is_published' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($testimonials as $entry) {
            DB::table('testimonials')->updateOrInsert(
                [
                    'author_name' => $entry['author_name'],
                    'company_name' => $entry['company_name'],
                ],
                [
                    'author_role' => $entry['author_role'],
                    'quote' => $entry['quote'],
                    'rating' => $entry['rating'],
                    'is_published' => $entry['is_published'],
                    'sort_order' => $entry['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
