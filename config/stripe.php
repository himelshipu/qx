<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe API Configuration
    |--------------------------------------------------------------------------
    |
    | The Stripe API keys for production and test environments
    | Get them from: https://dashboard.stripe.com/apikeys
    |
    */

    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    
    /*
    |--------------------------------------------------------------------------
    | Stripe API Version
    |--------------------------------------------------------------------------
    */
    'api_version' => '2024-04-10',

    /*
    |--------------------------------------------------------------------------
    | Supported Card Brands
    |--------------------------------------------------------------------------
    */
    'supported_brands' => ['visa', 'mastercard', 'amex', 'discover', 'diners', 'jcb'],

    /*
    |--------------------------------------------------------------------------
    | Card Storage Settings
    |--------------------------------------------------------------------------
    */
    'save_cards' => true,  // Enable saving cards for future use
    'require_cvv' => true, // Require CVV on payment
    'default_currency' => env('APP_CURRENCY', 'USD'),
];
