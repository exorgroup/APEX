<?php

return [
    /*
    |--------------------------------------------------------------------------
    | APEX Template Configuration
    |--------------------------------------------------------------------------
    |
    | This value determines which template will be used for the application.
    | You can switch between different templates by changing this value.
    | Available templates: 'laravel', 'glass-white', 'glass-black', 'material', 'omni'
    |
    */
    'template' => env('APEX_TEMPLATE', 'laravel'),

    /*
    |--------------------------------------------------------------------------
    | Template Configurations
    |--------------------------------------------------------------------------
    |
    | Here you can define configuration for each template
    |
    */
    'templates' => [
        'laravel' => [
            'name' => 'Laravel Default',
            'description' => 'Default Laravel template with sidebar navigation',
            'layout' => 'Laravel',
            'has_sidebar' => true,
            'theme' => 'default',
        ],
        'glass-white' => [
            'name' => 'Glass White',
            'description' => 'Modern glass morphism template with white transparent header',
            'layout' => 'GlassWhite',
            'has_sidebar' => false,
            'theme' => 'glass-white',
        ],
        'glass-black' => [
            'name' => 'Glass Black',
            'description' => 'Modern glass morphism template with black transparent header',
            'layout' => 'GlassBlack',
            'has_sidebar' => false,
            'theme' => 'glass-black',
        ],
        'material' => [
            'name' => 'Material Purple',
            'description' => 'Modern Material Design inspired template with purple theme',
            'layout' => 'Material',
            'has_sidebar' => true,
            'theme' => 'material-purple',
        ],
        'omni' => [
            'name' => 'Omni Yellow',
            'description' => 'Modern vibrant template with yellow theme',
            'layout' => 'Omni',
            'has_sidebar' => true,
            'theme' => 'omni-yellow',
        ],
    ],
];
