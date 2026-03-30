<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $brands = DB::table('brands')->get();
        $managerIds = DB::table('users')->whereIn('user_type', ['admin', 'moderator'])->pluck('id')->all();
        $hasCreatedByColumn = Schema::hasColumn('campaigns', 'created_by');

        $types = ['facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc', 'other'];
        $statuses = ['draft', 'published', 'paused', 'closed', 'archived'];

        foreach ($brands as $brand) {
            for ($i = 1; $i <= 2; $i++) {
                $startDate = now()->addDays(random_int(3, 30))->toDateString();
                $endDate = now()->addDays(random_int(31, 90))->toDateString();
                $budgetMin = random_int(1000, 5000);
                $budgetMax = $budgetMin + random_int(2000, 12000);
                $status = $faker->randomElement($statuses);
                $createdByUserId = $brand->user_id;

                // Some campaigns are created by admin/moderator on behalf of the brand.
                if (!empty($managerIds) && random_int(1, 100) <= 30) {
                    $createdByUserId = $managerIds[array_rand($managerIds)];
                }

                $payload = [
                    'campaign_type' => $faker->randomElement($types),
                    'description' => $faker->paragraphs(2, true),
                    'instructions' => 'Deliver original content aligned with brand tone and approved messaging.',
                    'status' => $status,
                    'budget_min' => $budgetMin,
                    'budget_max' => $budgetMax,
                    'currency' => 'USD',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'published_at' => $status === 'published' ? now()->subDays(random_int(1, 20)) : null,
                    'is_active' => !in_array($status, ['closed', 'archived'], true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($hasCreatedByColumn) {
                    $payload['created_by'] = $createdByUserId;
                }

                DB::table('campaigns')->updateOrInsert(
                    [
                        'brand_id' => $brand->id,
                        'title' => $brand->brand_name . ' Campaign ' . $i,
                    ],
                    $payload
                );
            }
        }
    }
}
