<?php
// app/Apex/Pro/Widgets/DataTable/Traits/HasCrudActions.php

namespace App\Apex\Pro\Widgets\DataTable\Traits;

trait HasCrudActions
{
    public function getCrudActionsSchema(): array
    {
        return [
            'properties' => [
                'showView' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show view button column'
                ],
                'showEdit' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show edit button column'
                ],
                'showDelete' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show delete button column'
                ],
                'showHistory' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show history button column'
                ],
                'showPrint' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show print button column'
                ],
                'crudActions' => [
                    'type' => 'object',
                    'description' => 'CRUD action configuration',
                    'properties' => [
                        'idField' => [
                            'type' => 'string',
                            'default' => 'id',
                            'description' => 'Field to use as record identifier'
                        ],
                        'permissions' => [
                            'type' => 'object',
                            'properties' => [
                                'view' => ['type' => 'boolean', 'default' => true],
                                'edit' => ['type' => 'boolean', 'default' => true],
                                'delete' => ['type' => 'boolean', 'default' => true],
                                'history' => ['type' => 'boolean', 'default' => true],
                                'print' => ['type' => 'boolean', 'default' => true]
                            ]
                        ],
                        'routes' => [
                            'type' => 'object',
                            'properties' => [
                                'view' => ['type' => 'string'],
                                'edit' => ['type' => 'string'],
                                'delete' => ['type' => 'string'],
                                'history' => ['type' => 'string'],
                                'print' => ['type' => 'string']
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformCrudActions(array $config): array
    {
        return [
            'showView' => $config['showView'] ?? false,
            'showEdit' => $config['showEdit'] ?? false,
            'showDelete' => $config['showDelete'] ?? false,
            'showHistory' => $config['showHistory'] ?? false,
            'showPrint' => $config['showPrint'] ?? false,
            'crudActions' => $config['crudActions'] ?? [
                'idField' => 'id',
                'permissions' => [
                    'view' => true,
                    'edit' => true,
                    'delete' => true,
                    'history' => true,
                    'print' => true
                ]
            ]
        ];
    }
}
