<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Configuration file for messaging providers including endpoints, features, and pricing rules
 * 
 * File location: apex/hermes/config/providers.php
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default messaging provider that will be used
    | when no specific provider is requested.
    |
    */

    'default' => env('HERMES_DEFAULT_PROVIDER', 'cm'),

    /*
    |--------------------------------------------------------------------------
    | Messaging Providers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the messaging providers for your application.
    | Each provider has specific configuration options and capabilities.
    |
    */

    'providers' => [

        'cm' => [
            'name' => 'CM Telecom',
            'endpoint' => 'https://gw.messaging.cm.com/v1.0/message',
            'features' => [
                'sms' => true,
                'whatsapp' => true,
                'rcs' => true,
                'voice' => false,
                'mms' => true,
                'delivery_reports' => true,
                'unicode' => true,
                'long_messages' => true,
                'scheduling' => true,
            ],
            'limits' => [
                'sms_length' => 160,
                'sms_length_unicode' => 70,
                'sender_id_length' => 11,
                'max_recipients_per_request' => 1000,
            ],
            'pricing' => [
                'markup_percentage' => env('HERMES_CM_MARKUP', 15), // 15% markup
                'minimum_charge' => 0.01, // Minimum charge per message
            ],
            'timeout' => 30, // Request timeout in seconds
            'retry_attempts' => 3,
            'retry_delay' => 1000, // Milliseconds
        ],

        'messente' => [
            'name' => 'Messente',
            'endpoint' => 'https://api.messente.com/v1/messages',
            'features' => [
                'sms' => true,
                'whatsapp' => true,
                'rcs' => false,
                'voice' => true,
                'mms' => false,
                'delivery_reports' => true,
                'unicode' => true,
                'long_messages' => true,
                'scheduling' => true,
            ],
            'limits' => [
                'sms_length' => 160,
                'sms_length_unicode' => 70,
                'sender_id_length' => 11,
                'max_recipients_per_request' => 100,
            ],
            'pricing' => [
                'markup_percentage' => env('HERMES_MESSENTE_MARKUP', 15),
                'minimum_charge' => 0.01,
            ],
            'timeout' => 30,
            'retry_attempts' => 3,
            'retry_delay' => 1000,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    |
    | Whether to encrypt API keys and secrets in the database
    |
    */

    'encrypt_keys' => env('HERMES_ENCRYPT_KEYS', true),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for API requests
    |
    */

    'rate_limit' => [
        'enabled' => env('HERMES_RATE_LIMIT_ENABLED', true),
        'requests_per_minute' => env('HERMES_RATE_LIMIT_PER_MINUTE', 60),
        'requests_per_hour' => env('HERMES_RATE_LIMIT_PER_HOUR', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure how messaging activities are logged
    |
    */

    'logging' => [
        'enabled' => env('HERMES_LOGGING_ENABLED', true),
        'channel' => env('HERMES_LOG_CHANNEL', 'hermes'),
        'log_message_content' => env('HERMES_LOG_MESSAGE_CONTENT', false), // PII consideration
        'retention_days' => env('HERMES_LOG_RETENTION_DAYS', 30),
    ],

];
