<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DataTableColumnFeaturesTestTrait
{
    protected function getColumnFeaturesDataTableWidgets(): array
    {
        return [
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Products with Column Locking - NEWEST FEATURE DEMO',
                    'subtitle' => 'Lock important columns for better analysis. Columns can be individually locked/unlocked.',
                    'actions' => [
                        [
                            'label' => 'Lock Important Columns',
                            'icon' => 'pi pi-lock',
                            'action' => 'lock-important',
                            'severity' => 'warning'
                        ],
                        [
                            'label' => 'Unlock All Columns',
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
                        'style' => 'width: 120px',
                        'lockColumn' => true,
                        'lockButton' => true
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'style' => 'min-width: 200px',
                        'lockColumn' => true,
                        'lockButton' => true
                    ],
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 120px',
                        'lockColumn' => true,
                        'lockButton' => true
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
                        'style' => 'width: 100px',
                        'lockColumn' => true,
                        'lockButton' => true
                    ],
                    [
                        'field' => 'quantity',
                        'header' => 'Stock',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px',
                        'lockColumn' => true,
                        'lockButton' => true
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
                'columnLocking' => [
                    'enabled' => true,
                    'maxLockedColumns' => 4,
                    'lockedColumnClasses' => 'bg-blue-50 border-r-2 border-blue-300',
                    'lockedColumnStyles' => [
                        'backgroundColor' => '#eff6ff',
                        'borderRight' => '2px solid #93c5fd'
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
                'exportFilename' => 'column-locked-products-' . now()->format('M d, Y H:i'),
                'tableStyle' => 'min-width: 80rem'
            ]
        ];
    }
}
