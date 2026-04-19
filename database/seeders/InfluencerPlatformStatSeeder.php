<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InfluencerPlatformStatSeeder extends Seeder
{
    public function run(): void
    {
        $faker       = \Faker\Factory::create('en_US');
        $influencers = DB::table('influencers')->get();
        $platforms   = ['facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc'];

        foreach ($influencers as $influencer) {
            $handleBase = strtolower(preg_replace('/[^a-z0-9]+/', '', (string) ($influencer->display_name ?? 'influencer')));
            $handleBase = $handleBase ?: 'influencer' . $influencer->id;

            foreach ($platforms as $platform) {
                $followers  = random_int(5000, 850000);
               
                $profileUrl = match ($platform) {
                    'tiktok'    => sprintf('https://www.tiktok.com/@%s', $handleBase),
                    'x'         => sprintf('https://x.com/%s', $handleBase),
                    'youtube'   => sprintf('https://youtube.com/@%s', $handleBase),
                    'instagram' => sprintf('https://instagram.com/%s', $handleBase),
                    'facebook'  => sprintf('https://facebook.com/%s', $handleBase),
                    'linkedin'  => sprintf('https://linkedin.com/in/%s', $handleBase),
                    default     => sprintf('https://www.example.com/%s/portfolio', $handleBase),
                };

                $payload = [
                    'handle'          => $handleBase,
                    'profile_url'     => $profileUrl,
                    'follower_count'  => $followers,
                    'is_active'       => true,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ];

                $payload = collect($payload)
                    ->filter(fn($_, $column) => Schema::hasColumn('influencer_platform_stats', $column))
                    ->all();

                DB::table('influencer_platform_stats')->updateOrInsert(
                    [
                        'influencer_id' => $influencer->id,
                        'platform'      => $platform
                    ],
                    $payload
                );
            }
        }
    }
}
