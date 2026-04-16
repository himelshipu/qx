<?php

/**
 * Settings Configuration
 *
 * This config defines all settings system constants, validation rules, and limits.
 * Referenced by FormRequest, Repository, and Service layers.
 */

return [
    // Branding Settings
    'branding' => [
        'site_name' => [
            'max' => 255,
            'label' => 'Site Name',
        ],
        'tagline' => [
            'max' => 255,
            'label' => 'Tagline',
        ],
        'logo_light' => [
            'max' => 6144, // 6MB in KB
            'label' => 'Light Logo',
            'mimes' => ['png', 'jpg', 'jpeg', 'webp', 'avif', 'gif', 'svg'],
        ],
        'logo_dark' => [
            'max' => 6144, // 6MB in KB
            'label' => 'Dark Logo',
            'mimes' => ['png', 'jpg', 'jpeg', 'webp', 'avif', 'gif', 'svg'],
        ],
        'favicon' => [
            'max' => 2048, // 2MB in KB
            'label' => 'Favicon',
            'mimes' => ['png', 'ico', 'svg'],
        ],
    ],

    // Email Settings
    'email' => [
        'mailer' => [
            'max' => 50,
            'label' => 'Mailer',
        ],
        'host' => [
            'max' => 255,
            'label' => 'SMTP Host',
        ],
        'port' => [
            'min' => 1,
            'max' => 65535,
            'label' => 'SMTP Port',
        ],
        'username' => [
            'max' => 255,
            'label' => 'Username',
        ],
        'password' => [
            'max' => 255,
            'label' => 'Password',
        ],
        'encryption' => [
            'max' => 20,
            'label' => 'Encryption',
            'allowed' => ['tls', 'ssl'],
        ],
        'from_name' => [
            'max' => 255,
            'label' => 'From Name',
        ],
        'from_address' => [
            'max' => 255,
            'label' => 'From Address',
        ],
    ],

    // Platform Settings
    'platform' => [
        'charge_type' => [
            'label' => 'Charge Type',
            'allowed' => ['percentage', 'fixed'],
        ],
        'charge_value' => [
            'min' => 0,
            'label' => 'Charge Value',
        ],
    ],

    // Footer Settings
    'footer' => [
        'max_pages' => 10,
        'label' => 'Footer Pages',
    ],

    // Dashboard Behavior
    'dashboard' => [
        'items_per_page' => 15,
        'search_debounce' => 300,
        'max_recovery_items' => 100,
    ],
];
