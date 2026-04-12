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

        // ADMIN: Broad management access
        $adminPermissions = [
            // Dashboard
            'dashboard.view', 'analytics.view', 'analytics.kpis', 'analytics.revenue',

            // User Management
            'users.index', 'users.create', 'users.store', 'users.show', 'users.edit',
            'users.update', 'users.toggle-status',

            // Role Management
            'roles.index', 'roles.create', 'roles.store', 'roles.edit', 'roles.update',
            'roles.toggle-status',

            // Permissions
            'permissions.index', 'permissions.manage',

            // Content Management
            'categories.index', 'categories.create', 'categories.store', 'categories.edit',
            'categories.update', 'categories.toggle-status',
            'brands.index', 'brands.create', 'brands.store', 'brands.show', 'brands.edit',
            'brands.update', 'brands.toggle-status',
            'influencers.index', 'influencers.create', 'influencers.store', 'influencers.show',
            'influencers.edit', 'influencers.update', 'influencers.toggle-status',
            'influencers.toggle-featured',

            // Campaigns
            'campaigns.index', 'campaigns.create', 'campaigns.store', 'campaigns.show',
            'campaigns.edit', 'campaigns.update', 'campaigns.update-status', 'campaigns.assign',
            'campaigns.assigned-influencers', 'content-library.index',

            // Packages & Commerce
            'packages.index', 'packages.create', 'packages.store', 'packages.show',
            'packages.edit', 'packages.update', 'packages.toggle-status',
            'carts.index', 'carts.show',
            'orders.index', 'orders.show', 'orders.update-status', 'orders.create-from-campaign',
            'orders.update-item-status', 'orders.mark-item-paid',
            'payments.index', 'payouts.index', 'wishlists.index',

            // Reviews & Testimonials
            'reviews.index', 'reviews.show', 'reviews.toggle-visibility',
            'testimonials.index', 'testimonials.create', 'testimonials.store',
            'testimonials.edit', 'testimonials.update', 'testimonials.toggle-status',

            // Case Studies
            'case-studies.index', 'case-studies.create', 'case-studies.store',
            'case-studies.edit', 'case-studies.update', 'case-studies.toggle-status',

            // FAQ & Knowledge Base
            'faqs.sections.index', 'faqs.sections.create', 'faqs.sections.store',
            'faqs.sections.edit', 'faqs.sections.update', 'faqs.sections.toggle',
            'faqs.items.index', 'faqs.items.create', 'faqs.items.store',
            'faqs.items.edit', 'faqs.items.update', 'faqs.items.toggle',
            'knowledge-base.index', 'knowledge-base.create', 'knowledge-base.store',
            'knowledge-base.edit', 'knowledge-base.update', 'knowledge-base.toggle-status',

            // Featured Collaborations
            'featured-collaborations.index', 'featured-collaborations.create',
            'featured-collaborations.store', 'featured-collaborations.edit',
            'featured-collaborations.update', 'featured-collaborations.toggle-publish',

            // Pages & Sections
            'pages.index', 'pages.create', 'pages.store', 'pages.edit', 'pages.update',
            'pages.toggle-status', 'page-sections.index', 'page-sections.create',
            'page-sections.store', 'page-sections.edit', 'page-sections.update',

            // Communication
            'conversations.index', 'support-tickets.index', 'support-tickets.show',
            'support-tickets.update', 'support-tickets.bulk-update',

            // Settings
            'settings.view', 'settings.edit', 'settings.update', 'system.logs',
            'system.activity-logs',

            // Reports
            'reports.view', 'reports.orders', 'reports.revenue', 'reports.users',
            'reports.campaigns', 'reports.export', 'reports.export-pdf', 'reports.export-csv',

            // Verification
            'verification.index', 'verification.approve', 'verification.reject', 'verification.show',

            // Bulk Operations
            'bulk.users-update', 'bulk.influencers-update', 'bulk.brands-update',
        ];

        $this->attachPermissionsBySlug($admin, $adminPermissions);

        // MODERATOR: Content review and support
        $moderatorPermissions = [
            // Dashboard (basic access)
            'dashboard.view',

            // Reviews & Moderation
            'reviews.index', 'reviews.show', 'reviews.toggle-visibility',
            'support-tickets.index', 'support-tickets.show', 'support-tickets.update',
            'support-tickets.bulk-update',
            'conversations.index',

            // Moderation
            'moderation.queue', 'moderation.review', 'moderation.block-users',
            'moderation.remove-content',

            // Verification
            'verification.index', 'verification.approve', 'verification.reject', 'verification.show',

            // Analytics (read-only)
            'analytics.view',
        ];

        $this->attachPermissionsBySlug($moderator, $moderatorPermissions);

        // MANAGER: Limited access
        $managerPermissions = [
            // Dashboard (basic access)
            'dashboard.view',

            // Can view resources but not create/edit
            'campaigns.index', 'campaigns.show',
            'orders.index', 'orders.show',
            'packages.index', 'packages.show',
            'influencers.index', 'influencers.show',
            'brands.index', 'brands.show',

            // Analytics (read-only)
            'analytics.view',
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
