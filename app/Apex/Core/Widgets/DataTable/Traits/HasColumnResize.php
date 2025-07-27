<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasColumnResize.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasColumnResize
{
    public function getColumnResizeSchema(): array
    {
        return [
            'properties' => [
                'resizableColumns' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable column resizing'
                ],
                'columnResizeMode' => [
                    'type' => 'string',
                    'enum' => ['fit', 'expand'],
                    'default' => 'fit',
                    'description' => 'Column resize behavior'
                ]
            ]
        ];
    }

    public function transformColumnResize(array $config): array
    {
        return [
            'resizableColumns' => $config['resizableColumns'] ?? false,
            'columnResizeMode' => $config['columnResizeMode'] ?? 'fit'
        ];
    }

    protected function addResizableToColumns(array $columns): array
    {
        return array_map(function ($column) {
            $column['resizable'] = $column['resizable'] ?? true;
            $column['minWidth'] = $column['minWidth'] ?? null;
            $column['maxWidth'] = $column['maxWidth'] ?? null;
            return $column;
        }, $columns);
    }
}
