<?php
// app/Apex/Pro/Widgets/DataTable/Traits/HasDataFormatting.php

namespace App\Apex\Pro\Widgets\DataTable\Traits;

trait HasDataFormatting
{
    public function getDataFormattingSchema(): array
    {
        return [
            'properties' => [
                'columns' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'dataType' => [
                                'type' => 'string',
                                'enum' => ['text', 'number', 'currency', 'shortdate', 'longdate1', 'longdate2', 'time', 'shortdatetime', 'longdate1time', 'longdate2time', 'percentage', 'image'],
                                'default' => 'text',
                                'description' => 'Column data type for formatting'
                            ],
                            'format' => [
                                'type' => ['string', 'integer'],
                                'description' => 'Format parameter based on data type'
                            ],
                            'leadText' => [
                                'type' => 'string',
                                'description' => 'Text to prepend to the value'
                            ],
                            'trailText' => [
                                'type' => 'string',
                                'description' => 'Text to append to the value'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformDataFormatting(array $config): array
    {
        return [];
    }

    public function transformDataFormattingColumns(array $columns): array
    {
        return array_map(function ($column) {
            return array_merge($column, [
                'dataType' => $column['dataType'] ?? 'text',
                'format' => $column['format'] ?? null,
                'leadText' => $column['leadText'] ?? '',
                'trailText' => $column['trailText'] ?? ''
            ]);
        }, $columns);
    }
}
