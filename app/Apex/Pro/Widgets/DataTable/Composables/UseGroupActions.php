<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseGroupActions.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseGroupActions
{
    public function getGroupActionsSchema(): array
    {
        return [
            'properties' => [
                'groupActions' => [
                    'type' => 'array',
                    'description' => 'Actions for selected items',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'label' => ['type' => 'string'],
                            'icon' => ['type' => 'string'],
                            'action' => ['type' => 'string'],
                            'severity' => ['type' => 'string'],
                            'confirm' => ['type' => 'boolean'],
                            'confirmMessage' => ['type' => 'string']
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformGroupActions(array $config): array
    {
        return [
            'groupActions' => $config['groupActions'] ?? []
        ];
    }
}
