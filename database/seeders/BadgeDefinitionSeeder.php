<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('badge_definitions')->updateOrInsert(
            ['code' => 'top_influencer'],
            [
                'name'        => 'Top Influencer',
                'description' => 'Earned by completing multiple orders with high ratings from brands.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now()
            ]
        );

        DB::table('badge_definitions')->updateOrInsert(
            ['code' => 'responds_fast'],
            [
                'name'        => 'Responds Fast',
                'description' => 'Earned by responding to requests consistently within 12 hours.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now()
            ]
        );
    }
}
