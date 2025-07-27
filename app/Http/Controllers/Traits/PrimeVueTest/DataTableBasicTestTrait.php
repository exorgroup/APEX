<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DataTableBasicTestTrait
{
    protected function getBasicDataTableWidgets(): array
    {
        return [
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Basic Products Table',
                    'subtitle' => 'Simple data table with sorting and pagination'
                ],
                'columns' => [
                    [
                        'field' => 'code',
                        'header' => 'Code',
                        'sortable' => true,
                        'style' => 'width: 120px; vertical-align: top'
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'sortable' => true,
                        'dataType' => 'currency',
                        'format' => 2,
                        'leadText' => '$',
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right',
                        'style' => 'width: 100px'
                    ],
                    [
                        'field' => 'quantity',
                        'header' => 'Stock',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px'
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'style' => 'width: 120px'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false
                ],
                'gridLines' => 'both',
                'stripedRows' => true,
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'bottom',
                'rows' => 10,
                'rowsPerPageOptions' => [5, 10, 15, 20],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'single',
                'removableSort' => true,
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '400px',
                'exportable' => true,
                'exportFormats' => ['csv'],
                'exportFilename' => 'products-' . now()->format('Y-m-d'),
                'tableStyle' => 'min-width: 80rem'
            ],
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Auto Mode with Smart Search',
                    'subtitle' => 'Global search that respects column configuration'
                ],
                'columns' => [
                    [
                        'field' => 'code',
                        'header' => 'Code',
                        'sortable' => true
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true
                    ],
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right',
                        'searchExclude' => true
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'searchExclude' => true
                    ]
                ],
                'dataSource' => [
                    'url' => '/products/all',
                    'method' => 'GET',
                    'lazy' => 'auto',
                    'lazyThreshold' => 20,
                    'countUrl' => '/products/count'
                ],
                'globalFilter' => true,
                'paginator' => true,
                'rows' => 10,
                'stripedRows' => true,
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'Search works on Code, Name, and Category only'
                ],
                'tableStyle' => 'min-width: 60rem'
            ]
        ];
    }
}
