<?php
// app/Apex/Pro/Widgets/DataTable/Traits/HasPresort.php

namespace App\Apex\Pro\Widgets\DataTable\Traits;

trait HasPresort
{
    public function getPresortSchema(): array
    {
        return [
            'properties' => [
                'defaultSortField' => [
                    'type' => 'string',
                    'description' => 'Default field to sort by on load'
                ],
                'defaultSortOrder' => [
                    'type' => 'integer',
                    'enum' => [1, -1],
                    'default' => 1,
                    'description' => 'Default sort order (1 asc, -1 desc)'
                ],
                'multiSortMeta' => [
                    'type' => 'array',
                    'description' => 'Pre-configured multi-sort settings',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'field' => ['type' => 'string'],
                            'order' => ['type' => 'integer', 'enum' => [1, -1, 0]]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformPresort(array $config): array
    {
        return [
            'defaultSortField' => $config['defaultSortField'] ?? null,
            'defaultSortOrder' => $config['defaultSortOrder'] ?? 1,
            'multiSortMeta' => $config['multiSortMeta'] ?? []
        ];
    }
}
