<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasTemplate.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasTemplate
{
    public function getTemplateSchema(): array
    {
        return [
            'properties' => [
                'header' => [
                    'type' => 'object',
                    'description' => 'Header configuration',
                    'properties' => [
                        'title' => ['type' => 'string'],
                        'subtitle' => ['type' => 'string'],
                        'actions' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'label' => ['type' => 'string'],
                                    'icon' => ['type' => 'string'],
                                    'action' => ['type' => 'string'],
                                    'severity' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                'footer' => [
                    'type' => 'object',
                    'description' => 'Footer configuration',
                    'properties' => [
                        'showRecordCount' => ['type' => 'boolean', 'default' => true],
                        'text' => ['type' => 'string'],
                        'showSelectedCount' => ['type' => 'boolean', 'default' => true]
                    ]
                ]
            ]
        ];
    }

    public function transformTemplate(array $config): array
    {
        return [
            'header' => $config['header'] ?? null,
            'footer' => $config['footer'] ?? ['showRecordCount' => true, 'showSelectedCount' => true]
        ];
    }
}
