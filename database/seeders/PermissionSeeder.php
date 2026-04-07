<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'module' => 'users'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'rbac'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'rbac'],
            ['name' => 'View Brands', 'slug' => 'brands.view', 'module' => 'brands'],
            ['name' => 'Manage Brands', 'slug' => 'brands.manage', 'module' => 'brands'],
            ['name' => 'View Influencers', 'slug' => 'influencers.view', 'module' => 'influencers'],
            ['name' => 'Manage Influencers', 'slug' => 'influencers.manage', 'module' => 'influencers'],
            ['name' => 'View Campaigns', 'slug' => 'campaigns.view', 'module' => 'campaigns'],
            ['name' => 'Manage Campaigns', 'slug' => 'campaigns.manage', 'module' => 'campaigns'],
            ['name' => 'Manage Orders', 'slug' => 'orders.manage', 'module' => 'orders'],
            ['name' => 'Manage Reviews', 'slug' => 'reviews.manage', 'module' => 'reviews'],
            ['name' => 'View Support Tickets', 'slug' => 'support.view', 'module' => 'support'],
            ['name' => 'Manage Support Tickets', 'slug' => 'support.manage', 'module' => 'support'],
            ['name' => 'Manage Content', 'slug' => 'content.manage', 'module' => 'content']
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                [
                    'name'        => $permission['name'],
                    'module'      => $permission['module'],
                    'description' => $permission['name'] . ' permission.',
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]
            );
        }
    }
}
