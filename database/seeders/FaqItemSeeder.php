<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $sectionIds = DB::table('faq_sections')->pluck('id', 'section_code');

        $items = [
            'general' => [
                [
                    'question' => 'What is ROCKIES and who is it for?',
                    'answer' => 'ROCKIES is a collaboration platform that helps brands discover and work with influencers for campaigns, UGC content, and long-term partnerships.',
                ],
                [
                    'question' => 'Do I need to pay to create an account?',
                    'answer' => 'Creating an account is free. Brands pay when they launch campaigns or place influencer orders, depending on their workflow.',
                ],
            ],
            'for-brands' => [
                [
                    'question' => 'How do I choose the right influencers for my campaign?',
                    'answer' => 'Start with your audience, campaign objective, and budget. Use category fit, content quality, and platform performance to shortlist influencers.',
                ],
                [
                    'question' => 'Can I run multiple campaigns at the same time?',
                    'answer' => 'Yes. You can run concurrent campaigns and target different audiences, markets, and content types simultaneously.',
                ],
            ],
            'for-influencers' => [
                [
                    'question' => 'How do influencers get selected for campaigns?',
                    'answer' => 'Influencers are evaluated based on profile quality, category relevance, audience fit, and campaign requirements defined by brands.',
                ],
                [
                    'question' => 'Can I reject a collaboration request?',
                    'answer' => 'Yes. You can decline opportunities that do not align with your audience, pricing, or content direction.',
                ],
            ],
            'payments-and-safety' => [
                [
                    'question' => 'How are payments handled on ROCKIES?',
                    'answer' => 'Payments are tracked through the platform workflow. Brands can review deliverables before final approval according to campaign terms.',
                ],
                [
                    'question' => 'What happens if a deliverable is delayed?',
                    'answer' => 'Both parties can communicate through the platform and update timelines. Escalation/support can be used when needed.',
                ],
            ],
        ];

        foreach ($items as $sectionCode => $entries) {
            $sectionId = $sectionIds[$sectionCode] ?? null;
            if (!$sectionId) {
                continue;
            }

            foreach ($entries as $index => $entry) {
                DB::table('faq_items')->updateOrInsert(
                    [
                        'faq_section_id' => $sectionId,
                        'question' => $entry['question'],
                    ],
                    [
                        'answer' => $entry['answer'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
