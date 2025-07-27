<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseColumnGroup.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseColumnGroup
{
    public function getColumnGroupSchema(): array
    {
        return [
            'properties' => [
                'columnGrouping' => [
                    'type' => 'object',
                    'description' => 'Column grouping configuration for header and footer groups',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable column grouping functionality'
                        ],
                        'headerGroups' => [
                            'type' => 'array',
                            'description' => 'Array of header group rows',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'cells' => [
                                        'type' => 'array',
                                        'description' => 'Array of cells in this row',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'header' => ['type' => 'string'],
                                                'field' => ['type' => 'string'],
                                                'sortable' => ['type' => 'boolean', 'default' => false],
                                                'rowspan' => ['type' => 'integer'],
                                                'colspan' => ['type' => 'integer'],
                                                'headerStyle' => ['type' => 'string'],
                                                'isTotal' => ['type' => 'boolean', 'default' => false],
                                                'totalField' => ['type' => 'string'],
                                                'totalType' => ['type' => 'string', 'enum' => ['sum', 'avg', 'count', 'min', 'max'], 'default' => 'sum'],
                                                'formatType' => ['type' => 'string', 'enum' => ['currency', 'percentage', 'number', 'text'], 'default' => 'number'],
                                                'formatDecimals' => ['type' => 'integer', 'default' => 2]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'footerGroups' => [
                            'type' => 'array',
                            'description' => 'Array of footer group rows',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'cells' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'footer' => ['type' => 'string'],
                                                'rowspan' => ['type' => 'integer'],
                                                'colspan' => ['type' => 'integer'],
                                                'footerStyle' => ['type' => 'string'],
                                                'isTotal' => ['type' => 'boolean', 'default' => false],
                                                'totalField' => ['type' => 'string'],
                                                'totalType' => ['type' => 'string', 'enum' => ['sum', 'avg', 'count', 'min', 'max'], 'default' => 'sum'],
                                                'formatType' => ['type' => 'string', 'enum' => ['currency', 'percentage', 'number', 'text'], 'default' => 'number'],
                                                'formatDecimals' => ['type' => 'integer', 'default' => 2]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformColumnGroup(array $config): array
    {
        $columnGrouping = $config['columnGrouping'] ?? [];

        if (!empty($columnGrouping) && $columnGrouping['enabled']) {
            // Process header groups
            if (isset($columnGrouping['headerGroups'])) {
                $columnGrouping['headerGroups'] = array_map(function ($row) {
                    if (isset($row['cells'])) {
                        $row['cells'] = array_map(function ($cell) {
                            return [
                                'header' => $cell['header'] ?? '',
                                'field' => $cell['field'] ?? null,
                                'sortable' => $cell['sortable'] ?? false,
                                'rowspan' => $cell['rowspan'] ?? null,
                                'colspan' => $cell['colspan'] ?? null,
                                'headerStyle' => $cell['headerStyle'] ?? null,
                                'isTotal' => $cell['isTotal'] ?? false,
                                'totalField' => $cell['totalField'] ?? null,
                                'totalType' => $cell['totalType'] ?? 'sum',
                                'formatType' => $cell['formatType'] ?? 'number',
                                'formatDecimals' => $cell['formatDecimals'] ?? 2,
                            ];
                        }, $row['cells']);
                    }
                    return $row;
                }, $columnGrouping['headerGroups']);
            }

            // Process footer groups
            if (isset($columnGrouping['footerGroups'])) {
                $columnGrouping['footerGroups'] = array_map(function ($row) {
                    if (isset($row['cells'])) {
                        $row['cells'] = array_map(function ($cell) {
                            return [
                                'footer' => $cell['footer'] ?? '',
                                'rowspan' => $cell['rowspan'] ?? null,
                                'colspan' => $cell['colspan'] ?? null,
                                'footerStyle' => $cell['footerStyle'] ?? null,
                                'isTotal' => $cell['isTotal'] ?? false,
                                'totalField' => $cell['totalField'] ?? null,
                                'totalType' => $cell['totalType'] ?? 'sum',
                                'formatType' => $cell['formatType'] ?? 'number',
                                'formatDecimals' => $cell['formatDecimals'] ?? 2,
                            ];
                        }, $row['cells']);
                    }
                    return $row;
                }, $columnGrouping['footerGroups']);
            }
        } else {
            $columnGrouping = [
                'enabled' => false,
                'headerGroups' => [],
                'footerGroups' => []
            ];
        }

        return [
            'columnGrouping' => $columnGrouping
        ];
    }
}
