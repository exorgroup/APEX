<?php
// app/Apex/Pro/Widgets/DataTable/Composables/UseBasicFilter.php

namespace App\Apex\Pro\Widgets\DataTable\Composables;

trait UseBasicFilter
{
    public function getBasicFilterSchema(): array
    {
        return [
            'properties' => [
                'columnFilters' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable basic column filtering'
                ],
                'filterMode' => [
                    'type' => 'string',
                    'enum' => ['lenient', 'strict'],
                    'default' => 'lenient',
                    'description' => 'Filter matching mode'
                ],
                'filterDisplay' => [
                    'type' => 'string',
                    'enum' => ['menu', 'row'],
                    'default' => 'menu',
                    'description' => 'Filter display mode'
                ]
            ]
        ];
    }

    public function transformBasicFilter(array $config): array
    {
        return [
            'columnFilters' => $config['columnFilters'] ?? false,
            'filterMode' => $config['filterMode'] ?? 'lenient',
            'filterDisplay' => $config['filterDisplay'] ?? 'menu'
        ];
    }
}
