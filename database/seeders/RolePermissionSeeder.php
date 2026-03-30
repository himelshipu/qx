<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'slug');
        $permissions = DB::table('permissions')->pluck('id', 'slug');

        $map = [
            'admin' => array_keys($permissions->toArray()),
            'moderator' => [
                'dashboard.view',
                'brands.view',
                'creators.view',
                'campaigns.view',
                'orders.manage',
                'reviews.manage',
                'support.view',
                'support.manage',
                'content.manage',
            ],
            'brand' => [
                'dashboard.view',
                'campaigns.view',
                'campaigns.manage',
                'orders.manage',
                'reviews.manage',
                'support.view',
            ],
            'creator' => [
                'dashboard.view',
                'campaigns.view',
                'orders.manage',
                'reviews.manage',
                'support.view',
            ],
        ];

        foreach ($map as $roleSlug => $permissionSlugs) {
            $roleId = $roles[$roleSlug] ?? null;
            if (!$roleId) {
                continue;
            }

            foreach ($permissionSlugs as $permissionSlug) {
                $permissionId = $permissions[$permissionSlug] ?? null;
                if (!$permissionId) {
                    continue;
                }

                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
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

