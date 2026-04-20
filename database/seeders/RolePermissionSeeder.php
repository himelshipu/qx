<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed role-permission associations.
     *
     * Strategy:
     * 1. Superadmin gets ALL active permissions
     * 2. Admin role gets broad management permissions
     * 3. Moderator role gets moderation and review permissions
     * 4. Manager role gets basic read-only access
     *
     * This ensures:
     * - Superadmin has complete control
     * - Admin can manage most resources
     * - Moderator can review content and support
     * - Manager has limited access
     * - Brand and Influencer are user_types, not roles (no dashboard access)
     */
    public function run(): void
    {
        // Get or create roles
        $superadmin = Role::where('is_superadmin', true)->first();
        $admin      = Role::firstOrCreate(['slug' => 'admin'], [
            'name'          => 'Admin',
            'slug'          => 'admin',
            'description'   => 'Administrator with broad management access.',
            'is_active'     => true,
            'is_superadmin' => false
        ]);
        $moderator = Role::firstOrCreate(['slug' => 'moderator'], [
            'name'          => 'Moderator',
            'slug'          => 'moderator',
            'description'   => 'Moderator with content review and support access.',
            'is_active'     => true,
            'is_superadmin' => false
        ]);
        $manager = Role::firstOrCreate(['slug' => 'manager'], [
            'name'          => 'Manager',
            'slug'          => 'manager',
            'description'   => 'Manager with limited access to specific resources.',
            'is_active'     => true,
            'is_superadmin' => false
        ]);

        // SUPERADMIN: Gets ALL active permissions
        if ($superadmin) {
            $allPermissions = Permission::where('is_active', true)->pluck('id')->toArray();
            $superadmin->permissions()->sync($allPermissions);
        }

        // ADMIN: all active permissions except destructive account deletion.
        $adminPermissions = Permission::where('is_active', true)
            ->whereNotIn('slug', ['account.destroy'])
            ->pluck('slug')
            ->toArray();

        $this->attachPermissionsBySlug($admin, $adminPermissions);

        // MODERATOR: content review + support + limited read-only operational access.
        $moderatorPermissions = [
            'dashboard.view',
            'profile.edit',
            'profile.update',

            // Support and communication
            'support-tickets.index', 'support-tickets.show', 'support-tickets.update',
            'support-tickets.bulk-update',
            'conversations.index', 'conversations.show', 'conversations.storeMessage', 'conversations.assign-moderator',
            'notifications.index', 'notifications.api.unread', 'notifications.mark-as-read',
            'notifications.mark-as-unread', 'notifications.mark-all-as-read', 'notifications.show',

            // Reviews
            'reviews.index', 'reviews.show', 'reviews.toggle-visibility',

            // Read-only visibility of key resources
            'users.index',
            'campaigns.index', 'campaigns.view',
            'orders.index', 'orders.show',
            'packages.index', 'packages.view',
            'influencers.index', 'influencers.view',
            'brands.index', 'brands.view',

        ];

        $this->attachPermissionsBySlug($moderator, $moderatorPermissions);

        // MANAGER: read-only dashboard operations.
        $managerPermissions = [
            'dashboard.view',
            'profile.edit',
            'profile.update',

            'users.index',
            'campaigns.index', 'campaigns.view',
            'orders.index', 'orders.show',
            'packages.index', 'packages.view',
            'influencers.index', 'influencers.view',
            'brands.index', 'brands.view',
            'payments.index',
            'reviews.index',
            'support-tickets.index',
        ];

        $this->attachPermissionsBySlug($manager, $managerPermissions);
    }

    /**
     * Attach permissions to role by permission slugs.
     */
    private function attachPermissionsBySlug(Role $role, array $permissionSlugs): void
    {
        $permissions = Permission::whereIn('slug', $permissionSlugs)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $role->permissions()->sync($permissions);
    }
}
