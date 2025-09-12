<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Use Shipment
    |--------------------------------------------------------------------------
    |
    | This option controls whether the application should use shipment
    | functionality. When disabled, weight fields become optional and
    | shipment costs are not calculated.
    |
    */
    'use_shipment' => env('USE_SHIPMENT', false),

    /*
    |--------------------------------------------------------------------------
    | Raja Ongkir API Configuration
    |--------------------------------------------------------------------------
    |
    | These options are for Raja Ongkir API integration for address
    | and shipping cost calculation.
    |
    */
    'rajaongkir' => [
        'api_key' => env('RAJAONGKIR_API_KEY'),
        'base_url' => env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com/starter'),
    ],
];
