<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasStateful.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasStateful
{
    public function getStatefulSchema(): array
    {
        return [
            'properties' => [
                'stateStorage' => [
                    'type' => 'string',
                    'enum' => ['session', 'local'],
                    'description' => 'State persistence'
                ],
                'stateKey' => [
                    'type' => 'string',
                    'description' => 'Unique key for state storage'
                ]
            ]
        ];
    }

    public function transformStateful(array $config): array
    {
        return [
            'stateStorage' => $config['stateStorage'] ?? null,
            'stateKey' => $config['stateKey'] ?? null
        ];
    }
}
