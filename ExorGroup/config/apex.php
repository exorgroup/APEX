<?php

return [
    /*
    |--------------------------------------------------------------------------
    | APEX Template Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for the APEX admin framework.
    | You can customize these settings to match your application's needs.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Template Processing
    |--------------------------------------------------------------------------
    |
    | These settings control how APEX processes templates and widgets.
    |
    */
    'processing' => [
        // Whether to process AJAX requests (default: false)
        'process_ajax' => env('APEX_PROCESS_AJAX', false),

        // Cache processed templates (recommended for production)
        'cache_templates' => env('APEX_CACHE_TEMPLATES', true),

        // Cache duration in minutes
        'cache_duration' => env('APEX_CACHE_DURATION', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for widget behavior and rendering.
    |
    */
    'widgets' => [
        // Default CSS class prefix for widgets
        'css_prefix' => 'apex-',

        // Whether to generate unique IDs for widgets without explicit IDs
        'auto_generate_ids' => true,

        // Default widget parameters
        'defaults' => [
            'cssClass' => '',
            'animate' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Icon Configuration
    |--------------------------------------------------------------------------
    |
    | Configure icon providers and settings.
    |
    */
    'icons' => [
        // Icon provider: 'prime', 'fontawesome', 'heroicons', 'custom', 'svg'
        'provider' => env('APEX_ICON_PROVIDER', 'prime'),

        // Icon class prefix (used with class-based icon providers)
        'prefix' => env('APEX_ICON_PREFIX', 'pi pi-'),

        // Path to custom SVG icons (used with 'custom' provider)
        'custom_path' => env('APEX_ICON_CUSTOM_PATH', '/icons/'),

        // Fallback icon when requested icon isn't found
        'fallback_icon' => env('APEX_ICON_FALLBACK', 'circle'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Event System Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the widget event system.
    |
    */
    'events' => [
        // Enable event system globally
        'enabled' => env('APEX_EVENTS_ENABLED', true),

        // Enable event debugging (adds console logging)
        'debug' => env('APEX_EVENTS_DEBUG', false),

        // Default event options
        'defaults' => [
            'throttle' => 0,        // Default throttle time in milliseconds
            'persist' => false,     // Persist events in localStorage
            'broadcast' => false,   // Broadcast events to other tabs
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for template themes and styling.
    |
    */
    'theme' => [
        // Default theme
        'default' => env('APEX_THEME', 'tailwind'),

        // Available themes
        'available' => [
            'tailwind' => 'Tailwind CSS',
            'bootstrap' => 'Bootstrap 5',
        ],

        // Theme-specific settings
        'settings' => [
            'tailwind' => [
                'container_classes' => 'container mx-auto px-4',
                'card_classes' => 'bg-white shadow rounded-lg p-6',
            ],
            'bootstrap' => [
                'container_classes' => 'container-fluid',
                'card_classes' => 'card',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for layout templates and structure.
    |
    */
    'layout' => [
        // Default layout template
        'default' => 'apex::layouts.app',

        // Layout options
        'sidebar_width' => '250px',
        'header_height' => '64px',
        'footer_height' => '60px',

        // Responsive breakpoints
        'breakpoints' => [
            'sm' => '640px',
            'md' => '768px',
            'lg' => '1024px',
            'xl' => '1280px',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for CSS and JavaScript assets.
    |
    */
    'assets' => [
        // CSS files to include
        'css' => [
            // Will be resolved from public path
        ],

        // JavaScript files to include
        'js' => [
            // Will be resolved from public path
        ],

        // CDN assets
        'cdn' => [
            'css' => [
                // External CSS files
            ],
            'js' => [
                // External JavaScript files
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security settings for template processing.
    |
    */
    'security' => [
        // Allowed HTML tags in widget content
        'allowed_html_tags' => [
            'div',
            'span',
            'p',
            'a',
            'img',
            'i',
            'strong',
            'em',
            'ul',
            'ol',
            'li',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'br',
            'hr',
            'table',
            'thead',
            'tbody',
            'tr',
            'td',
            'th',
            'form',
            'input',
            'button',
            'select',
            'option',
            'textarea',
            'label',
            'fieldset',
            'legend'
        ],

        // Sanitize widget parameters
        'sanitize_params' => true,

        // Maximum widget nesting depth
        'max_nesting_depth' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Settings to optimize APEX performance.
    |
    */
    'performance' => [
        // Enable widget caching
        'cache_widgets' => env('APEX_CACHE_WIDGETS', true),

        // Widget cache duration in minutes
        'widget_cache_duration' => env('APEX_WIDGET_CACHE_DURATION', 30),

        // Lazy load widgets
        'lazy_load' => env('APEX_LAZY_LOAD', false),

        // Minify generated JavaScript
        'minify_js' => env('APEX_MINIFY_JS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for development and debugging.
    |
    */
    'development' => [
        // Show widget boundaries in development
        'show_widget_boundaries' => env('APEX_SHOW_BOUNDARIES', false),

        // Log widget rendering times
        'log_render_times' => env('APEX_LOG_RENDER_TIMES', false),

        // Enable widget inspector
        'widget_inspector' => env('APEX_WIDGET_INSPECTOR', false),

        // Show processing statistics
        'show_stats' => env('APEX_SHOW_STATS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-tenancy Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for multi-tenant applications.
    |
    */
    'tenancy' => [
        // Enable tenant-specific widgets
        'tenant_widgets' => env('APEX_TENANT_WIDGETS', false),

        // Tenant-specific theme override
        'tenant_themes' => env('APEX_TENANT_THEMES', false),

        // Cache key prefix for tenant-specific data
        'cache_prefix' => env('APEX_TENANT_CACHE_PREFIX', 'apex_tenant_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for internationalization and localization.
    |
    */
    'localization' => [
        // Enable localization
        'enabled' => env('APEX_LOCALIZATION_ENABLED', false),

        // Default locale for widgets
        'default_locale' => env('APEX_DEFAULT_LOCALE', 'en'),

        // Supported locales
        'supported_locales' => ['en', 'es', 'fr', 'de', 'it'],

        // Locale detection method: 'header', 'session', 'url'
        'detection_method' => env('APEX_LOCALE_DETECTION', 'header'),
    ],
];
