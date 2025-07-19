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



            //DD1 - Begin: Subheader Row Grouping DataTable Demo
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Row Grouping Demo - Subheader Mode',
                    'subtitle' => '🆕 NEW FEATURE: Group rows with headers and footers showing totals and counts',
                    'actions' => [
                        ['label' => 'Add Product', 'icon' => 'pi pi-plus', 'action' => 'add', 'severity' => 'success'],
                        ['label' => 'Export Grouped Data', 'icon' => 'pi pi-download', 'action' => 'export-grouped', 'severity' => 'info']
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'showSelectedCount' => true,
                    'text' => 'Products grouped by Category and Inventory Status with totals'
                ],
                'columns' => [
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 150px'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
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
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false // Client-side for demo
                ],
                // Row Grouping Configuration - Subheader Mode
                'rowGrouping' => [
                    'enabled' => true,
                    'rowGroupMode' => 'subheader',
                    'groupRowsBy' => ['category'],
                    'groupRowsTotals' => ['price', 'quantity'], // Calculate totals for these fields
                    // Header configuration
                    'showHeaderTotal' => true,
                    'showHeaderRowCount' => true,
                    'headerRowCountText' => 'Products in this group: ',
                    'headerText' => 'Group Summary Information',
                    'headerTemplate' => 'Products in category {category}',
                    'headerImageUrl' => 'https://img.icons8.com/?size=100&id=73&format=png',
                    'headerImagePosition' => 'after',

                    // Footer configuration
                    'showFooterTotal' => true,
                    'showFooterRowCount' => true,
                    'footerRowCountText' => 'Total items: ',
                    'footerText' => 'End of group data'
                ],
                'gridLines' => 'both',
                'stripedRows' => true,
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'bottom',
                'rows' => 5,
                'rowsPerPageOptions' => [5, 10, 15, 25, 50],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'single',
                'removableSort' => true,
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '500px',
                'exportable' => true,
                'exportFormats' => ['csv'],
                'exportFilename' => 'grouped-products-subheader-' . date('Y-m-d'),
                'responsiveLayout' => 'scroll',
                'tableStyle' => 'min-width: 80rem',
                'emptyMessage' => 'No products found'
            ],
            //DD1 - End

            //DD2 - Begin: Expandable Row Grouping DataTable Demo
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Row Grouping Demo - Expandable Mode',
                    'subtitle' => '🆕 NEW FEATURE: Collapsible groups with expand/collapse all functionality',
                    'actions' => [
                        ['label' => 'Add Product', 'icon' => 'pi pi-plus', 'action' => 'add', 'severity' => 'success'],
                        ['label' => 'Group Analysis', 'icon' => 'pi pi-chart-bar', 'action' => 'analyze-groups', 'severity' => 'info']
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'showSelectedCount' => true,
                    'text' => 'Click group headers to expand/collapse or use the controls above'
                ],
                'columns' => [
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 150px'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
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
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false // Client-side for demo
                ],
                // Row Grouping Configuration - Expandable Mode
                'rowGrouping' => [
                    'enabled' => true,
                    'rowGroupMode' => 'expandable',
                    'groupRowsBy' => ['category'],
                    'groupRowsTotals' => ['price', 'quantity'], // Calculate totals for these fields
                    // Header configuration
                    'showHeaderTotal' => true,
                    'showHeaderRowCount' => true,
                    'headerRowCountText' => 'Items count: ',
                    'headerText' => 'Expandable Group Details',
                    // Footer configuration
                    'showFooterTotal' => true,
                    'showFooterRowCount' => true,
                    'footerRowCountText' => 'Group total: ',
                    'footerText' => 'Group footer information',
                    // Expandable mode specific
                    'showExpandCollapseAllButton' => true,
                    'expandAllLabel' => 'Expand All Categories',
                    'collapseAllLabel' => 'Collapse All Categories',
                    'expandCollapsePosition' => 'header'
                ],
                'gridLines' => 'both',
                'stripedRows' => true,
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'bottom',
                'rows' => 15,
                'rowsPerPageOptions' => [10, 15, 25, 50],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'single',
                'removableSort' => true,
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '500px',
                'exportable' => true,
                'exportFormats' => ['csv'],
                'exportFilename' => 'grouped-products-expandable-' . date('Y-m-d'),
                'responsiveLayout' => 'scroll',
                'tableStyle' => 'min-width: 80rem',
                'emptyMessage' => 'No products found'
            ],
            //DD2 - End

            //DD3 - Begin: Rowspan Row Grouping DataTable Demo
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Row Grouping Demo - Rowspan Mode',
                    'subtitle' => '🆕 NEW FEATURE: Grouped rows with spanning cells for clean visual grouping',
                    'actions' => [
                        ['label' => 'Add Product', 'icon' => 'pi pi-plus', 'action' => 'add', 'severity' => 'success'],
                        ['label' => 'Print Grouped View', 'icon' => 'pi pi-print', 'action' => 'print-groups', 'severity' => 'secondary']
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'showSelectedCount' => true,
                    'text' => 'Grouped data with spanning cells for visual organization'
                ],
                'columns' => [
                    [
                        'field' => 'category',
                        'header' => 'Category',
                        'sortable' => true,
                        'style' => 'width: 150px; vertical-align: top'
                    ],
                    [
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'style' => 'width: 120px; vertical-align: top'
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
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false // Client-side for demo
                ],
                // Row Grouping Configuration - Rowspan Mode
                'rowGrouping' => [
                    'enabled' => true,
                    'rowGroupMode' => 'rowspan',
                    'groupRowsBy' => ['category']
                ],
                'gridLines' => 'both',
                'stripedRows' => false, // Disabled for better rowspan visibility
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'bottom',
                'rows' => 15,
                'rowsPerPageOptions' => [5, 10, 15, 25, 50],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'single',
                'removableSort' => true,
                'globalFilter' => true,
                'scrollable' => true,
                'scrollHeight' => '500px',
                'exportable' => true,
                'exportFormats' => ['csv'],
                'exportFilename' => 'grouped-products-rowspan-' . date('Y-m-d'),
                'responsiveLayout' => 'scroll',
                'tableStyle' => 'min-width: 80rem',
                'emptyMessage' => 'No products found'
            ],
            //DD3 - End

            //DD 20250714:1400 - BEGIN - NEW: Column Locking DataTable Demo (NEWEST FEATURE)
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Products with Column Locking - NEWEST FEATURE DEMO',
                    'subtitle' => '🆕 LATEST FEATURE: Lock/unlock columns to prevent horizontal scrolling',
                    'actions' => [
                        ['label' => 'Add Product', 'icon' => 'pi pi-plus', 'action' => 'add', 'severity' => 'success'],
                        ['label' => 'Refresh', 'icon' => 'pi pi-refresh', 'action' => 'refresh', 'severity' => 'info'],
                        ['label' => 'Lock Important Columns', 'icon' => 'pi pi-lock', 'action' => 'lock-important', 'severity' => 'warning']
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'showSelectedCount' => true,
                    'text' => 'Lock columns to keep them visible while scrolling horizontally through wide data sets'
                ],
                'columns' => [
                    [
                        'field' => 'code',
                        'header' => 'Product Code',
                        'sortable' => true,
                        'style' => 'width: 120px',
                        'lockColumn' => true,    // Initially locked
                        'lockButton' => false     // Show lock/unlock button
                    ],
                    [
                        'field' => 'name',
                        'header' => 'Product Name',
                        'sortable' => true,
                        'style' => 'min-width: 200px',
                        'lockColumn' => true,   // Not initially locked
                        'lockButton' => true     // But user can lock it
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
                        'lockColumn' => true,    // Initially locked (important price data)
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
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'style' => 'width: 120px',
                        'lockColumn' => false,
                        'lockButton' => false    // No lock button for this column
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px',
                        'lockColumn' => true,
                        'lockButton' => true
                    ],
                    [
                        'field' => 'supplier',
                        'header' => 'Supplier',
                        'sortable' => true,
                        'style' => 'width: 150px',
                        'lockColumn' => false,
                        'lockButton' => false
                    ],
                    [
                        'field' => 'description',
                        'header' => 'Description',
                        'sortable' => true,
                        'style' => 'min-width: 300px',
                        'lockColumn' => false,
                        'lockButton' => false
                    ],
                    [
                        'field' => 'lastUpdated',
                        'header' => 'Last Updated',
                        'sortable' => true,
                        'dataType' => 'shortdatetime',
                        'format' => 'US-12',
                        'style' => 'width: 140px',
                        'lockColumn' => false,
                        'lockButton' => false
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false // Client-side for demo to show more interaction
                ],
                // NEW: Column Locking Configuration
                'columnLocking' => [
                    'enabled' => true,
                    'buttonPosition' => 'toolbar',    // Show buttons in toolbar
                    'buttonStyle' => 'margin: 0 2px;',
                    'buttonClass' => 'column-lock-btn'
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
                'exportFilename' => 'column-locked-products-' . date('Y-m-d'),
                'responsiveLayout' => 'scroll',
                'tableStyle' => 'min-width: 120rem', // Wide table to demonstrate horizontal scrolling
                'emptyMessage' => 'No products found',
                // Enhanced conditional styling for demonstration
                'conditionalStyles' => [
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'LOWSTOCK',
                        'operator' => 'eq',
                        'priority' => 2,
                        'styleObject' => [
                            'backgroundColor' => '#fefce8',
                            'color' => '#a16207'
                        ]
                    ],
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'OUTOFSTOCK',
                        'operator' => 'eq',
                        'priority' => 1,
                        'styleObject' => [
                            'backgroundColor' => '#fef2f2',
                            'color' => '#dc2626'
                        ]
                    ],
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'INSTOCK',
                        'operator' => 'eq',
                        'priority' => 3,
                        'styleObject' => [
                            'backgroundColor' => '#f0fdf4',
                            'color' => '#166534'
                        ]
                    ],
                    [
                        'column' => 'price',
                        'value' => 200,
                        'operator' => 'gt',
                        'priority' => 4,
                        'styleObject' => [
                            'fontWeight' => 'bold',
                            'textDecoration' => 'underline'
                        ]
                    ]
                ]
            ],
            //DD 20250714:1400 - END

            //DD 20250713:2021 - BEGIN - NEW: Row Locking DataTable Demo (at the top)
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Products with Row Locking - Latest Feature Demo',
                    'subtitle' => '🆕 NEW FEATURE: Lock up to 3 rows to prevent them from scrolling',
                    'actions' => [
                        ['label' => 'Add Product', 'icon' => 'pi pi-plus', 'action' => 'add', 'severity' => 'success'],
                        ['label' => 'Refresh', 'icon' => 'pi pi-refresh', 'action' => 'refresh', 'severity' => 'info'],
                        ['label' => 'Lock All Available', 'icon' => 'pi pi-lock', 'action' => 'lock-all', 'severity' => 'warning']
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'showSelectedCount' => true,
                    'text' => 'Lock rows to keep them visible while scrolling through other data'
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
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'style' => 'width: 120px'
                    ],
                    [
                        'field' => 'rating',
                        'header' => 'Rating',
                        'sortable' => true,
                        'bodyStyle' => 'text-align: center',
                        'headerStyle' => 'text-align: center',
                        'style' => 'width: 80px'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => false // Client-side for demo to show more interaction
                ],
                // NEW: Row Locking Configuration
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
                'exportFilename' => 'locked-products-' . date('Y-m-d'),
                'responsiveLayout' => 'scroll',
                'tableStyle' => 'min-width: 80rem',
                'emptyMessage' => 'No products found',
                // Enhanced conditional styling for locked row demonstration
                'conditionalStyles' => [
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'LOWSTOCK',
                        'operator' => 'eq',
                        'priority' => 2,
                        'styleObject' => [
                            'backgroundColor' => '#fefce8',
                            'color' => '#a16207'
                        ]
                    ],
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'OUTOFSTOCK',
                        'operator' => 'eq',
                        'priority' => 1,
                        'styleObject' => [
                            'backgroundColor' => '#fef2f2',
                            'color' => '#dc2626'
                        ]
                    ],
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'INSTOCK',
                        'operator' => 'eq',
                        'priority' => 3,
                        'styleObject' => [
                            'backgroundColor' => '#f0fdf4',
                            'color' => '#166534'
                        ]
                    ],
                    [
                        'column' => 'price',
                        'value' => 200,
                        'operator' => 'gt',
                        'priority' => 4,
                        'styleObject' => [
                            'fontWeight' => 'bold',
                            'textDecoration' => 'underline'
                        ]
                    ]
                ]
            ],
            //DD 20250713:2021 - END

            // DD20250712-1930 BEGIN - NEW: Row Expansion DataTable Demo (before existing tables)
            [
                'type' => 'datatable',
                'header' => [
                    'title' => 'Products with Order History - Row Expansion Demo',
                    'subtitle' => '🆕 NEW FEATURE: Click the expand icon to view order details for each product',
                    'actions' => [
                        ['label' => 'Add Product', 'icon' => 'pi pi-plus', 'action' => 'add', 'severity' => 'success'],
                        ['label' => 'Refresh', 'icon' => 'pi pi-refresh', 'action' => 'refresh', 'severity' => 'info']
                    ]
                ],
                'footer' => [
                    'showRecordCount' => true,
                    'showSelectedCount' => true,
                    'text' => 'Expand rows to see order history with nested DataTableWidget'
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
                        'field' => 'inventoryStatus',
                        'header' => 'Status',
                        'sortable' => true,
                        'style' => 'width: 120px'
                    ]
                ],
                'dataSource' => [
                    'url' => '/products/with-orders',
                    'method' => 'GET',
                    'lazy' => false // Client-side for demo
                ],
                'rowExpansion' => [
                    'enabled' => true,
                    'expanderColumn' => [
                        'style' => 'width: 5rem',
                        'frozen' => false
                    ],
                    'expandControls' => [
                        'showExpandAll' => true,
                        'showCollapseAll' => true,
                        'expandAllLabel' => 'Expand All Products',
                        'collapseAllLabel' => 'Collapse All Products',
                        'position' => 'header'
                    ],
                    'expandedContent' => [
                        'type' => 'datatable',
                        'title' => 'Current orders for',
                        'titleField' => 'name',
                        'dataField' => 'orders',
                        'widget' => [
                            'columns' => [
                                [
                                    'field' => 'id',
                                    'header' => 'Order ID',
                                    'sortable' => true,
                                    'style' => 'width: 120px'
                                ],
                                [
                                    'field' => 'customer',
                                    'header' => 'Customer',
                                    'sortable' => true,
                                    'style' => 'min-width: 150px'
                                ],
                                [
                                    'field' => 'date',
                                    'header' => 'Order Date',
                                    'sortable' => true,
                                    'dataType' => 'shortdate',
                                    'style' => 'width: 120px'
                                ],
                                [
                                    'field' => 'quantity',
                                    'header' => 'Qty',
                                    'sortable' => true,
                                    'bodyStyle' => 'text-align: center',
                                    'headerStyle' => 'text-align: center',
                                    'style' => 'width: 60px'
                                ],
                                [
                                    'field' => 'amount',
                                    'header' => 'Amount',
                                    'sortable' => true,
                                    'dataType' => 'currency',
                                    'format' => 2,
                                    'leadText' => '$',
                                    'bodyStyle' => 'text-align: right',
                                    'headerStyle' => 'text-align: right',
                                    'style' => 'width: 100px'
                                ],
                                [
                                    'field' => 'status',
                                    'header' => 'Status',
                                    'sortable' => true,
                                    'style' => 'width: 120px'
                                ]
                            ],
                            'paginator' => false,
                            'size' => 'small',
                            'stripedRows' => false,
                            'showGridlines' => true,
                            'emptyMessage' => 'No orders found for this product',
                            'tableStyle' => 'min-width: 700px'
                        ]
                    ],
                    'events' => [
                        'onExpand' => true,
                        'onCollapse' => true
                    ]
                ],
                'gridLines' => 'both',
                'stripedRows' => true,
                'showGridlines' => true,
                'size' => 'normal',
                'paginator' => true,
                'paginatorPosition' => 'bottom',
                'rows' => 5,
                'rowsPerPageOptions' => [3, 5, 10],
                'currentPageReportTemplate' => 'Showing {first} to {last} of {totalRecords} products',
                'sortMode' => 'single',
                'removableSort' => true,
                'selectionMode' => 'single',
                'globalFilter' => true,
                'exportable' => true,
                'exportFormats' => ['csv'],
                'exportFilename' => 'products-with-orders-' . date('Y-m-d'),
                'responsiveLayout' => 'scroll',
                'tableStyle' => 'min-width: 80rem',
                'emptyMessage' => 'No products found',
                // Add conditional styling for inventory status
                'conditionalStyles' => [
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'LOWSTOCK',
                        'operator' => 'eq',
                        'priority' => 1,
                        'styleObject' => [
                            'backgroundColor' => '#fefce8',
                            'color' => '#a16207'
                        ]
                    ],
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'OUTOFSTOCK',
                        'operator' => 'eq',
                        'priority' => 1,
                        'styleObject' => [
                            'backgroundColor' => '#fef2f2',
                            'color' => '#dc2626'
                        ]
                    ],
                    [
                        'column' => 'inventoryStatus',
                        'value' => 'INSTOCK',
                        'operator' => 'eq',
                        'priority' => 2,
                        'styleObject' => [
                            'backgroundColor' => '#f0fdf4',
                            'color' => '#166534'
                        ]
                    ]
                ]
            ],
            // DD20250712-1930 END

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
