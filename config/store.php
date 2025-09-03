<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Store Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for store location and shipping settings
    |
    */

    'name' => env('STORE_NAME', 'Toko Online'),
    'address' => env('STORE_ADDRESS', 'Jl. Contoh No. 123'),
    
    // Shipping origin location (Raja Ongkir city_id)
    'shipping_origin' => [
        'city_id' => env('STORE_SHIPPING_CITY_ID', 501), // Default: Yogyakarta
        'city_name' => env('STORE_SHIPPING_CITY_NAME', 'Yogyakarta'),
        'province_id' => env('STORE_SHIPPING_PROVINCE_ID', 19),
        'province_name' => env('STORE_SHIPPING_PROVINCE_NAME', 'DI Yogyakarta'),
    ],
    
    // Store contact
    'phone' => env('STORE_PHONE', '08123456789'),
    'email' => env('STORE_EMAIL', 'info@toko.com'),
];
