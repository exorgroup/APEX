<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DataTableGroupingTestTrait
{
    protected function getGroupingDataTableWidgets(): array
    {
        return [
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Row Grouping Demo - Subheader Mode',
                    'subtitle' => 'Products grouped by category with subheaders'
                ],
                'columns' => [
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
                'rowGrouping' => [
                    'enabled' => true,
                    'mode' => 'subheader',
                    'groupField' => 'category',
                    'expandableRowGroups' => true,
                    'sortField' => 'category',
                    'sortOrder' => 1
                ],
                'paginator' => true,
                'rows' => 15,
                'stripedRows' => true,
                'tableStyle' => 'min-width: 60rem'
            ],
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Column Grouping Demo - Header & Footer Groups',
                    'subtitle' => 'Columns grouped with headers and calculated totals in footers',
                    'actions' => [
                        [
                            'label' => 'Export Analysis',
                            'icon' => 'pi pi-download',
                            'action' => 'export-analysis',
                            'severity' => 'info'
                        ]
                    ]
                ],
                'columns' => [
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 120px'
                    ],
                    [
                        'field' => 'code',
                        'header' => 'Product Code',
                        'sortable' => true,
                        'style' => 'width: 120px'
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
                'columnGrouping' => [
                    'enabled' => true,
                    'headerGroups' => [
                        [
                            'cells' => [
                                [
                                    'header' => 'Product Information',
                                    'colspan' => 7,
                                    'headerStyle' => 'text-align: center; background-color: #f3f4f6; font-weight: bold;'
                                ]
                            ]
                        ],
                        [
                            'cells' => [
                                [
                                    'header' => 'Category',
                                    'field' => 'category',
                                    'sortable' => true
                                ],
                                [
                                    'header' => 'Code',
                                    'field' => 'code',
                                    'sortable' => true
                                ],
                                [
                                    'header' => 'Identity',
                                    'field' => 'name',
                                    'sortable' => true
                                ],
                                [
                                    'header' => 'Price',
                                    'field' => 'price',
                                    'sortable' => true
                                ],
                                [
                                    'header' => 'Quantity',
                                    'field' => 'quantity',
                                    'sortable' => true
                                ],
                                [
                                    'header' => 'Rating',
                                    'field' => 'rating',
                                    'sortable' => true
                                ],
                                [
                                    'header' => 'Status',
                                    'field' => 'inventoryStatus',
                                    'sortable' => true
                                ]
                            ]
                        ]
                    ],
                    'footerGroups' => [
                        [
                            'cells' => [
                                [
                                    'footer' => 'TOTALS:',
                                    'colspan' => 3,
                                    'footerStyle' => 'text-align: right; font-weight: bold; background-color: #f9fafb;'
                                ],
                                [
                                    'isTotal' => true,
                                    'totalField' => 'price',
                                    'totalType' => 'sum',
                                    'formatType' => 'currency',
                                    'formatDecimals' => 2,
                                    'footerStyle' => 'text-align: right; font-weight: bold; background-color: #dbeafe;'
                                ],
                                [
                                    'isTotal' => true,
                                    'totalField' => 'quantity',
                                    'totalType' => 'sum',
                                    'formatType' => 'number',
                                    'formatDecimals' => 0,
                                    'footerStyle' => 'text-align: center; font-weight: bold; background-color: #dbeafe;'
                                ],
                                [
                                    'isTotal' => true,
                                    'totalField' => 'rating',
                                    'totalType' => 'avg',
                                    'formatType' => 'number',
                                    'formatDecimals' => 1,
                                    'footerStyle' => 'text-align: center; font-weight: bold; background-color: #dbeafe;'
                                ],
                                [
                                    'footer' => 'Items',
                                    'footerStyle' => 'text-align: center; font-weight: bold; background-color: #f9fafb;'
                                ]
                            ]
                        ]
                    ]
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
                'exportFormats' => ['csv'],
                'exportFilename' => 'grouped-analysis-' . now()->format('M d, Y H:i'),
                'tableStyle' => 'min-width: 80rem'
            ],
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Row Grouping Demo - Rowspan Mode',
                    'subtitle' => 'Products grouped by category using rowspan (merged cells)'
                ],
                'columns' => [
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 120px'
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
                'rowGrouping' => [
                    'enabled' => true,
                    'mode' => 'rowspan',
                    'groupField' => 'category',
                    'sortField' => 'category',
                    'sortOrder' => 1
                ],
                'paginator' => true,
                'rows' => 15,
                'stripedRows' => true,
                'tableStyle' => 'min-width: 60rem'
            ]
        ];
    }
}
