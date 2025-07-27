<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DataTableReorderTestTrait
{
    protected function getReorderDataTableWidgets(): array
    {
        return [
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'ReOrder Feature Demo - NEWEST FUNCTIONALITY!',
                    'subtitle' => 'Drag and drop to reorder both columns and rows. Export maintains your custom order.',
                    'actions' => [
                        [
                            'label' => 'Reset Order',
                            'icon' => 'pi pi-refresh',
                            'action' => 'reset-order',
                            'severity' => 'warning'
                        ],
                        [
                            'label' => 'Export Reordered',
                            'icon' => 'pi pi-download',
                            'action' => 'export-reordered',
                            'severity' => 'success'
                        ]
                    ]
                ],
                'columns' => [
                    [
                        'field' => 'code',
                        'header' => 'Product Code',
                        'sortable' => true,
                        'reorderable' => true,
                        'style' => 'width: 120px'
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'reorderable' => true,
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'reorderable' => true,
                        'style' => 'width: 120px'
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'sortable' => true,
                        'reorderable' => true,
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
                        'reorderable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px'
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'reorderable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'reorderable' => true,
                        'style' => 'width: 120px'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false
                ],
                'reorderableColumns' => true,
                'reorderableRows' => true,
                'columnReorderMode' => 'dragdrop',
                'rowReorderMode' => 'dragdrop',
                'rowReorderField' => 'displayOrder',
                'rowReorderIcon' => 'pi pi-bars',
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
                'selectionMode' => 'single',
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '400px',
                'exportable' => true,
                'exportFormats' => ['csv', 'xlsx'],
                'exportFilename' => 'reordered-products-' . now()->format('M d, Y H:i'),
                'tableStyle' => 'min-width: 80rem',
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'Drag column headers to reorder columns, drag row handles to reorder rows'
                ]
            ]
        ];
    }
}
