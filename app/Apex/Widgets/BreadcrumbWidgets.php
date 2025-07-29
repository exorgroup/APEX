<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class BreadcrumbWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'breadcrumb';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the breadcrumb widget'
                ],
                'items' => [
                    'type' => 'array',
                    'description' => 'Array of breadcrumb items',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'label' => [
                                'type' => 'string',
                                'required' => true
                            ],
                            'url' => [
                                'type' => 'string'
                            ],
                            'icon' => [
                                'type' => 'string'
                            ],
                            'disabled' => [
                                'type' => 'boolean',
                                'default' => false
                            ]
                        ]
                    ]
                ],
                'home' => [
                    'type' => 'object',
                    'description' => 'Home icon configuration',
                    'properties' => [
                        'icon' => [
                            'type' => 'string',
                            'default' => 'pi pi-home'
                        ],
                        'url' => [
                            'type' => 'string',
                            'default' => '/'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transform(array $config): array
    {
        $items = $config['items'] ?? [];
        $home = $config['home'] ?? null;

        // Process items to ensure they have proper structure
        $processedItems = array_map(function ($item) {
            return [
                'label' => $item['label'] ?? '',
                'url' => $item['url'] ?? null,
                'icon' => $item['icon'] ?? null,
                'disabled' => $item['disabled'] ?? false,
            ];
        }, $items);

        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'items' => $processedItems,
            'home' => $home,
        ];
    }
}
