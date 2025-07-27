<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasGridLines.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasGridLines
{
    public function getGridLinesSchema(): array
    {
        return [
            'properties' => [
                'gridLines' => [
                    'type' => 'string',
                    'enum' => ['both', 'horizontal', 'vertical', 'none'],
                    'default' => 'both',
                    'description' => 'Grid lines display'
                ],
                'showGridlines' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Show grid lines'
                ]
            ]
        ];
    }

    public function transformGridLines(array $config): array
    {
        return [
            'gridLines' => $config['gridLines'] ?? 'both',
            'showGridlines' => $config['showGridlines'] ?? true
        ];
    }
}
