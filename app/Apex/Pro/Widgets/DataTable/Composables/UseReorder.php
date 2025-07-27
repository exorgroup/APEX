<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseReorder.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseReorder
{
    public function getReorderSchema(): array
    {
        return [
            'properties' => [
                'reorderableColumns' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable column reordering'
                ],
                'reorderableRows' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable row reordering'
                ],
                'reOrder' => [
                    'type' => 'object',
                    'description' => 'ReOrder configuration for enhanced column and row reordering',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable ReOrder functionality'
                        ],
                        'reOrderColumn' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable column reordering when reOrder is enabled'
                        ],
                        'reOrderRows' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row reordering when reOrder is enabled - adds drag handles'
                        ],
                        'excludeOrdering' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Comma-separated list of column fields to exclude from reordering (e.g., "price,stock")'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformReorder(array $config): array
    {
        $reOrder = $config['reOrder'] ?? [];
        if (!empty($reOrder)) {
            $reOrder = [
                'enabled' => $reOrder['enabled'] ?? false,
                'reOrderColumn' => $reOrder['reOrderColumn'] ?? false,
                'reOrderRows' => $reOrder['reOrderRows'] ?? false,
                'excludeOrdering' => $reOrder['excludeOrdering'] ?? '',
            ];
        } else {
            $reOrder = [
                'enabled' => false,
                'reOrderColumn' => false,
                'reOrderRows' => false,
                'excludeOrdering' => '',
            ];
        }

        return [
            'reorderableColumns' => $config['reorderableColumns'] ?? false,
            'reorderableRows' => $config['reorderableRows'] ?? false,
            'reOrder' => $reOrder
        ];
    }

    protected function addReorderableToColumns(array $columns): array
    {
        return array_map(function ($column) {
            $column['reorderable'] = $column['reorderable'] ?? true;
            return $column;
        }, $columns);
    }
}
