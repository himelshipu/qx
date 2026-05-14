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

            // MANAGEMENT: CATEGORIES
            ['name' => 'View Categories', 'slug' => 'categories.index', 'module' => 'categories'],
            ['name' => 'Create Category', 'slug' => 'categories.create', 'module' => 'categories'],
            ['name' => 'Store Category', 'slug' => 'categories.store', 'module' => 'categories'],
            ['name' => 'Edit Category', 'slug' => 'categories.edit', 'module' => 'categories'],
            ['name' => 'Update Category', 'slug' => 'categories.update', 'module' => 'categories'],
            ['name' => 'Delete Category', 'slug' => 'categories.destroy', 'module' => 'categories'],
            ['name' => 'Toggle Category Status', 'slug' => 'categories.toggle-status', 'module' => 'categories'],
            ['name' => 'Reorder Categories', 'slug' => 'categories.reorder', 'module' => 'categories'],

            // MANAGEMENT: BRANDS
            ['name' => 'View Brands', 'slug' => 'brands.index', 'module' => 'brands'],
            ['name' => 'View Brand Table', 'slug' => 'brands.table', 'module' => 'brands'],
            ['name' => 'Create Brand', 'slug' => 'brands.create', 'module' => 'brands'],
            ['name' => 'Store Brand', 'slug' => 'brands.store', 'module' => 'brands'],
            ['name' => 'View Brand Details', 'slug' => 'brands.view', 'module' => 'brands'],
            ['name' => 'Edit Brand', 'slug' => 'brands.edit', 'module' => 'brands'],
            ['name' => 'Update Brand', 'slug' => 'brands.update', 'module' => 'brands'],
            ['name' => 'Delete Brand', 'slug' => 'brands.destroy', 'module' => 'brands'],
            ['name' => 'Toggle Brand Status', 'slug' => 'brands.toggle-status', 'module' => 'brands'],
            ['name' => 'Reorder Brands', 'slug' => 'brands.reorder', 'module' => 'brands'],

            // MANAGEMENT: INFLUENCERS
            ['name' => 'View Influencers', 'slug' => 'influencers.index', 'module' => 'influencers'],
            ['name' => 'View Influencer Table', 'slug' => 'influencers.table', 'module' => 'influencers'],
            ['name' => 'Create Influencer', 'slug' => 'influencers.create', 'module' => 'influencers'],
            ['name' => 'Store Influencer', 'slug' => 'influencers.store', 'module' => 'influencers'],
            ['name' => 'View Influencer Details', 'slug' => 'influencers.view', 'module' => 'influencers'],
            ['name' => 'Edit Influencer', 'slug' => 'influencers.edit', 'module' => 'influencers'],
            ['name' => 'Update Influencer', 'slug' => 'influencers.update', 'module' => 'influencers'],
            ['name' => 'Delete Influencer', 'slug' => 'influencers.destroy', 'module' => 'influencers'],
            ['name' => 'Toggle Influencer Status', 'slug' => 'influencers.toggle-status', 'module' => 'influencers'],
            ['name' => 'Toggle Influencer Featured', 'slug' => 'influencers.toggle-featured', 'module' => 'influencers'],

            // MANAGEMENT: INFLUENCER PORTFOLIO
            ['name' => 'View Portfolio Items', 'slug' => 'influencers.portfolio.index', 'module' => 'influencers'],
            ['name' => 'Create Portfolio Item', 'slug' => 'influencers.portfolio.create', 'module' => 'influencers'],
            ['name' => 'Store Portfolio Item', 'slug' => 'influencers.portfolio.store', 'module' => 'influencers'],
            ['name' => 'Edit Portfolio Item', 'slug' => 'influencers.portfolio.edit', 'module' => 'influencers'],
            ['name' => 'Update Portfolio Item', 'slug' => 'influencers.portfolio.update', 'module' => 'influencers'],
            ['name' => 'Delete Portfolio Item', 'slug' => 'influencers.portfolio.destroy', 'module' => 'influencers'],
            ['name' => 'Reorder Portfolio Items', 'slug' => 'influencers.portfolio.reorder', 'module' => 'influencers'],
            ['name' => 'Toggle Portfolio Item Status', 'slug' => 'influencers.portfolio.toggle', 'module' => 'influencers'],

            // MANAGEMENT: CASE STUDIES
            ['name' => 'View Case Studies', 'slug' => 'case-studies.index', 'module' => 'content'],
            ['name' => 'Create Case Study', 'slug' => 'case-studies.create', 'module' => 'content'],
            ['name' => 'Store Case Study', 'slug' => 'case-studies.store', 'module' => 'content'],
            ['name' => 'View Case Study Details', 'slug' => 'case-studies.show', 'module' => 'content'],
            ['name' => 'Edit Case Study', 'slug' => 'case-studies.edit', 'module' => 'content'],
            ['name' => 'Update Case Study', 'slug' => 'case-studies.update', 'module' => 'content'],
            ['name' => 'Delete Case Study', 'slug' => 'case-studies.destroy', 'module' => 'content'],
            ['name' => 'Toggle Case Study Status', 'slug' => 'case-studies.toggle-status', 'module' => 'content'],
            ['name' => 'Reorder Case Studies', 'slug' => 'case-studies.reorder', 'module' => 'content'],

            // MANAGEMENT: TESTIMONIALS
            ['name' => 'View Testimonials', 'slug' => 'testimonials.index', 'module' => 'content'],
            ['name' => 'Create Testimonial', 'slug' => 'testimonials.create', 'module' => 'content'],
            ['name' => 'Store Testimonial', 'slug' => 'testimonials.store', 'module' => 'content'],
            ['name' => 'Edit Testimonial', 'slug' => 'testimonials.edit', 'module' => 'content'],
            ['name' => 'Update Testimonial', 'slug' => 'testimonials.update', 'module' => 'content'],
            ['name' => 'Delete Testimonial', 'slug' => 'testimonials.destroy', 'module' => 'content'],
            ['name' => 'Toggle Testimonial Status', 'slug' => 'testimonials.toggle-status', 'module' => 'content'],
            ['name' => 'Reorder Testimonials', 'slug' => 'testimonials.reorder', 'module' => 'content'],

            // MANAGEMENT: FAQ SECTIONS
            ['name' => 'View FAQ Sections', 'slug' => 'faqs.sections.index', 'module' => 'content'],
            ['name' => 'Create FAQ Section', 'slug' => 'faqs.sections.create', 'module' => 'content'],
            ['name' => 'Store FAQ Section', 'slug' => 'faqs.sections.store', 'module' => 'content'],
            ['name' => 'Edit FAQ Section', 'slug' => 'faqs.sections.edit', 'module' => 'content'],
            ['name' => 'Update FAQ Section', 'slug' => 'faqs.sections.update', 'module' => 'content'],
            ['name' => 'Delete FAQ Section', 'slug' => 'faqs.sections.destroy', 'module' => 'content'],
            ['name' => 'Toggle FAQ Section Status', 'slug' => 'faqs.sections.toggle-status', 'module' => 'content'],

            // MANAGEMENT: FAQ ITEMS
            ['name' => 'View FAQ Items', 'slug' => 'faqs.items.index', 'module' => 'content'],
            ['name' => 'Create FAQ Item', 'slug' => 'faqs.items.create', 'module' => 'content'],
            ['name' => 'Store FAQ Item', 'slug' => 'faqs.items.store', 'module' => 'content'],
            ['name' => 'Edit FAQ Item', 'slug' => 'faqs.items.edit', 'module' => 'content'],
            ['name' => 'Update FAQ Item', 'slug' => 'faqs.items.update', 'module' => 'content'],
            ['name' => 'Delete FAQ Item', 'slug' => 'faqs.items.destroy', 'module' => 'content'],
            ['name' => 'Toggle FAQ Item Status', 'slug' => 'faqs.items.toggle-status', 'module' => 'content'],

            // MANAGEMENT: KNOWLEDGE BASE
            ['name' => 'View Knowledge Base', 'slug' => 'knowledge-base.index', 'module' => 'content'],
            ['name' => 'Create Article', 'slug' => 'knowledge-base.create', 'module' => 'content'],
            ['name' => 'Store Article', 'slug' => 'knowledge-base.store', 'module' => 'content'],
            ['name' => 'Edit Article', 'slug' => 'knowledge-base.edit', 'module' => 'content'],
            ['name' => 'Update Article', 'slug' => 'knowledge-base.update', 'module' => 'content'],
            ['name' => 'Delete Article', 'slug' => 'knowledge-base.destroy', 'module' => 'content'],
            ['name' => 'Toggle Article Status', 'slug' => 'knowledge-base.toggle-status', 'module' => 'content'],

            // MANAGEMENT: BLOG
            ['name' => 'View Blog Posts', 'slug' => 'blogs.index', 'module' => 'content'],
            ['name' => 'Create Blog Post', 'slug' => 'blogs.create', 'module' => 'content'],
            ['name' => 'Store Blog Post', 'slug' => 'blogs.store', 'module' => 'content'],
            ['name' => 'View Blog Details', 'slug' => 'blogs.show', 'module' => 'content'],
            ['name' => 'Edit Blog Post', 'slug' => 'blogs.edit', 'module' => 'content'],
            ['name' => 'Update Blog Post', 'slug' => 'blogs.update', 'module' => 'content'],
            ['name' => 'Delete Blog Post', 'slug' => 'blogs.destroy', 'module' => 'content'],
            ['name' => 'Toggle Blog Status', 'slug' => 'blogs.toggle-status', 'module' => 'content'],
            ['name' => 'Restore Blog Post', 'slug' => 'blogs.restore', 'module' => 'content'],

            // MANAGEMENT: FEATURED COLLABORATIONS
            ['name' => 'View Collaborations', 'slug' => 'featured-collaborations.index', 'module' => 'content'],
            ['name' => 'Create Collaboration', 'slug' => 'featured-collaborations.create', 'module' => 'content'],
            ['name' => 'Store Collaboration', 'slug' => 'featured-collaborations.store', 'module' => 'content'],
            ['name' => 'Edit Collaboration', 'slug' => 'featured-collaborations.edit', 'module' => 'content'],
            ['name' => 'Update Collaboration', 'slug' => 'featured-collaborations.update', 'module' => 'content'],
            ['name' => 'Delete Collaboration', 'slug' => 'featured-collaborations.destroy', 'module' => 'content'],
            ['name' => 'Toggle Collaboration Published', 'slug' => 'featured-collaborations.toggle-publish', 'module' => 'content'],

            // MANAGEMENT: STATIC PAGES + FOOTER SETTINGS
            ['name' => 'View Static Pages', 'slug' => 'static-pages.index', 'module' => 'content'],
            ['name' => 'Create Static Page', 'slug' => 'static-pages.create', 'module' => 'content'],
            ['name' => 'Store Static Page', 'slug' => 'static-pages.store', 'module' => 'content'],
            ['name' => 'View Page Details', 'slug' => 'static-pages.show', 'module' => 'content'],
            ['name' => 'Edit Static Page', 'slug' => 'static-pages.edit', 'module' => 'content'],
            ['name' => 'Update Static Page', 'slug' => 'static-pages.update', 'module' => 'content'],
            ['name' => 'Delete Static Page', 'slug' => 'static-pages.destroy', 'module' => 'content'],
            ['name' => 'Toggle Page Status', 'slug' => 'static-pages.toggle-status', 'module' => 'content'],
            ['name' => 'View Settings', 'slug' => 'settings.index', 'module' => 'settings'],
            ['name' => 'Update Settings', 'slug' => 'settings.update', 'module' => 'settings'],
            ['name' => 'Update Settings Order', 'slug' => 'settings.update-order', 'module' => 'settings'],
            ['name' => 'Restore Deleted Content', 'slug' => 'settings.restore', 'module' => 'settings'],

            // CAMPAIGNS
            ['name' => 'View Campaigns', 'slug' => 'campaigns.index', 'module' => 'campaigns'],
            ['name' => 'Matchmaking Campaign', 'slug' => 'campaigns.create', 'module' => 'campaigns'],
            ['name' => 'Store Campaign', 'slug' => 'campaigns.store', 'module' => 'campaigns'],
            ['name' => 'View Campaign Details', 'slug' => 'campaigns.view', 'module' => 'campaigns'],
            ['name' => 'Edit Campaign', 'slug' => 'campaigns.edit', 'module' => 'campaigns'],
            ['name' => 'Update Campaign', 'slug' => 'campaigns.update', 'module' => 'campaigns'],
            ['name' => 'Delete Campaign', 'slug' => 'campaigns.destroy', 'module' => 'campaigns'],
            ['name' => 'Update Campaign Status', 'slug' => 'campaigns.update-status', 'module' => 'campaigns'],
            ['name' => 'Update Campaign Application Status', 'slug' => 'campaigns.update-application-status', 'module' => 'campaigns'],
            ['name' => 'Assign Campaign', 'slug' => 'campaigns.assign', 'module' => 'campaigns'],
            ['name' => 'Store Campaign Assignment', 'slug' => 'campaigns.assign.store', 'module' => 'campaigns'],
            ['name' => 'View Assigned Influencers', 'slug' => 'campaigns.assigned-influencers', 'module' => 'campaigns'],

            // CAMPAIGN INFLUENCERS
            ['name' => 'View Campaign Influencers', 'slug' => 'campaigns.influencers.index', 'module' => 'campaigns'],
            ['name' => 'Create Campaign Influencer', 'slug' => 'campaigns.influencers.create', 'module' => 'campaigns'],
            ['name' => 'Store Campaign Influencer', 'slug' => 'campaigns.influencers.store', 'module' => 'campaigns'],
            ['name' => 'Approve Campaign Influencer', 'slug' => 'campaign-influencers.approve', 'module' => 'campaigns'],
            ['name' => 'Reject Campaign Influencer', 'slug' => 'campaign-influencers.reject', 'module' => 'campaigns'],
            ['name' => 'Cancel Campaign Influencer', 'slug' => 'campaign-influencers.cancel', 'module' => 'campaigns'],
            ['name' => 'Delete Campaign Influencer', 'slug' => 'campaign-influencers.destroy', 'module' => 'campaigns'],

            // CAMPAIGNS: REVIEWS
            ['name' => 'View Reviews', 'slug' => 'reviews.index', 'module' => 'reviews'],
            ['name' => 'View Review Details', 'slug' => 'reviews.show', 'module' => 'reviews'],
            ['name' => 'Toggle Review Visibility', 'slug' => 'reviews.toggle-visibility', 'module' => 'reviews'],

            // COMMERCE: PACKAGES
            ['name' => 'View Packages', 'slug' => 'packages.index', 'module' => 'packages'],
            ['name' => 'Create Package', 'slug' => 'packages.create', 'module' => 'packages'],
            ['name' => 'Store Package', 'slug' => 'packages.store', 'module' => 'packages'],
            ['name' => 'View Package Details', 'slug' => 'packages.view', 'module' => 'packages'],
            ['name' => 'Edit Package', 'slug' => 'packages.edit', 'module' => 'packages'],
            ['name' => 'Update Package', 'slug' => 'packages.update', 'module' => 'packages'],
            ['name' => 'Delete Package', 'slug' => 'packages.destroy', 'module' => 'packages'],
            ['name' => 'Toggle Package Status', 'slug' => 'packages.toggle-status', 'module' => 'packages'],
            ['name' => 'Purchase Package', 'slug' => 'packages.purchase', 'module' => 'packages'],
            ['name' => 'Store Package Purchase', 'slug' => 'packages.purchase.store', 'module' => 'packages'],

            // COMMERCE: ORDERS
            ['name' => 'View Orders', 'slug' => 'orders.index', 'module' => 'orders'],
            ['name' => 'View Order Details', 'slug' => 'orders.show', 'module' => 'orders'],
            ['name' => 'Update Order Status', 'slug' => 'orders.update-status', 'module' => 'orders'],
            ['name' => 'Create Order from Campaign', 'slug' => 'orders.create-from-campaign', 'module' => 'orders'],
            ['name' => 'Update Sub-Order Status', 'slug' => 'sub-orders.update-status', 'module' => 'orders'],
            ['name' => 'Mark Sub-Order Paid', 'slug' => 'sub-orders.mark-paid', 'module' => 'orders'],
            ['name' => 'Update Order Item Status', 'slug' => 'order-items.update-status', 'module' => 'orders'],
            ['name' => 'Mark Order Item Paid', 'slug' => 'order-items.mark-paid', 'module' => 'orders'],

            // COMMERCE: PAYMENTS
            ['name' => 'View Payments', 'slug' => 'payments.index', 'module' => 'payments'],
            ['name' => 'View Payment Details', 'slug' => 'payments.show', 'module' => 'payments'],
            ['name' => 'Refund Payment', 'slug' => 'payments.refund', 'module' => 'payments'],
            ['name' => 'Retry Payment', 'slug' => 'payments.retry', 'module' => 'payments'],

            // COMMERCE: PAYMENT QUEUE
            ['name' => 'View Payment Queue', 'slug' => 'payment-queue.index', 'module' => 'payments'],
            ['name' => 'Bulk Mark Payment Queue', 'slug' => 'payment-queue.bulk-mark', 'module' => 'payments'],

            // COMMERCE: PAYMENT AUDIT
            ['name' => 'View Payment Audit Logs', 'slug' => 'payment-audit.index', 'module' => 'payments'],
            ['name' => 'Undo Order Item Payment', 'slug' => 'payment-audit.undo-item', 'module' => 'payments'],
            ['name' => 'Undo Sub-Order Payment', 'slug' => 'payment-audit.undo-suborder', 'module' => 'payments'],

            // COMMERCE: PAYMENT STATEMENTS
            ['name' => 'View Payment Statements', 'slug' => 'payment-statement.index', 'module' => 'payments'],
            ['name' => 'View Payment Statement Details', 'slug' => 'payment-statement.show', 'module' => 'payments'],
            ['name' => 'Export Payment Statement PDF', 'slug' => 'payment-statement.pdf', 'module' => 'payments'],

            // ACCESS CONTROL: USERS
            ['name' => 'View Users', 'slug' => 'users.index', 'module' => 'users'],
            ['name' => 'Create User', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Store User', 'slug' => 'users.store', 'module' => 'users'],
            ['name' => 'Edit User', 'slug' => 'users.edit', 'module' => 'users'],
            ['name' => 'Update User', 'slug' => 'users.update', 'module' => 'users'],
            ['name' => 'Toggle User Status', 'slug' => 'users.toggle-status', 'module' => 'users'],
            ['name' => 'Assign User Roles', 'slug' => 'users.roles.assign', 'module' => 'users'],
            ['name' => 'Store User Role Assignment', 'slug' => 'users.roles.assign.store', 'module' => 'users'],
            ['name' => 'View User Roles', 'slug' => 'users.roles.get', 'module' => 'users'],

            // ACCESS CONTROL: ROLES & PERMISSIONS
            ['name' => 'View Roles', 'slug' => 'roles.index', 'module' => 'roles'],
            ['name' => 'Store Role', 'slug' => 'roles.store', 'module' => 'roles'],
            ['name' => 'View Role Data', 'slug' => 'roles.get-data', 'module' => 'roles'],
            ['name' => 'View Role Permissions', 'slug' => 'roles.permissions', 'module' => 'roles'],
            ['name' => 'Update Role', 'slug' => 'roles.update', 'module' => 'roles'],
            ['name' => 'Delete Role', 'slug' => 'roles.destroy', 'module' => 'roles'],
            ['name' => 'Toggle Role Status', 'slug' => 'roles.toggle-status', 'module' => 'roles'],
            ['name' => 'Assign Permissions', 'slug' => 'permissions.assign', 'module' => 'permissions'],
            ['name' => 'Store Permission Assignment', 'slug' => 'permissions.assign.store', 'module' => 'permissions'],

            // ACCESS CONTROL: MODERATORS
            ['name' => 'View Moderators', 'slug' => 'moderators.index', 'module' => 'moderators'],
            ['name' => 'Create Moderator', 'slug' => 'moderators.create', 'module' => 'moderators'],
            ['name' => 'Store Moderator', 'slug' => 'moderators.store', 'module' => 'moderators'],
            ['name' => 'View Moderator Details', 'slug' => 'moderators.show', 'module' => 'moderators'],
            ['name' => 'Edit Moderator', 'slug' => 'moderators.edit', 'module' => 'moderators'],
            ['name' => 'Update Moderator', 'slug' => 'moderators.update', 'module' => 'moderators'],
            ['name' => 'Delete Moderator', 'slug' => 'moderators.destroy', 'module' => 'moderators'],
            ['name' => 'Toggle Moderator Status', 'slug' => 'moderators.toggle-status', 'module' => 'moderators'],

            // COMMUNICATION: CONVERSATIONS
            ['name' => 'View Conversations', 'slug' => 'conversations.index', 'module' => 'conversations'],
            ['name' => 'View Conversation Details', 'slug' => 'conversations.show', 'module' => 'conversations'],
            ['name' => 'Store Conversation Message', 'slug' => 'conversations.storeMessage', 'module' => 'conversations'],
            ['name' => 'Assign Conversation Moderator', 'slug' => 'conversations.assign-moderator', 'module' => 'conversations'],

            // COMMUNICATION: SUPPORT TICKETS
            ['name' => 'View Support Tickets', 'slug' => 'support-tickets.index', 'module' => 'support'],
            ['name' => 'View Ticket Details', 'slug' => 'support-tickets.show', 'module' => 'support'],
            ['name' => 'Update Ticket Status', 'slug' => 'support-tickets.update', 'module' => 'support'],
            ['name' => 'Delete Ticket', 'slug' => 'support-tickets.destroy', 'module' => 'support'],
            ['name' => 'Bulk Update Tickets', 'slug' => 'support-tickets.bulk-update', 'module' => 'support'],

            // COMMUNICATION: NOTIFICATIONS
            ['name' => 'View Notifications', 'slug' => 'notifications.index', 'module' => 'notifications'],
            ['name' => 'View Unread Notifications', 'slug' => 'notifications.api.unread', 'module' => 'notifications'],
            ['name' => 'Mark Notification as Read', 'slug' => 'notifications.mark-as-read', 'module' => 'notifications'],
            ['name' => 'Mark Notification as Unread', 'slug' => 'notifications.mark-as-unread', 'module' => 'notifications'],
            ['name' => 'Mark All Notifications as Read', 'slug' => 'notifications.mark-all-as-read', 'module' => 'notifications'],
            ['name' => 'Delete Notification', 'slug' => 'notifications.destroy', 'module' => 'notifications'],
            ['name' => 'Clear All Notifications', 'slug' => 'notifications.clear-all', 'module' => 'notifications'],
            ['name' => 'View Notification Details', 'slug' => 'notifications.show', 'module' => 'notifications'],

            // PROFILE/ACCOUNT
            ['name' => 'View Account Profile', 'slug' => 'account.edit', 'module' => 'account'],
            ['name' => 'Update Account Details', 'slug' => 'account.details.update', 'module' => 'account'],
            ['name' => 'Update Account Billing', 'slug' => 'account.billing.update', 'module' => 'account'],
            ['name' => 'Update Account Password', 'slug' => 'account.password.update', 'module' => 'account'],
            ['name' => 'Toggle Account Status', 'slug' => 'account.toggle-status', 'module' => 'account'],
            ['name' => 'Delete Account', 'slug' => 'account.destroy', 'module' => 'account'],
            ['name' => 'View Own Profile', 'slug' => 'profile.edit', 'module' => 'account'],
            ['name' => 'Update Own Profile', 'slug' => 'profile.update', 'module' => 'account'],
        ];
    }
}
