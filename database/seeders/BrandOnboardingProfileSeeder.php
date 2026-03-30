<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandOnboardingProfileSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $brands = DB::table('brands')->get();

        $objectives = ['one-time-campaign', 'ongoing-content', 'exploring'];
        $budgets = ['under-1000', '1000-5000', '5000-10000', '10000-25000', '25000-50000', '50000-plus'];
        $businessTypes = ['agency', 'ecommerce', 'saas', 'local', 'other'];
        $companySizes = ['just-me', '2-10', '11-50', '51-200', '201-500', '500-plus'];

        foreach ($brands as $brand) {
            $isCompleted = $faker->boolean(80);

            $payload = [
                'objective' => $faker->randomElement($objectives),
                'budget_range' => $faker->randomElement($budgets),
                'business_type' => $faker->randomElement($businessTypes),
                'company_size' => $faker->randomElement($companySizes),
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now()->subDays(random_int(1, 60)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('brand_onboarding_profiles', $column))
                ->all();

            DB::table('brand_onboarding_profiles')->updateOrInsert(
                ['brand_id' => $brand->id],
                $payload
            );
        }
    }
}
