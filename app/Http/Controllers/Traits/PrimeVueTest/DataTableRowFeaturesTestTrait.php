<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DataTableRowFeaturesTestTrait
{
    protected function getRowFeaturesDataTableWidgets(): array
    {
        return [
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Products with Order History - Row Expansion Demo',
                    'subtitle' => 'Click the expand icon to view order history for each product',
                    'actions' => [
                        [
                            'label' => 'Add Product',
                            'icon' => 'pi pi-plus',
                            'action' => 'add',
                            'severity' => 'success'
                        ],
                        [
                            'label' => 'Import Orders',
                            'icon' => 'pi pi-upload',
                            'action' => 'import',
                            'severity' => 'info'
                        ],
                        [
                            'label' => 'Refresh Data',
                            'icon' => 'pi pi-refresh',
                            'action' => 'refresh'
                        ]
                    ]
                ],
                'columns' => [
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
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 120px'
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
                    'url' => '/products/with-orders',
                    'method' => 'GET',
                    'lazy' => false
                ],
                'rowExpansion' => [
                    'enabled' => true,
                    'template' => 'order-history',
                    'expandedRowKeys' => [],
                    'dataField' => 'orders'
                ],
                'gridLines' => 'both',
                'stripedRows' => true,
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'bottom',
                'rows' => 8,
                'rowsPerPageOptions' => [5, 8, 10, 15],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'single',
                'removableSort' => true,
                'selectionMode' => 'single',
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '400px',
                'exportable' => true,
                'exportFormats' => ['csv'],
                'exportFilename' => 'products-with-orders-' . now()->format('M d, Y H:i'),
                'tableStyle' => 'min-width: 80rem'
            ],
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Products with Row Locking - Latest Feature Demo',
                    'subtitle' => 'Lock important rows for comparison. Max 3 rows can be locked.',
                    'actions' => [
                        [
                            'label' => 'Lock All Available',
                            'icon' => 'pi pi-lock',
                            'action' => 'lock-all',
                            'severity' => 'warning'
                        ],
                        [
                            'label' => 'Unlock All',
                            'icon' => 'pi pi-unlock',
                            'action' => 'unlock-all',
                            'severity' => 'help'
                        ]
                    ]
                ],
                'columns' => [
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
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 120px'
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
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false
                ],
                'rowLocking' => [
                    'enabled' => true,
                    'maxLockedRows' => 3,
                    'lockColumn' => [
                        'style' => 'width: 4rem',
                        'frozen' => true,
                        'header' => 'Lock'
                    ],
                    'lockedRowClasses' => 'font-bold bg-blue-50 border-l-4 border-blue-500',
                    'lockedRowStyles' => [
                        'backgroundColor' => '#eff6ff',
                        'borderLeft' => '4px solid #3b82f6'
                    ]
                ],
                'gridLines' => 'both',
                'stripedRows' => true,
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'bottom',
                'rows' => 8,
                'rowsPerPageOptions' => [5, 8, 10, 15],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'single',
                'removableSort' => true,
                'selectionMode' => 'single',
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '400px',
                'exportable' => true,
                'exportFormats' => ['csv'],
                'exportFilename' => 'locked-products-' . now()->format('M d, Y H:i'),
                'tableStyle' => 'min-width: 80rem'
            ]
        ];
    }
}
