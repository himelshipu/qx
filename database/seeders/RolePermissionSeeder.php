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

        // ADMIN: Broad management access (~60 permissions - clear scope)
        $adminPermissions = [
            // Dashboard & Analytics
            'dashboard.view', 'analytics.view', 'analytics.kpis', 'analytics.revenue',
            'analytics.users', 'analytics.campaigns', 'analytics.orders',

            // User Management - Full CRUD
            'users.index', 'users.create', 'users.store', 'users.show',
            'users.edit', 'users.update', 'users.toggle-status', 'users.destroy',

            // Role Management - Full CRUD
            'roles.index', 'roles.create', 'roles.store', 'roles.edit',
            'roles.update', 'roles.toggle-status', 'roles.destroy',

            // Permission Management
            'permissions.index', 'permissions.manage',

            // Audit & Logs
            'audit.rbac.view', 'audit.rbac.export', 'logs.activity.view', 'logs.activity.export',
            'system.logs', 'system.activity-logs',

            // Content Management - Full CRUD
            'categories.index', 'categories.create', 'categories.store', 'categories.edit',
            'categories.update', 'categories.toggle-status', 'categories.destroy', 'categories.reorder',
            'brands.index', 'brands.create', 'brands.store', 'brands.show',
            'brands.edit', 'brands.update', 'brands.toggle-status', 'brands.destroy', 'brands.reorder',
            'influencers.index', 'influencers.create', 'influencers.store', 'influencers.show',
            'influencers.edit', 'influencers.update', 'influencers.toggle-status',
            'influencers.toggle-featured', 'influencers.destroy', 'influencers.reorder',

            // Campaigns - Full Management
            'campaigns.index', 'campaigns.create', 'campaigns.store', 'campaigns.show',
            'campaigns.edit', 'campaigns.update', 'campaigns.update-status', 'campaigns.assign',
            'campaigns.assigned-influencers', 'campaigns.destroy',
            'campaign-influencers.index', 'campaign-influencers.create', 'campaign-influencers.store',
            'campaign-influencers.approve', 'campaign-influencers.reject', 'campaign-influencers.cancel',
            'campaign-influencers.destroy',

            // Orders - Full Management
            'orders.index', 'orders.show', 'orders.create-from-campaign',
            'orders.update-status', 'orders.update-item-status', 'orders.mark-item-paid',

            // Packages - Full Management
            'packages.index', 'packages.create', 'packages.store', 'packages.show',
            'packages.edit', 'packages.update', 'packages.toggle-status', 'packages.destroy',

            // Payments & Payouts - Full Access
            'payments.index', 'payouts.index', 'payment-queue.index', 'payment-queue.process',
            'payment-queue.retry', 'payment-audit.index', 'payment-statement.index',
            'payment-statement.export',

            // Reviews & Verification
            'reviews.index', 'reviews.show', 'reviews.toggle-visibility',
            'verification.index', 'verification.show', 'verification.approve', 'verification.reject',

            // Support & Communication
            'support-tickets.index', 'support-tickets.show', 'support-tickets.update',
            'support-tickets.destroy', 'support-tickets.bulk-update', 'conversations.index',

            // Settings
            'settings.view', 'settings.edit', 'settings.update',

            // Notifications
            'notifications.index', 'notifications.send', 'notifications.destroy',

            // Testimonials & Case Studies
            'testimonials.index', 'testimonials.create', 'testimonials.store',
            'testimonials.edit', 'testimonials.update', 'testimonials.toggle-status', 'testimonials.destroy', 'testimonials.reorder',
            'case-studies.index', 'case-studies.create', 'case-studies.store',
            'case-studies.edit', 'case-studies.update', 'case-studies.toggle-status', 'case-studies.destroy',

            // FAQs & Knowledge Base
            'faqs.sections.index', 'faqs.sections.create', 'faqs.sections.store',
            'faqs.sections.edit', 'faqs.sections.update', 'faqs.sections.toggle', 'faqs.sections.destroy',
            'faqs.items.index', 'faqs.items.create', 'faqs.items.store',
            'faqs.items.edit', 'faqs.items.update', 'faqs.items.toggle', 'faqs.items.destroy',
            'knowledge-base.index', 'knowledge-base.create', 'knowledge-base.store',
            'knowledge-base.edit', 'knowledge-base.update', 'knowledge-base.toggle-status', 'knowledge-base.destroy',

            // Pages & Sections
            'pages.index', 'pages.create', 'pages.store', 'pages.edit', 'pages.update',
            'pages.toggle-status', 'pages.destroy', 'page-sections.index', 'page-sections.create',
            'page-sections.store', 'page-sections.edit', 'page-sections.update', 'page-sections.destroy',

            // Collaborations
            'featured-collaborations.index', 'featured-collaborations.create', 'featured-collaborations.store',
            'featured-collaborations.edit', 'featured-collaborations.update', 'featured-collaborations.toggle-publish',
            'featured-collaborations.destroy',

            // Static Pages
            'static-pages.index', 'static-pages.create', 'static-pages.store', 'static-pages.show',
            'static-pages.edit', 'static-pages.update', 'static-pages.destroy', 'static-pages.toggle-status',

            // Settings
            'settings.index', 'settings.update',

            // Reports
            'reports.view', 'reports.orders', 'reports.revenue', 'reports.users',
            'reports.campaigns', 'reports.export', 'reports.export-pdf', 'reports.export-csv',

            // Bulk Operations
            'bulk.users-update', 'bulk.users-delete', 'bulk.influencers-update', 'bulk.brands-update',
        ];

        $this->attachPermissionsBySlug($admin, $adminPermissions);

        // MODERATOR: Content review and support (~30 permissions - clear focus)
        $moderatorPermissions = [
            // Dashboard (basic access)
            'dashboard.view', 'analytics.view',

            // Support & Communication - Full Access
            'support-tickets.index', 'support-tickets.show', 'support-tickets.update',
            'support-tickets.bulk-update', 'conversations.index',

            // Reviews & Content Moderation
            'reviews.index', 'reviews.show', 'reviews.toggle-visibility',
            'moderation.queue', 'moderation.review', 'moderation.block-users',
            'moderation.remove-content',

            // User Verification
            'verification.index', 'verification.show', 'verification.approve', 'verification.reject',

            // View Users (Read-only)
            'users.index', 'users.show',

            // View Resources (Read-only)
            'campaigns.index', 'campaigns.show',
            'orders.index', 'orders.show',
            'packages.index', 'packages.show',
            'influencers.index', 'influencers.show',
            'brands.index', 'brands.show',

            // Activity Logs (Read-only)
            'logs.activity.view', 'system.activity-logs',

            // Communication
            'notifications.index',
        ];

        $this->attachPermissionsBySlug($moderator, $moderatorPermissions);

        // MANAGER: Limited access - Read-only for most resources (~15 permissions)
        $managerPermissions = [
            // Dashboard (basic access)
            'dashboard.view',

            // Analytics (read-only)
            'analytics.view', 'analytics.kpis',

            // Read-only access to main resources
            'users.index', 'users.show',
            'campaigns.index', 'campaigns.show',
            'orders.index', 'orders.show',
            'packages.index', 'packages.show',
            'influencers.index', 'influencers.show',
            'brands.index', 'brands.show',

            // Reports (read-only)
            'reports.view', 'reports.orders', 'reports.revenue',
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
