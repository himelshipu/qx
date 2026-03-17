<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use App\Models\FaqSection;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFaqs();
        $this->seedTestimonials();
    }

    private function seedFaqs(): void
    {
        $section = FaqSection::firstOrCreate(
            ['section_code' => 'homepage_general'],
            [
                'section_title' => 'General Questions',
                'audience_type' => 'all',
                'sort_order'    => 1,
                'is_active'     => true
            ]
        );

        $items = [
            [
                'question'   => 'How does Rockies work?',
                'answer'     => 'Rockies allows influencers to create a profile and list their services for brands to purchase directly. You set your own prices and manage your collaborations all in one place.',
                'sort_order' => 1
            ],
            [
                'question'   => 'How do I get paid?',
                'answer'     => 'Payments are made directly through our platform. Once you complete an order, the funds are released to your wallet where you can choose your preferred payout method.',
                'sort_order' => 2
            ],
            [
                'question'   => 'What platforms does Rockies support?',
                'answer'     => 'You can list services for Instagram, TikTok, YouTube, LinkedIn, X (Twitter), Facebook, and UGC content creation.',
                'sort_order' => 3
            ],
            [
                'question'   => 'Is Rockies free to join?',
                'answer'     => 'Yes, creating a creator profile on Rockies is completely free. We only take a small platform fee when you complete a paid collaboration.',
                'sort_order' => 4
            ],
            [
                'question'   => 'How do brands find me?',
                'answer'     => 'Brands can search our creator marketplace by platform, niche, follower count, and location. A complete, high-quality profile significantly increases your discovery chances.',
                'sort_order' => 5
            ]
        ];

        foreach ($items as $item) {
            FaqItem::firstOrCreate(
                [
                    'faq_section_id' => $section->id,
                    'question'       => $item['question']
                ],
                [
                    'answer'     => $item['answer'],
                    'sort_order' => $item['sort_order'],
                    'is_active'  => true
                ]
            );
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            [
                'author_name'  => 'Layla',
                'author_role'  => 'Influencer & Founder',
                'company_name' => null,
                'quote'        => "I've used Rockies from both the Creator side and the Brand side — extremely user-friendly and has led to some great relationships I wouldn't have found otherwise.",
                'rating'       => 5,
                'sort_order'   => 1
            ],
            [
                'author_name'  => 'Myriam',
                'author_role'  => 'Founder',
                'company_name' => 'BBeyond',
                'quote'        => "Best platform to connect with influencers and content creators. The easiest to use and gives the best results for my brand.",
                'rating'       => 5,
                'sort_order'   => 2
            ],
            [
                'author_name'  => 'Courtney',
                'author_role'  => 'Marketer',
                'company_name' => null,
                'quote'        => "Been using Rockies to generate content for our seasonal clothing lines. Super easy to search for relevant influencers. We save at least 10–20 hours a month.",
                'rating'       => 5,
                'sort_order'   => 3
            ]
        ];

        foreach ($testimonials as $data) {
            Testimonial::firstOrCreate(
                ['author_name' => $data['author_name'], 'author_role' => $data['author_role']],
                [
                    'company_name' => $data['company_name'],
                    'quote'        => $data['quote'],
                    'rating'       => $data['rating'],
                    'is_published' => true,
                    'sort_order'   => $data['sort_order']
                ]
            );
        }
    }
}
