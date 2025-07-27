<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseRowGroup.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseRowGroup
{
    public function getRowGroupSchema(): array
    {
        return [
            'properties' => [
                'rowGrouping' => [
                    'type' => 'object',
                    'description' => 'Row grouping configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row grouping functionality'
                        ],
                        'rowGroupMode' => [
                            'type' => 'string',
                            'enum' => ['subheader', 'rowspan'],
                            'default' => 'rowspan',
                            'description' => 'Row grouping mode - rowspan or subheader'
                        ],
                        'groupRowsBy' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Array of field names to group by (only first field is used currently)'
                        ],
                        'sortField' => [
                            'type' => 'string',
                            'description' => 'Field to sort by for grouping (usually same as groupRowsBy field)'
                        ],
                        'sortOrder' => [
                            'type' => 'integer',
                            'enum' => [1, -1],
                            'default' => 1,
                            'description' => 'Sort order for grouping (1 for ascending, -1 for descending)'
                        ],
                        'groupRowsTotals' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Array of field names to calculate totals for in group footer'
                        ],
                        'showHeaderTotal' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Show totals in group header'
                        ],
                        'showHeaderRowCount' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Show row count in group header'
                        ],
                        'headerRowCountText' => [
                            'type' => 'string',
                            'default' => 'Items in this group: ',
                            'description' => 'Text to display before row count in header'
                        ],
                        'headerTemplate' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Template for group header with {fieldName} placeholders'
                        ],
                        'headerImageField' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Field name containing image URL for group header'
                        ],
                        'headerImageUrl' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Static image URL for group header'
                        ],
                        'headerImagePosition' => [
                            'type' => 'string',
                            'enum' => ['before', 'after'],
                            'default' => 'before',
                            'description' => 'Position of image relative to text in group header'
                        ],
                        'showFooterTotal' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Show totals in group footer'
                        ],
                        'showFooterRowCount' => [
                            'type' => 'boolean',
                            'default' => true,
                            'description' => 'Show row count in group footer'
                        ],
                        'footerRowCountText' => [
                            'type' => 'string',
                            'default' => 'Total items: ',
                            'description' => 'Text to display before row count in footer'
                        ],
                        'footerTemplate' => [
                            'type' => 'string',
                            'default' => 'Total items: {rowCount}',
                            'description' => 'Template for group footer with {rowCount} and other placeholders'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformRowGroup(array $config): array
    {
        return [
            'rowGrouping' => $config['rowGrouping'] ?? [
                'enabled' => false,
                'rowGroupMode' => 'rowspan',
                'groupRowsBy' => [],
                'sortField' => null,
                'sortOrder' => 1,
                'groupRowsTotals' => [],
                'showHeaderTotal' => false,
                'showHeaderRowCount' => false,
                'headerRowCountText' => 'Items in this group: ',
                'headerTemplate' => '',
                'headerImageField' => '',
                'headerImageUrl' => '',
                'headerImagePosition' => 'before',
                'showFooterTotal' => false,
                'showFooterRowCount' => true,
                'footerRowCountText' => 'Total items: ',
                'footerTemplate' => 'Total items: {rowCount}'
            ]
        ];
    }
}
