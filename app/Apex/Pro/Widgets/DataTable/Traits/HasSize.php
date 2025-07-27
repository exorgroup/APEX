<?php
// app/Apex/Pro/Widgets/DataTable/Traits/HasSize.php

namespace App\Apex\Pro\Widgets\DataTable\Traits;

trait HasSize
{
    public function getSizeSchema(): array
    {
        return [
            'properties' => [
                'size' => [
                    'type' => 'string',
                    'enum' => ['small', 'normal', 'large'],
                    'default' => 'normal',
                    'description' => 'Table size'
                ]
            ]
        ];
    }

    public function transformSize(array $config): array
    {
        return [
            'size' => $config['size'] ?? 'normal'
        ];
    }
}
