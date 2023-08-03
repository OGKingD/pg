<?php


return [
    'secret_key' => env('SECRET_KEY'),
    'percent_secret_key' => env('PERCENT_SECRET_KEY'),
    'public_key' => env('PUBLIC_KEY'),
    'percent_public_key' => env('PERCENT_PUBLIC_KEY'),
    'encryption_key' => env('SECRET_HASH'),
    'percent_encryption_key' => env('PERCENT_SECRET_HASH'),
    'google_pay_url' => env('FLW_GOOGLE_PAY_URL'),
    'apple_pay_url' => env('FLW_APPLE_PAY_URL'),
    'env' => env('ENVIRONMENT'),
];
