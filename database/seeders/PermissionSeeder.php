<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->buildPermissions();

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                [
                    'name'        => $permission['name'],
                    'module'      => $permission['module'],
                    'description' => $permission['description'] ?? $permission['name'],
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]
            );
        }
    }

    private function buildPermissions(): array
    {
        return [
            // DASHBOARD
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],

            // USERS
            ['name' => 'View Users', 'slug' => 'users.index', 'module' => 'users'],
            ['name' => 'Create User', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Store User', 'slug' => 'users.store', 'module' => 'users'],
            ['name' => 'View User Details', 'slug' => 'users.show', 'module' => 'users'],
            ['name' => 'Edit User', 'slug' => 'users.edit', 'module' => 'users'],
            ['name' => 'Update User', 'slug' => 'users.update', 'module' => 'users'],
            ['name' => 'Toggle User Status', 'slug' => 'users.toggle-status', 'module' => 'users'],
            ['name' => 'Delete User', 'slug' => 'users.destroy', 'module' => 'users'],

            // ROLES
            ['name' => 'View Roles', 'slug' => 'roles.index', 'module' => 'roles'],
            ['name' => 'Create Role', 'slug' => 'roles.create', 'module' => 'roles'],
            ['name' => 'Store Role', 'slug' => 'roles.store', 'module' => 'roles'],
            ['name' => 'Edit Role', 'slug' => 'roles.edit', 'module' => 'roles'],
            ['name' => 'Update Role', 'slug' => 'roles.update', 'module' => 'roles'],
            ['name' => 'Delete Role', 'slug' => 'roles.destroy', 'module' => 'roles'],
            ['name' => 'Toggle Role Status', 'slug' => 'roles.toggle-status', 'module' => 'roles'],

            // CATEGORIES
            ['name' => 'View Categories', 'slug' => 'categories.index', 'module' => 'categories'],
            ['name' => 'Create Category', 'slug' => 'categories.create', 'module' => 'categories'],
            ['name' => 'Store Category', 'slug' => 'categories.store', 'module' => 'categories'],
            ['name' => 'Edit Category', 'slug' => 'categories.edit', 'module' => 'categories'],
            ['name' => 'Update Category', 'slug' => 'categories.update', 'module' => 'categories'],
            ['name' => 'Delete Category', 'slug' => 'categories.destroy', 'module' => 'categories'],
            ['name' => 'Toggle Category Status', 'slug' => 'categories.toggle-status', 'module' => 'categories'],
            ['name' => 'Reorder Categories', 'slug' => 'categories.reorder', 'module' => 'categories'],

            // BRANDS
            ['name' => 'View Brands', 'slug' => 'brands.index', 'module' => 'brands'],
            ['name' => 'Create Brand', 'slug' => 'brands.create', 'module' => 'brands'],
            ['name' => 'Store Brand', 'slug' => 'brands.store', 'module' => 'brands'],
            ['name' => 'View Brand Details', 'slug' => 'brands.show', 'module' => 'brands'],
            ['name' => 'Edit Brand', 'slug' => 'brands.edit', 'module' => 'brands'],
            ['name' => 'Update Brand', 'slug' => 'brands.update', 'module' => 'brands'],
            ['name' => 'Delete Brand', 'slug' => 'brands.destroy', 'module' => 'brands'],
            ['name' => 'Toggle Brand Status', 'slug' => 'brands.toggle-status', 'module' => 'brands'],
            ['name' => 'Reorder Brands', 'slug' => 'brands.reorder', 'module' => 'brands'],

            // INFLUENCERS
            ['name' => 'View Influencers', 'slug' => 'influencers.index', 'module' => 'influencers'],
            ['name' => 'Create Influencer', 'slug' => 'influencers.create', 'module' => 'influencers'],
            ['name' => 'Store Influencer', 'slug' => 'influencers.store', 'module' => 'influencers'],
            ['name' => 'View Influencer Details', 'slug' => 'influencers.show', 'module' => 'influencers'],
            ['name' => 'Edit Influencer', 'slug' => 'influencers.edit', 'module' => 'influencers'],
            ['name' => 'Update Influencer', 'slug' => 'influencers.update', 'module' => 'influencers'],
            ['name' => 'Delete Influencer', 'slug' => 'influencers.destroy', 'module' => 'influencers'],
            ['name' => 'Toggle Influencer Status', 'slug' => 'influencers.toggle-status', 'module' => 'influencers'],
            ['name' => 'Toggle Influencer Featured', 'slug' => 'influencers.toggle-featured', 'module' => 'influencers'],
            ['name' => 'Reorder Influencers', 'slug' => 'influencers.reorder', 'module' => 'influencers'],

            // INFLUENCER PORTFOLIOS
            ['name' => 'View Portfolios', 'slug' => 'portfolios.index', 'module' => 'portfolios'],
            ['name' => 'Create Portfolio Item', 'slug' => 'portfolios.create', 'module' => 'portfolios'],
            ['name' => 'Store Portfolio Item', 'slug' => 'portfolios.store', 'module' => 'portfolios'],
            ['name' => 'Edit Portfolio Item', 'slug' => 'portfolios.edit', 'module' => 'portfolios'],
            ['name' => 'Update Portfolio Item', 'slug' => 'portfolios.update', 'module' => 'portfolios'],
            ['name' => 'Delete Portfolio Item', 'slug' => 'portfolios.destroy', 'module' => 'portfolios'],
            ['name' => 'Toggle Portfolio Item Status', 'slug' => 'portfolios.toggle', 'module' => 'portfolios'],
            ['name' => 'Reorder Portfolio Items', 'slug' => 'portfolios.reorder', 'module' => 'portfolios'],

            // MODERATORS
            ['name' => 'View Moderators', 'slug' => 'moderators.index', 'module' => 'moderators'],
            ['name' => 'Create Moderator', 'slug' => 'moderators.create', 'module' => 'moderators'],
            ['name' => 'Store Moderator', 'slug' => 'moderators.store', 'module' => 'moderators'],
            ['name' => 'View Moderator Details', 'slug' => 'moderators.show', 'module' => 'moderators'],
            ['name' => 'Edit Moderator', 'slug' => 'moderators.edit', 'module' => 'moderators'],
            ['name' => 'Update Moderator', 'slug' => 'moderators.update', 'module' => 'moderators'],
            ['name' => 'Delete Moderator', 'slug' => 'moderators.destroy', 'module' => 'moderators'],
            ['name' => 'Toggle Moderator Status', 'slug' => 'moderators.toggle-status', 'module' => 'moderators'],

            // CAMPAIGNS
            ['name' => 'View Campaigns', 'slug' => 'campaigns.index', 'module' => 'campaigns'],
            ['name' => 'Create Campaign', 'slug' => 'campaigns.create', 'module' => 'campaigns'],
            ['name' => 'Store Campaign', 'slug' => 'campaigns.store', 'module' => 'campaigns'],
            ['name' => 'View Campaign Details', 'slug' => 'campaigns.show', 'module' => 'campaigns'],
            ['name' => 'Edit Campaign', 'slug' => 'campaigns.edit', 'module' => 'campaigns'],
            ['name' => 'Update Campaign', 'slug' => 'campaigns.update', 'module' => 'campaigns'],
            ['name' => 'Delete Campaign', 'slug' => 'campaigns.destroy', 'module' => 'campaigns'],
            ['name' => 'Update Campaign Status', 'slug' => 'campaigns.update-status', 'module' => 'campaigns'],
            ['name' => 'Assign Influencers to Campaign', 'slug' => 'campaigns.assign', 'module' => 'campaigns'],
            ['name' => 'Get Assigned Influencers', 'slug' => 'campaigns.assigned-influencers', 'module' => 'campaigns'],

            // CAMPAIGN INFLUENCERS
            ['name' => 'View Campaign Influencers', 'slug' => 'campaign-influencers.index', 'module' => 'campaigns'],
            ['name' => 'Create Campaign Influencer', 'slug' => 'campaign-influencers.create', 'module' => 'campaigns'],
            ['name' => 'Store Campaign Influencer', 'slug' => 'campaign-influencers.store', 'module' => 'campaigns'],
            ['name' => 'Approve Campaign Influencer', 'slug' => 'campaign-influencers.approve', 'module' => 'campaigns'],
            ['name' => 'Reject Campaign Influencer', 'slug' => 'campaign-influencers.reject', 'module' => 'campaigns'],
            ['name' => 'Cancel Campaign Influencer', 'slug' => 'campaign-influencers.cancel', 'module' => 'campaigns'],
            ['name' => 'Delete Campaign Influencer', 'slug' => 'campaign-influencers.destroy', 'module' => 'campaigns'],

            // PACKAGES
            ['name' => 'View Packages', 'slug' => 'packages.index', 'module' => 'packages'],
            ['name' => 'Create Package', 'slug' => 'packages.create', 'module' => 'packages'],
            ['name' => 'Store Package', 'slug' => 'packages.store', 'module' => 'packages'],
            ['name' => 'View Package Details', 'slug' => 'packages.show', 'module' => 'packages'],
            ['name' => 'Edit Package', 'slug' => 'packages.edit', 'module' => 'packages'],
            ['name' => 'Update Package', 'slug' => 'packages.update', 'module' => 'packages'],
            ['name' => 'Delete Package', 'slug' => 'packages.destroy', 'module' => 'packages'],
            ['name' => 'Toggle Package Status', 'slug' => 'packages.toggle-status', 'module' => 'packages'],
            ['name' => 'Purchase Package', 'slug' => 'packages.purchase', 'module' => 'packages'],

            // ORDERS
            ['name' => 'View Orders', 'slug' => 'orders.index', 'module' => 'orders'],
            ['name' => 'View Order Details', 'slug' => 'orders.show', 'module' => 'orders'],
            ['name' => 'Update Order Status', 'slug' => 'orders.update-status', 'module' => 'orders'],
            ['name' => 'Create Order from Campaign', 'slug' => 'orders.create-from-campaign', 'module' => 'orders'],
            ['name' => 'Update Sub-Order Status', 'slug' => 'orders.update-sub-order-status', 'module' => 'orders'],
            ['name' => 'Mark Sub-Order Paid', 'slug' => 'orders.mark-sub-order-paid', 'module' => 'orders'],
            ['name' => 'Update Order Item Status', 'slug' => 'orders.update-item-status', 'module' => 'orders'],
            ['name' => 'Mark Order Item Paid', 'slug' => 'orders.mark-item-paid', 'module' => 'orders'],

            // PAYMENTS
            ['name' => 'View Payments', 'slug' => 'payments.index', 'module' => 'payments'],

            // PAYOUTS
            ['name' => 'View Payouts', 'slug' => 'payouts.index', 'module' => 'payouts'],

            // REVIEWS
            ['name' => 'View Reviews', 'slug' => 'reviews.index', 'module' => 'reviews'],
            ['name' => 'View Review Details', 'slug' => 'reviews.show', 'module' => 'reviews'],
            ['name' => 'Toggle Review Visibility', 'slug' => 'reviews.toggle-visibility', 'module' => 'reviews'],

            // SUPPORT TICKETS
            ['name' => 'View Support Tickets', 'slug' => 'support-tickets.index', 'module' => 'support'],
            ['name' => 'View Ticket Details', 'slug' => 'support-tickets.show', 'module' => 'support'],
            ['name' => 'Update Ticket Status', 'slug' => 'support-tickets.update', 'module' => 'support'],
            ['name' => 'Delete Ticket', 'slug' => 'support-tickets.destroy', 'module' => 'support'],
            ['name' => 'Bulk Update Tickets', 'slug' => 'support-tickets.bulk-update', 'module' => 'support'],

            // CONVERSATIONS
            ['name' => 'View Conversations', 'slug' => 'conversations.index', 'module' => 'conversations'],

            // CASE STUDIES
            ['name' => 'View Case Studies', 'slug' => 'case-studies.index', 'module' => 'content'],
            ['name' => 'Create Case Study', 'slug' => 'case-studies.create', 'module' => 'content'],
            ['name' => 'Store Case Study', 'slug' => 'case-studies.store', 'module' => 'content'],
            ['name' => 'View Case Study Details', 'slug' => 'case-studies.show', 'module' => 'content'],
            ['name' => 'Edit Case Study', 'slug' => 'case-studies.edit', 'module' => 'content'],
            ['name' => 'Update Case Study', 'slug' => 'case-studies.update', 'module' => 'content'],
            ['name' => 'Delete Case Study', 'slug' => 'case-studies.destroy', 'module' => 'content'],
            ['name' => 'Toggle Case Study Status', 'slug' => 'case-studies.toggle-status', 'module' => 'content'],

            // TESTIMONIALS
            ['name' => 'View Testimonials', 'slug' => 'testimonials.index', 'module' => 'content'],
            ['name' => 'Create Testimonial', 'slug' => 'testimonials.create', 'module' => 'content'],
            ['name' => 'Store Testimonial', 'slug' => 'testimonials.store', 'module' => 'content'],
            ['name' => 'Edit Testimonial', 'slug' => 'testimonials.edit', 'module' => 'content'],
            ['name' => 'Update Testimonial', 'slug' => 'testimonials.update', 'module' => 'content'],
            ['name' => 'Delete Testimonial', 'slug' => 'testimonials.destroy', 'module' => 'content'],
            ['name' => 'Toggle Testimonial Status', 'slug' => 'testimonials.toggle-status', 'module' => 'content'],
            ['name' => 'Reorder Testimonials', 'slug' => 'testimonials.reorder', 'module' => 'content'],

            // FAQ SECTIONS
            ['name' => 'View FAQ Sections', 'slug' => 'faqs.sections.index', 'module' => 'content'],
            ['name' => 'Create FAQ Section', 'slug' => 'faqs.sections.create', 'module' => 'content'],
            ['name' => 'Store FAQ Section', 'slug' => 'faqs.sections.store', 'module' => 'content'],
            ['name' => 'Edit FAQ Section', 'slug' => 'faqs.sections.edit', 'module' => 'content'],
            ['name' => 'Update FAQ Section', 'slug' => 'faqs.sections.update', 'module' => 'content'],
            ['name' => 'Delete FAQ Section', 'slug' => 'faqs.sections.destroy', 'module' => 'content'],
            ['name' => 'Toggle FAQ Section Status', 'slug' => 'faqs.sections.toggle', 'module' => 'content'],

            // FAQ ITEMS
            ['name' => 'View FAQ Items', 'slug' => 'faqs.items.index', 'module' => 'content'],
            ['name' => 'Create FAQ Item', 'slug' => 'faqs.items.create', 'module' => 'content'],
            ['name' => 'Store FAQ Item', 'slug' => 'faqs.items.store', 'module' => 'content'],
            ['name' => 'Edit FAQ Item', 'slug' => 'faqs.items.edit', 'module' => 'content'],
            ['name' => 'Update FAQ Item', 'slug' => 'faqs.items.update', 'module' => 'content'],
            ['name' => 'Delete FAQ Item', 'slug' => 'faqs.items.destroy', 'module' => 'content'],
            ['name' => 'Toggle FAQ Item Status', 'slug' => 'faqs.items.toggle', 'module' => 'content'],

            // KNOWLEDGE BASE
            ['name' => 'View Knowledge Base', 'slug' => 'knowledge-base.index', 'module' => 'content'],
            ['name' => 'Create Article', 'slug' => 'knowledge-base.create', 'module' => 'content'],
            ['name' => 'Store Article', 'slug' => 'knowledge-base.store', 'module' => 'content'],
            ['name' => 'Edit Article', 'slug' => 'knowledge-base.edit', 'module' => 'content'],
            ['name' => 'Update Article', 'slug' => 'knowledge-base.update', 'module' => 'content'],
            ['name' => 'Delete Article', 'slug' => 'knowledge-base.destroy', 'module' => 'content'],
            ['name' => 'Toggle Article Status', 'slug' => 'knowledge-base.toggle-status', 'module' => 'content'],

            // FEATURED COLLABORATIONS
            ['name' => 'View Collaborations', 'slug' => 'featured-collaborations.index', 'module' => 'content'],
            ['name' => 'Create Collaboration', 'slug' => 'featured-collaborations.create', 'module' => 'content'],
            ['name' => 'Store Collaboration', 'slug' => 'featured-collaborations.store', 'module' => 'content'],
            ['name' => 'Edit Collaboration', 'slug' => 'featured-collaborations.edit', 'module' => 'content'],
            ['name' => 'Update Collaboration', 'slug' => 'featured-collaborations.update', 'module' => 'content'],
            ['name' => 'Delete Collaboration', 'slug' => 'featured-collaborations.destroy', 'module' => 'content'],
            ['name' => 'Toggle Collaboration Published', 'slug' => 'featured-collaborations.toggle-publish', 'module' => 'content'],

            // USERS MANAGEMENT - CRUD OPERATIONS
            ['name' => 'Create User', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Store User', 'slug' => 'users.store', 'module' => 'users'],
            ['name' => 'View User Details', 'slug' => 'users.show', 'module' => 'users'],
            ['name' => 'Edit User', 'slug' => 'users.edit', 'module' => 'users'],
            ['name' => 'Update User', 'slug' => 'users.update', 'module' => 'users'],
            ['name' => 'Delete User', 'slug' => 'users.destroy', 'module' => 'users'],

            // DASHBOARD ANALYTICS & INSIGHTS
            ['name' => 'View Dashboard Analytics', 'slug' => 'analytics.view', 'module' => 'dashboard'],
            ['name' => 'View System KPIs', 'slug' => 'analytics.kpis', 'module' => 'dashboard'],
            ['name' => 'View Revenue Reports', 'slug' => 'analytics.revenue', 'module' => 'dashboard'],
            ['name' => 'View User Analytics', 'slug' => 'analytics.users', 'module' => 'dashboard'],
            ['name' => 'View Campaign Analytics', 'slug' => 'analytics.campaigns', 'module' => 'dashboard'],
            ['name' => 'View Order Analytics', 'slug' => 'analytics.orders', 'module' => 'dashboard'],

            // PAGES & CONTENT SECTIONS
            ['name' => 'View Pages', 'slug' => 'pages.index', 'module' => 'content'],
            ['name' => 'Create Page', 'slug' => 'pages.create', 'module' => 'content'],
            ['name' => 'Store Page', 'slug' => 'pages.store', 'module' => 'content'],
            ['name' => 'Edit Page', 'slug' => 'pages.edit', 'module' => 'content'],
            ['name' => 'Update Page', 'slug' => 'pages.update', 'module' => 'content'],
            ['name' => 'Delete Page', 'slug' => 'pages.destroy', 'module' => 'content'],
            ['name' => 'Toggle Page Status', 'slug' => 'pages.toggle-status', 'module' => 'content'],

            ['name' => 'View Page Sections', 'slug' => 'page-sections.index', 'module' => 'content'],
            ['name' => 'Create Page Section', 'slug' => 'page-sections.create', 'module' => 'content'],
            ['name' => 'Store Page Section', 'slug' => 'page-sections.store', 'module' => 'content'],
            ['name' => 'Edit Page Section', 'slug' => 'page-sections.edit', 'module' => 'content'],
            ['name' => 'Update Page Section', 'slug' => 'page-sections.update', 'module' => 'content'],
            ['name' => 'Delete Page Section', 'slug' => 'page-sections.destroy', 'module' => 'content'],

            // SETTINGS & CONFIGURATION
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'module' => 'settings'],
            ['name' => 'Update Settings', 'slug' => 'settings.update', 'module' => 'settings'],
            ['name' => 'View System Logs', 'slug' => 'system.logs', 'module' => 'settings'],
            ['name' => 'View Activity Logs', 'slug' => 'system.activity-logs', 'module' => 'settings'],

            // USER VERIFICATION & APPROVAL
            ['name' => 'View Pending Verifications', 'slug' => 'verification.index', 'module' => 'verification'],
            ['name' => 'Approve User Verification', 'slug' => 'verification.approve', 'module' => 'verification'],
            ['name' => 'Reject User Verification', 'slug' => 'verification.reject', 'module' => 'verification'],
            ['name' => 'View Verification Details', 'slug' => 'verification.show', 'module' => 'verification'],

            // REPORTS & EXPORTS
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports'],
            ['name' => 'View Order Reports', 'slug' => 'reports.orders', 'module' => 'reports'],
            ['name' => 'View Revenue Reports', 'slug' => 'reports.revenue', 'module' => 'reports'],
            ['name' => 'View User Reports', 'slug' => 'reports.users', 'module' => 'reports'],
            ['name' => 'View Campaign Reports', 'slug' => 'reports.campaigns', 'module' => 'reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports'],
            ['name' => 'Export to PDF', 'slug' => 'reports.export-pdf', 'module' => 'reports'],
            ['name' => 'Export to CSV', 'slug' => 'reports.export-csv', 'module' => 'reports'],

            // PERMISSION MANAGEMENT
            ['name' => 'View Permissions', 'slug' => 'permissions.index', 'module' => 'permissions'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'permissions'],

            // BADGE & VERIFICATION SYSTEM
            ['name' => 'View Badge Definitions', 'slug' => 'badges.index', 'module' => 'badges'],
            ['name' => 'Create Badge', 'slug' => 'badges.create', 'module' => 'badges'],
            ['name' => 'Edit Badge', 'slug' => 'badges.edit', 'module' => 'badges'],
            ['name' => 'Assign Badges', 'slug' => 'badges.assign', 'module' => 'badges'],

            // BULK OPERATIONS
            ['name' => 'Bulk Update Users', 'slug' => 'bulk.users-update', 'module' => 'admin'],
            ['name' => 'Bulk Delete Users', 'slug' => 'bulk.users-delete', 'module' => 'admin'],
            ['name' => 'Bulk Update Influencers', 'slug' => 'bulk.influencers-update', 'module' => 'admin'],
            ['name' => 'Bulk Update Brands', 'slug' => 'bulk.brands-update', 'module' => 'admin'],

            // BILLING & INVOICING
            ['name' => 'View Invoices', 'slug' => 'invoices.index', 'module' => 'billing'],
            ['name' => 'Generate Invoice', 'slug' => 'invoices.generate', 'module' => 'billing'],
            ['name' => 'Export Invoice', 'slug' => 'invoices.export', 'module' => 'billing'],

            // PLATFORM MODERATION
            ['name' => 'View Moderation Queue', 'slug' => 'moderation.queue', 'module' => 'moderation'],
            ['name' => 'Review Flagged Content', 'slug' => 'moderation.review', 'module' => 'moderation'],
            ['name' => 'Block/Unblock Users', 'slug' => 'moderation.block-users', 'module' => 'moderation'],
            ['name' => 'Remove Content', 'slug' => 'moderation.remove-content', 'module' => 'moderation'],

            // NOTIFICATIONS MANAGEMENT
            ['name' => 'View Notifications', 'slug' => 'notifications.index', 'module' => 'notifications'],
            ['name' => 'Send Notification', 'slug' => 'notifications.send', 'module' => 'notifications'],
            ['name' => 'Delete Notification', 'slug' => 'notifications.destroy', 'module' => 'notifications'],

            // PAYMENT QUEUE MANAGEMENT
            ['name' => 'View Payment Queue', 'slug' => 'payment-queue.index', 'module' => 'payments'],
            ['name' => 'Process Payment Queue', 'slug' => 'payment-queue.process', 'module' => 'payments'],
            ['name' => 'Retry Failed Payments', 'slug' => 'payment-queue.retry', 'module' => 'payments'],

            // PAYMENT AUDIT
            ['name' => 'View Payment Audit Logs', 'slug' => 'payment-audit.index', 'module' => 'payments'],

            // PAYMENT STATEMENTS  
            ['name' => 'View Payment Statements', 'slug' => 'payment-statement.index', 'module' => 'payments'],
            ['name' => 'Export Payment Statements', 'slug' => 'payment-statement.export', 'module' => 'payments'],

            // ACTIVITY & AUDIT LOGGING
            ['name' => 'View Activity Logs', 'slug' => 'logs.activity.view', 'module' => 'logs'],
            ['name' => 'Export Activity Logs', 'slug' => 'logs.activity.export', 'module' => 'logs'],
            ['name' => 'View RBAC Audit Trail', 'slug' => 'audit.rbac.view', 'module' => 'audit'],
            ['name' => 'Export RBAC Audit', 'slug' => 'audit.rbac.export', 'module' => 'audit'],

            // STATIC PAGES
            ['name' => 'View Static Pages', 'slug' => 'static-pages.index', 'module' => 'content'],
            ['name' => 'Create Static Page', 'slug' => 'static-pages.create', 'module' => 'content'],
            ['name' => 'Store Static Page', 'slug' => 'static-pages.store', 'module' => 'content'],
            ['name' => 'View Page Details', 'slug' => 'static-pages.show', 'module' => 'content'],
            ['name' => 'Edit Static Page', 'slug' => 'static-pages.edit', 'module' => 'content'],
            ['name' => 'Update Static Page', 'slug' => 'static-pages.update', 'module' => 'content'],
            ['name' => 'Delete Static Page', 'slug' => 'static-pages.destroy', 'module' => 'content'],
            ['name' => 'Toggle Page Status', 'slug' => 'static-pages.toggle-status', 'module' => 'content'],

            // Settings
            ['name' => 'View Settings', 'slug' => 'settings.index', 'module' => 'settings'],
            ['name' => 'Update Settings', 'slug' => 'settings.update', 'module' => 'settings'],
        ];
    }
}
