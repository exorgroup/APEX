<?php
// app/Apex/Core/Widgets/DataTable/Composables/UseLazy.php

namespace App\Apex\Core\Widgets\DataTable\Composables;

trait UseLazy
{
    public function getLazySchema(): array
    {
        return [
            'properties' => [
                'dataSource' => [
                    'type' => 'object',
                    'description' => 'Data source configuration',
                    'properties' => [
                        'url' => ['type' => 'string', 'required' => true],
                        'method' => ['type' => 'string', 'enum' => ['GET', 'POST'], 'default' => 'GET'],
                        'lazy' => ['type' => ['boolean', 'string'], 'default' => 'auto'],
                        'lazyThreshold' => ['type' => 'integer', 'default' => 1000],
                        'countUrl' => ['type' => 'string']
                    ]
                ],
                'staticData' => [
                    'type' => 'array',
                    'description' => 'Static data for client-side mode'
                ]
            ]
        ];
    }

    public function transformLazy(array $config): array
    {
        return [
            'dataSource' => $config['dataSource'] ?? null,
            'staticData' => $config['staticData'] ?? null
        ];
    }
}
