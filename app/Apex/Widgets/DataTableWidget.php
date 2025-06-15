<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class DataTableWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'datatable';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the datatable widget'
                ],
                'columns' => [
                    'type' => 'array',
                    'description' => 'Array of column definitions',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'field' => [
                                'type' => 'string',
                                'required' => true,
                                'description' => 'Field name in the data object'
                            ],
                            'header' => [
                                'type' => 'string',
                                'required' => true,
                                'description' => 'Column header text'
                            ],
                            'sortable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Whether column is sortable'
                            ],
                            'filter' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Whether column has filter'
                            ],
                            'style' => [
                                'type' => 'string',
                                'description' => 'Column style'
                            ],
                            'exportable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Include in export'
                            ]
                        ]
                    ]
                ],
                'dataKey' => [
                    'type' => 'string',
                    'description' => 'Unique key for row data',
                    'default' => 'id'
                ],
                'paginator' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Enable pagination'
                ],
                'rows' => [
                    'type' => 'integer',
                    'default' => 10,
                    'description' => 'Rows per page'
                ],
                'rowsPerPageOptions' => [
                    'type' => 'array',
                    'default' => [5, 10, 25, 50],
                    'items' => [
                        'type' => 'integer'
                    ],
                    'description' => 'Options for rows per page dropdown'
                ],
                'sortMode' => [
                    'type' => 'string',
                    'enum' => ['single', 'multiple'],
                    'default' => 'single',
                    'description' => 'Sort mode'
                ],
                'globalFilter' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable global search'
                ],
                'exportable' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable export functionality'
                ],
                'selectionMode' => [
                    'type' => 'string',
                    'enum' => ['single', 'multiple', null],
                    'default' => null,
                    'description' => 'Row selection mode'
                ],
                'dataSource' => [
                    'type' => 'object',
                    'description' => 'Server-side data configuration',
                    'properties' => [
                        'url' => [
                            'type' => 'string',
                            'required' => true,
                            'description' => 'API endpoint URL'
                        ],
                        'method' => [
                            'type' => 'string',
                            'enum' => ['GET', 'POST'],
                            'default' => 'GET'
                        ],
                        'lazy' => [
                            'type' => 'boolean',
                            'default' => true,
                            'description' => 'Enable lazy loading'
                        ]
                    ]
                ],
                'tableStyle' => [
                    'type' => 'string',
                    'default' => 'min-width: 50rem',
                    'description' => 'Table style attribute'
                ],
                'stripedRows' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Striped row styling'
                ]
            ]
        ];
    }

    public function transform(array $config): array
    {
        $columns = $config['columns'] ?? [];
        $dataSource = $config['dataSource'] ?? null;

        // Process columns to ensure proper structure
        $processedColumns = array_map(function ($column) {
            return [
                'field' => $column['field'],
                'header' => $column['header'],
                'sortable' => $column['sortable'] ?? true,
                'filter' => $column['filter'] ?? false,
                'style' => $column['style'] ?? null,
                'exportable' => $column['exportable'] ?? true,
            ];
        }, $columns);

        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'columns' => $processedColumns,
            'dataKey' => $config['dataKey'] ?? 'id',
            'paginator' => $config['paginator'] ?? true,
            'rows' => $config['rows'] ?? 10,
            'rowsPerPageOptions' => $config['rowsPerPageOptions'] ?? [5, 10, 25, 50],
            'sortMode' => $config['sortMode'] ?? 'single',
            'globalFilter' => $config['globalFilter'] ?? false,
            'exportable' => $config['exportable'] ?? false,
            'selectionMode' => $config['selectionMode'] ?? null,
            'dataSource' => $dataSource,
            'tableStyle' => $config['tableStyle'] ?? 'min-width: 50rem',
            'stripedRows' => $config['stripedRows'] ?? true,
        ];
    }
}
