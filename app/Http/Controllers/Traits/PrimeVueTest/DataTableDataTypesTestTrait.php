<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DataTableDataTypesTestTrait
{
    protected function getDataTypesDataTableWidgets(): array
    {
        return [
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Data Types Demo - Format Showcase',
                    'subtitle' => 'Demonstrates different data types and formatting options available in the DataTable widget'
                ],
                'columns' => [
                    [
                        'field' => 'id',
                        'header' => 'ID',
                        'sortable' => true,
                        'dataType' => 'number',
                        'style' => 'width: 80px'
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'dataType' => 'text',
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price (USD)',
                        'sortable' => true,
                        'dataType' => 'currency',
                        'format' => 2,
                        'leadText' => '$',
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right',
                        'style' => 'width: 120px'
                    ],
                    [
                        'field' => 'priceEuro',
                        'header' => 'Price (EUR)',
                        'sortable' => true,
                        'dataType' => 'currency',
                        'format' => 2,
                        'trailText' => '€',
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right',
                        'style' => 'width: 120px'
                    ],
                    [
                        'field' => 'discount',
                        'header' => 'Discount',
                        'sortable' => true,
                        'dataType' => 'percentage',
                        'format' => 1,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 100px'
                    ],
                    [
                        'field' => 'createdDate',
                        'header' => 'Created Date',
                        'sortable' => true,
                        'dataType' => 'date',
                        'dateFormat' => 'short',
                        'style' => 'width: 120px'
                    ],
                    [
                        'field' => 'lastModified',
                        'header' => 'Last Modified',
                        'sortable' => true,
                        'dataType' => 'datetime',
                        'dateFormat' => 'long',
                        'timeFormat' => '24h',
                        'style' => 'width: 180px'
                    ],
                    [
                        'field' => 'description',
                        'header' => 'Description',
                        'sortable' => false,
                        'dataType' => 'text',
                        'truncate' => 50,
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'image',
                        'header' => 'Image',
                        'sortable' => false,
                        'dataType' => 'image',
                        'imageWidth' => '50px',
                        'style' => 'width: 80px'
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'dataType' => 'widget',
                        'widgetType' => 'knob',
                        'widgetProps' => [
                            'size' => 40,
                            'strokeWidth' => 6,
                            'showValue' => false,
                            'valueColor' => '#10b981',
                            'rangeColor' => '#d1fae5',
                            'min' => 0,
                            'max' => 5
                        ],
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products/datatypes',
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
                'scrollHeight' => '500px',
                'exportable' => true,
                'exportFormats' => ['csv', 'xlsx'],
                'exportFilename' => 'datatypes-demo-' . now()->format('Y-m-d'),
                'tableStyle' => 'min-width: 100rem'
            ]
        ];
    }
}
