<?php

namespace Database\Seeders;

use App\Models\BadgeDefinition;
use App\Models\Brand;
use App\Models\BrandBillingProfile;
use App\Models\BrandOnboardingProfile;
use App\Models\BrandSocialLink;
use App\Models\Category;
use App\Models\Creator;
use App\Models\CreatorPlatformStat;
use App\Models\CreatorSocialLink;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserProfileSeeder extends Seeder
{
    /**
     * Seed users with realistic brand/creator profile data.
     */
    public function run(): void
    {
        $faker = fake();

        $roles      = Role::whereIn('slug', ['admin', 'moderator', 'brand', 'creator'])->get()->keyBy('slug');
        $categories = Category::all();
        $badges     = BadgeDefinition::all();

        $admin = User::updateOrCreate(
            ['email' => 'admin@qx.local'],
            [
                'name'              => 'System Admin',
                'user_type'         => 'admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'phone'             => '+8801700000000',
                'country'           => 'Bangladesh',
                'city'              => 'Dhaka',
                'is_active'         => true
            ]
        );
        $admin->roles()->sync([$roles['admin']->id]);

        User::factory()
            ->count(3)
            ->state(fn() => [
                'user_type' => 'moderator',
                'job_title' => 'Community Moderator'
            ])
            ->create()
            ->each(function (User $moderator) use ($roles): void {
                $moderator->roles()->sync([$roles['moderator']->id]);
            });

        User::factory()
            ->count(10)
            ->state(fn() => [
                'user_type'    => 'brand',
                'company_name' => fake()->company(),
                'job_title'    => fake()->jobTitle()
            ])
            ->create()
            ->each(function (User $brandUser) use ($roles, $categories, $faker): void {
                $brandUser->roles()->sync([$roles['brand']->id]);

                $brand = Brand::create([
                    'user_id'            => $brandUser->id,
                    'brand_name'         => $brandUser->company_name ?? ($brandUser->name . ' Ventures'),
                    'description'        => $faker->paragraph(),
                    'industry'           => $faker->randomElement(['Fashion', 'Tech', 'Lifestyle', 'Food', 'Beauty']),
                    'phone'              => $brandUser->phone,
                    'email'              => $brandUser->email,
                    'website'            => 'https://' . Str::slug($brandUser->name) . '.example.com',
                    'location'           => $faker->streetAddress(),
                    'city'               => $brandUser->city,
                    'country'            => $brandUser->country,
                    'postal_code'        => $brandUser->postal_code,
                    'profile_image_path' => 'images/brands/profile-' . $brandUser->id . '.jpg',
                    'cover_image_path'   => 'images/brands/cover-' . $brandUser->id . '.jpg',
                    'is_verified'        => $faker->boolean(40),
                    'is_active'          => true
                ]);

                BrandSocialLink::create([
                    'brand_id'      => $brand->id,
                    'instagram_url' => 'https://instagram.com/' . Str::slug($brand->brand_name),
                    'tiktok_url'    => 'https://tiktok.com/@' . Str::slug($brand->brand_name),
                    'facebook_url'  => 'https://facebook.com/' . Str::slug($brand->brand_name),
                    'x_url'         => 'https://x.com/' . Str::slug($brand->brand_name),
                    'youtube_url'   => 'https://youtube.com/@' . Str::slug($brand->brand_name),
                    'other_url'     => null
                ]);

                BrandBillingProfile::create([
                    'brand_id'            => $brand->id,
                    'legal_company_name'  => $brand->brand_name . ' Ltd.',
                    'vat_id'              => strtoupper(Str::random(10)),
                    'billing_address'     => $faker->streetAddress(),
                    'billing_city'        => $faker->city(),
                    'billing_country'     => $brand->country,
                    'billing_postal_code' => $faker->postcode()
                ]);

                $onboarding = BrandOnboardingProfile::create([
                    'brand_id'      => $brand->id,
                    'objective'     => $faker->randomElement(['awareness', 'sales', 'community', 'product_launch']),
                    'budget_range'  => $faker->randomElement(['500-1500', '1500-5000', '5000-12000', '12000+']),
                    'business_type' => $faker->randomElement(['ecommerce', 'service', 'startup', 'enterprise']),
                    'company_size'  => $faker->randomElement(['1-10', '11-50', '51-200', '201+']),
                    'is_completed'  => true,
                    'completed_at'  => now()->subDays(rand(5, 120))
                ]);

                $categoryIds = collect($categories->random(rand(2, 4)))->pluck('id')->all();
                $onboarding->categories()->sync($categoryIds);
            });

        User::factory()
            ->count(24)
            ->state(fn() => [
                'user_type'    => 'creator',
                'job_title'    => 'Content Creator',
                'company_name' => null
            ])
            ->create()
            ->each(function (User $creatorUser) use ($roles, $categories, $badges, $faker): void {
                $creatorUser->roles()->sync([$roles['creator']->id]);

                $creator = Creator::create([
                    'user_id'            => $creatorUser->id,
                    'display_name'       => $creatorUser->name,
                    'title_name'         => $faker->randomElement(['Lifestyle Creator', 'Video Storyteller', 'UGC Specialist', 'Tech Reviewer']),
                    'description'        => $faker->paragraph(),
                    'audience'           => $faker->sentence(),
                    'brands_worked_with' => $faker->sentence(),
                    'location'           => $faker->streetAddress(),
                    'city'               => $creatorUser->city,
                    'country'            => $creatorUser->country,
                    'postal_code'        => $creatorUser->postal_code,
                    'gender'             => $creatorUser->gender,
                    'profile_image_path' => 'images/creators/profile-' . $creatorUser->id . '.jpg',
                    'cover_image_path'   => 'images/creators/cover-' . $creatorUser->id . '.jpg',
                    'is_active'          => true
                ]);

                CreatorSocialLink::create([
                    'creator_id'   => $creator->id,
                    'facebook_url' => 'https://facebook.com/' . Str::slug($creator->display_name),
                    'youtube_url'  => 'https://youtube.com/@' . Str::slug($creator->display_name),
                    'tiktok_url'   => 'https://tiktok.com/@' . Str::slug($creator->display_name),
                    'linkedin_url' => 'https://linkedin.com/in/' . Str::slug($creator->display_name),
                    'x_url'        => 'https://x.com/' . Str::slug($creator->display_name),
                    'other_url'    => 'https://portfolio.example.com/' . Str::slug($creator->display_name)
                ]);

                $platforms = collect(['youtube', 'tiktok', 'linkedin', 'facebook', 'x'])->shuffle()->take(rand(2, 4));
                foreach ($platforms as $platform) {
                    CreatorPlatformStat::create([
                        'creator_id'      => $creator->id,
                        'platform'        => $platform,
                        'handle'          => '@' . Str::slug($creator->display_name) . '_' . $platform,
                        'profile_url'     => 'https://' . $platform . '.com/' . Str::slug($creator->display_name),
                        'follower_count'  => rand(5000, 500000),
                        'avg_views'       => rand(500, 80000),
                        'engagement_rate' => rand(150, 850) / 100,
                        'is_active'       => true
                    ]);
                }

                $categoryIds = collect($categories->random(rand(2, 4)))->pluck('id')->all();
                $creator->categories()->sync($categoryIds);

                $selectedBadgeModels = $badges->random(rand(1, 2));
                $selectedBadgeModels = $selectedBadgeModels instanceof BadgeDefinition
                ? collect([$selectedBadgeModels])
                : $selectedBadgeModels;
                $selectedBadges = $selectedBadgeModels->pluck('id')->all();
                $badgePayload   = [];
                foreach ($selectedBadges as $badgeId) {
                    $badgePayload[$badgeId] = [
                        'earned_at' => $faker->dateTimeBetween('-8 months', 'now'),
                        'is_active' => true
                    ];
                }
                $creator->badges()->sync($badgePayload);
            });
    }
}
