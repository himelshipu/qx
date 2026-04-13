<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class MenuHelper
{
    public static function getMainNavItems()
    {
        return [
            'dashboard'     => [
                'icon'  => 'dashboard',
                'name'  => 'Dashboard',
                'route' => '/dashboard'
            ],

            'management'    => [
                'type'  => 'group',
                'name'  => 'MANAGEMENT',
                'items' => [
                    [
                        'icon'     => 'categories',
                        'name'     => 'Categories',
                        'subItems' => [
                            ['name' => 'All Categories', 'route' => 'categories.index', 'icon' => 'categories'],
                            ['name' => 'Create Category', 'route' => 'categories.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'brands',
                        'name'     => 'Brands',
                        'subItems' => [
                            ['name' => 'All Brands', 'route' => 'brands.index', 'icon' => 'brands'],
                            ['name' => 'Create Brand', 'route' => 'brands.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'influencers',
                        'name'     => 'Influencers',
                        'subItems' => [
                            ['name' => 'All Influencers', 'route' => 'influencers.index', 'icon' => 'influencers'],
                            ['name' => 'Create Influencer', 'route' => 'influencers.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'case-studies',
                        'name'     => 'Case Studies',
                        'subItems' => [
                            ['name' => 'All Case Studies', 'route' => 'case-studies.index', 'icon' => 'case-studies'],
                            ['name' => 'Create Case Study', 'route' => 'case-studies.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'testimonials',
                        'name'     => 'Testimonials',
                        'subItems' => [
                            ['name' => 'All Testimonials', 'route' => 'testimonials.index', 'icon' => 'testimonials'],
                            ['name' => 'Create Testimonial', 'route' => 'testimonials.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'collaborations',
                        'name'     => 'Collaborations',
                        'subItems' => [
                            ['name' => 'All Collaborations', 'route' => 'featured-collaborations.index', 'icon' => 'collaborations'],
                            ['name' => 'Add Collaboration', 'route' => 'featured-collaborations.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'faqs',
                        'name'     => 'FAQs',
                        'subItems' => [
                            ['name' => 'FAQ Sections', 'route' => 'faqs.sections.index', 'icon' => 'faqs'],
                            ['name' => 'Create Section', 'route' => 'faqs.sections.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'knowledge-base',
                        'name'     => 'Knowledge Base',
                        'subItems' => [
                            ['name' => 'All Articles', 'route' => 'knowledge-base.index', 'icon' => 'knowledge-base'],
                            ['name' => 'Create Article', 'route' => 'knowledge-base.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'     => 'pages',
                        'name'     => 'Static Pages',
                        'subItems' => [
                            ['name' => 'All Pages', 'route' => 'static-pages.index', 'icon' => 'pages'],
                            ['name' => 'Create Page', 'route' => 'static-pages.create', 'icon' => 'campaign-new'],
                            ['name' => 'Footer Settings', 'route' => 'settings.index', 'icon' => 'settings']
                        ]
                    ]
                ]
            ],

            'campaigns'     => [
                'type'  => 'group',
                'name'  => 'CAMPAIGNS',
                'items' => [
                    [
                        'icon'  => 'campaign',
                        'name'  => 'All Campaigns',
                        'route' => 'campaigns.standard'
                    ],
                    [
                        'icon'  => 'campaign-new',
                        'name'  => 'New Campaign',
                        'route' => 'campaigns.standard.create'
                    ],
                    [
                        'icon'  => 'reviews',
                        'name'  => 'Reviews',
                        'route' => 'reviews.index',
                        'count' => true
                    ]
                ]
            ],

            'quick_actions' => [
                'type'  => 'group',
                'name'  => 'QUICK ACTIONS',
                'items' => [
                    [
                        'icon'  => 'user-add',
                        'name'  => 'Assign Campaign',
                        'route' => 'campaigns.assign'
                    ],
                    [
                        'icon'  => 'packages',
                        'name'  => 'Purchase Package',
                        'route' => 'packages.purchase'
                    ]
                ]
            ],

            'commerce'      => [
                'type'  => 'group',
                'name'  => 'COMMERCE',
                'items' => [
                    [
                        'icon'     => 'packages',
                        'name'     => 'Packages',
                        'subItems' => [
                            ['name' => 'All Packages', 'route' => 'packages.index', 'icon' => 'packages'],
                            ['name' => 'Create Package', 'route' => 'packages.create', 'icon' => 'campaign-new']
                        ]
                    ],
                    [
                        'icon'  => 'orders',
                        'name'  => 'Orders',
                        'route' => 'orders.index'
                    ],
                    [
                        'icon'     => 'payments',
                        'name'     => 'Payments',
                        'subItems' => [
                            ['name' => 'All Payments', 'route' => 'payments.index', 'icon' => 'payments'],
                            ['name' => 'Payment Queue', 'route' => 'payment-queue.index', 'icon' => 'packages'],
                            ['name' => 'Payment Audit Log', 'route' => 'payment-audit.index', 'icon' => 'history'],
                            ['name' => 'Payment Statements', 'route' => 'payment-statement.index', 'icon' => 'document']
                        ]
                    ],
                    [
                        'icon'  => 'payouts',
                        'name'  => 'Payouts',
                        'route' => 'payouts.index'
                    ]
                ]
            ],

            'access'        => [
                'type'  => 'group',
                'name'  => 'ACCESS CONTROL',
                'items' => [
                    [
                        'icon'  => 'group',
                        'name'  => 'Users',
                        'route' => 'users.index'
                    ],
                    [
                        'icon'  => 'assign-roles',
                        'name'  => 'Assign User Roles',
                        'route' => 'users.roles.assign'
                    ],
                    [
                        'icon'  => 'roles',
                        'name'  => 'Roles',
                        'route' => 'roles.index'
                    ],
                    [
                        'icon'  => 'permissions',
                        'name'  => 'Assign Permissions',
                        'route' => 'permissions.assign'
                    ]
                ]
            ],

            'communication' => [
                'type'  => 'group',
                'name'  => 'COMMUNICATION',
                'items' => [
                    [
                        'icon'  => 'chat',
                        'name'  => 'Conversations',
                        'route' => 'conversations.index',
                        'count' => true
                    ],
                    [
                        'icon'  => 'support',
                        'name'  => 'Support Tickets',
                        'route' => 'support-tickets.index',
                        'count' => true
                    ],
                    [
                        'icon'  => 'notifications',
                        'name'  => 'Notifications',
                        'route' => 'notifications.index',
                        'count' => true
                    ]
                ]
            ],

            'profile'       => [
                'icon'  => 'profile',
                'name'  => 'Profile',
                'route' => '/profile'
            ]
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    public static function buildSidebarMenu(string $currentRoute): array
    {
        $user            = auth()->user();
        $menuItems       = self::getMainNavItems();
        $preparedItems   = [];
        $activeAccordion = null;

        foreach ($menuItems as $key => $item) {
            if ($key === 'dashboard') {
                // Dashboard always visible to authenticated users who passed middleware
                [$routeName, $url]   = self::resolveRouteMeta($item['route'] ?? null, false);
                $preparedItems[$key] = [
                     ...$item,
                    'route_name' => $routeName,
                    'url'        => $url,
                    'active'     => self::isRouteMatch($currentRoute, $routeName)
                ];

                continue;
            }

            if (($item['type'] ?? null) === 'group') {
                $groupItems  = [];
                $groupActive = false;

                foreach ($item['items'] as $index => $subItem) {
                    $menuId      = $key . '_' . $index;
                    $hasSubItems = isset($subItem['subItems']);
                    
                    // Check if user has permission to view this menu item
                    $permission = self::getPermissionForMenuItem($key, $subItem);
                    if ($permission && !$user->hasPermission($permission)) {
                        continue; // Skip this menu item if user doesn't have permission
                    }

                    if ($hasSubItems) {
                        $nestedItems   = [];
                        $subItemActive = false;

                        foreach ($subItem['subItems'] as $nestedItem) {
                            [$routeName, $url] = self::resolveRouteMeta($nestedItem['route'] ?? null, true);
                            $isActive          = self::isRouteMatch($currentRoute, $routeName);
                            $subItemActive     = $subItemActive || $isActive;

                            // Check if user has permission for nested item
                            $nestedPermission = self::getPermissionForMenuItem($key, $nestedItem);
                            if ($nestedPermission && !$user->hasPermission($nestedPermission)) {
                                continue; // Skip if no permission
                            }

                            $nestedItems[] = [
                                 ...$nestedItem,
                                'route_name' => $routeName,
                                'url'        => $url,
                                'active'     => $isActive
                            ];
                        }

                        // Skip if no nested items left after filtering
                        if (empty($nestedItems)) {
                            continue;
                        }

                        if ($subItemActive && $activeAccordion === null) {
                            $activeAccordion = $menuId;
                        }

                        $groupItems[] = [
                             ...$subItem,
                            'menu_id'       => $menuId,
                            'has_sub_items' => true,
                            'sub_items'     => $nestedItems,
                            'default_url'   => $nestedItems[0]['url'] ?? '#',
                            'active'        => $subItemActive
                        ];

                        $groupActive = $groupActive || $subItemActive;

                        continue;
                    }

                    [$routeName, $url] = self::resolveRouteMeta($subItem['route'] ?? null, true);
                    $isActive          = self::isRouteMatch($currentRoute, $routeName);
                    $groupActive       = $groupActive || $isActive;

                    $groupItems[] = [
                         ...$subItem,
                        'has_sub_items' => false,
                        'route_name'    => $routeName,
                        'url'           => $url,
                        'active'        => $isActive
                    ];
                }

                // Only add group if it has items
                if (!empty($groupItems)) {
                    $preparedItems[$key] = [
                         ...$item,
                        'items'  => $groupItems,
                        'active' => $groupActive
                    ];
                }

                continue;
            }

            if ($key === 'profile') {
                [$routeName, $url]   = self::resolveRouteMeta($item['route'] ?? null, false);
                $preparedItems[$key] = [
                     ...$item,
                    'route_name' => $routeName,
                    'url'        => $url,
                    'active'     => self::isRouteMatch($currentRoute, $routeName) || request()->is('profile*')
                ];

                continue;
            }

            $preparedItems[$key] = $item;
        }

        return [
            'items'           => $preparedItems,
            'activeAccordion' => $activeAccordion
        ];
    }

    private static function resolveRouteMeta(?string $route, bool $dashboardPrefix = true): array
    {
        if (!$route) {
            return [null, '#'];
        }

        if ($route === '/dashboard') {
            return [
                Route::has('dashboard.index') ? 'dashboard.index' : null,
                Route::has('dashboard.index') ? route('dashboard.index') : '/dashboard'
            ];
        }

        if (str_starts_with($route, '/')) {
            return [null, url($route)];
        }

        $routeName = $dashboardPrefix && !str_starts_with($route, 'dashboard.') ? 'dashboard.' . $route : $route;

        return [
            $routeName,
            Route::has($routeName) ? route($routeName) : '#'
        ];
    }

    private static function isRouteMatch(string $currentRoute, ?string $routeName): bool
    {
        return $routeName !== null && $currentRoute === $routeName;
    }

    /**
     * Map menu items to their required permissions.
     * Returns null if no specific permission is required (always visible).
     */
    private static function getPermissionForMenuItem(string $groupKey, array $item): ?string
    {
        // Extract route name from 'route' field
        $route = $item['route'] ?? null;
        if (!$route) {
            return null;
        }

        // Map special routes to permissions
        $specialMappings = [
            'dashboard.index' => 'dashboard.view',
            '/dashboard' => 'dashboard.view',
            'campaigns.standard' => 'campaigns.index',
            'campaigns.standard.create' => 'campaigns.create',
            'packages.purchase' => 'packages.purchase',
            'payment-queue.index' => 'payment-queue.index',
            'payment-audit.index' => 'payment-audit.index',
            'payment-statement.index' => 'payment-statement.index',
            'notifications.index' => 'notifications.index',
            'settings' => 'settings.index',
            'reviews.index' => 'reviews.index',
            'case-studies.index' => 'case-studies.index',
            'testimonials.index' => 'testimonials.index',
            'faqs.sections.index' => 'faqs.sections.index',
            'knowledge-base.index' => 'knowledge-base.index',
            'featured-collaborations.index' => 'featured-collaborations.index',
            'static-pages.index' => 'static-pages.index',
            'settings.index' => 'settings.index',
        ];

        if (isset($specialMappings[$route])) {
            return $specialMappings[$route];
        }

        // Standard pattern: resource.action -> resource.action permission
        if (strpos($route, '.') !== false) {
            if (str_starts_with($route, 'dashboard.')) {
                return $route;
            }
            
            // For routes like 'users.index', check 'users.index' permission
            return $route;
        }

        return null;
    }

    public static function getIconSvg($iconName)
    {
        if (view()->exists('components.icons.' . $iconName)) {
            return Blade::render('<x-icons.' . $iconName . ' class="w-5 h-5" />');
        }

        $icons = [

            'dashboard'       => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">  <path d="M3 13h8V3H3v10zm10 8h8v-8h-8v8zm0-18v6h8V3h-8zM3 21h8v-6H3v6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',

            'collaborations'  => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M16 11c1.657 0 3-1.567 3-3.5S17.657 4 16 4s-3 1.567-3 3.5S14.343 11 16 11zM8 11c1.657 0 3-1.567 3-3.5S9.657 4 8 4 5 5.567 5 7.5 6.343 11 8 11z" stroke="currentColor" stroke-width="1.5"/> <path d="M2 20v-1c0-2.761 2.686-5 6-5s6 2.239 6 5v1M14 20v-1c0-1.657-.672-3.156-1.757-4.243C13.12 13.988 14.48 13 16 13c3.314 0 6 2.239 6 5v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/> </svg>',

            'campaigns'       => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M3 11l18-8v18L3 13v-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>  <path d="M11 13v6a2 2 0 002 2h1"    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>   </svg>',
            'campaign'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M3 11l18-8v18L3 13v-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>  <path d="M11 13v6a2 2 0 002 2h1"    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>   </svg>',

            'categories'      => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

            'brands'          => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'influencers'     => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'moderators'      => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'case-studies'    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m7-3H8a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'testimonials'    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01M3 21h18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'faqs'            => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'knowledge-base'  => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6a2 2 0 012-2h5a3 3 0 013 3v11a3 3 0 00-3-3H6a2 2 0 01-2-2V6zm16 0a2 2 0 00-2-2h-5a3 3 0 00-3 3v11a3 3 0 013-3h5a2 2 0 002-2V6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'pages'           => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 4H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 9h6M9 15h3M17 3v6h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'campaign-new'    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4v16m8-8H4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'user-add'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M20 8v6M23 11h-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'packages'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 7l9 4 9-4M3 7l9-4 9 4M3 7v10l9 4 9-4V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'orders'          => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18 17l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'payments'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2.5" y="5" width="19" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 10h19" stroke="currentColor" stroke-width="1.5"/><path d="M7 15h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'payouts'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4v16M5 11l7-7 7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="4" y="18" width="16" height="2" rx="1" fill="currentColor"/></svg>',

            'support'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 9a3 3 0 116 0c0 2-3 2-3 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/></svg>',

            'reviews'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" stroke="currentColor" stroke-width="1.5"/></svg>',

            'roles'           => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'permissions'     => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM4 19v-2a5 5 0 0110 0v2M16 15.5l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'assign'          => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="1.5"/></svg>',

            'chat'            => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'group'           => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="1.5"/></svg>',

            'notifications'   => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'profile'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke="currentColor" stroke-width="1.5"/></svg>'
        ];

        return $icons[$iconName] ?? '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>';
    }
}
