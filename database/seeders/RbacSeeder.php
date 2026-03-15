<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Seed the application's role/permission data.
     */
    public function run(): void
    {
        $rolePermissions = [
            'admin'     => [
                'dashboard.view',
                'users.manage',
                'roles.manage',
                'permissions.manage',
                'campaigns.manage',
                'packages.manage',
                'orders.manage',
                'conversations.manage',
                'support.manage',
                'content.manage',
                'reports.view'
            ],
            'moderator' => [
                'dashboard.view',
                'campaigns.manage',
                'packages.manage',
                'orders.manage',
                'conversations.manage',
                'support.manage',
                'content.manage'
            ],
            'brand'     => [
                'dashboard.view',
                'campaigns.view',
                'creators.view',
                'orders.create',
                'orders.view',
                'conversations.brand',
                'support.create'
            ],
            'creator'   => [
                'dashboard.view',
                'campaigns.view',
                'orders.view',
                'profile.manage',
                'support.create'
            ]
        ];

        $allPermissions = collect($rolePermissions)
            ->flatten()
            ->unique()
            ->values();

        foreach ($allPermissions as $slug) {
            [$module] = explode('.', $slug);
            Permission::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => ucwords(str_replace('.', ' ', $slug)),
                    'module'      => $module,
                    'description' => 'Allows ' . str_replace('.', ' ', $slug),
                    'is_active'   => true
                ]
            );
        }

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $role = Role::updateOrCreate(
                ['slug' => $roleSlug],
                [
                    'name'        => ucfirst($roleSlug),
                    'description' => ucfirst($roleSlug) . ' system role',
                    'is_active'   => true
                ]
            );

            $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id')->all();
            $role->permissions()->sync($permissionIds);
        }
    }
}
