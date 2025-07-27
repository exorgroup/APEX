<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UsePreload.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UsePreload
{
    public function getPreloadSchema(): array
    {
        return [
            'properties' => [
                'preload' => [
                    'type' => 'object',
                    'description' => 'Preload configuration for eager data loading',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable preloading functionality'
                        ],
                        'dataUrl' => [
                            'type' => 'string',
                            'description' => 'URL to preload data from'
                        ],
                        'cacheKey' => [
                            'type' => 'string',
                            'description' => 'Cache key for preloaded data'
                        ],
                        'cacheDuration' => [
                            'type' => 'integer',
                            'default' => 300,
                            'description' => 'Cache duration in seconds'
                        ],
                        'backgroundRefresh' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable background data refresh'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformPreload(array $config): array
    {
        return [
            'preload' => $config['preload'] ?? [
                'enabled' => false,
                'dataUrl' => null,
                'cacheKey' => null,
                'cacheDuration' => 300,
                'backgroundRefresh' => false
            ]
        ];
    }
}
