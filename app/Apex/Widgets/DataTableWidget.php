<?php

namespace App\Apex\Widgets;
//works
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
            'properties' => [
                // Widget Identification
                'widgetId' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Unique widget identifier'
                ],
                // Header/Footer Configuration
                'header' => [
                    'type' => 'object',
                    'description' => 'Header configuration',
                    'properties' => [
                        'title' => ['type' => 'string'],
                        'subtitle' => ['type' => 'string'],
                        'actions' => [
                            'type' => 'array',
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
                'footer' => [
                    'type' => 'object',
                    'description' => 'Footer configuration',
                    'properties' => [
                        'showRecordCount' => ['type' => 'boolean', 'default' => true],
                        'text' => ['type' => 'string'],
                        'showSelectedCount' => ['type' => 'boolean', 'default' => true]
                    ]
                ],
                // Columns Configuration
                'columns' => [
                    'type' => 'array',
                    'required' => true,
                    'description' => 'Column definitions',
                    'items' => [
                        'type' => 'object',
                        'required' => ['field', 'header'],
                        'properties' => [
                            'field' => [
                                'type' => 'string',
                                'description' => 'Field name'
                            ],
                            'header' => [
                                'type' => 'string',
                                'description' => 'Column header'
                            ],
                            'sortable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Enable sorting'
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
                            'dataType' => [
                                'type' => 'string',
                                'enum' => ['text', 'number', 'currency', 'shortdate', 'longdate1', 'longdate2', 'time', 'shortdatetime', 'longdate1time', 'longdate2time', 'percentage', 'image', 'apexwidget'],
                                'default' => 'text',
                                'description' => 'Column data type for formatting'
                            ],
                            'format' => [
                                'type' => ['string', 'integer'],
                                'description' => 'Format parameter based on data type'
                            ],
                            'leadText' => [
                                'type' => 'string',
                                'description' => 'Text to prepend to the value'
                            ],
                            'trailText' => [
                                'type' => 'string',
                                'description' => 'Text to append to the value'
                            ],
                            'widgetConfig' => [
                                'type' => 'object',
                                'description' => 'Configuration for ApexWidget data type'
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
                            'searchExclude' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Exclude from global search'
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
                                'description' => 'Freeze column (always frozen)'
                            ],
                            //DD 20250714:1400 - BEGIN (Column Locking)
                            'lockColumn' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Whether column should be locked/frozen when lockColumn is true'
                            ],
                            'lockButton' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Whether to show lock/unlock button for this column (requires lockColumn to be true)'
                            ]
                            //DD 20250714:1400 - END
                        ]
                    ]
                ],
                // Visual Configuration
                'gridLines' => [
                    'type' => 'string',
                    'enum' => ['both', 'horizontal', 'vertical', 'none'],
                    'default' => 'both',
                    'description' => 'Grid lines display'
                ],
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
                    'default' => 'id',
                    'description' => 'Unique identifier field'
                ],
                'dataSource' => [
                    'type' => 'object',
                    'description' => 'Data source configuration',
                    'properties' => [
                        'url' => ['type' => 'string', 'required' => true],
                        'method' => ['type' => 'string', 'enum' => ['GET', 'POST'], 'default' => 'GET'],
                        'lazy' => ['type' => ['boolean', 'string'], 'default' => 'auto'],
                        'lazyThreshold' => ['type' => 'integer', 'default' => 1000],
                        'preload' => ['type' => 'boolean', 'default' => false],
                        'countUrl' => ['type' => 'string']
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
                    'description' => 'Number of rows per page'
                ],
                'rowsPerPageOptions' => [
                    'type' => 'array',
                    'default' => [5, 10, 25, 50, 100],
                    'items' => ['type' => 'integer'],
                    'description' => 'Rows per page options'
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
                    'description' => 'Default sort order (1 asc, -1 desc)'
                ],
                'multiSortMeta' => [
                    'type' => 'array',
                    'description' => 'Multi-sort configuration',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'field' => ['type' => 'string'],
                            'order' => ['type' => 'integer', 'enum' => [1, -1, 0]]
                        ]
                    ]
                ],
                // Selection Configuration
                'selectionMode' => [
                    'type' => 'string',
                    'enum' => ['single', 'multiple', 'checkbox'],
                    'description' => 'Selection mode'
                ],
                'selection' => [
                    'type' => 'array',
                    'default' => [],
                    'description' => 'Selected items'
                ],
                'metaKeySelection' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Require meta key for selection'
                ],
                'selectAll' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable select all'
                ],
                // Group Actions Configuration
                'groupActions' => [
                    'type' => 'array',
                    'description' => 'Actions for selected items',
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
                // Global Filter Configuration
                'globalFilter' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable global filter'
                ],
                'globalFilterFields' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Fields to include in global filter'
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
                // Column Toggle Configuration
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
                //DD 20250720:2115 - BEGIN (ReOrder Feature)
                // ReOrder Configuration
                'reOrder' => [
                    'type' => 'object',
                    'description' => 'ReOrder configuration for enhanced column and row reordering',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable ReOrder functionality'
                        ],
                        'reOrderColumn' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable column reordering when reOrder is enabled'
                        ],
                        'reOrderRows' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row reordering when reOrder is enabled - adds drag handles'
                        ],
                        'excludeOrdering' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Comma-separated list of column fields to exclude from reordering (e.g., "price,stock")'
                        ]
                    ]
                ],
                //DD 20250720:2115 - END
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
                    'description' => 'Available export formats (Note: excel and pdf may require additional implementation)'
                ],
                'exportFilename' => [
                    'type' => 'string',
                    'default' => 'data-export',
                    'description' => 'Base filename for exported files (extension will be added automatically)'
                ],
                'conditionalStyles' => [
                    'type' => 'array',
                    'description' => 'Conditional row styling rules',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'column' => [
                                'type' => 'string',
                                'required' => true,
                                'description' => 'Column field name to evaluate'
                            ],
                            'value' => [
                                'type' => ['string', 'number', 'boolean', 'array'],
                                'required' => true,
                                'description' => 'Value to compare against'
                            ],
                            'operator' => [
                                'type' => 'string',
                                'enum' => ['eq', 'ne', 'lt', 'lte', 'gt', 'gte', 'contains', 'startsWith', 'endsWith', 'in', 'notIn'],
                                'default' => 'eq',
                                'description' => 'Comparison operator'
                            ],
                            'priority' => [
                                'type' => 'integer',
                                'default' => 9999,
                                'description' => 'Priority level (1 = highest priority, 9999 = default)'
                            ],
                            'cssClasses' => [
                                'type' => 'string',
                                'description' => 'CSS classes to apply when condition matches'
                            ],
                            'inlineStyles' => [
                                'type' => 'string',
                                'description' => 'Inline CSS styles to apply when condition matches'
                            ],
                            'styleObject' => [
                                'type' => 'object',
                                'description' => 'Style object to apply when condition matches'
                            ]
                        ]
                    ]
                ],
                //DD 20250713:2021 - BEGIN
                // Row Locking Configuration
                'rowLocking' => [
                    'type' => 'object',
                    'description' => 'Row locking configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row locking functionality'
                        ],
                        'maxLockedRows' => [
                            'type' => 'integer',
                            'default' => 5,
                            'description' => 'Maximum number of rows that can be locked simultaneously'
                        ],
                        'lockColumn' => [
                            'type' => 'object',
                            'description' => 'Lock column configuration',
                            'properties' => [
                                'style' => [
                                    'type' => 'string',
                                    'default' => 'width: 4rem',
                                    'description' => 'CSS style for the lock column'
                                ],
                                'frozen' => [
                                    'type' => 'boolean',
                                    'default' => false,
                                    'description' => 'Whether the lock column should be frozen'
                                ],
                                'header' => [
                                    'type' => 'string',
                                    'default' => '',
                                    'description' => 'Header text for the lock column'
                                ]
                            ]
                        ],
                        'lockedRowClasses' => [
                            'type' => 'string',
                            'default' => 'font-bold',
                            'description' => 'CSS classes to apply to locked rows'
                        ],
                        'lockedRowStyles' => [
                            'type' => 'object',
                            'description' => 'CSS style object to apply to locked rows'
                        ]
                    ]
                ],
                //DD 20250713:2021 - END
                //DD 20250714:1400 - BEGIN (Column Locking)
                // Column Locking Configuration
                'columnLocking' => [
                    'type' => 'object',
                    'description' => 'Column locking configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable column locking functionality'
                        ],
                        'buttonPosition' => [
                            'type' => 'string',
                            'enum' => ['header', 'toolbar'],
                            'default' => 'toolbar',
                            'description' => 'Position of column lock buttons'
                        ],
                        'buttonStyle' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'CSS style for column lock buttons'
                        ],
                        'buttonClass' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'CSS classes for column lock buttons'
                        ]
                    ]
                ],
                //DD 20250714:1400 - END
                //DD 20250715:1600 - BEGIN (Row Grouping)
                // Row Grouping Configuration
                'rowGrouping' => [
                    'type' => 'object',
                    'description' => 'Row grouping configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row grouping functionality'
                        ],
                        'rowGroupMode' => [
                            'type' => 'string',
                            'enum' => ['subheader', 'rowspan'],
                            'default' => 'rowspan',
                            'description' => 'Row grouping mode - rowspan or subheader'
                        ],
                        'groupRowsBy' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Array of field names to group by (only first field is used currently)'
                        ],
                        'sortField' => [
                            'type' => 'string',
                            'description' => 'Field to sort by for grouping (usually same as groupRowsBy field)'
                        ],
                        'sortOrder' => [
                            'type' => 'integer',
                            'enum' => [1, -1],
                            'default' => 1,
                            'description' => 'Sort order for grouping (1 for ascending, -1 for descending)'
                        ],
                        // Subheader specific properties
                        'groupRowsTotals' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Array of field names to calculate totals for in group footer'
                        ],
                        'showHeaderTotal' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Show totals in group header'
                        ],
                        'showHeaderRowCount' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Show row count in group header'
                        ],
                        'headerRowCountText' => [
                            'type' => 'string',
                            'default' => 'Items in this group: ',
                            'description' => 'Text to display before row count in header'
                        ],
                        'headerText' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Static text to display in group header'
                        ],
                        'headerTemplate' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Template for group header with {fieldName} placeholders'
                        ],
                        'headerImageField' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Field name containing image URL for group header'
                        ],
                        'headerImageUrl' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Static image URL for group header'
                        ],
                        'headerImagePosition' => [
                            'type' => 'string',
                            'enum' => ['before', 'after'],
                            'default' => 'before',
                            'description' => 'Position of image relative to text in group header'
                        ],
                        'showFooterTotal' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Show totals in group footer'
                        ],
                        'showFooterRowCount' => [
                            'type' => 'boolean',
                            'default' => true,
                            'description' => 'Show row count in group footer'
                        ],
                        'footerRowCountText' => [
                            'type' => 'string',
                            'default' => 'Total items: ',
                            'description' => 'Text to display before row count in footer'
                        ],
                        'footerText' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Static text to display in group footer'
                        ],
                        'footerTemplate' => [
                            'type' => 'string',
                            'default' => 'Total items: {rowCount}',
                            'description' => 'Template for group footer with {rowCount} and other placeholders'
                        ]
                    ]
                ],
                //DD 20250715:1600 - END
                //DD 20250720:2100 - BEGIN (Column Grouping)
                // Column Grouping Configuration
                'columnGrouping' => [
                    'type' => 'object',
                    'description' => 'Column grouping configuration for header and footer groups',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable column grouping functionality'
                        ],
                        'headerGroups' => [
                            'type' => 'array',
                            'description' => 'Array of header group rows',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'cells' => [
                                        'type' => 'array',
                                        'description' => 'Array of cells in this row',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'header' => [
                                                    'type' => 'string',
                                                    'description' => 'Header text (can include {fieldName} placeholders)'
                                                ],
                                                'field' => [
                                                    'type' => 'string',
                                                    'description' => 'Field name for sortable headers'
                                                ],
                                                'sortable' => [
                                                    'type' => 'boolean',
                                                    'default' => false,
                                                    'description' => 'Whether this header cell is sortable'
                                                ],
                                                'rowspan' => [
                                                    'type' => 'integer',
                                                    'minimum' => 1,
                                                    'description' => 'Number of rows this cell spans'
                                                ],
                                                'colspan' => [
                                                    'type' => 'integer',
                                                    'minimum' => 1,
                                                    'description' => 'Number of columns this cell spans'
                                                ],
                                                'headerStyle' => [
                                                    'type' => 'string',
                                                    'description' => 'CSS style for this header cell'
                                                ],
                                                'isTotal' => [
                                                    'type' => 'boolean',
                                                    'default' => false,
                                                    'description' => 'Whether this cell should display calculated totals'
                                                ],
                                                'totalField' => [
                                                    'type' => 'string',
                                                    'description' => 'Field name for total calculation (required if isTotal is true)'
                                                ],
                                                'totalType' => [
                                                    'type' => 'string',
                                                    'enum' => ['sum', 'avg', 'count', 'min', 'max'],
                                                    'default' => 'sum',
                                                    'description' => 'Type of calculation for totals'
                                                ],
                                                'formatType' => [
                                                    'type' => 'string',
                                                    'enum' => ['currency', 'percentage', 'number', 'text'],
                                                    'default' => 'number',
                                                    'description' => 'How to format the calculated total'
                                                ],
                                                'formatDecimals' => [
                                                    'type' => 'integer',
                                                    'minimum' => 0,
                                                    'maximum' => 10,
                                                    'default' => 2,
                                                    'description' => 'Number of decimal places for formatting'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'footerGroups' => [
                            'type' => 'array',
                            'description' => 'Array of footer group rows',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'cells' => [
                                        'type' => 'array',
                                        'description' => 'Array of cells in this row',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'footer' => [
                                                    'type' => 'string',
                                                    'description' => 'Footer text (can include {fieldName} placeholders)'
                                                ],
                                                'rowspan' => [
                                                    'type' => 'integer',
                                                    'minimum' => 1,
                                                    'description' => 'Number of rows this cell spans'
                                                ],
                                                'colspan' => [
                                                    'type' => 'integer',
                                                    'minimum' => 1,
                                                    'description' => 'Number of columns this cell spans'
                                                ],
                                                'footerStyle' => [
                                                    'type' => 'string',
                                                    'description' => 'CSS style for this footer cell'
                                                ],
                                                'isTotal' => [
                                                    'type' => 'boolean',
                                                    'default' => false,
                                                    'description' => 'Whether this cell should display calculated totals'
                                                ],
                                                'totalField' => [
                                                    'type' => 'string',
                                                    'description' => 'Field name for total calculation (required if isTotal is true)'
                                                ],
                                                'totalType' => [
                                                    'type' => 'string',
                                                    'enum' => ['sum', 'avg', 'count', 'min', 'max'],
                                                    'default' => 'sum',
                                                    'description' => 'Type of calculation for totals'
                                                ],
                                                'formatType' => [
                                                    'type' => 'string',
                                                    'enum' => ['currency', 'percentage', 'number', 'text'],
                                                    'default' => 'number',
                                                    'description' => 'How to format the calculated total'
                                                ],
                                                'formatDecimals' => [
                                                    'type' => 'integer',
                                                    'minimum' => 0,
                                                    'maximum' => 10,
                                                    'default' => 2,
                                                    'description' => 'Number of decimal places for formatting'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'groupColumnsTotal' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Array of field names to calculate totals for (outputs "--NaN--" for non-numeric fields)'
                        ],
                        'showTotalsInHeader' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Whether to show calculated totals in header groups'
                        ],
                        'showTotalsInFooter' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Whether to show calculated totals in footer groups'
                        ],
                        'footerText' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Default footer text to display'
                        ],
                        'headerText' => [
                            'type' => 'string',
                            'default' => '',
                            'description' => 'Default header text to display'
                        ]
                    ]
                ],
                //DD 20250720:2100 - END
                // Context Menu Configuration
                'contextMenu' => [
                    'type' => 'object',
                    'description' => 'Context menu configuration for right-click actions',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable context menu functionality'
                        ],
                        'items' => [
                            'type' => 'array',
                            'description' => 'Array of context menu items',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'icon' => [
                                        'type' => 'string',
                                        'description' => 'Icon for menu item - can be URL to image/SVG or PrimeIcon class (e.g., "pi pi-search")'
                                    ],
                                    'label' => [
                                        'type' => 'string',
                                        'required' => true,
                                        'description' => 'Text label for the menu item'
                                    ],
                                    'url' => [
                                        'type' => 'string',
                                        'description' => 'URL to navigate to, supports field placeholders like /product/{productID}'
                                    ],
                                    'urlTarget' => [
                                        'type' => 'string',
                                        'enum' => ['_self', '_blank', '_parent', '_top'],
                                        'default' => '_self',
                                        'description' => 'Link target for URL navigation'
                                    ],
                                    'action' => [
                                        'type' => 'string',
                                        'description' => 'Custom action name to emit when clicked'
                                    ],
                                    'separator' => [
                                        'type' => 'boolean',
                                        'default' => false,
                                        'description' => 'Show separator after this menu item'
                                    ],
                                    'disabled' => [
                                        'type' => 'boolean',
                                        'default' => false,
                                        'description' => 'Disable this menu item'
                                    ],
                                    'visible' => [
                                        'type' => 'boolean',
                                        'default' => true,
                                        'description' => 'Show/hide this menu item'
                                    ]
                                ]
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
                    'enum' => ['session', 'local'],
                    'description' => 'State persistence'
                ],
                'stateKey' => [
                    'type' => 'string',
                    'description' => 'Unique key for state storage'
                ],
                'rowExpansion' => [
                    'type' => 'object',
                    'description' => 'Row expansion configuration',
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Enable row expansion'
                        ],
                        'expanderColumn' => [
                            'type' => 'object',
                            'properties' => [
                                'style' => ['type' => 'string', 'default' => 'width: 5rem'],
                                'frozen' => ['type' => 'boolean', 'default' => false]
                            ]
                        ],
                        'expandControls' => [
                            'type' => 'object',
                            'properties' => [
                                'showExpandAll' => ['type' => 'boolean', 'default' => true],
                                'showCollapseAll' => ['type' => 'boolean', 'default' => true],
                                'expandAllLabel' => ['type' => 'string', 'default' => 'Expand All'],
                                'collapseAllLabel' => ['type' => 'string', 'default' => 'Collapse All'],
                                'position' => ['type' => 'string', 'enum' => ['header', 'toolbar'], 'default' => 'header']
                            ]
                        ],
                        'expandedContent' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => ['type' => 'string', 'enum' => ['datatable', 'custom']],
                                'title' => ['type' => 'string'],
                                'titleField' => ['type' => 'string'],
                                'titleTemplate' => ['type' => 'string'],
                                'dataField' => ['type' => 'string', 'description' => 'Field containing nested data'],
                                'widget' => ['type' => 'object', 'description' => 'DataTableWidget configuration'],
                                'customTemplate' => ['type' => 'string']
                            ]
                        ],
                        'events' => [
                            'type' => 'object',
                            'properties' => [
                                'onExpand' => ['type' => 'boolean', 'default' => false],
                                'onCollapse' => ['type' => 'boolean', 'default' => false]
                            ]
                        ]
                    ]
                ],
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
                'style' => $column['style'] ?? null,
                'bodyStyle' => $column['bodyStyle'] ?? null,
                'headerStyle' => $column['headerStyle'] ?? null,
                'hidden' => $column['hidden'] ?? false,
                'resizable' => $column['resizable'] ?? true,
                'minWidth' => $column['minWidth'] ?? null,
                'maxWidth' => $column['maxWidth'] ?? null,
                'dataType' => $column['dataType'] ?? 'text',
                'format' => $column['format'] ?? null,
                'leadText' => $column['leadText'] ?? '',
                'trailText' => $column['trailText'] ?? '',
                'widgetConfig' => $column['widgetConfig'] ?? null,
                'url' => $column['url'] ?? null,
                'urlTarget' => $column['urlTarget'] ?? '_self',
                'clickable' => $column['clickable'] ?? false,
                'action' => $column['action'] ?? null,
                'actionField' => $column['actionField'] ?? null,
                'searchExclude' => $column['searchExclude'] ?? false,
                'exportable' => $column['exportable'] ?? true,
                'reorderable' => $column['reorderable'] ?? true,
                'frozen' => $column['frozen'] ?? false,
                //DD 20250714:1400 - BEGIN (Column Locking)
                'lockColumn' => $column['lockColumn'] ?? false,
                'lockButton' => $column['lockButton'] ?? false,
                //DD 20250714:1400 - END
            ];
        }, $columns);

        //DD 20250720:2100 - BEGIN (Column Grouping Processing)
        // Process column grouping configuration
        $columnGrouping = $config['columnGrouping'] ?? [];
        if (!empty($columnGrouping) && $columnGrouping['enabled']) {
            // Process header groups
            if (isset($columnGrouping['headerGroups'])) {
                $columnGrouping['headerGroups'] = array_map(function ($row) {
                    if (isset($row['cells'])) {
                        $row['cells'] = array_map(function ($cell) {
                            return [
                                'header' => $cell['header'] ?? '',
                                'field' => $cell['field'] ?? null,
                                'sortable' => $cell['sortable'] ?? false,
                                'rowspan' => $cell['rowspan'] ?? null,
                                'colspan' => $cell['colspan'] ?? null,
                                'headerStyle' => $cell['headerStyle'] ?? null,
                                'isTotal' => $cell['isTotal'] ?? false,
                                'totalField' => $cell['totalField'] ?? null,
                                'totalType' => $cell['totalType'] ?? 'sum',
                                'formatType' => $cell['formatType'] ?? 'number',
                                'formatDecimals' => $cell['formatDecimals'] ?? 2,
                            ];
                        }, $row['cells']);
                    }
                    return $row;
                }, $columnGrouping['headerGroups']);
            }

            // Process footer groups
            if (isset($columnGrouping['footerGroups'])) {
                $columnGrouping['footerGroups'] = array_map(function ($row) {
                    if (isset($row['cells'])) {
                        $row['cells'] = array_map(function ($cell) {
                            return [
                                'footer' => $cell['footer'] ?? '',
                                'rowspan' => $cell['rowspan'] ?? null,
                                'colspan' => $cell['colspan'] ?? null,
                                'footerStyle' => $cell['footerStyle'] ?? null,
                                'isTotal' => $cell['isTotal'] ?? false,
                                'totalField' => $cell['totalField'] ?? null,
                                'totalType' => $cell['totalType'] ?? 'sum',
                                'formatType' => $cell['formatType'] ?? 'number',
                                'formatDecimals' => $cell['formatDecimals'] ?? 2,
                            ];
                        }, $row['cells']);
                    }
                    return $row;
                }, $columnGrouping['footerGroups']);
            }
        }
        //DD 20250720:2100 - END

        //DD 20250720:2115 - BEGIN (ReOrder Feature Processing)
        // Process reOrder configuration
        $reOrder = $config['reOrder'] ?? [];
        if (!empty($reOrder)) {
            $reOrder = [
                'enabled' => $reOrder['enabled'] ?? false,
                'reOrderColumn' => $reOrder['reOrderColumn'] ?? false,
                'reOrderRows' => $reOrder['reOrderRows'] ?? false,
                'excludeOrdering' => $reOrder['excludeOrdering'] ?? '',
            ];
        } else {
            $reOrder = [
                'enabled' => false,
                'reOrderColumn' => false,
                'reOrderRows' => false,
                'excludeOrdering' => '',
            ];
        }
        //DD 20250720:2115 - END

        // Process context menu configuration
        $contextMenu = $config['contextMenu'] ?? [];
        if (!empty($contextMenu) && $contextMenu['enabled']) {
            // Process menu items
            if (isset($contextMenu['items'])) {
                $contextMenu['items'] = array_map(function ($item) {
                    // Handle separator items that only have 'separator' property
                    if (isset($item['separator']) && $item['separator'] === true) {
                        return ['separator' => true];
                    }

                    return [
                        'icon' => $item['icon'] ?? null,
                        'label' => $item['label'] ?? '',
                        'url' => $item['url'] ?? null,
                        'urlTarget' => $item['urlTarget'] ?? '_self',
                        'action' => $item['action'] ?? null,
                        'separator' => $item['separator'] ?? false,
                        'disabled' => $item['disabled'] ?? false,
                        'visible' => $item['visible'] ?? true,
                    ];
                }, $contextMenu['items']);
            }
        } else {
            $contextMenu = [
                'enabled' => false,
                'items' => []
            ];
        }

        // Use parent transform and merge with our specific config
        return array_merge(parent::transform($config), [
            'props' => [
                'widgetId' => $this->getId(),
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
                'multiSortMeta' => $config['multiSortMeta'] ?? [],
                // Selection
                'selectionMode' => $config['selectionMode'] ?? null,
                'selection' => $config['selection'] ?? [],
                'metaKeySelection' => $config['metaKeySelection'] ?? true,
                'selectAll' => $config['selectAll'] ?? false,
                // Group Actions
                'groupActions' => $config['groupActions'] ?? [],
                // Global Filter
                'globalFilter' => $config['globalFilter'] ?? false,
                'globalFilterFields' => $config['globalFilterFields'] ?? [],
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
                //DD 20250720:2115 - BEGIN (ReOrder Feature)
                // ReOrder
                'reOrder' => $reOrder,
                //DD 20250720:2115 - END
                'exportable' => $config['exportable'] ?? false,
                'exportFormats' => $config['exportFormats'] ?? ['csv', 'excel', 'pdf'],
                'exportFilename' => $config['exportFilename'] ?? 'data-export',
                'conditionalStyles' => $config['conditionalStyles'] ?? [],
                //DD 20250713:2021 - BEGIN
                // Row Locking
                'rowLocking' => $config['rowLocking'] ?? [
                    'enabled' => false,
                    'maxLockedRows' => 5,
                    'lockColumn' => [
                        'style' => 'width: 4rem',
                        'frozen' => false,
                        'header' => ''
                    ],
                    'lockedRowClasses' => 'font-bold',
                    'lockedRowStyles' => []
                ],
                //DD 20250713:2021 - END
                //DD 20250714:1400 - BEGIN (Column Locking)
                // Column Locking
                'columnLocking' => $config['columnLocking'] ?? [
                    'enabled' => false,
                    'buttonPosition' => 'toolbar',
                    'buttonStyle' => '',
                    'buttonClass' => ''
                ],
                //DD 20250714:1400 - END
                //DD 20250715:1600 - BEGIN (Row Grouping)
                // Row Grouping
                'rowGrouping' => $config['rowGrouping'] ?? [
                    'enabled' => false,
                    'rowGroupMode' => 'rowspan',
                    'groupRowsBy' => [],
                    'sortField' => null,
                    'sortOrder' => 1,
                    // Subheader properties
                    'groupRowsTotals' => [],
                    'showHeaderTotal' => false,
                    'showHeaderRowCount' => false,
                    'headerRowCountText' => 'Items in this group: ',
                    'headerText' => '',
                    'headerTemplate' => '',
                    'headerImageField' => '',
                    'headerImageUrl' => '',
                    'headerImagePosition' => 'before',
                    'showFooterTotal' => false,
                    'showFooterRowCount' => true,
                    'footerRowCountText' => 'Total items: ',
                    'footerText' => '',
                    'footerTemplate' => 'Total items: {rowCount}'
                ],
                //DD 20250715:1600 - END
                //DD 20250720:2100 - BEGIN (Column Grouping)
                // Column Grouping
                'columnGrouping' => $columnGrouping ?: [
                    'enabled' => false,
                    'headerGroups' => [],
                    'footerGroups' => [],
                    'groupColumnsTotal' => [],
                    'showTotalsInHeader' => false,
                    'showTotalsInFooter' => false,
                    'footerText' => '',
                    'headerText' => ''
                ],
                //DD 20250720:2100 - END
                // Context Menu
                'contextMenu' => $contextMenu,
                // Other
                'loading' => $config['loading'] ?? false,
                'emptyMessage' => $config['emptyMessage'] ?? 'No records found',
                'tableStyle' => $config['tableStyle'] ?? 'min-width: 50rem',
                'tableClass' => $config['tableClass'] ?? null,
                'responsiveLayout' => $config['responsiveLayout'] ?? 'scroll',
                'stateStorage' => $config['stateStorage'] ?? null,
                'stateKey' => $config['stateKey'] ?? null,
                'rowExpansion' => $config['rowExpansion'] ?? ['enabled' => false],
            ]
        ]);
    }
}
