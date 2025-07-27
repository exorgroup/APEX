<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasScroll.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasScroll
{
    public function getScrollSchema(): array
    {
        return [
            'properties' => [
                'scrollable' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable scrolling'
                ],
                'scrollHeight' => [
                    'type' => 'string',
                    'default' => 'flex',
                    'description' => 'Scroll height'
                ],
                'virtualScroll' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable virtual scrolling for large datasets'
                ]
            ]
        ];
    }

    public function transformScroll(array $config): array
    {
        return [
            'scrollable' => $config['scrollable'] ?? false,
            'scrollHeight' => $config['scrollHeight'] ?? 'flex',
            'virtualScroll' => $config['virtualScroll'] ?? false
        ];
    }
}
