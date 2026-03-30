<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatorSocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $creators = DB::table('creators')->get();

        foreach ($creators as $creator) {
            $handle = strtolower(preg_replace('/[^a-z0-9]+/', '', (string) ($creator->display_name ?? 'creator')));
            $handle = $handle ?: 'creator' . $creator->id;

            $payload = [
                'instagram_url' => 'https://instagram.com/' . $handle,
                'tiktok_url' => 'https://www.tiktok.com/@' . $handle,
                'youtube_url' => 'https://youtube.com/@' . $handle,
                'facebook_url' => 'https://facebook.com/' . $handle,
                'linkedin_url' => 'https://linkedin.com/in/' . $handle,
                'x_url' => 'https://x.com/' . $handle,
                'other_url' => 'https://beacons.ai/' . $handle,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('creator_social_links', $column))
                ->all();

            DB::table('creator_social_links')->updateOrInsert(
                ['creator_id' => $creator->id],
                $payload
            );
        }
    }
}
