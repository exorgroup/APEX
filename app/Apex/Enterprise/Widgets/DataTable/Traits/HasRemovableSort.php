<?php
// app/Apex/Enterprise/Widgets/DataTable/Traits/HasRemovableSort.php

namespace App\Apex\Enterprise\Widgets\DataTable\Traits;

trait HasRemovableSort
{
    public function getRemovableSortSchema(): array
    {
        return [
            'properties' => [
                'removableSort' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Allow removing sort with 3rd click'
                ]
            ]
        ];
    }

    public function transformRemovableSort(array $config): array
    {
        return [
            'removableSort' => $config['removableSort'] ?? true
        ];
    }
}
