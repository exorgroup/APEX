<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasSingleRowSelection.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasSingleRowSelection
{
    public function getSingleRowSelectionSchema(): array
    {
        return [
            'properties' => [
                'selectionMode' => [
                    'type' => 'string',
                    'enum' => ['single'],
                    'description' => 'Selection mode - Core only supports single'
                ],
                'selection' => [
                    'type' => 'array',
                    'default' => [],
                    'description' => 'Selected items'
                ],
                'metaKeySelection' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Require meta key for selection'
                ]
            ]
        ];
    }

    public function transformSingleRowSelection(array $config): array
    {
        // Only allow single selection mode in Core
        $selectionMode = $config['selectionMode'] ?? null;
        if ($selectionMode && $selectionMode !== 'single') {
            $selectionMode = 'single';
        }

        return [
            'selectionMode' => $selectionMode,
            'selection' => $config['selection'] ?? [],
            'metaKeySelection' => $config['metaKeySelection'] ?? true
        ];
    }
}
