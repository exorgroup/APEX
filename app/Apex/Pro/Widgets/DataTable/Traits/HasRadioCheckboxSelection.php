<?php
// app/Apex/Pro/Widgets/DataTable/Traits/HasRadioCheckboxSelection.php

namespace App\Apex\Pro\Widgets\DataTable\Traits;

trait HasRadioCheckboxSelection
{
    public function getRadioCheckboxSelectionSchema(): array
    {
        return [
            'properties' => [
                'selectionMode' => [
                    'type' => 'string',
                    'enum' => ['single', 'multiple', 'checkbox'],
                    'description' => 'Selection mode including checkbox'
                ],
                'selectAll' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable select all for checkbox mode'
                ],
                'showCheckboxColumn' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show checkbox column for selection'
                ]
            ]
        ];
    }

    public function transformRadioCheckboxSelection(array $config): array
    {
        $selectionMode = $config['selectionMode'] ?? null;
        $showCheckboxColumn = ($selectionMode === 'checkbox') ||
            ($selectionMode === 'multiple' && ($config['selectAll'] ?? false));

        return [
            'selectionMode' => $selectionMode,
            'selectAll' => $config['selectAll'] ?? false,
            'showCheckboxColumn' => $showCheckboxColumn
        ];
    }
}
