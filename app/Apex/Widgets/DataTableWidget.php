<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class DataTableWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'datatable';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the datatable widget'
                ],
                // Header Configuration
                'header' => [
                    'type' => 'object',
                    'description' => 'Header configuration',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'description' => 'Header title text'
                        ],
                        'subtitle' => [
                            'type' => 'string',
                            'description' => 'Header subtitle text'
                        ],
                        'actions' => [
                            'type' => 'array',
                            'description' => 'Header action buttons',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'label' => ['type' => 'string'],
                                    'icon' => ['type' => 'string'],
                                    'action' => ['type' => 'string'],
                                    'severity' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ],
                // Footer Configuration
                'footer' => [
                    'type' => 'object',
                    'description' => 'Footer configuration',
                    'properties' => [
                        'showRecordCount' => [
                            'type' => 'boolean',
                            'default' => true,
                            'description' => 'Show total record count'
                        ],
                        'text' => [
                            'type' => 'string',
                            'description' => 'Custom footer text'
                        ],
                        'showSelectedCount' => [
                            'type' => 'boolean',
                            'default' => true,
                            'description' => 'Show selected records count'
                        ]
                    ]
                ],
                // Column Configuration
                'columns' => [
                    'type' => 'array',
                    'description' => 'Array of column definitions',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'field' => [
                                'type' => 'string',
                                'required' => true,
                                'description' => 'Field name in the data object'
                            ],
                            'header' => [
                                'type' => 'string',
                                'required' => true,
                                'description' => 'Column header text'
                            ],
                            'sortable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Whether column is sortable'
                            ],
                            'filter' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Whether column has filter'
                            ],
                            'filterType' => [
                                'type' => 'string',
                                'enum' => ['text', 'numeric', 'date', 'dropdown', 'multiselect'],
                                'default' => 'text',
                                'description' => 'Type of filter'
                            ],
                            'filterOptions' => [
                                'type' => 'array',
                                'description' => 'Options for dropdown/multiselect filters',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'label' => ['type' => 'string'],
                                        'value' => ['type' => 'string']
                                    ]
                                ]
                            ],
                            'style' => [
                                'type' => 'string',
                                'description' => 'Column style'
                            ],
                            'bodyStyle' => [
                                'type' => 'string',
                                'description' => 'Column body cell style'
                            ],
                            'headerStyle' => [
                                'type' => 'string',
                                'description' => 'Column header style'
                            ],
                            'hidden' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Hide column by default'
                            ],
                            'resizable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Allow column width resizing'
                            ],
                            'minWidth' => [
                                'type' => 'string',
                                'description' => 'Minimum column width for resizing'
                            ],
                            'maxWidth' => [
                                'type' => 'string',
                                'description' => 'Maximum column width for resizing'
                            ],
                            'url' => [
                                'type' => 'string',
                                'description' => 'URL for clickable columns'
                            ],
                            'urlTarget' => [
                                'type' => 'string',
                                'enum' => ['_self', '_blank', '_parent', '_top'],
                                'default' => '_self',
                                'description' => 'Target for URL links'
                            ],
                            'clickable' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Make column content clickable'
                            ],
                            'action' => [
                                'type' => 'string',
                                'description' => 'Custom action name to emit on click'
                            ],
                            'actionField' => [
                                'type' => 'string',
                                'description' => 'Field to use as parameter for action'
                            ],
                            'exportable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Include in export'
                            ],
                            'reorderable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Can be reordered'
                            ],
                            'frozen' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Freeze column'
                            ]
                        ]
                    ]
                ],
                // Grid Lines Configuration
                'gridLines' => [
                    'type' => 'string',
                    'enum' => ['both', 'horizontal', 'vertical', 'none'],
                    'default' => 'both',
                    'description' => 'Grid lines display'
                ],
                // Visual Configuration
                'stripedRows' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Striped row styling'
                ],
                'showGridlines' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Show grid lines'
                ],
                'size' => [
                    'type' => 'string',
                    'enum' => ['small', 'normal', 'large'],
                    'default' => 'normal',
                    'description' => 'Table size'
                ],
                // Data Configuration
                'dataKey' => [
                    'type' => 'string',
                    'description' => 'Unique key for row data',
                    'default' => 'id'
                ],
                'dataSource' => [
                    'type' => 'object',
                    'description' => 'Server-side data configuration',
                    'properties' => [
                        'url' => [
                            'type' => 'string',
                            'required' => true,
                            'description' => 'API endpoint URL'
                        ],
                        'method' => [
                            'type' => 'string',
                            'enum' => ['GET', 'POST'],
                            'default' => 'GET'
                        ],
                        'lazy' => [
                            'type' => ['boolean', 'string'],
                            'default' => true,
                            'description' => 'Enable lazy loading (true/false/auto)'
                        ],
                        'lazyThreshold' => [
                            'type' => 'integer',
                            'default' => 1000,
                            'description' => 'Record count threshold for auto lazy mode'
                        ],
                        'preload' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Preload data on mount'
                        ],
                        'countUrl' => [
                            'type' => 'string',
                            'description' => 'Optional endpoint to get total count for auto mode'
                        ]
                    ]
                ],
                // Pagination Configuration
                'paginator' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Enable pagination'
                ],
                'paginatorPosition' => [
                    'type' => 'string',
                    'enum' => ['top', 'bottom', 'both'],
                    'default' => 'bottom',
                    'description' => 'Paginator position'
                ],
                'rows' => [
                    'type' => 'integer',
                    'default' => 10,
                    'description' => 'Rows per page'
                ],
                'rowsPerPageOptions' => [
                    'type' => 'array',
                    'default' => [5, 10, 25, 50, 100],
                    'items' => [
                        'type' => 'integer'
                    ],
                    'description' => 'Options for rows per page dropdown'
                ],
                'currentPageReportTemplate' => [
                    'type' => 'string',
                    'default' => 'Showing {first} to {last} of {totalRecords} entries',
                    'description' => 'Page report template'
                ],
                // Sorting Configuration
                'sortMode' => [
                    'type' => 'string',
                    'enum' => ['single', 'multiple'],
                    'default' => 'single',
                    'description' => 'Sort mode'
                ],
                'removableSort' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Allow removing sort'
                ],
                'defaultSortOrder' => [
                    'type' => 'integer',
                    'enum' => [1, -1],
                    'default' => 1,
                    'description' => 'Default sort order (1: asc, -1: desc)'
                ],
                'multiSortMeta' => [
                    'type' => 'array',
                    'description' => 'Pre-configured multi-sort',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'field' => ['type' => 'string'],
                            'order' => ['type' => 'integer']
                        ]
                    ]
                ],
                // Selection Configuration
                'selectionMode' => [
                    'type' => 'string',
                    'enum' => ['single', 'multiple', 'checkbox', null],
                    'default' => null,
                    'description' => 'Row selection mode'
                ],
                'selection' => [
                    'type' => 'array',
                    'default' => [],
                    'description' => 'Pre-selected rows'
                ],
                'metaKeySelection' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Use meta key for multiple selection'
                ],
                'selectAll' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show select all checkbox in header'
                ],
                // Group Actions Configuration
                'groupActions' => [
                    'type' => 'array',
                    'description' => 'Actions for selected rows',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'label' => ['type' => 'string'],
                            'icon' => ['type' => 'string'],
                            'action' => ['type' => 'string'],
                            'severity' => ['type' => 'string'],
                            'confirm' => ['type' => 'boolean'],
                            'confirmMessage' => ['type' => 'string']
                        ]
                    ]
                ],
                // Filter Configuration
                'filters' => [
                    'type' => 'object',
                    'description' => 'Pre-configured filters'
                ],
                'filterDisplay' => [
                    'type' => 'string',
                    'enum' => ['menu', 'row'],
                    'default' => 'row',
                    'description' => 'Filter display mode'
                ],
                'globalFilter' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable global search'
                ],
                'globalFilterFields' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Fields to search in global filter'
                ],
                'filterMatchModeOptions' => [
                    'type' => 'object',
                    'description' => 'Available filter match modes per column'
                ],
                // Scroll Configuration
                'scrollable' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable scrolling'
                ],
                'scrollHeight' => [
                    'type' => 'string',
                    'default' => 'flex',
                    'description' => 'Scroll height'
                ],
                'virtualScroll' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable virtual scrolling for large datasets'
                ],
                'frozenColumns' => [
                    'type' => 'integer',
                    'default' => 0,
                    'description' => 'Number of frozen columns from left'
                ],
                // Reorder Configuration
                'reorderableColumns' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable column reordering'
                ],
                'reorderableRows' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable row reordering'
                ],
                // Export Configuration
                'exportable' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable export functionality'
                ],
                'exportFormats' => [
                    'type' => 'array',
                    'default' => ['csv', 'excel', 'pdf'],
                    'items' => [
                        'type' => 'string',
                        'enum' => ['csv', 'excel', 'pdf']
                    ],
                    'description' => 'Available export formats'
                ],
                'exportFilename' => [
                    'type' => 'string',
                    'default' => 'data-export',
                    'description' => 'Export filename'
                ],
                // Column Toggling Configuration
                'columnToggle' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable column visibility toggle'
                ],
                'columnTogglePosition' => [
                    'type' => 'string',
                    'enum' => ['left', 'right'],
                    'default' => 'right',
                    'description' => 'Position of column toggle button'
                ],
                // Resizable Configuration
                'resizableColumns' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable column resizing'
                ],
                'columnResizeMode' => [
                    'type' => 'string',
                    'enum' => ['fit', 'expand'],
                    'default' => 'fit',
                    'description' => 'Column resize behavior'
                ],
                // CRUD Actions Configuration
                'showView' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show view button column'
                ],
                'showEdit' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show edit button column'
                ],
                'showDelete' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show delete button column'
                ],
                'showHistory' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show history button column'
                ],
                'showPrint' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show print button column'
                ],
                'crudActions' => [
                    'type' => 'object',
                    'description' => 'CRUD action configuration',
                    'properties' => [
                        'idField' => [
                            'type' => 'string',
                            'default' => 'id',
                            'description' => 'Field to use as record identifier'
                        ],
                        'permissions' => [
                            'type' => 'object',
                            'properties' => [
                                'view' => ['type' => 'boolean', 'default' => true],
                                'edit' => ['type' => 'boolean', 'default' => true],
                                'delete' => ['type' => 'boolean', 'default' => true],
                                'history' => ['type' => 'boolean', 'default' => true],
                                'print' => ['type' => 'boolean', 'default' => true]
                            ]
                        ],
                        'routes' => [
                            'type' => 'object',
                            'properties' => [
                                'view' => ['type' => 'string'],
                                'edit' => ['type' => 'string'],
                                'delete' => ['type' => 'string'],
                                'history' => ['type' => 'string'],
                                'print' => ['type' => 'string']
                            ]
                        ]
                    ]
                ],
                // Other Configuration
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
                ],
                'stateStorage' => [
                    'type' => 'string',
                    'enum' => ['session', 'local', null],
                    'default' => null,
                    'description' => 'State persistence'
                ],
                'stateKey' => [
                    'type' => 'string',
                    'description' => 'Unique key for state storage'
                ]
            ]
        ];
    }

    public function transform(array $config): array
    {
        $columns = $config['columns'] ?? [];

        // Process columns to ensure proper structure
        $processedColumns = array_map(function ($column) {
            return [
                'field' => $column['field'],
                'header' => $column['header'],
                'sortable' => $column['sortable'] ?? true,
                'filter' => $column['filter'] ?? false,
                'filterType' => $column['filterType'] ?? 'text',
                'filterOptions' => $column['filterOptions'] ?? null,
                'style' => $column['style'] ?? null,
                'bodyStyle' => $column['bodyStyle'] ?? null,
                'headerStyle' => $column['headerStyle'] ?? null,
                'hidden' => $column['hidden'] ?? false,
                'resizable' => $column['resizable'] ?? true,
                'minWidth' => $column['minWidth'] ?? null,
                'maxWidth' => $column['maxWidth'] ?? null,
                'url' => $column['url'] ?? null,
                'urlTarget' => $column['urlTarget'] ?? '_self',
                'clickable' => $column['clickable'] ?? false,
                'action' => $column['action'] ?? null,
                'actionField' => $column['actionField'] ?? null,
                'searchExclude' => $column['searchExclude'] ?? false,
                'exportable' => $column['exportable'] ?? true,
                'reorderable' => $column['reorderable'] ?? true,
                'frozen' => $column['frozen'] ?? false,
            ];
        }, $columns);

        return [
            'id' => $this->id,
            'type' => $this->getType(),
            // Header/Footer
            'header' => $config['header'] ?? null,
            'footer' => $config['footer'] ?? ['showRecordCount' => true, 'showSelectedCount' => true],
            // Columns
            'columns' => $processedColumns,
            // Visual
            'gridLines' => $config['gridLines'] ?? 'both',
            'stripedRows' => $config['stripedRows'] ?? true,
            'showGridlines' => $config['showGridlines'] ?? true,
            'size' => $config['size'] ?? 'normal',
            // Data
            'dataKey' => $config['dataKey'] ?? 'id',
            'dataSource' => $config['dataSource'] ?? null,
            // Pagination
            'paginator' => $config['paginator'] ?? true,
            'paginatorPosition' => $config['paginatorPosition'] ?? 'bottom',
            'rows' => $config['rows'] ?? 10,
            'rowsPerPageOptions' => $config['rowsPerPageOptions'] ?? [5, 10, 25, 50, 100],
            'currentPageReportTemplate' => $config['currentPageReportTemplate'] ?? 'Showing {first} to {last} of {totalRecords} entries',
            // Sorting
            'sortMode' => $config['sortMode'] ?? 'single',
            'removableSort' => $config['removableSort'] ?? true,
            'defaultSortOrder' => $config['defaultSortOrder'] ?? 1,
            'multiSortMeta' => $config['multiSortMeta'] ?? null,
            // Selection
            'selectionMode' => $config['selectionMode'] ?? null,
            'selection' => $config['selection'] ?? [],
            'metaKeySelection' => $config['metaKeySelection'] ?? true,
            'selectAll' => $config['selectAll'] ?? false,
            // Group Actions
            'groupActions' => $config['groupActions'] ?? [],
            // Filters
            'filters' => $config['filters'] ?? null,
            'filterDisplay' => $config['filterDisplay'] ?? 'row',
            'globalFilter' => $config['globalFilter'] ?? false,
            'globalFilterFields' => $config['globalFilterFields'] ?? null,
            'filterMatchModeOptions' => $config['filterMatchModeOptions'] ?? null,
            // Scroll
            'scrollable' => $config['scrollable'] ?? false,
            'scrollHeight' => $config['scrollHeight'] ?? 'flex',
            'virtualScroll' => $config['virtualScroll'] ?? false,
            'frozenColumns' => $config['frozenColumns'] ?? 0,
            // CRUD Actions
            'showView' => $config['showView'] ?? false,
            'showEdit' => $config['showEdit'] ?? false,
            'showDelete' => $config['showDelete'] ?? false,
            'showHistory' => $config['showHistory'] ?? false,
            'showPrint' => $config['showPrint'] ?? false,
            'crudActions' => $config['crudActions'] ?? [
                'idField' => 'id',
                'permissions' => [
                    'view' => true,
                    'edit' => true,
                    'delete' => true,
                    'history' => true,
                    'print' => true
                ]
            ],
            // Column Toggle
            'columnToggle' => $config['columnToggle'] ?? false,
            'columnTogglePosition' => $config['columnTogglePosition'] ?? 'right',
            // Resizable
            'resizableColumns' => $config['resizableColumns'] ?? false,
            'columnResizeMode' => $config['columnResizeMode'] ?? 'fit',
            // Reorder
            'reorderableColumns' => $config['reorderableColumns'] ?? false,
            'reorderableRows' => $config['reorderableRows'] ?? false,
            // Export
            'exportable' => $config['exportable'] ?? false,
            'exportFormats' => $config['exportFormats'] ?? ['csv', 'excel', 'pdf'],
            'exportFilename' => $config['exportFilename'] ?? 'data-export',
            // Other
            'loading' => $config['loading'] ?? false,
            'emptyMessage' => $config['emptyMessage'] ?? 'No records found',
            'tableStyle' => $config['tableStyle'] ?? 'min-width: 50rem',
            'tableClass' => $config['tableClass'] ?? null,
            'responsiveLayout' => $config['responsiveLayout'] ?? 'scroll',
            'stateStorage' => $config['stateStorage'] ?? null,
            'stateKey' => $config['stateKey'] ?? null,
        ];
    }
}
