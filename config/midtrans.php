<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-xxxxxxxx'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-xxxxxxxx'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'G037382691'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'base_url' => env('MIDTRANS_IS_PRODUCTION', false) 
        ? 'https://api.midtrans.com' 
        : 'https://api.sandbox.midtrans.com',
];