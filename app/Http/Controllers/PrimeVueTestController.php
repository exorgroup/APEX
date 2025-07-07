<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Apex\Core\Widget\WidgetRenderer;
use Inertia\Inertia;

class PrimeVueTestController extends Controller
{
    public function index()
    {
        $widgetRenderer = app(WidgetRenderer::class);

        $widgets = [
            // Existing breadcrumb widget
            [
                'type' => 'breadcrumb',
                'items' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Components', 'url' => '/components'],
                    ['label' => 'PrimeVue Test'],
                ],
                'home' => ['icon' => 'pi pi-home', 'url' => '/']
            ],

            // Existing knob widgets
            [
                'type' => 'knob',
                'value' => 20,
                'min' => 0,
                'max' => 40,
                'step' => 1,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#ef4444',
                'rangeColor' => '#fecaca',
                'valueTemplate' => '{value}°C',
                'label' => 'Temperature',
                'readonly' => false
            ],
            [
                'type' => 'knob',
                'value' => 50,
                'min' => 0,
                'max' => 100,
                'step' => 10,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#3b82f6',
                'rangeColor' => '#dbeafe',
                'valueTemplate' => '{value}%',
                'label' => 'Volume',
                'readonly' => false
            ],
            [
                'type' => 'knob',
                'value' => 75,
                'min' => 0,
                'max' => 100,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#10b981',
                'rangeColor' => '#d1fae5',
                'valueTemplate' => '{value}%',
                'label' => 'Progress',
                'readonly' => true
            ],

            // Existing datepicker widgets
            [
                'type' => 'datepicker',
                'placeholder' => 'Select a date',
                'dateFormat' => 'mm/dd/yy',
                'showIcon' => true,
                'showButtonBar' => true,
                'label' => 'Basic Date'
            ],
            [
                'type' => 'datepicker',
                'placeholder' => 'Select date range',
                'selectionMode' => 'range',
                'dateFormat' => 'mm/dd/yy',
                'showIcon' => true,
                'label' => 'Date Range'
            ],
            [
                'type' => 'datepicker',
                'placeholder' => 'Pick multiple dates',
                'selectionMode' => 'multiple',
                'dateFormat' => 'mm/dd/yy',
                'showIcon' => true,
                'label' => 'Multiple Dates'
            ],

            // COMPREHENSIVE DataTable with all features
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Product Inventory Management',
                    'subtitle' => 'Complete DataTable with all features enabled',
                    'actions' => [
                        ['label' => 'Add Product', 'icon' => 'pi pi-plus', 'action' => 'add', 'severity' => 'success'],
                        ['label' => 'Import', 'icon' => 'pi pi-upload', 'action' => 'import', 'severity' => 'info']
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'showSelectedCount' => true,
                    'text' => 'Last updated: ' . now()->format('M d, Y H:i')
                ],
                'columns' => [
                    [
                        'field' => 'code',
                        'header' => 'Code',
                        'sortable' => true,
                        'filter' => true,
                        'filterType' => 'text',
                        'frozen' => true,
                        'style' => 'width: 100px'
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'filter' => true,
                        'filterType' => 'text',
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'filter' => true,
                        'filterType' => 'dropdown',
                        'filterOptions' => [
                            ['label' => 'Accessories', 'value' => 'Accessories'],
                            ['label' => 'Clothing', 'value' => 'Clothing'],
                            ['label' => 'Electronics', 'value' => 'Electronics'],
                            ['label' => 'Fitness', 'value' => 'Fitness']
                        ]
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'sortable' => true,
                        'filter' => true,
                        'filterType' => 'numeric',
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right'
                    ],
                    [
                        'field' => 'quantity',
                        'header' => 'Stock',
                        'sortable' => true,
                        'filter' => true,
                        'filterType' => 'numeric',
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'filter' => true,
                        'filterType' => 'multiselect',
                        'filterOptions' => [
                            ['label' => 'In Stock', 'value' => 'INSTOCK'],
                            ['label' => 'Low Stock', 'value' => 'LOWSTOCK'],
                            ['label' => 'Out of Stock', 'value' => 'OUTOFSTOCK']
                        ]
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'filter' => true,
                        'filterType' => 'numeric',
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => true,
                    'preload' => true
                ],
                'gridLines' => 'both',
                'stripedRows' => true,
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'both',
                'rows' => 10,
                'rowsPerPageOptions' => [5, 10, 25, 50, 100],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'multiple',
                'removableSort' => true,
                'multiSortMeta' => [
                    ['field' => 'category', 'order' => 1],
                    ['field' => 'price', 'order' => -1]
                ],
                'selectionMode' => 'checkbox',
                'selectAll' => true,
                'metaKeySelection' => false,
                'groupActions' => [
                    [
                        'label' => 'Delete Selected',
                        'icon' => 'pi pi-trash',
                        'action' => 'delete',
                        'severity' => 'danger',
                        'confirm' => true,
                        'confirmMessage' => 'Are you sure you want to delete the selected products?'
                    ],
                    [
                        'label' => 'Export Selected',
                        'icon' => 'pi pi-download',
                        'action' => 'export-selected',
                        'severity' => 'info'
                    ],
                    [
                        'label' => 'Update Status',
                        'icon' => 'pi pi-refresh',
                        'action' => 'update-status',
                        'severity' => 'warning'
                    ]
                ],
                'filterDisplay' => 'row',
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '400px',
                'frozenColumns' => 1,
                'reorderableColumns' => true,
                'reorderableRows' => false,
                'exportable' => true,
                'exportFormats' => ['csv', 'excel', 'pdf'],
                'exportFilename' => 'product-inventory-' . date('Y-m-d'),
                'responsiveLayout' => 'scroll',
                'stateStorage' => 'local',
                'stateKey' => 'dt-state-products',
                'tableStyle' => 'min-width: 80rem',
                'emptyMessage' => 'No products found matching your criteria'
            ],

            // SIMPLE CLIENT-SIDE DataTable
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Client-Side DataTable',
                    'subtitle' => 'All data loaded at once'
                ],
                'columns' => [
                    ['field' => 'code', 'header' => 'Code', 'sortable' => true],
                    ['field' => 'name', 'header' => 'Product Name', 'sortable' => true],
                    ['field' => 'category', 'header' => 'Category', 'sortable' => true],
                    ['field' => 'price', 'header' => 'Price', 'sortable' => true]
                ],
                'dataSource' => [
                    'url' => '/products/mini',
                    'method' => 'GET',
                    'lazy' => false
                ],
                'paginator' => true,
                'rows' => 5,
                'stripedRows' => true,
                'tableStyle' => 'min-width: 50rem'
            ],

            // AUTO MODE DataTable
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Auto Mode DataTable',
                    'subtitle' => 'Automatically chooses client or server processing'
                ],
                'columns' => [
                    ['field' => 'code', 'header' => 'Code', 'sortable' => true],
                    ['field' => 'name', 'header' => 'Product Name', 'sortable' => true],
                    ['field' => 'category', 'header' => 'Category', 'sortable' => true],
                    ['field' => 'price', 'header' => 'Price', 'sortable' => true]
                ],
                'dataSource' => [
                    'url' => '/products/mini',
                    'method' => 'GET',
                    'lazy' => 'auto',
                    'lazyThreshold' => 10
                ],
                'paginator' => true,
                'rows' => 5,
                'stripedRows' => true,
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'Threshold: 10 records'
                ],
                'tableStyle' => 'min-width: 50rem'
            ],

            // AUTO MODE with Smart Search
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
            ],

            // DataTable with Action Handlers
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
                        'url' => '/products/{id}/details',  // URL with placeholder
                        'urlTarget' => '_blank',
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'clickable' => true,
                        'action' => 'filterByCategory',  // Custom action
                        'actionField' => 'category'       // Use category as action parameter
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
                // Enable CRUD actions
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
                        'delete' => false,  // Delete disabled by permission
                        'history' => true,
                        'print' => true
                    ],
                    'routes' => [
                        'view' => '/products/{id}',
                        'edit' => '/products/{id}/edit',
                        // Delete will emit event instead of route
                        'history' => '/products/{id}/history',
                        'print' => '/products/{id}/print'
                    ]
                ],
                // Other settings
                'paginator' => true,
                'rows' => 10,
                'stripedRows' => true,
                'globalFilter' => true,
                'columnToggle' => true,
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'Click product names for details, categories to filter'
                ],
                'tableStyle' => 'min-width: 70rem'
            ],

            // DataTable with Various Data Types
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Data Type Formatting Demo',
                    'subtitle' => 'Different column data types with formatting'
                ],
                'columns' => [
                    [
                        'field' => 'name',
                        'header' => 'Product',
                        'dataType' => 'text',
                        'format' => 20,  // Trim at 20 characters
                        'sortable' => true
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'dataType' => 'currency',
                        'format' => 2,  // 2 decimal places
                        'leadText' => '$',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right'
                    ],
                    [
                        'field' => 'cost',
                        'header' => 'Cost (EUR)',
                        'dataType' => 'currency',
                        'format' => 2,
                        'trailText' => ' €',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right'
                    ],
                    [
                        'field' => 'discount',
                        'header' => 'Discount',
                        'dataType' => 'percentage',
                        'format' => 1,  // 1 decimal place
                        'trailText' => '%',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center'
                    ],
                    [
                        'field' => 'quantity',
                        'header' => 'Stock',
                        'dataType' => 'number',
                        'format' => 0,  // No decimals
                        'leadText' => '',
                        'trailText' => ' units',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center'
                    ],
                    [
                        'field' => 'createdDate',
                        'header' => 'Created (US)',
                        'dataType' => 'shortdate',
                        'format' => 'US',
                        'sortable' => true
                    ],
                    [
                        'field' => 'createdDate',
                        'header' => 'Created (EU)',
                        'dataType' => 'longdate1',
                        'format' => 'EU',
                        'sortable' => true
                    ],
                    [
                        'field' => 'lastUpdate',
                        'header' => 'Last Update',
                        'dataType' => 'shortdatetime',
                        'format' => 'US-12',  // US format, 12-hour
                        'sortable' => true
                    ],
                    [
                        'field' => 'nextReview',
                        'header' => 'Next Review',
                        'dataType' => 'longdate2time',
                        'format' => 'EU-24',  // EU format, 24-hour
                        'sortable' => true
                    ],
                    [
                        'field' => 'openTime',
                        'header' => 'Opens At',
                        'dataType' => 'time',
                        'format' => '12',  // 12-hour format
                        'sortable' => true
                    ],
                    [
                        'field' => 'image',
                        'header' => 'Image',
                        'dataType' => 'image',
                        'format' => 50,  // 50px width
                        'sortable' => false,
                        'exportable' => false
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'dataType' => 'apexwidget',
                        'widgetConfig' => [
                            'type' => 'knob',
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                            'size' => 50,
                            'showValue' => true,
                            'readonly' => true,
                            'valueColor' => '#10b981',
                            'rangeColor' => '#d1fae5'
                        ],
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'exportable' => false
                    ]
                ],
                'dataSource' => [
                    'url' => '/products/datatypes',  // You'll need to create this endpoint
                    'method' => 'GET',
                    'lazy' => false
                ],
                'paginator' => true,
                'rows' => 5,
                'stripedRows' => true,
                'globalFilter' => true,
                'columnToggle' => true,
                'resizableColumns' => true,
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'Various data type formatting examples'
                ],
                'tableStyle' => 'min-width: 90rem'
            ],

            // DD20250710-1240 - NEW: DataTable with Conditional Row Styling
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Conditional Row Styling Demo',
                    'subtitle' => 'Rows change color based on inventory status and price'
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'Red=Out of Stock, Orange=Low Stock, Green=In Stock, Blue=Expensive (>$100), Yellow=Very Low Quantity (<5)'
                ],
                'columns' => [
                    [
                        'field' => 'code',
                        'header' => 'Code',
                        'sortable' => true,
                        'style' => 'width: 100px'
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
                        'sortable' => true
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'dataType' => 'currency',
                        'format' => 2,  // 2 decimal places
                        'leadText' => '$ ',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right'
                    ],
                    [
                        'field' => 'quantity',
                        'header' => 'Stock',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false
                ],

                // DD20250710-1240 - Conditional styling rules with priorities and user-defined styles
                'conditionalStyles' => [
                    // Priority 1 (HIGHEST): Out of stock items - RED background
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'OUTOFSTOCK',
                        'operator' => 'eq',
                        'priority' => 1,
                        'styleObject' => [
                            'backgroundColor' => '#fee2e2',
                            'color' => '#7f1d1d',
                            'fontWeight' => 'bold'
                        ]
                    ],
                    // Priority 2: Low stock items - ORANGE background
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'LOWSTOCK',
                        'operator' => 'eq',
                        'priority' => 2,
                        'styleObject' => [
                            'backgroundColor' => '#fed7aa',
                            'color' => '#9a3412'
                        ]
                    ],
                    // Priority 3: In stock items - GREEN background
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'INSTOCK',
                        'operator' => 'eq',
                        'priority' => 4,
                        'styleObject' => [
                            'backgroundColor' => '#dcfce7',
                            'color' => '#14532d'
                        ]
                    ],
                    // Priority 4: Expensive items (price > 100) - BLUE background with italic
                    [
                        'column' => 'price',
                        'value' => 100,
                        'operator' => 'gt',
                        'priority' => 3,
                        'styleObject' => [
                            'backgroundColor' => '#dbeafe',
                            'color' => '#1e3a8a',
                            'fontStyle' => 'italic'
                        ]
                    ],
                    // Priority 5 (LOWEST): Very low quantity items (< 5) - YELLOW background with left border
                    [
                        'column' => 'quantity',
                        'value' => 5,
                        'operator' => 'lt',
                        'priority' => 5,
                        'styleObject' => [
                            'backgroundColor' => '#fef3c7',
                            'color' => '#92400e',
                            'borderLeft' => '4px solid #f59e0b'
                        ]
                    ]
                ],
                'paginator' => true,
                'rows' => 10,
                'stripedRows' => false, // Disable striped rows to better see conditional styling
                'globalFilter' => true,
                'tableStyle' => 'min-width: 70rem'
            ],

            // NEW: DataTable with Advanced Column Features
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Advanced Column Features',
                    'subtitle' => 'Hidden columns, resizable columns, and column toggling'
                ],
                'columns' => [
                    [
                        'field' => 'id',
                        'header' => 'ID',
                        'sortable' => true,
                        'hidden' => true,  // Hidden by default
                        'style' => 'width: 80px'
                    ],
                    [
                        'field' => 'code',
                        'header' => 'Code',
                        'sortable' => true,
                        'frozen' => true,  // Frozen columns can't be hidden
                        'resizable' => true,
                        'minWidth' => '80px',
                        'maxWidth' => '150px',
                        'style' => 'width: 100px'
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'resizable' => true,
                        'minWidth' => '150px',
                        'style' => 'min-width: 200px'
                    ],
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'resizable' => true,
                        'style' => 'width: 150px'
                    ],
                    [
                        'field' => 'price',
                        'header' => 'Price',
                        'sortable' => true,
                        'resizable' => false,  // Not resizable
                        'bodyStyle' => 'text-align: right',
                        'headerStyle' => 'text-align: right',
                        'style' => 'width: 100px'
                    ],
                    [
                        'field' => 'quantity',
                        'header' => 'Stock',
                        'sortable' => true,
                        'hidden' => true,  // Hidden by default
                        'resizable' => true,
                        'style' => 'width: 100px'
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'resizable' => true,
                        'style' => 'width: 100px'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => true
                ],
                // Enable column features
                'columnToggle' => true,
                'columnTogglePosition' => 'right',
                'resizableColumns' => true,
                'columnResizeMode' => 'fit',
                'reorderableColumns' => true,
                // Other settings
                'paginator' => true,
                'rows' => 10,
                'stripedRows' => true,
                'globalFilter' => true,
                'footer' => [
                    'showRecordCount' => true,
                    'text' => 'ID and Stock columns are hidden by default. Drag column borders to resize.'
                ],
                'tableStyle' => 'min-width: 60rem'
            ]
        ];

        $renderedWidgets = $widgetRenderer->renderMany($widgets);

        return Inertia::render('PrimeVueTest', [
            'widgets' => $renderedWidgets
        ]);
    }
}
