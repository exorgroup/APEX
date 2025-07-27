<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasPagination.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasPagination
{
    public function getPaginationSchema(): array
    {
        return [
            'properties' => [
                'paginator' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Enable pagination'
                ],
                'paginatorPosition' => [
                    'type' => 'string',
                    'enum' => ['top', 'bottom', 'both'],
                    'default' => 'bottom',
                    'description' => 'Paginator position'
                ],
                'rows' => [
                    'type' => 'integer',
                    'default' => 10,
                    'description' => 'Number of rows per page'
                ],
                'rowsPerPageOptions' => [
                    'type' => 'array',
                    'default' => [5, 10, 25, 50, 100],
                    'items' => ['type' => 'integer'],
                    'description' => 'Rows per page options'
                ]
            ]
        ];
    }

    public function transformPagination(array $config): array
    {
        return [
            'paginator' => $config['paginator'] ?? true,
            'paginatorPosition' => $config['paginatorPosition'] ?? 'bottom',
            'rows' => $config['rows'] ?? 10,
            'rowsPerPageOptions' => $config['rowsPerPageOptions'] ?? [5, 10, 25, 50, 100]
        ];
    }
}
