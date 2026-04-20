<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminMenuSeeder extends Seeder
{
    /**
     * Seed the admin menus table.
     */
    public function run(): void
    {
        DB::table('admin_menus')->truncate();

        $menus = $this->getMenuStructure();

        // Process menus and handle parent-child relationships
        $this->insertMenus($menus, null);
    }

    /**
     * Recursively insert menus with parent-child relationships.
     */
    private function insertMenus(array $menus, ?int $parentId = null): void
    {
        foreach ($menus as $index => $menu) {
            $children = $menu['children'] ?? [];
            unset($menu['children']);

            // Insert parent menu
            $id = DB::table('admin_menus')->insertGetId([
                'label' => $menu['label'],
                'icon' => $menu['icon'] ?? null,
                'route' => $menu['route'] ?? null,
                'permission' => $menu['permission'] ?? null,
                'module' => $menu['module'] ?? null,
                'parent_id' => $parentId,
                'order' => $menu['order'] ?? $index,
                'is_active' => $menu['is_active'] ?? true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Recursively insert children if they exist
            if (!empty($children)) {
                $this->insertMenus($children, $id);
            }
        }
    }

    /**
     * Get the menu structure.
     *
     * @return array
     */
    private function getMenuStructure(): array
    {
        return [
            // DASHBOARD
            [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'route' => 'dashboard.index',
                'permission' => 'dashboard.view',
                'module' => 'dashboard',
                'order' => 0,
            ],

            // MANAGEMENT
            [
                'label' => 'Management',
                'icon' => 'folder',
                'permission' => 'categories.index',
                'module' => 'content',
                'order' => 1,
                'children' => [
                    [
                        'label' => 'Categories',
                        'icon' => 'category',
                        'route' => 'dashboard.categories.index',
                        'permission' => 'categories.index',
                        'module' => 'categories',
                    ],
                    [
                        'label' => 'Brands',
                        'icon' => 'business',
                        'route' => 'dashboard.brands.index',
                        'permission' => 'brands.index',
                        'module' => 'brands',
                    ],
                    [
                        'label' => 'Influencers',
                        'icon' => 'star_rate',
                        'route' => 'dashboard.influencers.index',
                        'permission' => 'influencers.index',
                        'module' => 'influencers',
                    ],
                    [
                        'label' => 'Case Studies',
                        'icon' => 'assignment',
                        'route' => 'dashboard.case-studies.index',
                        'permission' => 'case-studies.index',
                        'module' => 'content',
                    ],
                    [
                        'label' => 'Testimonials',
                        'icon' => 'rate_review',
                        'route' => 'dashboard.testimonials.index',
                        'permission' => 'testimonials.index',
                        'module' => 'content',
                    ],
                    [
                        'label' => 'FAQ',
                        'icon' => 'help',
                        'route' => 'dashboard.faqs.sections.index',
                        'permission' => 'faqs.sections.index',
                        'module' => 'content',
                    ],
                    [
                        'label' => 'Knowledge Base',
                        'icon' => 'library_books',
                        'route' => 'dashboard.knowledge-base.index',
                        'permission' => 'knowledge-base.index',
                        'module' => 'content',
                    ],
                    [
                        'label' => 'Blog',
                        'icon' => 'article',
                        'route' => 'dashboard.blogs.index',
                        'permission' => 'blogs.index',
                        'module' => 'content',
                    ],
                    [
                        'label' => 'Collaborations',
                        'icon' => 'groups',
                        'route' => 'dashboard.featured-collaborations.index',
                        'permission' => 'featured-collaborations.index',
                        'module' => 'content',
                    ],
                    [
                        'label' => 'Static Pages',
                        'icon' => 'description',
                        'route' => 'dashboard.static-pages.index',
                        'permission' => 'static-pages.index',
                        'module' => 'content',
                    ],
                ],
            ],

            // SETTINGS
            [
                'label' => 'Settings',
                'icon' => 'tune',
                'permission' => 'settings.index',
                'module' => 'settings',
                'order' => 7,
                'children' => [
                    [
                        'label' => 'Static Pages',
                        'icon' => 'description',
                        'route' => 'dashboard.static-pages.index',
                        'permission' => 'static-pages.index',
                        'module' => 'settings',
                    ],
                    [
                        'label' => 'Create Static Page',
                        'icon' => 'add',
                        'route' => 'dashboard.static-pages.create',
                        'permission' => 'static-pages.create',
                        'module' => 'settings',
                    ],
                    [
                        'label' => 'Site Settings',
                        'icon' => 'settings',
                        'route' => 'dashboard.settings.index',
                        'permission' => 'settings.index',
                        'module' => 'settings',
                    ],
                ],
            ],

            // CAMPAIGNS
            [
                'label' => 'Campaigns',
                'icon' => 'campaign',
                'permission' => 'campaigns.index',
                'module' => 'campaigns',
                'order' => 2,
                'children' => [
                    [
                        'label' => 'All Campaigns',
                        'icon' => 'flag',
                        'route' => 'dashboard.campaigns.standard',
                        'permission' => 'campaigns.index',
                        'module' => 'campaigns',
                    ],
                    [
                        'label' => 'New Campaign',
                        'icon' => 'add',
                        'route' => 'dashboard.campaigns.standard.create',
                        'permission' => 'campaigns.create',
                        'module' => 'campaigns',
                    ],
                    [
                        'label' => 'Assign Campaign',
                        'icon' => 'person_add',
                        'route' => 'dashboard.campaigns.assign',
                        'permission' => 'campaigns.assign',
                        'module' => 'campaigns',
                    ],
                    [
                        'label' => 'Reviews',
                        'icon' => 'star',
                        'route' => 'dashboard.reviews.index',
                        'permission' => 'reviews.index',
                        'module' => 'reviews',
                    ],
                ],
            ],

            // COMMERCE
            [
                'label' => 'Commerce',
                'icon' => 'shopping_cart',
                'permission' => 'packages.index',
                'module' => 'orders',
                'order' => 3,
                'children' => [
                    [
                        'label' => 'Packages',
                        'icon' => 'inventory_2',
                        'route' => 'dashboard.packages.index',
                        'permission' => 'packages.index',
                        'module' => 'packages',
                    ],
                    [
                        'label' => 'Purchase Package',
                        'icon' => 'point_of_sale',
                        'route' => 'dashboard.packages.purchase',
                        'permission' => 'packages.purchase',
                        'module' => 'packages',
                    ],
                    [
                        'label' => 'Orders',
                        'icon' => 'receipt_long',
                        'route' => 'dashboard.orders.index',
                        'permission' => 'orders.index',
                        'module' => 'orders',
                    ],
                    [
                        'label' => 'Payouts',
                        'icon' => 'payment',
                        'route' => 'dashboard.payments.index',
                        'permission' => 'payments.index',
                        'module' => 'payments',
                    ],
                    [
                        'label' => 'Payment Queue',
                        'icon' => 'pending_actions',
                        'route' => 'dashboard.payment-queue.index',
                        'permission' => 'payment-queue.index',
                        'module' => 'payments',
                    ],
                    [
                        'label' => 'Payment Audit',
                        'icon' => 'history',
                        'route' => 'dashboard.payment-audit.index',
                        'permission' => 'payment-audit.index',
                        'module' => 'payments',
                    ],
                    [
                        'label' => 'Payment Statements',
                        'icon' => 'description',
                        'route' => 'dashboard.payment-statement.index',
                        'permission' => 'payment-statement.index',
                        'module' => 'payments',
                    ],
                ],
            ],

            // ACCESS CONTROL
            [
                'label' => 'Access Control',
                'icon' => 'security',
                'permission' => 'users.index',
                'module' => 'users',
                'order' => 4,
                'children' => [
                    [
                        'label' => 'Users',
                        'icon' => 'people',
                        'route' => 'dashboard.users.index',
                        'permission' => 'users.index',
                        'module' => 'users',
                    ],
                    [
                        'label' => 'Assign User Roles',
                        'icon' => 'manage_accounts',
                        'route' => 'dashboard.users.roles.assign',
                        'permission' => 'users.roles.assign',
                        'module' => 'users',
                    ],
                    [
                        'label' => 'Roles',
                        'icon' => 'admin_panel_settings',
                        'route' => 'dashboard.roles.index',
                        'permission' => 'roles.index',
                        'module' => 'roles',
                    ],
                    [
                        'label' => 'Assign Permissions',
                        'icon' => 'vpn_key',
                        'route' => 'dashboard.permissions.assign',
                        'permission' => 'permissions.assign',
                        'module' => 'permissions',
                    ],
                    [
                        'label' => 'Moderators',
                        'icon' => 'verified_user',
                        'route' => 'dashboard.moderators.index',
                        'permission' => 'moderators.index',
                        'module' => 'moderators',
                    ],
                ],
            ],

            // COMMUNICATION
            [
                'label' => 'Communication',
                'icon' => 'chat',
                'permission' => 'conversations.index',
                'module' => 'conversations',
                'order' => 5,
                'children' => [
                    [
                        'label' => 'Conversations',
                        'icon' => 'forum',
                        'route' => 'dashboard.conversations.index',
                        'permission' => 'conversations.index',
                        'module' => 'conversations',
                    ],
                    [
                        'label' => 'Support Tickets',
                        'icon' => 'support_agent',
                        'route' => 'dashboard.support-tickets.index',
                        'permission' => 'support-tickets.index',
                        'module' => 'support',
                    ],
                    [
                        'label' => 'Notifications',
                        'icon' => 'notifications',
                        'route' => 'dashboard.notifications.index',
                        'permission' => 'notifications.index',
                        'module' => 'notifications',
                    ],
                ],
            ],
        ];
    }
}
