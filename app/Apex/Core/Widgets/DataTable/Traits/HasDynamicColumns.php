<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasDynamicColumns.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasDynamicColumns
{
    public function getDynamicColumnsSchema(): array
    {
        return [
            'properties' => [
                'columns' => [
                    'type' => 'array',
                    'required' => true,
                    'description' => 'Column definitions',
                    'items' => [
                        'type' => 'object',
                        'required' => ['field', 'header'],
                        'properties' => [
                            'field' => [
                                'type' => 'string',
                                'description' => 'Field name'
                            ],
                            'header' => [
                                'type' => 'string',
                                'description' => 'Column header'
                            ],
                            'style' => [
                                'type' => 'string',
                                'description' => 'Column style'
                            ],
                            'bodyStyle' => [
                                'type' => 'string',
                                'description' => 'Column body cell style'
                            ],
                            'headerStyle' => [
                                'type' => 'string',
                                'description' => 'Column header style'
                            ],
                            'hidden' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Hide column by default'
                            ],
                            'exportable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Include in export'
                            ],
                            'frozen' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Freeze column (always frozen)'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformDynamicColumns(array $columns): array
    {
        return array_map(function ($column) {
            return [
                'field' => $column['field'],
                'header' => $column['header'],
                'style' => $column['style'] ?? null,
                'bodyStyle' => $column['bodyStyle'] ?? null,
                'headerStyle' => $column['headerStyle'] ?? null,
                'hidden' => $column['hidden'] ?? false,
                'exportable' => $column['exportable'] ?? true,
                'frozen' => $column['frozen'] ?? false
            ];
        }, $columns);
    }
}
