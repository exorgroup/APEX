<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Configuration file defining available messaging service types and their properties
 * 
 * File location: apex/hermes/config/services.php
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Available Services
    |--------------------------------------------------------------------------
    |
    | Define the messaging services available through Hermes API
    |
    */

    'sms' => [
        'name' => 'SMS Messaging',
        'description' => 'Standard text messaging service',
        'enabled' => true,
        'features' => [
            'unicode_support' => true,
            'long_message_support' => true,
            'delivery_reports' => true,
            'scheduled_sending' => true,
            'bulk_sending' => true,
        ],
        'validation' => [
            'phone_number_regex' => '/^\+?[1-9]\d{1,14}$/', // E.164 format
            'sender_id_regex' => '/^[a-zA-Z0-9\s]{1,11}$/',
            'message_max_length' => 1600, // 10 concatenated messages
        ],
    ],

    'whatsapp' => [
        'name' => 'WhatsApp Business',
        'description' => 'WhatsApp messaging for business communications',
        'enabled' => true,
        'features' => [
            'media_support' => true,
            'template_messages' => true,
            'interactive_buttons' => true,
            'delivery_reports' => true,
            'rich_media' => true,
        ],
        'supported_media_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/mp4',
            'audio/mpeg',
            'audio/ogg',
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'limits' => [
            'media_size_mb' => 16,
            'caption_length' => 1024,
            'buttons_per_message' => 3,
        ],
    ],

    'voice' => [
        'name' => 'Voice Calls',
        'description' => 'Text-to-speech voice call service',
        'enabled' => false, // Not implemented with CM yet
        'features' => [
            'text_to_speech' => true,
            'multiple_languages' => true,
            'call_recording' => false,
            'ivr_support' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Defaults
    |--------------------------------------------------------------------------
    |
    | Default settings applied to all services unless overridden
    |
    */

    'defaults' => [
        'character_encoding' => 'UTF-8',
        'timezone' => 'UTC',
        'retry_failed_messages' => true,
        'max_retry_attempts' => 3,
        'delivery_report_webhook' => env('HERMES_WEBHOOK_URL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Restrictions
    |--------------------------------------------------------------------------
    |
    | Global restrictions that apply to all services
    |
    */

    'restrictions' => [
        'blocked_countries' => env('HERMES_BLOCKED_COUNTRIES', ''), // Comma-separated country codes
        'allowed_countries' => env('HERMES_ALLOWED_COUNTRIES', ''), // If set, only these are allowed
        'content_filtering' => env('HERMES_CONTENT_FILTERING', false),
        'require_opt_in' => env('HERMES_REQUIRE_OPT_IN', false),
    ],

];
