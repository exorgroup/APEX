<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseFrozenColumns.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseFrozenColumns
{
    public function getFrozenColumnsSchema(): array
    {
        return [
            'properties' => [
                'frozenColumns' => [
                    'type' => 'integer',
                    'default' => 0,
                    'description' => 'Number of frozen columns from left'
                ],
                'columnLocking' => [
                    'type' => 'object',
                    'description' => 'Column locking configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable column locking functionality'
                        ],
                        'buttonPosition' => [
                            'type' => 'string',
                            'enum' => ['header', 'toolbar'],
                            'default' => 'toolbar',
                            'description' => 'Position of column lock buttons'
                        ],
                        'buttonStyle' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'CSS style for column lock buttons'
                        ],
                        'buttonClass' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'CSS classes for column lock buttons'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformFrozenColumns(array $config): array
    {
        return [
            'frozenColumns' => $config['frozenColumns'] ?? 0,
            'columnLocking' => $config['columnLocking'] ?? [
                'enabled' => false,
                'buttonPosition' => 'toolbar',
                'buttonStyle' => '',
                'buttonClass' => ''
            ]
        ];
    }

    protected function addLockableToColumns(array $columns): array
    {
        return array_map(function ($column) {
            $column['lockColumn'] = $column['lockColumn'] ?? false;
            $column['lockButton'] = $column['lockButton'] ?? false;
            return $column;
        }, $columns);
    }
}
