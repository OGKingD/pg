<?php


return [
    'secret_key' => env('SECRET_KEY'),
    'public_key' => env('PUBLIC_KEY'),
    'encryption_key' => env('SECRET_HASH'),
    'google_pay_url' => env('FLW_GOOGLE_PAY_URL'),
    'apple_pay_url' => env('FLW_APPLE_PAY_URL'),
    'env' => env('ENVIRONMENT'),
];
