<?php
// app/Apex/Enterprise/Widgets/DataTable/Composables/UseExport.php

namespace App\Apex\Enterprise\Widgets\DataTable\Composables;

trait UseExport
{
    public function getExportSchema(): array
    {
        return [
            'properties' => [
                'exportable' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable export functionality'
                ],
                'exportFormats' => [
                    'type' => 'array',
                    'default' => ['csv', 'excel', 'pdf'],
                    'items' => [
                        'type' => 'string',
                        'enum' => ['csv', 'excel', 'pdf']
                    ],
                    'description' => 'Available export formats (Note: excel and pdf may require additional implementation)'
                ],
                'exportFilename' => [
                    'type' => 'string',
                    'default' => 'data-export',
                    'description' => 'Base filename for exported files (extension will be added automatically)'
                ]
            ]
        ];
    }

    public function transformExport(array $config): array
    {
        return [
            'exportable' => $config['exportable'] ?? false,
            'exportFormats' => $config['exportFormats'] ?? ['csv', 'excel', 'pdf'],
            'exportFilename' => $config['exportFilename'] ?? 'data-export'
        ];
    }
}
