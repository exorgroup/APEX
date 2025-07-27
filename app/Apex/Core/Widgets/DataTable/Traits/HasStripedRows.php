<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasStripedRows.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasStripedRows
{
    public function getStripedRowsSchema(): array
    {
        return [
            'properties' => [
                'stripedRows' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Striped row styling'
                ]
            ]
        ];
    }

    public function transformStripedRows(array $config): array
    {
        return [
            'stripedRows' => $config['stripedRows'] ?? true
        ];
    }
}
