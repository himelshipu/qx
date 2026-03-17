<?php

namespace Database\Seeders;

use App\Models\BadgeDefinition;
use App\Models\Category;
use App\Models\FollowerRange;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Seed catalog, content, FAQ, and support lookup data.
     */
    public function run(): void
    {
        $categories = [
            'Fashion',
            'Beauty',
            'Tech',
            'Gaming',
            'Lifestyle',
            'Travel',
            'Food',
            'Fitness',
            'Education',
            'Finance',
            'Health',
            'Parenting'
        ];

        // Featured categories (first 4 will be featured)
        $featuredCategories = ['Fashion', 'Beauty', 'Food', 'Fitness'];

        foreach ($categories as $index => $name) {
            $isFeatured = in_array($name, $featuredCategories);
            $featuredOrder = $isFeatured ? array_search($name, $featuredCategories) + 1 : null;

            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'            => $name,
                    'description'     => $name . ' focused campaigns and creator segments',
                    'icon_path'       => 'images/categories/icons/' . Str::slug($name) . '.svg',
                    'image_path'      => 'images/categories/' . Str::slug($name) . '.jpg',
                    'is_active'       => true,
                    'sort_order'      => $index + 1,
                    'is_featured'     => $isFeatured,
                    'featured_order'  => $featuredOrder
                ]
            );
        }

        $followerRanges = [
            ['nano_1k_10k', '1K - 10K', 1000, 10000],
            ['micro_10k_50k', '10K - 50K', 10000, 50000],
            ['mid_50k_100k', '50K - 100K', 50000, 100000],
            ['macro_100k_500k', '100K - 500K', 100000, 500000],
            ['mega_500k_1m', '500K - 1M', 500000, 1000000],
            ['elite_1m_plus', '1M+', 1000000, null]
        ];

        foreach ($followerRanges as $index => [$code, $label, $min, $max]) {
            FollowerRange::updateOrCreate(
                ['code' => $code],
                [
                    'label'         => $label,
                    'min_followers' => $min,
                    'max_followers' => $max,
                    'sort_order'    => $index + 1,
                    'is_active'     => true
                ]
            );
        }

        $badges = [
            ['trusted_creator', 'Trusted Creator'],
            ['top_communicator', 'Top Communicator'],
            ['high_conversion', 'High Conversion'],
            ['on_time_delivery', 'On-Time Delivery'],
            ['audience_favorite', 'Audience Favorite'],
            ['premium_collaborator', 'Premium Collaborator']
        ];

        foreach ($badges as [$code, $name]) {
            BadgeDefinition::updateOrCreate(
                ['code' => $code],
                [
                    'name'        => $name,
                    'description' => $name . ' badge for standout creator performance',
                    'is_active'   => true
                ]
            );
        }

        DB::table('pages')->updateOrInsert(
            ['slug' => 'faq'],
            [
                'title'            => 'Frequently Asked Questions',
                'meta_title'       => 'FAQ',
                'meta_description' => 'Common questions for brands and creators',
                'is_published'     => true,
                'published_at'     => now(),
                'updated_at'       => now(),
                'created_at'       => now()
            ]
        );

        DB::table('pages')->updateOrInsert(
            ['slug' => 'support'],
            [
                'title'            => 'Support Center',
                'meta_title'       => 'Support',
                'meta_description' => 'Need help? Find guides and contact support',
                'is_published'     => true,
                'published_at'     => now(),
                'updated_at'       => now(),
                'created_at'       => now()
            ]
        );

        $faqPage     = DB::table('pages')->where('slug', 'faq')->first();
        $supportPage = DB::table('pages')->where('slug', 'support')->first();

        $faqSections = [
            ['general', 'General Questions', 'all'],
            ['brands', 'For Brands', 'brand'],
            ['creators', 'For Creators', 'creator']
        ];

        foreach ($faqSections as $order => [$code, $title, $audience]) {
            DB::table('faq_sections')->updateOrInsert(
                ['section_code' => $code],
                [
                    'page_id'       => $faqPage?->id,
                    'section_title' => $title,
                    'audience_type' => $audience,
                    'sort_order'    => $order + 1,
                    'is_active'     => true,
                    'updated_at'    => now(),
                    'created_at'    => now()
                ]
            );
        }

        $faqItems = [
            ['general', 'How does collaboration start?', 'A brand selects a creator profile, picks a package, and places an order.'],
            ['brands', 'Who handles creator messaging?', 'Moderators or admins handle all creator-side conversation on behalf of creators.'],
            ['brands', 'When is an order confirmed?', 'Orders start as pending and are accepted by admin/moderator before execution.'],
            ['creators', 'Can creators chat directly?', 'No. Creator communication is represented by moderator/admin on behalf of creators.'],
            ['creators', 'How are payouts processed?', 'Completed order items are grouped and released through payout batches.']
        ];

        DB::table('faq_items')->delete();
        foreach ($faqItems as $order => [$sectionCode, $question, $answer]) {
            $section = DB::table('faq_sections')->where('section_code', $sectionCode)->first();
            if (!$section) {
                continue;
            }

            DB::table('faq_items')->insert([
                'faq_section_id' => $section->id,
                'question'       => $question,
                'answer'         => $answer,
                'sort_order'     => $order + 1,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now()
            ]);
        }

        $supportCategories = [
            ['account', 'Account & Login'],
            ['orders', 'Orders & Payments'],
            ['campaigns', 'Campaign Setup'],
            ['technical', 'Technical Issues']
        ];

        foreach ($supportCategories as $order => [$slug, $name]) {
            DB::table('support_categories')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name'        => $name,
                    'description' => $name . ' support resources',
                    'sort_order'  => $order + 1,
                    'is_active'   => true,
                    'updated_at'  => now(),
                    'created_at'  => now()
                ]
            );
        }

        DB::table('support_articles')->delete();
        DB::table('support_questions')->delete();

        $articles = [
            ['account', 'Resetting Your Password', 'resetting-password', 'How to reset your account password safely.'],
            ['orders', 'Order Status Lifecycle', 'order-status-lifecycle', 'Understand pending, accepted, and delivery states.'],
            ['campaigns', 'Creating High-Performing Campaigns', 'creating-high-performing-campaigns', 'Campaign setup guidance to improve creator matches.'],
            ['technical', 'Troubleshooting Upload Issues', 'troubleshooting-upload-issues', 'Fix common asset upload problems quickly.']
        ];

        foreach ($articles as $index => [$slug, $title, $articleSlug, $summary]) {
            $category = DB::table('support_categories')->where('slug', $slug)->first();
            if (!$category) {
                continue;
            }

            DB::table('support_articles')->insert([
                'support_category_id' => $category->id,
                'title'               => $title,
                'slug'                => $articleSlug,
                'short_description'   => $summary,
                'body'                => $summary . ' This guide walks through practical steps and best practices.',
                'is_published'        => true,
                'published_at'        => now(),
                'sort_order'          => $index + 1,
                'created_at'          => now(),
                'updated_at'          => now()
            ]);
        }

        $questions = [
            ['account', 'Why am I not receiving verification emails?', 'Check spam folder first, then verify your registered email and mailbox filters.'],
            ['orders', 'Can I cancel a pending order?', 'Yes. Pending orders can be cancelled before moderator acceptance.'],
            ['campaigns', 'Do campaigns belong to a specific brand?', 'No. Campaign records are global and can be reused across brand workflows.'],
            ['technical', 'Which file formats are supported?', 'JPG, PNG, MP4, and PDF are supported for most campaign and order attachments.']
        ];

        foreach ($questions as $index => [$slug, $question, $answer]) {
            $category = DB::table('support_categories')->where('slug', $slug)->first();
            if (!$category) {
                continue;
            }

            DB::table('support_questions')->insert([
                'support_category_id' => $category->id,
                'question'            => $question,
                'answer'              => $answer,
                'sort_order'          => $index + 1,
                'is_active'           => true,
                'created_at'          => now(),
                'updated_at'          => now()
            ]);
        }

        DB::table('page_sections')->delete();
        if ($faqPage) {
            DB::table('page_sections')->insert([
                'page_id'      => $faqPage->id,
                'section_key'  => 'hero',
                'heading'      => 'Frequently Asked Questions',
                'subheading'   => 'Answers for brands and creators working on QX',
                'content_json' => json_encode(['cta' => 'Contact support']),
                'sort_order'   => 1,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now()
            ]);
        }

        if ($supportPage) {
            DB::table('page_sections')->insert([
                'page_id'      => $supportPage->id,
                'section_key'  => 'hero',
                'heading'      => 'Support Center',
                'subheading'   => 'Guides, answers, and ticket-based help',
                'content_json' => json_encode(['cta' => 'Open Ticket']),
                'sort_order'   => 1,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now()
            ]);
        }
    }
}
