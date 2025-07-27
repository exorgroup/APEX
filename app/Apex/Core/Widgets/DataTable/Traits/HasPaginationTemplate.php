<?php
// app/Apex/Core/Widgets/DataTable/Traits/HasPaginationTemplate.php

namespace App\Apex\Core\Widgets\DataTable\Traits;

trait HasPaginationTemplate
{
    public function getPaginationTemplateSchema(): array
    {
        return [
            'properties' => [
                'currentPageReportTemplate' => [
                    'type' => 'string',
                    'default' => 'Showing {first} to {last} of {totalRecords} entries',
                    'description' => 'Page report template'
                ]
            ]
        ];
    }

    public function transformPaginationTemplate(array $config): array
    {
        return [
            'currentPageReportTemplate' => $config['currentPageReportTemplate'] ?? 'Showing {first} to {last} of {totalRecords} entries'
        ];
    }
}
