<?php
// app/Apex/Enterprise/Widgets/DataTable/Composables/UseRowExpansion.php

namespace App\Apex\Enterprise\Widgets\DataTable\Composables;

trait UseRowExpansion
{
    public function getRowExpansionSchema(): array
    {
        return [
            'properties' => [
                'rowExpansion' => [
                    'type' => 'object',
                    'description' => 'Row expansion configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row expansion'
                        ],
                        'expanderColumn' => [
                            'type' => 'object',
                            'properties' => [
                                'style' => ['type' => 'string', 'default' => 'width: 5rem'],
                                'frozen' => ['type' => 'boolean', 'default' => false]
                            ]
                        ],
                        'expandControls' => [
                            'type' => 'object',
                            'properties' => [
                                'showExpandAll' => ['type' => 'boolean', 'default' => true],
                                'showCollapseAll' => ['type' => 'boolean', 'default' => true],
                                'expandAllLabel' => ['type' => 'string', 'default' => 'Expand All'],
                                'collapseAllLabel' => ['type' => 'string', 'default' => 'Collapse All'],
                                'position' => ['type' => 'string', 'enum' => ['header', 'toolbar'], 'default' => 'header']
                            ]
                        ],
                        'expandedContent' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => ['type' => 'string', 'enum' => ['datatable', 'custom']],
                                'title' => ['type' => 'string'],
                                'titleField' => ['type' => 'string'],
                                'titleTemplate' => ['type' => 'string'],
                                'dataField' => ['type' => 'string', 'description' => 'Field containing nested data'],
                                'widget' => ['type' => 'object', 'description' => 'DataTableWidget configuration'],
                                'customTemplate' => ['type' => 'string']
                            ]
                        ],
                        'events' => [
                            'type' => 'object',
                            'properties' => [
                                'onExpand' => ['type' => 'boolean', 'default' => false],
                                'onCollapse' => ['type' => 'boolean', 'default' => false]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformRowExpansion(array $config): array
    {
        return [
            'rowExpansion' => $config['rowExpansion'] ?? ['enabled' => false]
        ];
    }
}
