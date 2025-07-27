<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseFrozenRows.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseFrozenRows
{
    public function getFrozenRowsSchema(): array
    {
        return [
            'properties' => [
                'rowLocking' => [
                    'type' => 'object',
                    'description' => 'Row locking configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row locking functionality'
                        ],
                        'maxLockedRows' => [
                            'type' => 'integer',
                            'default' => 5,
                            'description' => 'Maximum number of rows that can be locked simultaneously'
                        ],
                        'lockColumn' => [
                            'type' => 'object',
                            'description' => 'Lock column configuration',
                            'properties' => [
                                'style' => [
                                    'type' => 'string',
                                    'default' => 'width: 4rem',
                                    'description' => 'CSS style for the lock column'
                                ],
                                'frozen' => [
                                    'type' => 'boolean',
                                    'default' => false,
                                    'description' => 'Whether the lock column should be frozen'
                                ],
                                'header' => [
                                    'type' => 'string',
                                    'default' => '',
                                    'description' => 'Header text for the lock column'
                                ]
                            ]
                        ],
                        'lockedRowClasses' => [
                            'type' => 'string',
                            'default' => 'font-bold',
                            'description' => 'CSS classes to apply to locked rows'
                        ],
                        'lockedRowStyles' => [
                            'type' => 'object',
                            'description' => 'CSS style object to apply to locked rows'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformFrozenRows(array $config): array
    {
        return [
            'rowLocking' => $config['rowLocking'] ?? [
                'enabled' => false,
                'maxLockedRows' => 5,
                'lockColumn' => [
                    'style' => 'width: 4rem',
                    'frozen' => false,
                    'header' => ''
                ],
                'lockedRowClasses' => 'font-bold',
                'lockedRowStyles' => []
            ]
        ];
    }
}
