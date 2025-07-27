<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasSearch.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasSearch
{
    public function getSearchSchema(): array
    {
        return [
            'properties' => [
                'globalFilter' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable global filter'
                ],
                'globalFilterFields' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Fields to include in global filter'
                ]
            ]
        ];
    }

    public function transformSearch(array $config): array
    {
        return [
            'globalFilter' => $config['globalFilter'] ?? false,
            'globalFilterFields' => $config['globalFilterFields'] ?? []
        ];
    }

    protected function addSearchableToColumns(array $columns): array
    {
        return array_map(function ($column) {
            $column['searchExclude'] = $column['searchExclude'] ?? false;
            return $column;
        }, $columns);
    }
}
