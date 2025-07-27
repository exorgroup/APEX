<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseColumnToggle.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseColumnToggle
{
    public function getColumnToggleSchema(): array
    {
        return [
            'properties' => [
                'columnToggle' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable column visibility toggle'
                ],
                'columnTogglePosition' => [
                    'type' => 'string',
                    'enum' => ['left', 'right'],
                    'default' => 'right',
                    'description' => 'Position of column toggle button'
                ]
            ]
        ];
    }

    public function transformColumnToggle(array $config): array
    {
        return [
            'columnToggle' => $config['columnToggle'] ?? false,
            'columnTogglePosition' => $config['columnTogglePosition'] ?? 'right'
        ];
    }
}
