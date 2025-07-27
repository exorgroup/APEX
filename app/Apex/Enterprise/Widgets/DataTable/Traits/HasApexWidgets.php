<?php
// app/Apex/Enterprise/Widgets/DataTable/Traits/HasApexWidgets.php

namespace App\Apex\Enterprise\Widgets\DataTable\Traits;

trait HasApexWidgets
{
    public function getApexWidgetsSchema(): array
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
                                'enum' => ['text', 'number', 'currency', 'shortdate', 'longdate1', 'longdate2', 'time', 'shortdatetime', 'longdate1time', 'longdate2time', 'percentage', 'image', 'apexwidget'],
                                'default' => 'text',
                                'description' => 'Column data type for formatting - Enterprise adds apexwidget'
                            ],
                            'widgetConfig' => [
                                'type' => 'object',
                                'description' => 'Configuration for ApexWidget data type'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformApexWidgets(array $config): array
    {
        return [];
    }

    public function transformApexWidgetColumns(array $columns): array
    {
        return array_map(function ($column) {
            if (($column['dataType'] ?? 'text') === 'apexwidget') {
                $column['widgetConfig'] = $column['widgetConfig'] ?? null;
            }
            return $column;
        }, $columns);
    }
}
