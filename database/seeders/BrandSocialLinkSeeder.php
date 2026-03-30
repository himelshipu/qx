<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandSocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $brands = DB::table('brands')->get();

        foreach ($brands as $brand) {
            $handle = strtolower(preg_replace('/[^a-z0-9]+/', '', (string) $brand->brand_name));
            $handle = $handle ?: 'brand' . $brand->id;

            $payload = [
                'instagram_url' => 'https://instagram.com/' . $handle,
                'tiktok_url' => 'https://www.tiktok.com/@' . $handle,
                'facebook_url' => 'https://facebook.com/' . $handle,
                'x_url' => 'https://x.com/' . $handle,
                'youtube_url' => 'https://youtube.com/@' . $handle,
                'other_url' => 'https://linktr.ee/' . $handle,
                'linkedin_url' => 'https://linkedin.com/company/' . $handle,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('brand_social_links', $column))
                ->all();

            DB::table('brand_social_links')->updateOrInsert(
                ['brand_id' => $brand->id],
                $payload
            );
        }
    }
}
