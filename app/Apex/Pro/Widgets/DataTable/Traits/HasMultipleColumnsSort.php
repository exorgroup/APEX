<?php
// app/Apex/Pro/Widgets/DataTable/Traits/HasMultipleColumnsSort.php

namespace App\Apex\Pro\Widgets\DataTable\Traits;

trait HasMultipleColumnsSort
{
    public function getMultipleColumnsSortSchema(): array
    {
        return [
            'properties' => [
                'sortMode' => [
                    'type' => 'string',
                    'enum' => ['single', 'multiple'],
                    'default' => 'single',
                    'description' => 'Sort mode'
                ],
                'multiSortMeta' => [
                    'type' => 'array',
                    'description' => 'Multi-sort configuration',
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

    public function transformMultipleColumnsSort(array $config): array
    {
        return [
            'sortMode' => $config['sortMode'] ?? 'single',
            'multiSortMeta' => $config['multiSortMeta'] ?? []
        ];
    }
}
