<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'How to submit your first campaign brief',
                'slug' => 'how-to-submit-your-first-campaign-brief',
                'badge' => 'Getting Started',
                'summary' => 'Learn the exact steps to create a clear brief so creators can apply faster and deliver the right content.',
                'content' => "To submit your first campaign brief, go to Dashboard > Campaigns > New Campaign.\n\nStart by defining your campaign goal, target audience, and expected creator deliverables. Add examples of tone, visual references, and mandatory talking points so submissions stay aligned.\n\nBefore publishing, double-check your budget range, timeline, and approval flow. A complete brief usually receives better applications within the first 24 to 48 hours.",
                'read_time_minutes' => 5,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(21),
                'sort_order' => 1,
            ],
            [
                'title' => 'Billing cycle and invoice download guide',
                'slug' => 'billing-cycle-and-invoice-download-guide',
                'badge' => 'Billing',
                'summary' => 'Understand when invoices are generated, what each line item means, and where to export PDF invoices.',
                'content' => "Invoices are generated at the end of each billing cycle and include campaign spend, platform fees, and applicable adjustments.\n\nTo download invoices, open Dashboard > Orders > Select Order > Billing Details. Click Download Invoice to export a PDF copy for finance records.\n\nIf your billing profile is incomplete, update company name, tax details, and billing address before requesting a revised invoice.",
                'read_time_minutes' => 4,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(17),
                'sort_order' => 2,
            ],
            [
                'title' => 'How creator approval works',
                'slug' => 'how-creator-approval-works',
                'badge' => 'Campaigns',
                'summary' => 'A practical overview of reviewing applicants, shortlisting creators, and sending final approvals.',
                'content' => "When applications arrive, open your campaign and review each creator profile, audience fit, and content history.\n\nUse shortlist to keep top candidates, then approve creators in batches or one by one. Approved creators receive automatic notifications and can start deliverables immediately.\n\nIf you need revisions before approval, use the campaign conversation thread to ask questions and clarify requirements.",
                'read_time_minutes' => 6,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(14),
                'sort_order' => 3,
            ],
            [
                'title' => 'Resetting your account password securely',
                'slug' => 'resetting-your-account-password-securely',
                'badge' => 'Account',
                'summary' => 'Step-by-step password reset instructions and recommended security practices for team accounts.',
                'content' => "From the login screen, choose Forgot password and enter your registered email address.\n\nUse the reset link from your inbox within the validity period. Create a strong password with upper/lowercase letters, numbers, and symbols.\n\nFor shared work devices, always sign out after use and enable email verification to reduce account risk.",
                'read_time_minutes' => 3,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(12),
                'sort_order' => 4,
            ],
            [
                'title' => 'Troubleshooting payment failures',
                'slug' => 'troubleshooting-payment-failures',
                'badge' => 'Payments',
                'summary' => 'Common reasons payments fail and how to resolve card, wallet, and authorization issues quickly.',
                'content' => "Payment failures are commonly caused by expired cards, insufficient limits, or strict bank authorization rules.\n\nFirst, re-check billing details and retry with a supported payment method. If the issue persists, contact your bank to allow merchant authorization, then attempt payment again.\n\nYou can also use an alternative card or submit a support ticket with the order ID and timestamp for faster diagnosis.",
                'read_time_minutes' => 5,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(9),
                'sort_order' => 5,
            ],
            [
                'title' => 'Content submission and revision workflow',
                'slug' => 'content-submission-and-revision-workflow',
                'badge' => 'Content',
                'summary' => 'See how drafts, revisions, and final approval are handled between brand and creator teams.',
                'content' => "Creators submit draft assets in the campaign deliverables section. You can review each file, leave comments, and request revisions where needed.\n\nOnce all requirements are met, approve the final asset set to move the campaign forward.\n\nKeep feedback specific and actionable to reduce turnaround time and improve creator response quality.",
                'read_time_minutes' => 4,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'sort_order' => 6,
            ],
            [
                'title' => 'How to manage team access and permissions',
                'slug' => 'how-to-manage-team-access-and-permissions',
                'badge' => 'Team Access',
                'summary' => 'Assign the right role for each teammate so campaign, billing, and moderation access stays controlled.',
                'content' => "Go to Dashboard > Roles and Permissions to review current access settings.\n\nAssign limited roles to team members who only need campaign or review access. Keep billing and account-level controls restricted to trusted administrators.\n\nReview permissions monthly and remove unused access to maintain account security.",
                'read_time_minutes' => 4,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'sort_order' => 7,
            ],
            [
                'title' => 'Understanding campaign performance metrics',
                'slug' => 'understanding-campaign-performance-metrics',
                'badge' => 'Analytics',
                'summary' => 'Interpret reach, engagement, CTR, and conversion indicators to improve your next collaboration.',
                'content' => "Campaign reporting shows top-level and per-creator performance signals.\n\nStart with reach and engagement to measure content visibility and audience interaction, then compare CTR and conversion to evaluate business outcomes.\n\nUse these metrics to refine creator selection, creative direction, and audience targeting in your next brief.",
                'read_time_minutes' => 6,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'sort_order' => 8,
            ],
        ];

        foreach ($articles as $article) {
            DB::table('knowledge_base_articles')->updateOrInsert(
                ['slug' => $article['slug']],
                [
                    'title' => $article['title'],
                    'badge' => $article['badge'],
                    'summary' => $article['summary'],
                    'content' => $article['content'],
                    'read_time_minutes' => $article['read_time_minutes'],
                    'is_featured' => $article['is_featured'],
                    'is_published' => $article['is_published'],
                    'published_at' => $article['published_at'],
                    'sort_order' => $article['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
