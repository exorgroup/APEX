<?php
// File location: app/Apex/Core/Widgets/Menu/BreadcrumbWidget.php

/*
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * Widget: APEX Breadcrumb Widget
 * Description: Breadcrumb provides contextual information about page hierarchy.
 * Implementing: PrimeVue/Menu/Breadcrumb v.4
 * 
 * Acceptable Parameters:
 * - widgetId (string, required): Unique identifier for the breadcrumb widget
 * - items (array): Array of breadcrumb navigation items
 *   - label (string, required): Display text for the breadcrumb item
 *   - url (string): URL to navigate to when clicked
 *   - icon (string): PrimeIcon class or icon identifier
 *   - disabled (boolean): Whether the breadcrumb item is disabled
 * - home (object): Home icon configuration
 *   - icon (string): PrimeIcon class for the home icon
 *   - url (string): URL for the home link
 */

namespace App\Apex\Core\Widgets\Menu;

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
