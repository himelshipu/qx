<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatorCategorySeeder extends Seeder
{
    public function run(): void
    {
        $creatorIds = DB::table('creators')->pluck('id');
        $categoryIds = DB::table('categories')->pluck('id')->all();

        if (empty($categoryIds)) {
            return;
        }

        foreach ($creatorIds as $creatorId) {
            $selected = collect($categoryIds)
                ->shuffle()
                ->take(random_int(1, min(4, count($categoryIds))));

            foreach ($selected as $categoryId) {
                DB::table('creator_categories')->updateOrInsert(
                    [
                        'creator_id' => $creatorId,
                        'category_id' => $categoryId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
