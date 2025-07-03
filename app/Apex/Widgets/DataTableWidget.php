<?php
// App\Apex\Widgets\DataTableWidget.php

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
                            'description' => 'Show selected items count'
                        ]
                    ]
                ],
                // DD20250710-1240 - Add conditional styling configuration
                'conditionalStyles' => [
                    'type' => 'array',
                    'description' => 'Conditional styling rules for table rows',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'column' => [
                                'type' => 'string',
                                'description' => 'Column name to test against'
                            ],
                            'value' => [
                                'description' => 'Value to compare (any type)'
                            ],
                            'operator' => [
                                'type' => 'string',
                                'enum' => ['eq', 'ne', 'lt', 'lte', 'gt', 'gte', 'contains', 'startsWith', 'endsWith', 'in', 'notIn'],
                                'default' => 'eq',
                                'description' => 'Comparison operator'
                            ],
                            'priority' => [
                                'type' => 'number',
                                'default' => 9999,
                                'description' => 'Priority level (1 = highest priority, lower numbers override higher numbers)'
                            ],
                            'cssClasses' => [
                                'type' => 'string',
                                'description' => 'CSS classes to apply when condition is met'
                            ],
                            'inlineStyles' => [
                                'type' => 'string',
                                'description' => 'Inline CSS styles as string (e.g., "color: red; font-weight: bold")'
                            ],
                            'styleObject' => [
                                'type' => 'object',
                                'description' => 'Style object with CSS properties'
                            ]
                        ],
                        'required' => ['column', 'value'],
                        'additionalProperties' => false
                    ]
                ],
                // Column Configuration
                'columns' => [
                    'type' => 'array',
                    'description' => 'Column definitions',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'field' => [
                                'type' => 'string',
                                'description' => 'Field name in data'
                            ],
                            'header' => [
                                'type' => 'string',
                                'description' => 'Column header text'
                            ],
                            'sortable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Enable sorting for this column'
                            ],
                            'filter' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Enable filtering for this column'
                            ],
                            'filterType' => [
                                'type' => 'string',
                                'enum' => ['text', 'numeric', 'date', 'dropdown', 'multiselect'],
                                'default' => 'text',
                                'description' => 'Type of filter control'
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
                                'description' => 'Column CSS style'
                            ],
                            'bodyStyle' => [
                                'type' => 'string',
                                'description' => 'Body cell CSS style'
                            ],
                            'headerStyle' => [
                                'type' => 'string',
                                'description' => 'Header cell CSS style'
                            ],
                            'hidden' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Hide column by default'
                            ],
                            'resizable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Allow column resizing'
                            ],
                            'minWidth' => [
                                'type' => 'string',
                                'description' => 'Minimum column width'
                            ],
                            'maxWidth' => [
                                'type' => 'string',
                                'description' => 'Maximum column width'
                            ],
                            'dataType' => [
                                'type' => 'string',
                                'enum' => ['text', 'number', 'currency', 'shortdate', 'longdate1', 'longdate2', 'time', 'shortdatetime', 'longdate1time', 'longdate2time', 'percentage', 'image', 'apexwidget'],
                                'default' => 'text',
                                'description' => 'Data type for formatting'
                            ],
                            'format' => [
                                'description' => 'Format configuration (string or number)'
                            ],
                            'leadText' => [
                                'type' => 'string',
                                'description' => 'Text to prepend to cell value'
                            ],
                            'trailText' => [
                                'type' => 'string',
                                'description' => 'Text to append to cell value'
                            ],
                            'widgetConfig' => [
                                'description' => 'Widget configuration for apexwidget type'
                            ],
                            'url' => [
                                'type' => 'string',
                                'description' => 'URL template for clickable cells'
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
                                'description' => 'Make cell clickable'
                            ],
                            'action' => [
                                'type' => 'string',
                                'description' => 'Action to trigger on click'
                            ],
                            'actionField' => [
                                'type' => 'string',
                                'description' => 'Field to use as action parameter'
                            ],
                            'searchExclude' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Exclude from global search'
                            ],
                            'exportable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Include in exports'
                            ],
                            'reorderable' => [
                                'type' => 'boolean',
                                'default' => true,
                                'description' => 'Allow column reordering'
                            ],
                            'frozen' => [
                                'type' => 'boolean',
                                'default' => false,
                                'description' => 'Freeze column during horizontal scrolling'
                            ]
                        ],
                        'required' => ['field', 'header'],
                        'additionalProperties' => false
                    ]
                ],
                // Visual Configuration
                'gridLines' => [
                    'type' => 'string',
                    'enum' => ['both', 'horizontal', 'vertical', 'none'],
                    'default' => 'both',
                    'description' => 'Grid line display mode'
                ],
                'stripedRows' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Enable striped rows'
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
                    'description' => 'Unique key field in data'
                ],
                'dataSource' => [
                    'type' => 'object',
                    'description' => 'Data source configuration',
                    'properties' => [
                        'url' => [
                            'type' => 'string',
                            'description' => 'Data source URL'
                        ],
                        'method' => [
                            'type' => 'string',
                            'enum' => ['GET', 'POST'],
                            'default' => 'GET',
                            'description' => 'HTTP method'
                        ],
                        'lazy' => [
                            'description' => 'Lazy loading mode (boolean or "auto")'
                        ],
                        'lazyThreshold' => [
                            'type' => 'number',
                            'default' => 1000,
                            'description' => 'Threshold for auto lazy mode'
                        ],
                        'preload' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => 'Preload data'
                        ],
                        'countUrl' => [
                            'type' => 'string',
                            'description' => 'URL for count endpoint'
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
                    'description' => 'Pagination position'
                ],
                'rows' => [
                    'type' => 'number',
                    'default' => 10,
                    'description' => 'Rows per page'
                ],
                'rowsPerPageOptions' => [
                    'type' => 'array',
                    'items' => ['type' => 'number'],
                    'default' => [5, 10, 25, 50, 100],
                    'description' => 'Available rows per page options'
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
                    'type' => 'number',
                    'enum' => [1, -1],
                    'default' => 1,
                    'description' => 'Default sort order'
                ],
                'multiSortMeta' => [
                    'type' => 'array',
                    'description' => 'Multi-sort metadata',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'field' => ['type' => 'string'],
                            'order' => ['type' => 'number']
                        ]
                    ]
                ],
                // Other configurations continue...
            ],
            'required' => ['columns'],
            'additionalProperties' => false
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
            ];
        }, $columns);

        // DD20250710-1240 - Process conditional styles
        $conditionalStyles = $config['conditionalStyles'] ?? [];
        $processedConditionalStyles = array_map(function ($style) {
            return [
                'column' => $style['column'],
                'value' => $style['value'],
                'operator' => $style['operator'] ?? 'eq',
                'priority' => $style['priority'] ?? 9999,
                'cssClasses' => $style['cssClasses'] ?? null,
                'inlineStyles' => $style['inlineStyles'] ?? null,
                'styleObject' => $style['styleObject'] ?? null,
            ];
        }, $conditionalStyles);

        return [
            'id' => $this->id,
            'type' => $this->getType(),
            // Header/Footer
            'header' => $config['header'] ?? null,
            'footer' => $config['footer'] ?? ['showRecordCount' => true, 'showSelectedCount' => true],
            // Columns
            'columns' => $processedColumns,
            // DD20250710-1240 - Add conditional styles
            'conditionalStyles' => $processedConditionalStyles,
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
