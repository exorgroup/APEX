<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DataTableAdvancedTestTrait
{
    protected function getAdvancedDataTableWidgets(): array
    {
        return [
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Products with Actions',
                    'subtitle' => 'Click on product names or use action buttons'
                ],
                'columns' => [
                    [
                        'field' => 'code',
                        'header' => 'Code',
                        'sortable' => true,
                        'frozen' => true,
                        'style' => 'width: 100px'
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'clickable' => true,
                        'url' => '/products/{id}/details',
                        'urlTarget' => '_blank',
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'clickable' => true,
                        'action' => 'filterByCategory',
                        'actionField' => 'category'
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => true
                ],
                'showView' => true,
                'showEdit' => true,
                'showDelete' => true,
                'showHistory' => true,
                'showPrint' => true,
                'crudActions' => [
                    'idField' => 'id',
                    'permissions' => [
                        'view' => true,
                        'edit' => true,
                        'delete' => false,
                        'history' => true,
                        'print' => true
                    ],
                    'routes' => [
                        'view' => '/products/{id}',
                        'edit' => '/products/{id}/edit',
                        'history' => '/products/{id}/history',
                        'print' => '/products/{id}/print'
                    ]
                ],
                'emitEvents' => [
                    'onRowClick' => 'product-selected',
                    'onDelete' => 'product-delete-requested'
                ],
                'header' => [
                    'title' => 'Products with Actions',
                    'subtitle' => 'Click on product names or use action buttons',
                    'actions' => [
                        [
                            'label' => 'Add Product',
                            'icon' => 'pi pi-plus',
                            'action' => 'add',
                            'severity' => 'success'
                        ],
                        [
                            'label' => 'Import',
                            'icon' => 'pi pi-upload',
                            'action' => 'import',
                            'severity' => 'info'
                        ],
                        [
                            'label' => 'Refresh',
                            'icon' => 'pi pi-refresh',
                            'action' => 'refresh'
                        ]
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'Click on product names to view details, categories to filter'
                ],
                'paginator' => true,
                'rows' => 8,
                'stripedRows' => true,
                'sortMode' => 'single',
                'globalFilter' => true,
                'tableStyle' => 'min-width: 70rem'
            ]
        ];
    }
}
