<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasSingleColumnSort.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasSingleColumnSort
{
    public function getSingleColumnSortSchema(): array
    {
        return [
            'properties' => [
                'sortMode' => [
                    'type' => 'string',
                    'enum' => ['single'],
                    'default' => 'single',
                    'description' => 'Sort mode - Core only supports single'
                ],
                'defaultSortOrder' => [
                    'type' => 'integer',
                    'enum' => [1, -1],
                    'default' => 1,
                    'description' => 'Default sort order (1 asc, -1 desc)'
                ]
            ]
        ];
    }

    public function transformSingleColumnSort(array $config): array
    {
        return [
            'sortMode' => 'single', // Core only supports single
            'defaultSortOrder' => $config['defaultSortOrder'] ?? 1
        ];
    }

    protected function addSortableToColumns(array $columns): array
    {
        return array_map(function ($column) {
            $column['sortable'] = $column['sortable'] ?? true;
            return $column;
        }, $columns);
    }
}
