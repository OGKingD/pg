<?php

return [
    'password' => env('GLOBUS_PASSWORD'),
    'client_id' => env('GLOBUS_CLIENT_ID'),
    'client_secret' => env('GLOBUS_CLIENT_SECRET'),
    'scope' => env('GLOBUS_SCOPE', 'SAANA'),
    'auth_base_url' => env('GLOBUS_AUTH_URL', 'https://omniauth.globusbank.com/AuthService/connect/token'),
    'base_url' => env('GLOBUS_BASE_URL', 'http://tppservice.globusbank.com:2020/TPPService_v2/api/v2/'),
    'linkedPartnerAccountNumber' => env('GLOBUS_LINKED_PARTNER_ACCOUNT_NUMBER'),
    'TransferLinkedAccount' => env('GLOBUS_TRANSFER_LINKED_ACCOUNT_NUMBER'),
];
