<?php
// app/Apex/Core/Widgets/DataTable/DataTableWidget.php

namespace App\Apex\Core\Widgets\DataTable;

use App\Apex\Core\Widget\BaseWidget;
use App\Apex\Core\Widgets\DataTable\Traits\HasDynamicColumns;
use App\Apex\Core\Widgets\DataTable\Traits\HasTemplate;
use App\Apex\Core\Widgets\DataTable\Traits\HasStripedRows;
use App\Apex\Core\Widgets\DataTable\Traits\HasPagination;
use App\Apex\Core\Widgets\DataTable\Traits\HasPaginationTemplate;
use App\Apex\Core\Widgets\DataTable\Traits\HasSingleColumnSort;
use App\Apex\Core\Widgets\DataTable\Traits\HasSingleRowSelection;
use App\Apex\Core\Widgets\DataTable\Traits\HasScroll;
use App\Apex\Core\Widgets\DataTable\Traits\HasGridLines;
use App\Apex\Core\Widgets\DataTable\Traits\HasColumnResize;
use App\Apex\Core\Widgets\DataTable\Traits\HasSearch;
use App\Apex\Core\Widgets\DataTable\Traits\HasStateful;

class DataTableWidget extends BaseWidget
{
    use HasDynamicColumns;
    use HasTemplate;
    use HasStripedRows;
    use HasPagination;
    use HasPaginationTemplate;
    use HasSingleColumnSort;
    use HasSingleRowSelection;
    use HasScroll;
    use HasGridLines;
    use HasColumnResize;
    use HasSearch;
    use HasStateful;

    public function getType(): string
    {
        return 'datatable';
    }

    public function getSchema(): array
    {
        return array_merge(
            $this->getBaseSchema(),
            $this->getDynamicColumnsSchema(),
            $this->getTemplateSchema(),
            $this->getStripedRowsSchema(),
            $this->getPaginationSchema(),
            $this->getPaginationTemplateSchema(),
            $this->getSingleColumnSortSchema(),
            $this->getSingleRowSelectionSchema(),
            $this->getScrollSchema(),
            $this->getGridLinesSchema(),
            $this->getColumnResizeSchema(),
            $this->getSearchSchema(),
            $this->getStatefulSchema()
        );
    }

    protected function getBaseSchema(): array
    {
        return [
            'properties' => [
                'widgetId' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Unique widget identifier'
                ],
                'dataKey' => [
                    'type' => 'string',
                    'default' => 'id',
                    'description' => 'Unique identifier field'
                ],
                'loading' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show loading state'
                ],
                'emptyMessage' => [
                    'type' => 'string',
                    'default' => 'No records found',
                    'description' => 'Empty state message'
                ],
                'tableStyle' => [
                    'type' => 'string',
                    'default' => 'min-width: 50rem',
                    'description' => 'Table style attribute'
                ],
                'tableClass' => [
                    'type' => 'string',
                    'description' => 'Additional CSS classes for table'
                ],
                'responsiveLayout' => [
                    'type' => 'string',
                    'enum' => ['scroll', 'stack'],
                    'default' => 'scroll',
                    'description' => 'Responsive behavior'
                ]
            ]
        ];
    }

    public function transform(array $config): array
    {
        $processedColumns = $this->transformDynamicColumns($config['columns'] ?? []);

        return array_merge(parent::transform($config), [
            'props' => [
                'widgetId' => $this->getId(),
                'dataKey' => $config['dataKey'] ?? 'id',
                'loading' => $config['loading'] ?? false,
                'emptyMessage' => $config['emptyMessage'] ?? 'No records found',
                'tableStyle' => $config['tableStyle'] ?? 'min-width: 50rem',
                'tableClass' => $config['tableClass'] ?? null,
                'responsiveLayout' => $config['responsiveLayout'] ?? 'scroll',

                // Core trait transformations
                'columns' => $processedColumns,
                ...$this->transformTemplate($config),
                ...$this->transformStripedRows($config),
                ...$this->transformPagination($config),
                ...$this->transformPaginationTemplate($config),
                ...$this->transformSingleColumnSort($config),
                ...$this->transformSingleRowSelection($config),
                ...$this->transformScroll($config),
                ...$this->transformGridLines($config),
                ...$this->transformColumnResize($config),
                ...$this->transformSearch($config),
                ...$this->transformStateful($config)
            ]
        ]);
    }
}
