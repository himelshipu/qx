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

            // ANALYTICS
            [
                'label' => 'Analytics',
                'icon' => 'analytics',
                'route' => 'dashboard.analytics',
                'permission' => 'analytics.view',
                'module' => 'dashboard',
                'order' => 1,
            ],

            // USERS & ROLES
            [
                'label' => 'Access Control',
                'icon' => 'security',
                'permission' => 'users.index',
                'module' => 'users',
                'order' => 2,
                'parent_id' => null,
                'children' => [
                    [
                        'label' => 'Users',
                        'icon' => 'people',
                        'route' => 'dashboard.users.index',
                        'permission' => 'users.index',
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
                        'label' => 'Permissions',
                        'icon' => 'vpn_key',
                        'route' => 'dashboard.permissions.index',
                        'permission' => 'permissions.index',
                        'module' => 'permissions',
                    ],
                ],
            ],

            // PLATFORM CONTENT
            [
                'label' => 'Platform Content',
                'icon' => 'folder',
                'permission' => 'categories.index',
                'module' => 'content',
                'order' => 3,
                'parent_id' => null,
                'children' => [
                    [
                        'label' => 'Categories',
                        'icon' => 'category',
                        'route' => 'dashboard.categories.index',
                        'permission' => 'categories.index',
                        'module' => 'categories',
                    ],
                    [
                        'label' => 'Pages',
                        'icon' => 'description',
                        'route' => 'dashboard.pages.index',
                        'permission' => 'pages.index',
                        'module' => 'content',
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
                ],
            ],

            // BRANDS & INFLUENCERS
            [
                'label' => 'Users Management',
                'icon' => 'group',
                'permission' => 'brands.index',
                'module' => 'admin',
                'order' => 4,
                'parent_id' => null,
                'children' => [
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
                        'label' => 'Portfolios',
                        'icon' => 'gallery',
                        'route' => 'dashboard.portfolios.index',
                        'permission' => 'portfolios.index',
                        'module' => 'portfolios',
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

            // CAMPAIGNS & PACKAGES
            [
                'label' => 'Campaigns',
                'icon' => 'campaign',
                'permission' => 'campaigns.index',
                'module' => 'campaigns',
                'order' => 5,
                'parent_id' => null,
                'children' => [
                    [
                        'label' => 'Campaigns',
                        'icon' => 'flag',
                        'route' => 'dashboard.campaigns.index',
                        'permission' => 'campaigns.index',
                        'module' => 'campaigns',
                    ],
                    [
                        'label' => 'Packages',
                        'icon' => 'card_giftcard',
                        'route' => 'dashboard.packages.index',
                        'permission' => 'packages.index',
                        'module' => 'packages',
                    ],
                ],
            ],

            // ORDERS & REVENUE
            [
                'label' => 'Orders & Revenue',
                'icon' => 'shopping_cart',
                'permission' => 'orders.index',
                'module' => 'orders',
                'order' => 6,
                'parent_id' => null,
                'children' => [
                    [
                        'label' => 'Orders',
                        'icon' => 'receipt_long',
                        'route' => 'dashboard.orders.index',
                        'permission' => 'orders.index',
                        'module' => 'orders',
                    ],
                    [
                        'label' => 'Payments',
                        'icon' => 'payment',
                        'route' => 'dashboard.payments.index',
                        'permission' => 'payments.index',
                        'module' => 'payments',
                    ],
                    [
                        'label' => 'Payouts',
                        'icon' => 'account_balance_wallet',
                        'route' => 'dashboard.payouts.index',
                        'permission' => 'payouts.index',
                        'module' => 'payouts',
                    ],
                    [
                        'label' => 'Invoices',
                        'icon' => 'invoice',
                        'route' => 'dashboard.invoices.index',
                        'permission' => 'invoices.index',
                        'module' => 'billing',
                    ],
                    [
                        'label' => 'Reports',
                        'icon' => 'bar_chart',
                        'route' => 'dashboard.reports.index',
                        'permission' => 'reports.view',
                        'module' => 'reports',
                    ],
                ],
            ],

            // FEEDBACK & SUPPORT
            [
                'label' => 'Support',
                'icon' => 'support_agent',
                'permission' => 'support-tickets.index',
                'module' => 'support',
                'order' => 7,
                'parent_id' => null,
                'children' => [
                    [
                        'label' => 'Support Tickets',
                        'icon' => 'mail_outline',
                        'route' => 'dashboard.support-tickets.index',
                        'permission' => 'support-tickets.index',
                        'module' => 'support',
                    ],
                    [
                        'label' => 'Conversations',
                        'icon' => 'chat',
                        'route' => 'dashboard.conversations.index',
                        'permission' => 'conversations.index',
                        'module' => 'conversations',
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

            // MODERATION
            [
                'label' => 'Moderation',
                'icon' => 'gavel',
                'permission' => 'moderation.queue',
                'module' => 'moderation',
                'order' => 8,
                'parent_id' => null,
                'children' => [
                    [
                        'label' => 'Moderation Queue',
                        'icon' => 'inbox',
                        'route' => 'dashboard.moderation.queue',
                        'permission' => 'moderation.queue',
                        'module' => 'moderation',
                    ],
                    [
                        'label' => 'Verification',
                        'icon' => 'verified',
                        'route' => 'dashboard.verification.index',
                        'permission' => 'verification.index',
                        'module' => 'verification',
                    ],
                ],
            ],

            // SETTINGS
            [
                'label' => 'Settings',
                'icon' => 'settings',
                'permission' => 'settings.view',
                'module' => 'settings',
                'order' => 9,
                'parent_id' => null,
                'children' => [
                    [
                        'label' => 'System Settings',
                        'icon' => 'tune',
                        'route' => 'dashboard.settings.edit',
                        'permission' => 'settings.edit',
                        'module' => 'settings',
                    ],
                    [
                        'label' => 'Activity Logs',
                        'icon' => 'history',
                        'route' => 'dashboard.system.activity-logs',
                        'permission' => 'system.activity-logs',
                        'module' => 'settings',
                    ],
                ],
            ],
        ];
    }
}
