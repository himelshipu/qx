<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Beauty',
            'Fashion',
            'Travel',
            'Health & Fitness',
            'Food & Drink',
            'Technology',
            'Gaming',
            'Lifestyle',
            'Education',
            'Automotive',
            'Home & Decor',
            'Finance',
            'Parenting',
            'Music & Dance',
            'Comedy & Entertainment',
        ];

        foreach ($categories as $index => $name) {
            $payload = [
                'name' => $name,
                'description' => $name . ' related influencers and campaigns.',
                'icon_path' => null,
                'image_path' => null,
                'is_active' => true,
                'is_featured' => $index < 6,
                'featured_order' => $index + 1,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('categories', $column))
                ->all();

            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($name)],
                $payload
            );
        }
    }
}
