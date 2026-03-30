<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatorPlatformStatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $creators = DB::table('creators')->get();
        $platforms = ['facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc'];

        foreach ($creators as $creator) {
            $handleBase = strtolower(preg_replace('/[^a-z0-9]+/', '', (string) ($creator->display_name ?? 'creator')));
            $handleBase = $handleBase ?: 'creator' . $creator->id;

            foreach ($platforms as $platform) {
                $followers = random_int(5000, 850000);
                $avgViews = (int) max(500, floor($followers * random_int(4, 45) / 100));
                $engagement = round(random_int(80, 950) / 100, 2);

                $profileUrl = match ($platform) {
                    'tiktok' => sprintf('https://www.tiktok.com/@%s', $handleBase),
                    'x' => sprintf('https://x.com/%s', $handleBase),
                    'youtube' => sprintf('https://youtube.com/@%s', $handleBase),
                    'instagram' => sprintf('https://instagram.com/%s', $handleBase),
                    'facebook' => sprintf('https://facebook.com/%s', $handleBase),
                    'linkedin' => sprintf('https://linkedin.com/in/%s', $handleBase),
                    default => sprintf('https://www.example.com/%s/portfolio', $handleBase),
                };

                $payload = [
                    'handle' => $handleBase,
                    'profile_url' => $profileUrl,
                    'follower_count' => $followers,
                    'avg_views' => $avgViews,
                    'engagement_rate' => $engagement,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $payload = collect($payload)
                    ->filter(fn($_, $column) => Schema::hasColumn('creator_platform_stats', $column))
                    ->all();

                DB::table('creator_platform_stats')->updateOrInsert(
                    [
                        'creator_id' => $creator->id,
                        'platform' => $platform,
                    ],
                    $payload
                );
            }
        }
    }
}
