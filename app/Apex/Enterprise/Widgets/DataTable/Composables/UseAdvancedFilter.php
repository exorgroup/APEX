<?php
// app/Apex/Enterprise/Widgets/DataTable/Composables/UseAdvancedFilter.php

namespace App\Apex\Enterprise\Widgets\DataTable\Composables;

trait UseAdvancedFilter
{
    public function getAdvancedFilterSchema(): array
    {
        return [
            'properties' => [
                'advancedFilter' => [
                    'type' => 'object',
                    'description' => 'Advanced filtering configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable advanced filtering'
                        ],
                        'filterBuilder' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable visual filter builder'
                        ],
                        'savedFilters' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable saving and loading filter presets'
                        ],
                        'customOperators' => [
                            'type' => 'array',
                            'description' => 'Custom filter operators',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string'],
                                    'label' => ['type' => 'string'],
                                    'operator' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformAdvancedFilter(array $config): array
    {
        return [
            'advancedFilter' => $config['advancedFilter'] ?? [
                'enabled' => false,
                'filterBuilder' => false,
                'savedFilters' => false,
                'customOperators' => []
            ]
        ];
    }
}
