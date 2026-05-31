<?php

return [
    'prefix' => env('ECWS_PREFIX', 'SSCS'),

    'secret_transactions' => [
        'enabled' => env('ECWS_SECRET_TRANSACTIONS_ENABLED', true),
    ],

    'ai' => [
        'enabled' => env('ECWS_AI_ENABLED', false),
        'provider' => env('ECWS_AI_PROVIDER', 'openai'),
        'model' => env('ECWS_AI_MODEL', 'gpt-4o-mini'),
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'files' => [
        'max_size' => env('MAX_FILE_SIZE', 10240), // KB
        'extensions' => explode(',', env('ALLOWED_EXTENSIONS', 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip')),
    ],

    'audit' => [
        'retention_days' => env('AUDIT_LOG_RETENTION_DAYS', 365),
    ],

    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'api_key' => env('WHATSAPP_API_KEY'),
        'from_number' => env('WHATSAPP_FROM_NUMBER'),
    ],

    'two_factor' => [
        'enabled' => env('TWO_FACTOR_ENABLED', true),
    ],
];
