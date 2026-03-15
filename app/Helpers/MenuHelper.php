<?php

namespace App\Helpers;

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
                            ['name' => 'All Categories', 'route' => 'categories.index'],
                            ['name' => 'Create Category', 'route' => 'categories.create']
                        ]
                    ],
                    [
                        'icon'     => 'brands',
                        'name'     => 'Brands',
                        'subItems' => [
                            ['name' => 'All Brands', 'route' => 'brands.index'],
                            ['name' => 'Create Brand', 'route' => 'brands.create']
                        ]
                    ],
                    [
                        'icon'     => 'creators',
                        'name'     => 'Creators',
                        'subItems' => [
                            ['name' => 'All Creators', 'route' => 'creators.index'],
                            ['name' => 'Create Creator', 'route' => 'creators.create']
                        ]
                    ],
                    [
                        'icon'     => 'moderators',
                        'name'     => 'Moderators',
                        'subItems' => [
                            ['name' => 'All Moderators', 'route' => 'moderators.index'],
                            ['name' => 'Create Moderator', 'route' => 'moderators.create']
                        ]
                    ]
                ]
            ],

            'campaigns'     => [
                'type'  => 'group',
                'name'  => 'CAMPAIGNS',
                'items' => [
                    [
                        'icon'  => 'campaigns',
                        'name'  => 'All Campaigns',
                        'route' => 'campaigns.index'
                    ],
                    [
                        'icon'  => 'campaign-new',
                        'name'  => 'New Campaign',
                        'route' => 'campaigns.create'
                    ],
                    [
                        'icon'  => 'content-library',
                        'name'  => 'Content Library',
                        'route' => 'content-library'
                    ],
                    [
                        'icon'  => 'reviews',
                        'name'  => 'Reviews',
                        'route' => 'reviews.index',
                        'count' => true
                    ]
                ]
            ],

            'commerce'      => [
                'type'  => 'group',
                'name'  => 'COMMERCE',
                'items' => [
                    [
                        'icon'  => 'packages',
                        'name'  => 'Packages',
                        'route' => 'packages.index'
                    ],
                    [
                        'icon'  => 'orders',
                        'name'  => 'Orders',
                        'route' => 'orders.index'
                    ],
                    [
                        'icon'  => 'payments',
                        'name'  => 'Payments',
                        'route' => 'payments.index'
                    ],
                    [
                        'icon'  => 'payouts',
                        'name'  => 'Payouts',
                        'route' => 'payouts.index'
                    ],
                    [
                        'icon'  => 'wishlist',
                        'name'  => 'Wishlists',
                        'route' => 'wishlists.index'
                    ]
                ]
            ],

            'access'        => [
                'type'  => 'group',
                'name'  => 'ACCESS CONTROL',
                'items' => [
                    [
                        'icon'  => 'roles',
                        'name'  => 'Roles',
                        'route' => 'roles.index'
                    ],
                    [
                        'icon'  => 'permissions',
                        'name'  => 'Permissions',
                        'route' => 'permissions.index'
                    ],
                    [
                        'icon'  => 'assign',
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

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard'       => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 13h6a1 1 0 001-1V4a1 1 0 00-1-1H4a1 1 0 00-1 1v8a1 1 0 001 1zm0 8h6a1 1 0 001-1v-4a1 1 0 00-1-1H4a1 1 0 00-1 1v4a1 1 0 001 1zm10 0h6a1 1 0 001-1v-8a1 1 0 00-1-1h-6a1 1 0 00-1 1v8a1 1 0 001 1zm0-18v4a1 1 0 001 1h6a1 1 0 001-1V4a1 1 0 00-1-1h-6a1 1 0 00-1 1z" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>',

            'categories'      => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

            'brands'          => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'creators'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'moderators'      => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'campaigns'       => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 3.055A9.001 9.001 0 0120.945 13H11V3.055zM3 13h8v8.945A9.001 9.001 0 013 13zm10-8.945V13h8.945A9.001 9.001 0 0013 4.055zM5 19l4-4m0 4L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'campaign-new'    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4v16m8-8H4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'content-library' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 5a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" stroke="currentColor" stroke-width="1.5"/><path d="M14 3v5a1 1 0 001 1h5" stroke="currentColor" stroke-width="1.5"/></svg>',

            'packages'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 7l9 4 9-4M3 7l9-4 9 4M3 7v10l9 4 9-4V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'orders'          => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18 17l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'payments'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2.5" y="5" width="19" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 10h19" stroke="currentColor" stroke-width="1.5"/><path d="M7 15h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'payouts'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4v16M5 11l7-7 7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="4" y="18" width="16" height="2" rx="1" fill="currentColor"/></svg>',

            'wishlist'        => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s-7-4.35-9.5-8A5.5 5.5 0 1112 6.5 5.5 5.5 0 0121.5 13C19 16.65 12 21 12 21z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'support'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 9a3 3 0 116 0c0 2-3 2-3 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/></svg>',

            'reviews'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" stroke="currentColor" stroke-width="1.5"/></svg>',

            'roles'           => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'permissions'     => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM4 19v-2a5 5 0 0110 0v2M16 15.5l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'assign'          => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="1.5"/></svg>',

            'chat'            => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'notifications'   => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'profile'         => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke="currentColor" stroke-width="1.5"/></svg>'
        ];

        return $icons[$iconName] ?? '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>';
    }
}
