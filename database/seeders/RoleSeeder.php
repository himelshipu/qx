<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full platform access across all admin modules.',
                'is_active' => true,
            ],
            [
                'name' => 'Moderator',
                'slug' => 'moderator',
                'description' => 'Reviews moderation queues, support tickets, and user content.',
                'is_active' => true,
            ],
            [
                'name' => 'Brand',
                'slug' => 'brand',
                'description' => 'Can create campaigns and manage brand collaborations.',
                'is_active' => true,
            ],
            [
                'name' => 'Creator',
                'slug' => 'creator',
                'description' => 'Can apply to campaigns and manage creator profile content.',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'is_active' => $role['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
