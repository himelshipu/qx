<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowerRangeSeeder extends Seeder
{
    public function run(): void
    {
        $ranges = [
            ['code' => 'nano', 'label' => 'Nano (1K - 10K)', 'min' => 1000, 'max' => 10000, 'order' => 1],
            ['code' => 'micro', 'label' => 'Micro (10K - 100K)', 'min' => 10001, 'max' => 100000, 'order' => 2],
            ['code' => 'mid', 'label' => 'Mid (100K - 500K)', 'min' => 100001, 'max' => 500000, 'order' => 3],
            ['code' => 'macro', 'label' => 'Macro (500K - 1M)', 'min' => 500001, 'max' => 1000000, 'order' => 4],
            ['code' => 'mega', 'label' => 'Mega (1M+)', 'min' => 1000001, 'max' => null, 'order' => 5],
        ];

        foreach ($ranges as $range) {
            DB::table('follower_ranges')->updateOrInsert(
                ['code' => $range['code']],
                [
                    'label' => $range['label'],
                    'min_followers' => $range['min'],
                    'max_followers' => $range['max'],
                    'sort_order' => $range['order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
