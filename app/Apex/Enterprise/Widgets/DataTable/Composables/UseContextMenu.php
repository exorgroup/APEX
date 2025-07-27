<?php
// app/Apex/Enterprise/Widgets/DataTable/Composables/UseContextMenu.php

namespace App\Apex\Enterprise\Widgets\DataTable\Composables;

trait UseContextMenu
{
    public function getContextMenuSchema(): array
    {
        return [
            'properties' => [
                'contextMenu' => [
                    'type' => 'object',
                    'description' => 'Context menu configuration for right-click actions',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable context menu functionality'
                        ],
                        'items' => [
                            'type' => 'array',
                            'description' => 'Array of context menu items',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'icon' => [
                                        'type' => 'string',
                                        'description' => 'Icon for menu item - can be URL to image/SVG or PrimeIcon class (e.g., "pi pi-search")'
                                    ],
                                    'label' => [
                                        'type' => 'string',
                                        'required' => true,
                                        'description' => 'Text label for the menu item'
                                    ],
                                    'url' => [
                                        'type' => 'string',
                                        'description' => 'URL to navigate to, supports field placeholders like /product/{productID}'
                                    ],
                                    'urlTarget' => [
                                        'type' => 'string',
                                        'enum' => ['_self', '_blank', '_parent', '_top'],
                                        'default' => '_self',
                                        'description' => 'Link target for URL navigation'
                                    ],
                                    'action' => [
                                        'type' => 'string',
                                        'description' => 'Custom action name to emit when clicked'
                                    ],
                                    'separator' => [
                                        'type' => 'boolean',
                                        'default' => false,
                                        'description' => 'Show separator after this menu item'
                                    ],
                                    'disabled' => [
                                        'type' => 'boolean',
                                        'default' => false,
                                        'description' => 'Disable this menu item'
                                    ],
                                    'visible' => [
                                        'type' => 'boolean',
                                        'default' => true,
                                        'description' => 'Show/hide this menu item'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformContextMenu(array $config): array
    {
        $contextMenu = $config['contextMenu'] ?? [];

        if (!empty($contextMenu) && $contextMenu['enabled']) {
            // Process menu items
            if (isset($contextMenu['items'])) {
                $contextMenu['items'] = array_map(function ($item) {
                    // Handle separator items that only have 'separator' property
                    if (isset($item['separator']) && $item['separator'] === true) {
                        return ['separator' => true];
                    }

                    return [
                        'icon' => $item['icon'] ?? null,
                        'label' => $item['label'] ?? '',
                        'url' => $item['url'] ?? null,
                        'urlTarget' => $item['urlTarget'] ?? '_self',
                        'action' => $item['action'] ?? null,
                        'separator' => $item['separator'] ?? false,
                        'disabled' => $item['disabled'] ?? false,
                        'visible' => $item['visible'] ?? true,
                    ];
                }, $contextMenu['items']);
            }
        } else {
            $contextMenu = [
                'enabled' => false,
                'items' => []
            ];
        }

        return [
            'contextMenu' => $contextMenu
        ];
    }
}
