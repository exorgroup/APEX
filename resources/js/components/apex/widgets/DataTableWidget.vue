// resources/js/components/apex/widgets/DataTableWidget.vue
//works
<script setup lang="ts">
import { ref, onMounted, computed, watch, defineAsyncComponent, nextTick, onUnmounted } from 'vue';
import axios from 'axios';
import PMultiSelect from 'primevue/multiselect';
import PButton from 'primevue/button';
import PDataTable from 'primevue/datatable';
import PColumn from 'primevue/column';
import PColumnGroup from 'primevue/columngroup';
import PRow from 'primevue/row';
import PInputText from 'primevue/inputtext';
import PDivider from 'primevue/divider';
import vTooltip from 'primevue/tooltip';
import WidgetRenderer from '../WidgetRenderer.vue';

interface Column {
    field: string;
    header: string;
    sortable?: boolean;
    style?: string;
    bodyStyle?: string;
    headerStyle?: string;
    hidden?: boolean;
    resizable?: boolean;
    minWidth?: string;
    maxWidth?: string;
    dataType?: 'text' | 'number' | 'currency' | 'shortdate' | 'longdate1' | 'longdate2' | 'time' | 'shortdatetime' | 'longdate1time' | 'longdate2time' | 'percentage' | 'image' | 'apexwidget';
    format?: string | number;
    leadText?: string;
    trailText?: string;
    widgetConfig?: any;
    url?: string;
    urlTarget?: '_self' | '_blank' | '_parent' | '_top';
    clickable?: boolean;
    action?: string;
    actionField?: string;
    searchExclude?: boolean;
    exportable?: boolean;
    reorderable?: boolean;
    frozen?: boolean;
    lockColumn?: boolean;  // New: Whether column should be frozen/locked
    lockButton?: boolean;  // New: Whether to show lock/unlock button for this column
}

interface DataSource {
    url: string;
    method?: 'GET' | 'POST';
    lazy?: boolean | 'auto';
    lazyThreshold?: number;
    preload?: boolean;
    countUrl?: string;
}

interface GroupAction {
    label: string;
    icon?: string;
    action: string;
    severity?: string;
    confirm?: boolean;
    confirmMessage?: string;
}

interface RowExpansion {
    enabled: boolean;
    expanderColumn?: {
        style?: string;
        frozen?: boolean;
    };
    expandControls?: {
        showExpandAll?: boolean;
        showCollapseAll?: boolean;
        expandAllLabel?: string;
        collapseAllLabel?: string;
        position?: 'header' | 'toolbar';
    };
    expandedContent?: {
        type: 'datatable' | 'custom';
        title?: string;
        titleField?: string; // Field to use in title template like "Orders for {titleField}"
        titleTemplate?: string; // Custom title template
        dataField?: string; // Field containing the nested data
        widget?: any; // DataTableWidget configuration for nested table
        customTemplate?: string; // For custom content type
    };
    events?: {
        onExpand?: boolean;
        onCollapse?: boolean;
    };
}

interface ConditionalStyle {
    column: string;
    value: any;
    operator?: 'eq' | 'ne' | 'lt' | 'lte' | 'gt' | 'gte' | 'contains' | 'startsWith' | 'endsWith' | 'in' | 'notIn';
    priority?: number; // Priority level (1 = highest priority, 9999 = default for no priority)
    cssClasses?: string;
    inlineStyles?: string;
    styleObject?: Record<string, any>;
}

//DD 20250713:2021 - BEGIN
interface RowLocking {
    enabled: boolean;
    maxLockedRows?: number;
    lockColumn?: {
        style?: string;
        frozen?: boolean;
        header?: string;
    };
    lockedRowClasses?: string;
    lockedRowStyles?: Record<string, any>;
}
//DD 20250713:2021 - END

//DD 20250714:1400 - BEGIN (Column Locking)
interface ColumnLocking {
    enabled: boolean;
    buttonPosition?: 'header' | 'toolbar';
    buttonStyle?: string;
    buttonClass?: string;
}
//DD 20250714:1400 - END

//DD 20250715:1600 - BEGIN (Row Grouping)
interface RowGrouping {
    enabled: boolean;
    rowGroupMode?: 'subheader' | 'rowspan';
    groupRowsBy?: string[];
    sortField?: string;
    sortOrder?: 1 | -1;
    // Subheader specific properties
    groupRowsTotals?: string[]; // Fields to calculate totals for
    showHeaderTotal?: boolean;
    showHeaderRowCount?: boolean;
    headerRowCountText?: string;
    headerText?: string;
    headerTemplate?: string; // Template with {fieldName} placeholders
    headerImageField?: string; // Field containing image URL
    headerImageUrl?: string; // Static image URL
    headerImagePosition?: 'before' | 'after'; // Position relative to text
    showFooterTotal?: boolean;
    showFooterRowCount?: boolean;
    footerRowCountText?: string;
    footerText?: string;
    footerTemplate?: string; // Template with {rowCount} and other placeholders
}
//DD 20250715:1600 - END

//DD 20250720:2100 - BEGIN (Column Grouping)
interface ColumnGroupCell {
    header?: string;
    footer?: string;
    field?: string;
    sortable?: boolean;
    rowspan?: number;
    colspan?: number;
    headerStyle?: string;
    footerStyle?: string;
    isTotal?: boolean; // Flag to indicate this cell should show calculated totals
    totalField?: string; // Field name for total calculation
    totalType?: 'sum' | 'avg' | 'count' | 'min' | 'max'; // Type of calculation
    formatType?: 'currency' | 'percentage' | 'number' | 'text'; // How to format the total
    formatDecimals?: number; // Number of decimal places
}

interface ColumnGroupRow {
    cells: ColumnGroupCell[];
}

interface ColumnGrouping {
    enabled: boolean;
    headerGroups?: ColumnGroupRow[]; // Array of header group rows
    footerGroups?: ColumnGroupRow[]; // Array of footer group rows
    groupColumnsTotal?: string[]; // Fields to calculate totals for
    showTotalsInHeader?: boolean;
    showTotalsInFooter?: boolean;
    footerText?: string;
    headerText?: string;
}
//DD 20250720:2100 - END

interface Props {
    widgetId: string;
    // Header/Footer
    header?: {
        title?: string;
        subtitle?: string;
        actions?: Array<{ label: string; icon?: string; action: string; severity?: string }>;
    };
    footer?: {
        showRecordCount?: boolean;
        text?: string;
        showSelectedCount?: boolean;
    };
    // Columns
    columns: Column[];
    // Visual
    gridLines?: 'both' | 'horizontal' | 'vertical' | 'none';
    stripedRows?: boolean;
    showGridlines?: boolean;
    size?: 'small' | 'normal' | 'large';
    // Data
    dataKey?: string;
    dataSource?: DataSource;
    // Pagination
    paginator?: boolean;
    paginatorPosition?: 'top' | 'bottom' | 'both';
    rows?: number;
    rowsPerPageOptions?: number[];
    currentPageReportTemplate?: string;
    // Sorting
    sortMode?: 'single' | 'multiple';
    removableSort?: boolean;
    defaultSortOrder?: 1 | -1;
    multiSortMeta?: Array<{ field: string; order: 1 | -1 | 0 }>;
    // Selection
    selectionMode?: 'single' | 'multiple' | 'checkbox' | undefined;
    selection?: any[];
    metaKeySelection?: boolean;
    selectAll?: boolean;
    // Group Actions
    groupActions?: GroupAction[];
    // Global Filter
    globalFilter?: boolean;
    globalFilterFields?: string[];
    // Scroll
    scrollable?: boolean;
    scrollHeight?: string;
    virtualScroll?: boolean;
    frozenColumns?: number;
    // CRUD Actions
    showView?: boolean;
    showEdit?: boolean;
    showDelete?: boolean;
    showHistory?: boolean;
    showPrint?: boolean;
    crudActions?: {
        idField?: string;
        permissions?: {
            view?: boolean;
            edit?: boolean;
            delete?: boolean;
            history?: boolean;
            print?: boolean;
        };
        routes?: {
            view?: string;
            edit?: string;
            delete?: string;
            history?: string;
            print?: string;
        };
    };
    // Column Toggle
    columnToggle?: boolean;
    columnTogglePosition?: 'left' | 'right';
    // Resizable
    resizableColumns?: boolean;
    columnResizeMode?: 'fit' | 'expand';
    // Reorder
    reorderableColumns?: boolean;
    reorderableRows?: boolean;
    // Export
    exportable?: boolean;
    exportFormats?: Array<'csv' | 'excel' | 'pdf'>;
    exportFilename?: string;
    // Other
    loading?: boolean;
    emptyMessage?: string;
    tableStyle?: string;
    tableClass?: string;
    responsiveLayout?: 'scroll' | 'stack';
    stateStorage?: 'session' | 'local' | undefined;
    stateKey?: string;
    staticData?: any[];
    rowExpansion?: RowExpansion;
    conditionalStyles?: ConditionalStyle[];
    //DD 20250713:2021 - BEGIN
    rowLocking?: RowLocking;
    //DD 20250713:2021 - END
    //DD 20250714:1400 - BEGIN (Column Locking)
    columnLocking?: ColumnLocking;
    //DD 20250714:1400 - END
    //DD 20250715:1600 - BEGIN (Row Grouping)
    rowGrouping?: RowGrouping;
    //DD 20250715:1600 - END
    //DD 20250720:2100 - BEGIN (Column Grouping)
    columnGrouping?: ColumnGrouping;
    //DD 20250720:2100 - END
}

const props = withDefaults(defineProps<Props>(), {
    // Defaults
    footer: () => ({ showRecordCount: true, showSelectedCount: true }),
    gridLines: 'both',
    stripedRows: true,
    showGridlines: true,
    size: 'normal',
    dataKey: 'id',
    paginator: true,
    paginatorPosition: 'bottom',
    rows: 10,
    rowsPerPageOptions: () => [5, 10, 25, 50, 100],
    currentPageReportTemplate: 'Showing {first} to {last} of {totalRecords} entries',
    sortMode: 'single',
    removableSort: true,
    defaultSortOrder: 1,
    selectionMode: undefined,
    selection: () => [],
    metaKeySelection: true,
    selectAll: false,
    groupActions: () => [],
    globalFilter: false,
    scrollable: false,
    scrollHeight: 'flex',
    virtualScroll: false,
    frozenColumns: 0,
    reorderableColumns: false,
    reorderableRows: false,
    exportable: false,
    exportFormats: () => ['csv', 'excel', 'pdf'],
    exportFilename: 'data-export',
    loading: false,
    emptyMessage: 'No records found',
    tableStyle: 'min-width: 50rem',
    responsiveLayout: 'scroll',
    stateStorage: undefined,
   // stateKey: undefined,
    rowExpansion: () => ({ enabled: false, expandedContent: undefined }),
    conditionalStyles: () => [],
    //DD 20250713:2021 - BEGIN
    rowLocking: () => ({ 
        enabled: false, 
        maxLockedRows: 5,
        lockColumn: {
            style: 'width: 4rem',
            frozen: false,
            header: ''
        },
        lockedRowClasses: 'font-bold',
        lockedRowStyles: {}
    }),
    //DD 20250713:2021 - END
    //DD 20250714:1400 - BEGIN (Column Locking)
    columnLocking: () => ({
        enabled: false,
        buttonPosition: 'toolbar',
        buttonStyle: '',
        buttonClass: ''
    }),
    //DD 20250714:1400 - END
    //DD 20250715:1600 - BEGIN (Row Grouping)
    rowGrouping: () => ({
        enabled: false,
        rowGroupMode: 'rowspan',
        groupRowsBy: [],
        sortField: undefined,
        sortOrder: 1,
        // Subheader defaults
        groupRowsTotals: [],
        showHeaderTotal: false,
        showHeaderRowCount: false,
        headerRowCountText: 'Items in this group: ',
        headerText: '',
        headerTemplate: '',
        headerImageField: '',
        headerImageUrl: '',
        headerImagePosition: 'before',
        showFooterTotal: false,
        showFooterRowCount: true,
        footerRowCountText: 'Total items: ',
        footerText: '',
        footerTemplate: 'Total items: {rowCount}'
    }),
    //DD 20250715:1600 - END
    //DD 20250720:2100 - BEGIN (Column Grouping)
    columnGrouping: () => ({
        enabled: false,
        headerGroups: [],
        footerGroups: [],
        groupColumnsTotal: [],
        showTotalsInHeader: false,
        showTotalsInFooter: false,
        footerText: '',
        headerText: ''
    })
    //DD 20250720:2100 - END
});

const dt = ref();
const loading = ref(props.loading);
const data = ref<any[]>([]);
const totalRecords = ref(0);
//const filters = ref(props.filters || {});
const lazyParams = ref({});
const selectedItems = ref<any[]>([...props.selection]);
const globalFilterValue = ref('');
const sortField = ref<string | undefined>();
const sortOrder = ref<1 | -1 | 0 | undefined>();
const multiSortMeta = ref<Array<{ field: string; order: 1 | -1 | 0 }>>(props.multiSortMeta || []);
const first = ref(0);
const isLazyMode = ref<boolean>(false); // Determined at runtime
const visibleColumns = ref<Column[]>([]); // Track visible columns as objects

//const dt = ref();

const expandedRows = ref<Record<string, boolean>>({});

//DD 20250713:2021 - BEGIN
// Row locking state
const lockedRows = ref<any[]>([]);
//DD 20250713:2021 - END

//DD 20250714:1400 - BEGIN (Column Locking)
// Column locking state - track which columns are currently locked
const lockedColumnFields = ref<Set<string>>(new Set());

// Initialize locked columns based on lockColumn property
const initializeLockedColumns = () => {
    const initialLockedFields = new Set<string>();
    props.columns.forEach(col => {
        if (col.lockColumn) {
            initialLockedFields.add(col.field);
        }
    });
    lockedColumnFields.value = initialLockedFields;
};

// Check if a column is currently locked
const isColumnLocked = (field: string): boolean => {
    return lockedColumnFields.value.has(field);
};

// Toggle column lock state
const toggleColumnLock = (field: string) => {
    const newLockedFields = new Set(lockedColumnFields.value);
    
    if (newLockedFields.has(field)) {
        newLockedFields.delete(field);
    } else {
        newLockedFields.add(field);
    }
    
    lockedColumnFields.value = newLockedFields;
    
    // Emit event for parent to handle
    emit('column-lock-change', {
        field: field,
        locked: newLockedFields.has(field),
        allLockedFields: Array.from(newLockedFields)
    });
};

// Get columns that have lockButton enabled
const lockableColumns = computed(() => {
    return props.columns.filter(col => col.lockButton);
});

// Check if column locking is enabled and has lockable columns
const hasColumnLocking = computed(() => {
    return props.columnLocking?.enabled && lockableColumns.value.length > 0;
});

// Get column lock button position
const columnLockButtonPosition = computed(() => {
    return props.columnLocking?.buttonPosition || 'toolbar';
});
//DD 20250714:1400 - END

//DD 20250715:1600 - BEGIN (Row Grouping)
// Row grouping computed properties and methods
const hasRowGrouping = computed(() => props.rowGrouping?.enabled || false);

const groupingField = computed(() => {
    if (!hasRowGrouping.value || !props.rowGrouping?.groupRowsBy?.length) {
        return undefined;
    }
    // For both rowspan and subheader mode, use the first grouping field
    return props.rowGrouping.groupRowsBy[0];
});

const groupingSortField = computed(() => {
    if (!hasRowGrouping.value) return undefined;
    return props.rowGrouping?.sortField || groupingField.value;
});

const groupingSortOrder = computed(() => {
    if (!hasRowGrouping.value) return undefined;
    return props.rowGrouping?.sortOrder || 1;
});

// Apply automatic sorting for row grouping
const applySortingForGrouping = () => {
    if (!hasRowGrouping.value || !groupingSortField.value) return;
    
    const sortOrderValue = groupingSortOrder.value || 1;
    
    sortField.value = groupingSortField.value;
    sortOrder.value = sortOrderValue;
    
    // Update multiSortMeta for consistency
    multiSortMeta.value = [{
        field: groupingSortField.value,
        order: sortOrderValue
    }];
};

// Subheader specific methods
const processTemplate = (template: string, data: any, extraParams: Record<string, any> = {}): string => {
    if (!template) return '';
    
    // Replace {fieldName} placeholders with actual values
    return template.replace(/{(\w+(?:\.\w+)*)}/g, (match, fieldPath) => {
        // Handle special parameters like rowCount
        if (extraParams[fieldPath] !== undefined) {
            return String(extraParams[fieldPath]);
        }
        
        // Handle nested field paths like 'representative.name'
        const value = fieldPath.split('.').reduce((obj: any, key: string) => {
            return obj && obj[key] !== undefined ? obj[key] : '';
        }, data);
        
        return String(value || '');
    });
};

const getGroupHeaderContent = (slotProps: any): { 
    imageUrl: string; 
    text: string; 
    imagePosition: string;
    showRowCount: boolean;
    rowCountText: string;
    customText: string;
    rowCount: number;
} => {
    if (!props.rowGrouping || props.rowGrouping.rowGroupMode !== 'subheader') {
        return { 
            imageUrl: '', 
            text: '', 
            imagePosition: 'before',
            showRowCount: false,
            rowCountText: '',
            customText: '',
            rowCount: 0
        };
    }
    
    const data = slotProps.data;
    let imageUrl = '';
    let text = '';
    
    // Determine image URL
    if (props.rowGrouping.headerImageField) {
        // Get image URL from a field in the data
        const imageFieldValue = props.rowGrouping.headerImageField.split('.').reduce((obj: any, key: string) => {
            return obj && obj[key] !== undefined ? obj[key] : '';
        }, data);
        imageUrl = imageFieldValue || '';
    } else if (props.rowGrouping.headerImageUrl) {
        // Use static image URL
        imageUrl = props.rowGrouping.headerImageUrl;
    }
    
    // Determine text content
    if (props.rowGrouping.headerTemplate) {
        // Use template with placeholders
        text = processTemplate(props.rowGrouping.headerTemplate, data);
    } else if (groupingField.value) {
        // Default to the grouping field value
        const fieldValue = groupingField.value.split('.').reduce((obj: any, key: string) => {
            return obj && obj[key] !== undefined ? obj[key] : '';
        }, data);
        text = String(fieldValue || '');
    }
    
    // Calculate row count for this group
    const groupValue = groupingField.value ? 
        groupingField.value.split('.').reduce((obj: any, key: string) => {
            return obj && obj[key] !== undefined ? obj[key] : '';
        }, data) : '';
    const rowCount = calculateGroupRowCount(groupValue);
    
    return {
        imageUrl,
        text,
        imagePosition: props.rowGrouping.headerImagePosition || 'before',
        showRowCount: props.rowGrouping.showHeaderRowCount || false,
        rowCountText: props.rowGrouping.headerRowCountText || 'Items in this group: ',
        customText: props.rowGrouping.headerText || '',
        rowCount
    };
};

const getGroupFooterContent = (slotProps: any): {
    showRowCount: boolean;
    rowCountText: string;
    customText: string;
    rowCount: number;
} => {
    if (!props.rowGrouping || props.rowGrouping.rowGroupMode !== 'subheader') {
        return {
            showRowCount: false,
            rowCountText: '',
            customText: '',
            rowCount: 0
        };
    }
    
    const data = slotProps.data;
    const groupValue = groupingField.value ? 
        groupingField.value.split('.').reduce((obj: any, key: string) => {
            return obj && obj[key] !== undefined ? obj[key] : '';
        }, data) : '';
    
    // Calculate row count for this group
    const rowCount = calculateGroupRowCount(groupValue);
    
    // Calculate totals if specified
    const totals: Record<string, number> = {};
    if (props.rowGrouping.groupRowsTotals?.length) {
        props.rowGrouping.groupRowsTotals.forEach(field => {
            totals[field] = calculateGroupTotal(groupValue, field);
        });
    }
    
    // Process footer template if provided
    let processedFooterText = '';
    if (props.rowGrouping.footerTemplate) {
        processedFooterText = processTemplate(props.rowGrouping.footerTemplate, data, { 
            rowCount, 
            ...totals 
        });
    }
    
    return {
        showRowCount: props.rowGrouping.showFooterRowCount !== false,
        rowCountText: props.rowGrouping.footerRowCountText || 'Total items: ',
        customText: processedFooterText || props.rowGrouping.footerText || '',
        rowCount
    };
};

const calculateGroupRowCount = (groupValue: any): number => {
    if (!groupingField.value) return 0;
    
    const currentData = isLazyMode.value ? data.value : filteredData.value;
    return currentData.filter(item => {
        const itemGroupValue = groupingField.value!.split('.').reduce((obj: any, key: string) => {
            return obj && obj[key] !== undefined ? obj[key] : '';
        }, item);
        return itemGroupValue === groupValue;
    }).length;
};

const calculateGroupTotal = (groupValue: any, field: string): number => {
    if (!groupingField.value) return 0;
    
    const currentData = isLazyMode.value ? data.value : filteredData.value;
    return currentData
        .filter(item => {
            const itemGroupValue = groupingField.value!.split('.').reduce((obj: any, key: string) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, item);
            return itemGroupValue === groupValue;
        })
        .reduce((sum, item) => {
            const fieldValue = field.split('.').reduce((obj: any, key: string) => {
                return obj && obj[key] !== undefined ? obj[key] : 0;
            }, item);
            return sum + (parseFloat(fieldValue) || 0);
        }, 0);
};
//DD 20250715:1600 - END

//DD 20250720:2100 - BEGIN (Column Grouping)
// Column grouping computed properties and methods
const hasColumnGrouping = computed(() => props.columnGrouping?.enabled || false);

const hasHeaderGroups = computed(() => 
    hasColumnGrouping.value && 
    props.columnGrouping?.headerGroups && 
    props.columnGrouping.headerGroups.length > 0
);

const hasFooterGroups = computed(() => 
    hasColumnGrouping.value && 
    props.columnGrouping?.footerGroups && 
    props.columnGrouping.footerGroups.length > 0
);

// Calculate totals for column grouping
const calculateColumnTotal = (field: string, type: 'sum' | 'avg' | 'count' | 'min' | 'max' = 'sum'): number => {
    const currentData = isLazyMode.value ? data.value : filteredData.value;
    
    if (currentData.length === 0) return 0;
    
    const values = currentData.map(item => {
        const value = field.split('.').reduce((obj: any, key: string) => {
            return obj && obj[key] !== undefined ? obj[key] : 0;
        }, item);
        return parseFloat(value) || 0;
    }).filter(val => !isNaN(val));
    
    if (values.length === 0) return 0;
    
    switch (type) {
        case 'sum':
            return values.reduce((sum, val) => sum + val, 0);
        case 'avg':
            return values.reduce((sum, val) => sum + val, 0) / values.length;
        case 'count':
            return values.length;
        case 'min':
            return Math.min(...values);
        case 'max':
            return Math.max(...values);
        default:
            return values.reduce((sum, val) => sum + val, 0);
    }
};

// Format total values based on format type
const formatColumnTotal = (value: number, formatType: string = 'number', decimals: number = 2): string => {
    if (isNaN(value)) return '--NaN--';
    
    switch (formatType) {
        case 'currency':
            return value.toLocaleString('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        case 'percentage':
            return `${value.toFixed(decimals)}%`;
        case 'number':
            return value.toFixed(decimals);
        default:
            return String(value);
    }
};

// Process cell content for column groups
const processCellContent = (cell: ColumnGroupCell): string => {
    if (cell.header) {
        // Handle header with template processing
        return processTemplate(cell.header, {}, {});
    }
    
    if (cell.footer) {
        // Handle footer with template processing
        return processTemplate(cell.footer, {}, {});
    }
    
    if (cell.isTotal && cell.totalField) {
        // Calculate and format total
        const total = calculateColumnTotal(cell.totalField, cell.totalType);
        return formatColumnTotal(total, cell.formatType, cell.formatDecimals);
    }
    
    return '';
};

// Check if column data is numeric for totals
const isNumericField = (field: string): boolean => {
    const column = props.columns.find(col => col.field === field);
    return column?.dataType === 'number' || 
           column?.dataType === 'currency' || 
           column?.dataType === 'percentage';
};
//DD 20250720:2100 - END

// Initialize visible columns based on hidden property
const initVisibleColumns = () => {
    // Include all data columns that are not marked as hidden
    const dataColumns = props.columns.filter(col => !col.hidden);
    // Always include action columns in visible columns
    visibleColumns.value = [...dataColumns, ...actionColumns.value, ...lockActionColumn.value];
};

// Handle column toggle
const onColumnToggle = (val: Column[]) => {
    // Filter to only data columns (exclude action columns from toggle)
    const selectedDataColumns = val.filter(col => !col.field.startsWith('_action_') && !col.field.startsWith('_lock_'));
    // Always keep action columns visible
    visibleColumns.value = [...selectedDataColumns, ...actionColumns.value, ...lockActionColumn.value];
};

// Handle cell click
const handleCellClick = (data: any, column: Column) => {
    if (column.url) {
        // Handle URL navigation
        const url = column.url.replace(/{(\w+)}/g, (match, field) => data[field] || '');
        window.open(url, column.urlTarget || '_self');
    } else if (column.action) {
        // Emit custom action
        const actionField = column.actionField || props.crudActions?.idField || 'id';
        emit('action', {
            action: column.action,
            data: data,
            value: data[actionField]
        });
    }
};

// Handle CRUD actions
const handleCrudAction = (action: string, data: any) => {
    const idField = props.crudActions?.idField || 'id';
    const routes = props.crudActions?.routes || {};
    
    // Type-safe way to access routes
    const actionRoute = routes[action as keyof typeof routes];
    
    if (actionRoute) {
        // Use configured route
        const url = actionRoute.replace(/{id}/g, data[idField]);
        window.location.href = url;
    } else {
        // Emit action event
        emit('crud-action', {
            action: action,
            id: data[idField],
            data: data
        });
    }
};

//DD 20250713:2021 - BEGIN
// Handle row locking toggle
const toggleRowLock = (rowData: any, isLocked: boolean, index: number) => {
    if (!props.rowLocking?.enabled) return;
    
    const dataKey = props.dataKey;
    const maxLocked = props.rowLocking.maxLockedRows || 5;
    
    if (isLocked) {
        // Unlock the row - remove from lockedRows and add back to main data
        const lockedIndex = lockedRows.value.findIndex(row => row[dataKey] === rowData[dataKey]);
        if (lockedIndex !== -1) {
            const unlockedRow = lockedRows.value.splice(lockedIndex, 1)[0];
            
            // Add back to main data array and sort by ID for consistency
            if (isLazyMode.value) {
                data.value.push(unlockedRow);
                data.value.sort((a, b) => a[dataKey] < b[dataKey] ? -1 : 1);
            } else {
                const allData = [...data.value, unlockedRow];
                allData.sort((a, b) => a[dataKey] < b[dataKey] ? -1 : 1);
                data.value = allData;
            }
            
            emit('row-unlock', { row: unlockedRow, index: lockedIndex });
        }
    } else {
        // Lock the row - check limit first
        if (lockedRows.value.length >= maxLocked) {
            console.warn(`Maximum ${maxLocked} rows can be locked`);
            return;
        }
        
        // Remove from main data and add to lockedRows
        const mainDataIndex = (isLazyMode.value ? data.value : filteredData.value)
            .findIndex(row => row[dataKey] === rowData[dataKey]);
            
        if (mainDataIndex !== -1) {
            const lockedRow = data.value.splice(mainDataIndex, 1)[0];
            lockedRows.value.push(lockedRow);
            
            emit('row-lock', { row: lockedRow, index: mainDataIndex });
        }
    }
};

// Check if a row is locked
const isRowLocked = (rowData: any): boolean => {
    if (!props.rowLocking?.enabled) return false;
    const dataKey = props.dataKey;
    return lockedRows.value.some(row => row[dataKey] === rowData[dataKey]);
};

// Check if max locked rows reached
const isMaxLockedRowsReached = computed(() => {
    if (!props.rowLocking?.enabled) return false;
    const maxLocked = props.rowLocking.maxLockedRows || 5;
    return lockedRows.value.length >= maxLocked;
});
//DD 20250713:2021 - END

// Format cell value based on data type
const formatCellValue = (value: any, column: Column): string => {
    if (value === null || value === undefined) return '';
    
    let formattedValue = '';
    
    switch (column.dataType) {
        case 'currency':
            const decimals = typeof column.format === 'number' ? column.format : 2;
            formattedValue = parseFloat(value).toFixed(decimals);
            break;
            
        case 'percentage':
            const percentDecimals = typeof column.format === 'number' ? column.format : 2;
            formattedValue = parseFloat(value).toFixed(percentDecimals);
            break;
            
        case 'number':
            const numberDecimals = typeof column.format === 'number' ? column.format : 0;
            formattedValue = parseFloat(value).toFixed(numberDecimals);
            break;
            
        case 'shortdate':
            formattedValue = formatDate(value, 'short', column.format as string);
            break;
            
        case 'longdate1':
            formattedValue = formatDate(value, 'long1', column.format as string);
            break;
            
        case 'longdate2':
            formattedValue = formatDate(value, 'long2', column.format as string);
            break;
            
        case 'time':
            formattedValue = formatTime(value, column.format as string);
            break;
            
        case 'shortdatetime':
            formattedValue = formatDateTime(value, 'short', column.format as string);
            break;
            
        case 'longdate1time':
            formattedValue = formatDateTime(value, 'long1', column.format as string);
            break;
            
        case 'longdate2time':
            formattedValue = formatDateTime(value, 'long2', column.format as string);
            break;
            
        case 'text':
            const maxLength = typeof column.format === 'number' ? column.format : 0;
            if (maxLength > 0 && value.length > maxLength) {
                formattedValue = value.substring(0, maxLength) + '...';
            } else {
                formattedValue = value;
            }
            break;
            
        default:
            formattedValue = String(value);
    }
    
    // Add lead and trail text
    return `${column.leadText || ''}${formattedValue}${column.trailText || ''}`;
};

// Format date based on culture
const formatDate = (value: any, style: 'short' | 'long1' | 'long2', culture?: string): string => {
    const date = new Date(value);
    if (isNaN(date.getTime())) return String(value);
    
    const cultureSetting = culture || 'US';
    
    if (style === 'short') {
        switch (cultureSetting) {
            case 'EU':
                return date.toLocaleDateString('en-GB'); // DD/MM/YYYY
            case 'Asia':
                return date.toLocaleDateString('zh-CN'); // YYYY/MM/DD
            default: // US
                return date.toLocaleDateString('en-US'); // MM/DD/YYYY
        }
    } else if (style === 'long1') {
        const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'short', year: 'numeric' };
        switch (cultureSetting) {
            case 'EU':
                return date.toLocaleDateString('en-GB', options); // 12 Oct 2020
            case 'Asia':
                return date.toLocaleDateString('zh-CN', options);
            default: // US
                return date.toLocaleDateString('en-US', options); // Oct 12, 2020
        }
    } else { // long2
        const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
        switch (cultureSetting) {
            case 'EU':
                return date.toLocaleDateString('en-GB', options); // 12 October 2020
            case 'Asia':
                return date.toLocaleDateString('zh-CN', options);
            default: // US
                return date.toLocaleDateString('en-US', options); // October 12, 2020
        }
    }
};

// Format time
const formatTime = (value: any, format?: string): string => {
    const date = new Date(value);
    if (isNaN(date.getTime())) return String(value);
    
    const is24Hour = format === '24';
    const options: Intl.DateTimeFormatOptions = {
        hour: 'numeric',
        minute: '2-digit',
        hour12: !is24Hour
    };
    
    return date.toLocaleTimeString('en-US', options);
};

// Format date and time
const formatDateTime = (value: any, dateStyle: 'short' | 'long1' | 'long2', format?: string): string => {
    const parts = format?.split('-') || ['US', '12'];
    const culture = parts[0];
    const timeFormat = parts[1];
    
    const dateStr = formatDate(value, dateStyle, culture);
    const timeStr = formatTime(value, timeFormat);
    
    return `${dateStr} ${timeStr}`;
};

const emit = defineEmits<{
    action: [payload: { action: string; data: any; value: any }];
    'crud-action': [payload: { action: string; id: any; data: any }];
    headerAction: [action: string];
    'row-expand': [event: any];
    'row-collapse': [event: any];
    //DD 20250713:2021 - BEGIN
    'row-lock': [payload: { row: any; index: number }];
    'row-unlock': [payload: { row: any; index: number }];
    //DD 20250713:2021 - END
    //DD 20250714:1400 - BEGIN (Column Locking)
    'column-lock-change': [payload: { field: string; locked: boolean; allLockedFields: string[] }];
    //DD 20250714:1400 - END
}>();

// Initialize lazy mode
if (props.dataSource?.lazy === true) {
    isLazyMode.value = true;
} else if (props.dataSource?.lazy === false) {
    isLazyMode.value = false;
}
// For 'auto' mode, will be set in determineLazyMode()

// Computed properties
const lazyThreshold = computed(() => props.dataSource?.lazyThreshold || 1000);
const showCheckboxColumn = computed(() => props.selectionMode === 'checkbox' || (props.selectionMode === 'multiple' && props.selectAll));
const hasSelectedItems = computed(() => selectedItems.value.length > 0);
const selectedCount = computed(() => selectedItems.value.length);

// Computed property for all columns (visible and hidden)
const allColumns = computed(() => props.columns);

//DD 20250713:2021 - BEGIN
// Lock action column based on row locking configuration
const lockActionColumn = computed(() => {
    const lockColumns: Column[] = [];
    
    if (props.rowLocking?.enabled) {
        lockColumns.push({
            field: '_lock_action',
            header: props.rowLocking.lockColumn?.header || '',
            sortable: false,
            exportable: false,
            reorderable: false,
            resizable: false,
            style: props.rowLocking.lockColumn?.style || 'width: 4rem',
            frozen: props.rowLocking.lockColumn?.frozen || false
        });
    }
    
    return lockColumns;
});
//DD 20250713:2021 - END

// Action columns based on permissions and configuration
const actionColumns = computed(() => {
    const actions: Column[] = [];
    const permissions = props.crudActions?.permissions || {};
    
    if (props.showView && permissions.view !== false) {
        actions.push({
            field: '_action_view',
            header: '',
            sortable: false,
            exportable: false,
            reorderable: false,
            resizable: false,
            style: 'width: 50px',
            frozen: true
        });
    }
    
    if (props.showEdit && permissions.edit !== false) {
        actions.push({
            field: '_action_edit',
            header: '',
            sortable: false,
            exportable: false,
            reorderable: false,
            resizable: false,
            style: 'width: 50px',
            frozen: true
        });
    }
    
    if (props.showDelete && permissions.delete !== false) {
        actions.push({
            field: '_action_delete',
            header: '',
            sortable: false,
            exportable: false,
            reorderable: false,
            resizable: false,
            style: 'width: 50px',
            frozen: true
        });
    }
    
    if (props.showHistory && permissions.history !== false) {
        actions.push({
            field: '_action_history',
            header: '',
            sortable: false,
            exportable: false,
            reorderable: false,
            resizable: false,
            style: 'width: 50px',
            frozen: true
        });
    }
    
    if (props.showPrint && permissions.print !== false) {
        actions.push({
            field: '_action_print',
            header: '',
            sortable: false,
            exportable: false,
            reorderable: false,
            resizable: false,
            style: 'width: 50px',
            frozen: true
        });
    }
    
    return actions;
});

// Combined columns with action columns at the end
//DD 20250713:2021 - BEGIN
const columnsWithActions = computed(() => [...allColumns.value, ...lockActionColumn.value, ...actionColumns.value]);
//DD 20250713:2021 - END

// Get widget component for ApexWidget type
const getWidgetComponent = (type: string) => {
    // Import widget components as needed
    const widgetMap: Record<string, any> = {
        'knob': defineAsyncComponent(() => import('./KnobWidget.vue')),
        'datepicker': defineAsyncComponent(() => import('./DatePickerWidget.vue')),
        'inputtext': defineAsyncComponent(() => import('./InputTextWidget.vue')),
        'inputnumber': defineAsyncComponent(() => import('./InputNumberWidget.vue')),
        'checkbox': defineAsyncComponent(() => import('./CheckboxWidget.vue')),
        'button': defineAsyncComponent(() => import('./ButtonWidget.vue')),
        // Add more widgets as needed
    };
    
    return widgetMap[type] || null;
};

const hasRowExpansion = computed(() => props.rowExpansion?.enabled || false);

const showExpandControls = computed(() => 
    hasRowExpansion.value && props.rowExpansion?.expandControls?.showExpandAll !== false
);

const expandAllLabel = computed(() => 
    props.rowExpansion?.expandControls?.expandAllLabel || 'Expand All'
);

const collapseAllLabel = computed(() => 
    props.rowExpansion?.expandControls?.collapseAllLabel || 'Collapse All'
);

const expandControlsPosition = computed(() => 
    props.rowExpansion?.expandControls?.position || 'header'
);

//DD 20250713:2021 - BEGIN
// Row locking computed properties
const hasRowLocking = computed(() => props.rowLocking?.enabled || false);

const lockedRowsCount = computed(() => lockedRows.value.length);

const maxLockedRows = computed(() => props.rowLocking?.maxLockedRows || 5);
//DD 20250713:2021 - END

// Row expansion methods
const onRowExpand = (event: any) => {
    if (props.rowExpansion?.events?.onExpand) {
        emit('row-expand', event);
    }
};

const onRowCollapse = (event: any) => {
    if (props.rowExpansion?.events?.onCollapse) {
        emit('row-collapse', event);
    }
};

const expandAll = () => {
    if (!hasRowExpansion.value) return;
    
    const currentData = isLazyMode.value ? data.value : filteredData.value;
    expandedRows.value = currentData.reduce((acc: Record<string, boolean>, item: any) => {
        acc[item[props.dataKey]] = true;
        return acc;
    }, {});
};

const collapseAll = () => {
    expandedRows.value = {};
};

const getExpansionTitle = (rowData: any): string => {
    const config = props.rowExpansion?.expandedContent;
    if (!config) return '';
    
    if (config.titleTemplate) {
        // Replace placeholders in template
        return config.titleTemplate.replace(/{(\w+)}/g, (match, field) => rowData[field] || '');
    } else if (config.title && config.titleField) {
        // Use title with field value
        return `${config.title} ${rowData[config.titleField]}`;
    } else if (config.title) {
        // Just use title
        return config.title;
    }
    
    return '';
};

const generateGUID = (): string => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};

const getNestedWidgetConfig = (rowData: any) => {
    const config = props.rowExpansion?.expandedContent;
    if (!config || config.type !== 'datatable' || !config.widget || !config.dataField) return null;
    
    // Get nested data from the specified field
    const nestedData = rowData[config.dataField] || [];
    
    // Create a copy of the widget configuration and set the data
    const widgetProps = { ...config.widget };
    
    // For nested tables, we typically want client-side mode
    if (widgetProps.dataSource) {
        widgetProps.dataSource = null; // Remove URL-based data source
    }
    
    const nestedWidgetProps = {
        ...widgetProps,
        // Pass the nested data directly
        staticData: nestedData,
        // Ensure nested table has a unique ID
        widgetId: `${props.widgetId}_expansion_${rowData[props.dataKey]}`
    };
    
    return {
        id: `nested_${generateGUID()}`,
        type: 'datatable',
        props: nestedWidgetProps
    };
};

const columnOptions = computed(() => {
    return props.columns.filter(col => !col.frozen && !col.hidden);
});

// Computed properties for client-side filtering
const filteredData = computed(() => {
    if (isLazyMode.value) {
        // In lazy mode, data is already filtered by server
        return data.value;
    }
    
    let filtered = [...data.value];
    
    // Apply global filter for client-side mode
    if (globalFilterValue.value && props.globalFilter) {
        const searchStr = globalFilterValue.value.toLowerCase();
        const searchableColumns = props.columns.filter(col => !col.searchExclude).map(col => col.field);
        
        filtered = filtered.filter(item => {
            return searchableColumns.some(field => {
                const value = item[field];
                if (value === null || value === undefined) return false;
                
                // Convert to string for searching
                const strValue = typeof value === 'object' ? JSON.stringify(value) : String(value);
                return strValue.toLowerCase().includes(searchStr);
            });
        });
    }
    
    return filtered;
});

// Update totalRecords for client-side filtering
const clientSideTotalRecords = computed(() => {
    return isLazyMode.value ? totalRecords.value : filteredData.value.length;
});

// Initialize filters
const initFilters = () => {
    // No column filters to initialize - filters were removed
};

// Determine lazy mode for auto
const determineLazyMode = async () => {
    if (!props.dataSource || props.dataSource.lazy !== 'auto') {
        return;
    }

    loading.value = true;
    
    try {
        // If a count URL is provided, use it
        if (props.dataSource.countUrl) {
            const response = await axios.get(props.dataSource.countUrl);
            const count = response.data.count || response.data.total || 0;
            isLazyMode.value = count > lazyThreshold.value;
            console.log(`Auto lazy mode: ${count} records, threshold: ${lazyThreshold.value}, using lazy: ${isLazyMode.value}`);
        } else {
            // Otherwise, try to fetch all data to check count
            const response = await axios({
                method: props.dataSource.method || 'GET',
                url: props.dataSource.url
            });
            
            const responseData = Array.isArray(response.data) ? response.data : (response.data.data || []);
            const count = response.data.total || responseData.length;
            
            // Determine based on count
            isLazyMode.value = count > lazyThreshold.value;
            console.log(`Auto lazy mode: ${count} records, threshold: ${lazyThreshold.value}, using lazy: ${isLazyMode.value}`);
            
            // If we're not using lazy mode, we already have the data
            if (!isLazyMode.value) {
                data.value = responseData;
                totalRecords.value = count;
                loading.value = false;
                return;
            }
        }
    } catch (error) {
        console.error('Error determining lazy mode:', error);
        // Default to lazy mode on error
        isLazyMode.value = true;
    }
    
    loading.value = false;
};

// Load data from server
const loadData = async (event: any = null) => {
    if (!props.dataSource?.url) return;

    loading.value = true;

    try {
        // For non-lazy loading, always fetch all data
        if (!isLazyMode.value) {
            const response = await axios({
                method: props.dataSource.method || 'GET',
                url: props.dataSource.url,
                params: props.dataSource.method === 'GET' ? { columns: props.columns } : undefined,
                data: props.dataSource.method === 'POST' ? { columns: props.columns } : undefined
            });
            
            data.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
            totalRecords.value = data.value.length;
        } else {
            // Lazy loading with server-side processing
            const params = {
                page: event?.page ?? 0,
                first: event?.first ?? 0,
                rows: event?.rows ?? props.rows,
                sortField: event?.sortField || sortField.value,
                sortOrder: event?.sortOrder || sortOrder.value,
                multiSortMeta: props.sortMode === 'multiple' ? multiSortMeta.value : undefined,
                globalFilter: globalFilterValue.value,
                columns: props.columns  // Send column configuration
            };

            const response = await axios({
                method: props.dataSource.method || 'GET',
                url: props.dataSource.url,
                params: props.dataSource.method === 'GET' ? params : undefined,
                data: props.dataSource.method === 'POST' ? params : undefined
            });

            data.value = response.data.data || response.data;
            totalRecords.value = response.data.total || response.data.length;
        }
    } catch (error) {
        console.error('Error loading data:', error);
        data.value = [];
        totalRecords.value = 0;
    } finally {
        loading.value = false;
    }
};

// Event handlers
const onPage = (event: any) => {
    lazyParams.value = event;
    first.value = event.first;
    // Only reload data if using lazy loading
    if (isLazyMode.value) {
        loadData(event);
    }
};

const onSort = (event: any) => {
    lazyParams.value = event;
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    multiSortMeta.value = event.multiSortMeta || [];
    // Only reload data if using lazy loading
    if (isLazyMode.value) {
        loadData(event);
    }
};

const onFilter = (event: any) => {
    // Column filters removed - only global filter supported
    console.log('Column filters not supported');
};

const onGlobalFilter = () => {
    // For client-side mode, the computed property will handle filtering
    if (!isLazyMode.value) {
        // Just trigger reactivity, no need to reload data
        first.value = 0; // Reset to first page
        return;
    }
    
    // For server-side mode, reload data
    const event = { ...lazyParams.value, globalFilter: globalFilterValue.value, first: 0 };
    first.value = 0;
    loadData(event);
};

// Row reorder
const onRowReorder = (event: any) => {
    data.value = event.value;
    // Emit event for parent to handle persistence
    console.log('Row reorder:', event);
};

// Column reorder
const onColReorder = (event: any) => {
    console.log('Column reorder:', event);
};

// Group actions
const executeGroupAction = (action: GroupAction) => {
    if (action.confirm) {
        if (confirm(action.confirmMessage || `Are you sure you want to ${action.label.toLowerCase()}?`)) {
            performGroupAction(action);
        }
    } else {
        performGroupAction(action);
    }
};

const performGroupAction = (action: GroupAction) => {
    console.log(`Executing ${action.action} on ${selectedCount.value} items:`, selectedItems.value);
    // Emit event for parent to handle
};

const exportData = (format: 'csv' | 'excel' | 'pdf') => {
    if (!dt.value) {
        console.error('DataTable ref not available for export');
        return;
    }

    try {
        // Get visible and exportable columns
        const exportableColumns = visibleColumns.value.filter(col => 
            col.exportable !== false && !col.field.startsWith('_action_') && !col.field.startsWith('_lock_')
        );
        
        // Use PrimeVue's built-in export methods
        switch (format) {
            case 'csv':
                dt.value.exportCSV({
                    selectionOnly: false,
                    filename: `${props.exportFilename}.csv`
                });
                break;
                
            case 'excel':
                // Note: PrimeVue doesn't have built-in Excel export, so we'll use CSV as fallback
                console.warn('Excel export not directly supported by PrimeVue, exporting as CSV');
                dt.value.exportCSV({
                    selectionOnly: false,
                    filename: `${props.exportFilename}.csv`
                });
                break;
                
            case 'pdf':
                // Note: PrimeVue doesn't have built-in PDF export
                console.warn('PDF export requires additional implementation');
                // You would need to implement PDF export using a library like jsPDF
                break;
                
            default:
                console.warn(`Unsupported export format: ${format}`);
        }
        
        console.log(`Exported ${format.toUpperCase()}:`, {
            filename: props.exportFilename,
            visibleColumns: exportableColumns.length,
            totalRows: isLazyMode.value ? totalRecords.value : filteredData.value.length
        });
    } catch (error) {
        console.error(`Error exporting ${format}:`, error);
    }
};

// Computed table classes
const tableClasses = computed(() => {
    const classes = [];
    if (props.size === 'small') classes.push('p-datatable-sm');
    if (props.size === 'large') classes.push('p-datatable-lg');
    if (props.gridLines === 'horizontal') classes.push('p-datatable-gridlines-horizontal');
    if (props.gridLines === 'vertical') classes.push('p-datatable-gridlines-vertical');
    if (props.gridLines === 'none') classes.push('p-datatable-gridlines-none');
    if (props.tableClass) classes.push(props.tableClass);
    return classes.join(' ');
});

const evaluateCondition = (rowData: any, style: ConditionalStyle): boolean => {
    const columnValue = rowData[style.column];
    const testValue = style.value;
    const operator = style.operator || 'eq';
    
    switch (operator) {
        case 'eq':
            return columnValue === testValue;
        case 'ne':
            return columnValue !== testValue;
        case 'lt':
            return columnValue < testValue;
        case 'lte':
            return columnValue <= testValue;
        case 'gt':
            return columnValue > testValue;
        case 'gte':
            return columnValue >= testValue;
        case 'contains':
            return String(columnValue).toLowerCase().includes(String(testValue).toLowerCase());
        case 'startsWith':
            return String(columnValue).toLowerCase().startsWith(String(testValue).toLowerCase());
        case 'endsWith':
            return String(columnValue).toLowerCase().endsWith(String(testValue).toLowerCase());
        case 'in':
            return Array.isArray(testValue) && testValue.includes(columnValue);
        case 'notIn':
            return Array.isArray(testValue) && !testValue.includes(columnValue);
        default:
            return false;
    }
};

const getRowClass = (rowData: any): string | string[] | Record<string, boolean> => {
    const classes: Record<string, boolean> = {};
    
    // Apply conditional styles
    if (props.conditionalStyles && props.conditionalStyles.length > 0) {
        // Sort styles by priority (1 = highest priority, 9999 = default)
        const sortedStyles = [...props.conditionalStyles].sort((a, b) => {
            const priorityA = a.priority || 9999;
            const priorityB = b.priority || 9999;
            return priorityA - priorityB; // Ascending order (1 first, 9999 last)
        });
        
        // Apply styles in priority order (lowest priority first, highest priority last to override)
        for (let i = sortedStyles.length - 1; i >= 0; i--) {
            const style = sortedStyles[i];
            if (evaluateCondition(rowData, style) && style.cssClasses) {
                // Split multiple classes and add them to the classes object
                const classNames = style.cssClasses.split(' ').filter(cls => cls.trim());
                classNames.forEach(className => {
                    classes[className.trim()] = true;
                });
            }
        }
    }
    
    //DD 20250713:2021 - BEGIN
    // Apply locked row styling
    if (props.rowLocking?.enabled && isRowLocked(rowData)) {
        if (props.rowLocking.lockedRowClasses) {
            const lockedClasses = props.rowLocking.lockedRowClasses.split(' ').filter(cls => cls.trim());
            lockedClasses.forEach(className => {
                classes[className.trim()] = true;
            });
        }
    }
    //DD 20250713:2021 - END
    
    return classes;
};

const getRowStyle = (rowData: any): Record<string, any> => {
    let styleObject = {};
    
    // Apply conditional styles
    if (props.conditionalStyles && props.conditionalStyles.length > 0) {
        // Sort styles by priority (1 = highest priority, 9999 = default)
        const sortedStyles = [...props.conditionalStyles].sort((a, b) => {
            const priorityA = a.priority || 9999;
            const priorityB = b.priority || 9999;
            return priorityA - priorityB; // Ascending order (1 first, 9999 last)
        });
        
        // Apply styles in priority order (lowest priority first, highest priority last to override)
        for (let i = sortedStyles.length - 1; i >= 0; i--) {
            const style = sortedStyles[i];
            if (evaluateCondition(rowData, style)) {
                // Apply styleObject if provided
                if (style.styleObject) {
                    styleObject = { ...styleObject, ...style.styleObject };
                }
                
                // Parse inline styles if provided
                if (style.inlineStyles) {
                    const parsedStyles = parseInlineStyles(style.inlineStyles);
                    styleObject = { ...styleObject, ...parsedStyles };
                }
            }
        }
    }
    
    //DD 20250713:2021 - BEGIN
    // Apply locked row styling
    if (props.rowLocking?.enabled && isRowLocked(rowData)) {
        if (props.rowLocking.lockedRowStyles) {
            styleObject = { ...styleObject, ...props.rowLocking.lockedRowStyles };
        }
    }
    //DD 20250713:2021 - END
    
    return styleObject;
};

const parseInlineStyles = (inlineStyles: string): Record<string, any> => {
    const styles: Record<string, any> = {};
    
    if (!inlineStyles) return styles;
    
    const declarations = inlineStyles.split(';').filter(decl => decl.trim());
    
    for (const declaration of declarations) {
        const colonIndex = declaration.indexOf(':');
        if (colonIndex !== -1) {
            const property = declaration.substring(0, colonIndex).trim();
            const value = declaration.substring(colonIndex + 1).trim();
            
            // Convert kebab-case to camelCase
            const camelCaseProperty = property.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
            styles[camelCaseProperty] = value;
        }
    }
    
    return styles;
};

// Initialize
onMounted(async () => {
    // Initialize filters first
    initFilters();
    
    // Initialize visible columns
    initVisibleColumns();
    
    //DD 20250714:1400 - BEGIN (Column Locking)
    // Initialize locked columns
    initializeLockedColumns();
    //DD 20250714:1400 - END
    
    //DD 20250715:1600 - BEGIN (Row Grouping)
    // Apply automatic sorting for row grouping if enabled
    if (hasRowGrouping.value) {
        applySortingForGrouping();
    }
    
    // Fix group header positioning for subheader mode with robust calculation
    if (hasRowGrouping.value && props.rowGrouping?.rowGroupMode === 'subheader') {
        // Use the robust calculation method
        ensureHeaderHeightCalculation();
        
        // Also set up resize listener for window resize events
        window.addEventListener('resize', calculateHeaderHeight);
    }
    //DD 20250715:1600 - END
    
    if (props.staticData) {
        data.value = props.staticData;
        totalRecords.value = props.staticData.length;
        loading.value = false;
        return;
    }
    
    // Skip loading if no data source
    if (!props.dataSource?.url) {
        console.log('No data source URL provided');
        return;
    }
    
    try {
        // Handle explicit lazy modes (true/false)
        if (props.dataSource.lazy === true || props.dataSource.lazy === false) {
            isLazyMode.value = props.dataSource.lazy;
            console.log(`Loading data with lazy mode: ${isLazyMode.value}`);
            await loadData();
            return;
        }
        
        // Handle auto mode
        if (props.dataSource.lazy === 'auto') {
            console.log('Auto mode detected, determining best loading strategy...');
            await determineLazyMode();
            
            // If client-side mode and data already loaded, we're done
            if (!isLazyMode.value && data.value.length > 0) {
                console.log('Data already loaded during auto detection');
                return;
            }
            
            // Otherwise load data
            console.log(`Auto mode determined: lazy = ${isLazyMode.value}`);
            await loadData();
        }
    } catch (error) {
        console.error('Error in onMounted:', error);
        loading.value = false;
    }
});

//DD 20250715:1600 - BEGIN (Row Grouping Header Height Fix)
// Store observer references for cleanup
const headerResizeObserver = ref<ResizeObserver | null>(null);
const headerMutationObserver = ref<MutationObserver | null>(null);

// Calculate and set the correct header height for group header positioning
const calculateHeaderHeight = () => {
    if (!dt.value?.$el) return;
    
    const tableEl = dt.value.$el;
    const headerEl = tableEl.querySelector('.p-datatable-thead');
    
    if (headerEl) {
        const headerHeight = headerEl.offsetHeight;
        if (headerHeight > 0) {
            tableEl.style.setProperty('--table-header-height', `${headerHeight}px`);
            
            // Also update any existing group headers
            const groupHeaders = tableEl.querySelectorAll('.p-datatable-row-group-header');
            groupHeaders.forEach((header: HTMLElement) => {
                header.style.top = `${headerHeight}px`;
            });
            
            console.log(`Header height set to: ${headerHeight}px (${groupHeaders.length} group headers updated)`);
        }
    }
};

// Set up persistent observers that don't disconnect
const setupPersistentObservers = () => {
    if (!dt.value?.$el || !hasRowGrouping.value || props.rowGrouping?.rowGroupMode !== 'subheader') {
        return;
    }
    
    const tableEl = dt.value.$el;
    const headerEl = tableEl.querySelector('.p-datatable-thead');
    
    // Set up ResizeObserver for header size changes (don't disconnect)
    if (headerEl && window.ResizeObserver && !headerResizeObserver.value) {
        headerResizeObserver.value = new ResizeObserver((entries) => {
            for (const entry of entries) {
                if (entry.contentRect.height > 0) {
                    calculateHeaderHeight();
                }
            }
        });
        
        headerResizeObserver.value.observe(headerEl);
    }
    
    // Set up MutationObserver for DOM changes (don't disconnect)
    if (window.MutationObserver && !headerMutationObserver.value) {
        headerMutationObserver.value = new MutationObserver((mutations) => {
            let shouldRecalculate = false;
            
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const element = node as HTMLElement;
                            if (element.classList?.contains('p-datatable-row-group-header') ||
                                element.querySelector?.('.p-datatable-row-group-header')) {
                                shouldRecalculate = true;
                            }
                        }
                    });
                }
            });
            
            if (shouldRecalculate) {
                // Small delay to ensure the new elements are rendered
                setTimeout(calculateHeaderHeight, 50);
            }
        });
        
        headerMutationObserver.value.observe(tableEl, {
            childList: true,
            subtree: true
        });
    }
};

// More robust header height calculation with multiple fallbacks
const ensureHeaderHeightCalculation = async () => {
    if (!hasRowGrouping.value || props.rowGrouping?.rowGroupMode !== 'subheader') {
        return;
    }
    
    // Method 1: Immediate calculation after nextTick
    await nextTick();
    calculateHeaderHeight();
    
    // Method 2: Use setTimeout to ensure DOM is fully painted
    setTimeout(() => {
        calculateHeaderHeight();
    }, 100);
    
    // Method 3: Use requestAnimationFrame for after paint
    requestAnimationFrame(() => {
        calculateHeaderHeight();
    });
    
    // Method 4: Set up persistent observers
    setTimeout(() => {
        setupPersistentObservers();
    }, 200);
};

// Clean up observers and event listeners
const cleanupObservers = () => {
    if (headerResizeObserver.value) {
        headerResizeObserver.value.disconnect();
        headerResizeObserver.value = null;
    }
    
    if (headerMutationObserver.value) {
        headerMutationObserver.value.disconnect();
        headerMutationObserver.value = null;
    }
    
    window.removeEventListener('resize', calculateHeaderHeight);
};

// Clean up event listener
onUnmounted(() => {
    cleanupObservers();
});
//DD 20250715:1600 - END

// Watch for external selection changes
watch(() => props.selection, (newVal) => {
    selectedItems.value = [...newVal];
});

//DD 20250715:1600 - BEGIN (Row Grouping Data Watch)
// Watch for data changes to recalculate header height when group headers are rendered
watch(() => data.value, async () => {
    if (hasRowGrouping.value && props.rowGrouping?.rowGroupMode === 'subheader' && data.value.length > 0) {
        // Wait a bit for the DataTable to render the new data and group headers
        await nextTick();
        setTimeout(calculateHeaderHeight, 100);
    }
}, { deep: true });

// Watch for lazy mode changes
watch(() => isLazyMode.value, async () => {
    if (hasRowGrouping.value && props.rowGrouping?.rowGroupMode === 'subheader') {
        await nextTick();
        setTimeout(calculateHeaderHeight, 100);
    }
});

// Watch for pagination changes (rows per page, current page)
watch(() => props.rows, async () => {
    if (hasRowGrouping.value && props.rowGrouping?.rowGroupMode === 'subheader') {
        await nextTick();
        setTimeout(calculateHeaderHeight, 150);
    }
});

watch(() => first.value, async () => {
    if (hasRowGrouping.value && props.rowGrouping?.rowGroupMode === 'subheader') {
        await nextTick();
        setTimeout(calculateHeaderHeight, 100);
    }
});

// Watch for global filter changes
watch(() => globalFilterValue.value, async () => {
    if (hasRowGrouping.value && props.rowGrouping?.rowGroupMode === 'subheader') {
        await nextTick();
        setTimeout(calculateHeaderHeight, 150);
    }
});

// Watch for sorting changes
watch(() => [sortField.value, sortOrder.value], async () => {
    if (hasRowGrouping.value && props.rowGrouping?.rowGroupMode === 'subheader') {
        await nextTick();
        setTimeout(calculateHeaderHeight, 100);
    }
});
//DD 20250715:1600 - END
</script>

<template>
    <div class="apex-datatable-widget">
        <!-- Header -->
        <div v-if="header" class="mb-4 rounded-t-lg border border-b-0 border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 v-if="header.title" class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ header.title }}
                    </h3>
                    <p v-if="header.subtitle" class="text-sm text-gray-600 dark:text-gray-400">
                        {{ header.subtitle }}
                    </p>
                </div>
                <div v-if="header.actions" class="flex gap-2">
                    <PButton
                        v-for="(action, idx) in header.actions"
                        :key="idx"
                        :label="action.label"
                        :icon="action.icon"
                        :severity="action.severity || 'secondary'"
                        size="small"
                        @click="$emit('headerAction', action.action)"
                    />
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div v-if="globalFilter || exportable || (hasSelectedItems && groupActions.length > 0) || (showExpandControls && expandControlsPosition === 'toolbar') || hasRowLocking || (hasColumnLocking && columnLockButtonPosition === 'toolbar')" 
             class="mb-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <!-- Global Filter -->
                <span v-if="globalFilter" class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <PInputText
                        v-model="globalFilterValue"
                        placeholder="Search all columns..."
                        @input="onGlobalFilter"
                        class="w-80"
                    />
                </span>

                <!-- Group Actions -->
                <template v-if="hasSelectedItems && groupActions.length > 0">
                    <PDivider layout="vertical" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ selectedCount }} selected
                    </span>
                    <PButton
                        v-for="(action, idx) in groupActions"
                        :key="idx"
                        :label="action.label"
                        :icon="action.icon"
                        :severity="action.severity || 'danger'"
                        size="small"
                        @click="executeGroupAction(action)"
                    />
                </template>

                <template v-if="showExpandControls && expandControlsPosition === 'toolbar'">
                    <PDivider layout="vertical" />
                    <PButton
                        :label="expandAllLabel"
                        icon="pi pi-plus"
                        text
                        size="small"
                        @click="expandAll"
                    />
                    <PButton
                        :label="collapseAllLabel"
                        icon="pi pi-minus"
                        text
                        size="small"
                        @click="collapseAll"
                    />
                </template>

                <!--DD 20250713:2021 - BEGIN-->
                <!-- Row Locking Info -->
                <template v-if="hasRowLocking">
                    <PDivider layout="vertical" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Locked: {{ lockedRowsCount }}/{{ maxLockedRows }}
                    </span>
                </template>
                <!--DD 20250713:2021 - END-->

                <!--DD 20250714:1400 - BEGIN (Column Locking)-->
                <!-- Column Lock Buttons -->
                <template v-if="hasColumnLocking && columnLockButtonPosition === 'toolbar'">
                    <PDivider layout="vertical" />
                    <PButton
                        v-for="column in lockableColumns"
                        :key="`column-lock-${column.field}`"
                        :label="column.header"
                        :icon="isColumnLocked(column.field) ? 'pi pi-lock' : 'pi pi-lock-open'"
                        :severity="isColumnLocked(column.field) ? 'info' : 'secondary'"
                        size="small"
                        text
                        @click="toggleColumnLock(column.field)"
                        :class="[
                            'flex items-center gap-1',
                            props.columnLocking?.buttonClass || ''
                        ]"
                        :style="props.columnLocking?.buttonStyle || ''"
                        v-tooltip="`${isColumnLocked(column.field) ? 'Unlock' : 'Lock'} ${column.header} column`"
                    />
                </template>
                <!--DD 20250714:1400 - END-->
            </div>

            <!-- Export Buttons -->
            <div v-if="exportable" class="flex items-center gap-2">
                <PButton
                    v-for="format in exportFormats"
                    :key="format"
                    :label="`Export ${format.toUpperCase()}`"
                    icon="pi pi-download"
                    severity="secondary"
                    size="small"
                    @click="exportData(format)"
                />
            </div>
        </div>

        <!-- DataTable -->
        <PDataTable
            ref="dt"
            :value="isLazyMode ? data : filteredData"
            :frozenValue="lockedRows"
            :loading="loading"
            :dataKey="dataKey"
            v-model:selection="selectedItems"
            v-model:expandedRows="expandedRows"
            :paginator="paginator"
            :paginatorPosition="paginatorPosition"
            :rows="rows"
            :rowsPerPageOptions="rowsPerPageOptions"
            :currentPageReportTemplate="currentPageReportTemplate"
            :first="first"
            :totalRecords="clientSideTotalRecords"
            :lazy="isLazyMode"
            :sortMode="sortMode"
            :removableSort="removableSort"
            :defaultSortOrder="defaultSortOrder"
            :multiSortMeta="multiSortMeta"
            :sortField="sortField"
            :sortOrder="sortOrder"
            :selectionMode="selectionMode === 'checkbox' ? undefined : selectionMode"
            :metaKeySelection="metaKeySelection"
            :globalFilterFields="globalFilterFields || columns.map(col => col.field)"
            :scrollable="scrollable"
            :scrollHeight="scrollHeight"
            :virtualScrollerOptions="virtualScroll ? { itemSize: 46 } : undefined"
            :reorderableColumns="reorderableColumns"
            :reorderableRows="reorderableRows"
            :resizableColumns="resizableColumns"
            :columnResizeMode="columnResizeMode"
            :stripedRows="stripedRows"
            :showGridlines="showGridlines"
            :responsiveLayout="responsiveLayout"
            :stateStorage="stateStorage || undefined"
            :stateKey="stateKey"
            :tableStyle="tableStyle"
            :class="tableClasses"
            :rowClass="getRowClass"
            :rowStyle="getRowStyle"
            :rowGroupMode="hasRowGrouping ? rowGrouping?.rowGroupMode : undefined"
            :groupRowsBy="hasRowGrouping ? groupingField : undefined"
            @page="onPage"
            @sort="onSort"
            @row-reorder="onRowReorder"
            @col-reorder="onColReorder"
            @row-expand="onRowExpand"
            @row-collapse="onRowCollapse"
            :pt="{
                bodyrow: ({ props }: { props: any }) => ({
                    class: [{ 'font-bold': props.frozenRow }]
                })
            }"
        >
            <!--DD 20250720:2100 - BEGIN (Column Grouping Header)-->
            <!-- Column Group Header -->
            <PColumnGroup v-if="hasHeaderGroups" type="header">
                <PRow v-for="(row, rowIndex) in columnGrouping?.headerGroups" :key="`header-row-${rowIndex}`">
                    <PColumn
                        v-for="(cell, cellIndex) in row.cells"
                        :key="`header-cell-${rowIndex}-${cellIndex}`"
                        :header="processCellContent(cell)"
                        :field="cell.field"
                        :sortable="cell.sortable || false"
                        :rowspan="cell.rowspan"
                        :colspan="cell.colspan"
                        :headerStyle="cell.headerStyle"
                    />
                </PRow>
            </PColumnGroup>
            <!--DD 20250720:2100 - END-->

            <!--DD 20250714:1400 - BEGIN (Column Locking)-->
            <template v-if="(showExpandControls && expandControlsPosition === 'header') || (hasColumnLocking && columnLockButtonPosition === 'header')" #header>
                <div class="flex items-center justify-between">
                    <!-- Expand Controls -->
                    <div v-if="showExpandControls && expandControlsPosition === 'header'" class="flex gap-2">
                        <PButton
                            :label="expandAllLabel"
                            icon="pi pi-plus"
                            text
                            size="small"
                            @click="expandAll"
                        />
                        <PButton
                            :label="collapseAllLabel"
                            icon="pi pi-minus"
                            text
                            size="small"
                            @click="collapseAll"
                        />
                    </div>
                    
                    <!-- Column Lock Buttons -->
                    <div v-if="hasColumnLocking && columnLockButtonPosition === 'header'" class="flex gap-2">
                        <PButton
                            v-for="column in lockableColumns"
                            :key="`column-lock-${column.field}`"
                            :label="column.header"
                            :icon="isColumnLocked(column.field) ? 'pi pi-lock' : 'pi pi-lock-open'"
                            :severity="isColumnLocked(column.field) ? 'info' : 'secondary'"
                            size="small"
                            text
                            @click="toggleColumnLock(column.field)"
                            :class="[
                                'flex items-center gap-1',
                                props.columnLocking?.buttonClass || ''
                            ]"
                            :style="props.columnLocking?.buttonStyle || ''"
                            v-tooltip="`${isColumnLocked(column.field) ? 'Unlock' : 'Lock'} ${column.header} column`"
                        />
                    </div>
                </div>
            </template>
            <!--DD 20250714:1400 - END-->

            <!-- Column Toggle in Header (fallback if no other header content) -->
            <template v-else-if="columnToggle" #header>
                <div :class="columnTogglePosition === 'left' ? 'text-left' : 'text-right'">
                    <PMultiSelect 
                        :modelValue="visibleColumns" 
                        :options="columnOptions" 
                        optionLabel="header" 
                        @update:modelValue="onColumnToggle"
                        display="chip" 
                        placeholder="Select Columns"
                        class="w-full max-w-md"
                    />
                </div>
            </template>
            
            <!-- Empty Message -->
            <template #empty>
                <div class="flex items-center justify-center p-8 text-gray-500">
                    {{ emptyMessage }}
                </div>
            </template>

            <!-- Loading -->
            <template #loading>
                <div class="flex items-center justify-center p-8">
                    <i class="pi pi-spin pi-spinner text-2xl"></i>
                </div>
            </template>

            <PColumn 
                v-if="hasRowExpansion"
                expander
                :style="rowExpansion?.expanderColumn?.style || 'width: 5rem'"
                :frozen="rowExpansion?.expanderColumn?.frozen || false"
                :exportable="false"
                :reorderableColumn="false"
                :resizeable="false"
            />

            <!-- Selection Column -->
            <PColumn
                v-if="showCheckboxColumn"
                selectionMode="multiple"
                :style="{ width: '3rem' }"
                :frozen="true"
                :exportable="false"
            />

            <!-- Reorder Column -->
            <PColumn
                v-if="reorderableRows"
                :reorderableColumn="false"
                rowReorder
                :style="{ width: '3rem' }"
                :frozen="true"
                :exportable="false"
            />

            <!-- Data Columns (All columns rendered, hidden ones use hidden attribute) -->
            <PColumn
                v-for="col in columnsWithActions"
                :key="col.field"
                :field="col.field"
                :sortable="col.sortable"
                :style="col.style"
                :bodyStyle="col.bodyStyle"
                :headerStyle="col.headerStyle"
                :exportable="col.exportable !== false"
                :reorderableColumn="col.reorderable !== false && reorderableColumns"
                :frozen="col.frozen || (col.lockColumn && isColumnLocked(col.field))"
                :resizeable="col.resizable !== false && resizableColumns"
                :pt="{
                    headerCell: { 
                        hidden: !visibleColumns.some(vc => vc.field === col.field) 
                    },
                    bodyCell: { 
                        hidden: !visibleColumns.some(vc => vc.field === col.field) 
                    }
                }"
            >
                <!-- Column Header Template -->
                <template #header>
                    <span class="font-semibold">{{ col.header }}</span>
                </template>

                <!-- Column Body Template -->
                <template #body="slotProps">
                    <!--DD 20250713:2021 - BEGIN-->
                    <!-- Row Lock Button -->
                    <template v-if="col.field === '_lock_action'">
                        <PButton 
                            type="button" 
                            :icon="isRowLocked(slotProps.data) ? 'pi pi-lock-open' : 'pi pi-lock'" 
                            :disabled="isRowLocked(slotProps.data) ? false : isMaxLockedRowsReached" 
                            text 
                            size="small" 
                            @click="toggleRowLock(slotProps.data, isRowLocked(slotProps.data), slotProps.index)"
                            v-tooltip="isRowLocked(slotProps.data) ? 'Unlock Row' : 'Lock Row'"
                        />
                    </template>
                    <!--DD 20250713:2021 - END-->
                    
                    <!-- Action Buttons -->
                    <template v-else-if="col.field.startsWith('_action_')">
                        <PButton
                            v-if="col.field === '_action_view'"
                            icon="pi pi-eye"
                            rounded
                            severity="success"
                            outlined
                            size="small"
                            @click="handleCrudAction('view', slotProps.data)"
                            v-tooltip="'View'"
                        />
                        <PButton
                            v-else-if="col.field === '_action_edit'"
                            icon="pi pi-pen-to-square"
                            rounded
                            severity="info"
                            outlined
                            size="small"
                            @click="handleCrudAction('edit', slotProps.data)"
                            v-tooltip="'Edit'"
                        />
                        <PButton
                            v-else-if="col.field === '_action_delete'"
                            icon="pi pi-eraser"
                            rounded
                            severity="danger"
                            outlined
                            size="small"
                            @click="handleCrudAction('delete', slotProps.data)"
                            v-tooltip="'Delete'"
                        />
                        <PButton
                            v-else-if="col.field === '_action_history'"
                            icon="pi pi-history"
                            rounded
                            severity="secondary"
                            outlined
                            size="small"
                            @click="handleCrudAction('history', slotProps.data)"
                            v-tooltip="'History'"
                        />
                        <PButton
                            v-else-if="col.field === '_action_print'"
                            icon="pi pi-print"
                            rounded
                            severity="help"
                            outlined
                            size="small"
                            @click="handleCrudAction('print', slotProps.data)"
                            v-tooltip="'Print'"
                        />
                    </template>
                    
                    <!-- Regular Cell Content -->
                    <template v-else>
                        <!-- Image Type -->
                        <img
                            v-if="col.dataType === 'image'"
                            :src="slotProps.data[col.field]"
                            :alt="col.header"
                            :style="{ width: typeof col.format === 'number' ? `${col.format}px` : col.format }"
                            class="h-auto"
                        />
                        
                        <!-- ApexWidget Type -->
                        <component
                            v-else-if="col.dataType === 'apexwidget' && col.widgetConfig"
                            :is="getWidgetComponent(col.widgetConfig.type)"
                            v-bind="{ ...col.widgetConfig, value: slotProps.data[col.field] }"
                        />
                        
                        <!-- Clickable Content -->
                        <a
                            v-else-if="col.url || col.clickable"
                            href="#"
                            @click.prevent="handleCellClick(slotProps.data, col)"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            {{ formatCellValue(slotProps.data[col.field], col) }}
                        </a>
                        
                        <!-- Regular Content -->
                        <span v-else>
                            {{ formatCellValue(slotProps.data[col.field], col) }}
                        </span>
                    </template>
                </template>
            </PColumn>

            <!-- Group Header Template for Subheader Mode -->
            <template v-if="hasRowGrouping && rowGrouping?.rowGroupMode === 'subheader'" #groupheader="slotProps">
                <div class="space-y-2">
                    <!-- Main header row with image and grouping value -->
                    <div class="flex items-center gap-2">
                        <!-- Image before text -->
                        <img 
                            v-if="getGroupHeaderContent(slotProps).imageUrl && getGroupHeaderContent(slotProps).imagePosition === 'before'"
                            :src="getGroupHeaderContent(slotProps).imageUrl"
                            :alt="getGroupHeaderContent(slotProps).text"
                            width="32" 
                            style="vertical-align: middle"
                            class="rounded"
                        />
                        
                        <!-- Text content -->
                        <span class="font-semibold text-lg">{{ getGroupHeaderContent(slotProps).text }}</span>
                        
                        <!-- Image after text -->
                        <img 
                            v-if="getGroupHeaderContent(slotProps).imageUrl && getGroupHeaderContent(slotProps).imagePosition === 'after'"
                            :src="getGroupHeaderContent(slotProps).imageUrl"
                            :alt="getGroupHeaderContent(slotProps).text"
                            width="32" 
                            style="vertical-align: middle"
                            class="rounded"
                        />
                    </div>
                    
                    <!-- Custom header text row (if headerText is provided) -->
                    <div 
                        v-if="getGroupHeaderContent(slotProps).customText"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        {{ getGroupHeaderContent(slotProps).customText }}
                    </div>
                    
                    <!-- Row count row (if showHeaderRowCount is true) -->
                    <div 
                        v-if="getGroupHeaderContent(slotProps).showRowCount"
                        class="text-sm text-gray-500 dark:text-gray-500"
                    >
                        {{ getGroupHeaderContent(slotProps).rowCountText }}{{ getGroupHeaderContent(slotProps).rowCount }}
                    </div>
                </div>
            </template>

            <!-- Group Footer Template for Subheader Mode -->
            <template v-if="hasRowGrouping && rowGrouping?.rowGroupMode === 'subheader'" #groupfooter="slotProps">
                <div class="flex justify-end w-full">
                    <div class="space-y-1 text-right">
                        <!-- Row count row (if showFooterRowCount is true) -->
                        <div 
                            v-if="getGroupFooterContent(slotProps).showRowCount"
                            class="font-bold text-sm"
                        >
                            {{ getGroupFooterContent(slotProps).rowCountText }}{{ getGroupFooterContent(slotProps).rowCount }}
                        </div>
                        
                        <!-- Custom footer text row (if footerText is provided) -->
                        <div 
                            v-if="getGroupFooterContent(slotProps).customText"
                            class="text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{ getGroupFooterContent(slotProps).customText }}
                        </div>
                    </div>
                </div>
            </template>

            <template v-if="hasRowExpansion" #expansion="slotProps">
                <div class="p-4">
                    <!-- Expansion title -->
                    <h5 v-if="getExpansionTitle(slotProps.data)" class="mb-4 text-lg font-semibold">
                        {{ getExpansionTitle(slotProps.data) }}
                    </h5>
                    
                    <!-- Nested DataTable Widget -->
                    <template v-if="rowExpansion?.expandedContent?.type === 'datatable'">
                        <template v-if="getNestedWidgetConfig(slotProps.data)">
                            <WidgetRenderer 
                                :widgets="[getNestedWidgetConfig(slotProps.data)!]"
                                @action="$emit('action', $event)"
                                @crud-action="$emit('crud-action', $event)"
                            />
                        </template>
                    </template>
                    
                    <!-- Custom content (for future extensibility) -->
                    <template v-else-if="rowExpansion?.expandedContent?.type === 'custom'">
                        <div v-html="rowExpansion?.expandedContent?.customTemplate"></div>
                    </template>
                </div>
            </template>

            <!--DD 20250720:2100 - BEGIN (Column Grouping Footer)-->
            <!-- Column Group Footer -->
            <PColumnGroup v-if="hasFooterGroups" type="footer">
                <PRow v-for="(row, rowIndex) in columnGrouping?.footerGroups" :key="`footer-row-${rowIndex}`">
                    <PColumn
                        v-for="(cell, cellIndex) in row.cells"
                        :key="`footer-cell-${rowIndex}-${cellIndex}`"
                        :footer="processCellContent(cell)"
                        :rowspan="cell.rowspan"
                        :colspan="cell.colspan"
                        :footerStyle="cell.footerStyle"
                    />
                </PRow>
            </PColumnGroup>
            <!--DD 20250720:2100 - END-->
        </PDataTable>

        <!-- Footer -->
        <div v-if="footer && (footer.showRecordCount || footer.text || footer.showSelectedCount)" 
             class="rounded-b-lg border border-t-0 border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span v-if="footer.showRecordCount">
                        Total Records: {{ clientSideTotalRecords }}
                    </span>
                    <span v-if="footer.showRecordCount && footer.showSelectedCount && hasSelectedItems" class="mx-2">|</span>
                    <span v-if="footer.showSelectedCount && hasSelectedItems">
                        Selected: {{ selectedCount }}
                    </span>
                    <!--DD 20250713:2021 - BEGIN-->
                    <span v-if="hasRowLocking && lockedRowsCount > 0" class="mx-2">|</span>
                    <span v-if="hasRowLocking && lockedRowsCount > 0">
                        Locked: {{ lockedRowsCount }}
                    </span>
                    <!--DD 20250713:2021 - END-->
                    <!--DD 20250714:1400 - BEGIN (Column Locking)-->
                    <span v-if="hasColumnLocking && lockedColumnFields.size > 0" class="mx-2">|</span>
                    <span v-if="hasColumnLocking && lockedColumnFields.size > 0">
                        Locked Columns: {{ lockedColumnFields.size }}
                    </span>
                    <!--DD 20250714:1400 - END-->
                    <!--DD 20250715:1600 - BEGIN (Row Grouping)-->
                    <span v-if="hasRowGrouping" class="mx-2">|</span>
                    <span v-if="hasRowGrouping">
                        Grouped by: {{ groupingField }}
                    </span>
                    <!--DD 20250715:1600 - END-->
                    <!--DD 20250720:2100 - BEGIN (Column Grouping)-->
                    <span v-if="hasColumnGrouping" class="mx-2">|</span>
                    <span v-if="hasColumnGrouping">
                        Column Groups: {{ (columnGrouping?.headerGroups?.length || 0) + (columnGrouping?.footerGroups?.length || 0) }}
                    </span>
                    <!--DD 20250720:2100 - END-->
                </div>
                <div v-if="footer.text">
                    {{ footer.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.apex-datatable-widget {
    width: 100%;
}

.apex-datatable :deep(.p-datatable) {
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.apex-datatable :deep(.p-paginator) {
    border-width: 0;
    background-color: transparent;
}

.apex-datatable :deep(.p-datatable.p-datatable-sm) {
    font-size: 0.75rem;
}

.apex-datatable :deep(.p-datatable.p-datatable-lg) {
    font-size: 1rem;
}

.apex-datatable :deep(.p-datatable-gridlines-horizontal) .p-datatable-tbody > tr > td {
    border-width: 1px 0;
}

.apex-datatable :deep(.p-datatable-gridlines-vertical) .p-datatable-tbody > tr > td {
    border-width: 0 1px;
}

.apex-datatable :deep(.p-datatable-gridlines-none) .p-datatable-tbody > tr > td {
    border-width: 0;
}

.apex-datatable :deep(.p-column-filter) {
    width: 100%;
}

/* DD 20250715:1600 - BEGIN (Row Grouping Styles) */
/* Enhanced styling for rowspan mode */
.apex-datatable :deep(.p-datatable-rowspan) .p-datatable-tbody > tr > td {
    vertical-align: top;
}

/* Subtle styling for grouped cells */
.apex-datatable :deep(.p-datatable-tbody > tr > td[rowspan]) {
    background-color: rgba(59, 130, 246, 0.05);
    border-right: 2px solid rgba(59, 130, 246, 0.2);
}

/* Dark mode support for grouped cells */
.dark .apex-datatable :deep(.p-datatable-tbody > tr > td[rowspan]) {
    background-color: rgba(59, 130, 246, 0.1);
    border-right: 2px solid rgba(59, 130, 246, 0.3);
}

/* FIX: Ensure group headers position correctly below table headers */
.apex-datatable :deep(.p-datatable-scrollable .p-datatable-thead) {
    position: sticky;
    top: 0;
    z-index: 2;
}

/* Force recalculation of group header positioning for subheader mode */
.apex-datatable :deep(.p-datatable-row-group-header) {
    position: sticky;
    z-index: 1;
    background: white;
}

.dark .apex-datatable :deep(.p-datatable-row-group-header) {
    background: rgb(31 41 55); /* dark:bg-gray-800 equivalent */
}

/* Ensure group headers don't overlap with table headers */
.apex-datatable :deep(.p-datatable-scrollable .p-datatable-row-group-header) {
    top: var(--table-header-height, 56px) !important;
}

/* Calculate and set header height dynamically */
.apex-datatable :deep(.p-datatable-scrollable) {
    --table-header-height: 56px; /* Default height, will be overridden by JS */
}
/* DD 20250715:1600 - END */

/*DD 20250720:2100 - BEGIN (Column Grouping Styles)*/
/* Column grouping header styling */
.apex-datatable :deep(.p-datatable-thead-group) th {
    background-color: rgba(59, 130, 246, 0.1);
    font-weight: 600;
    text-align: center;
}

/* Column grouping footer styling */
.apex-datatable :deep(.p-datatable-tfoot-group) td {
    background-color: rgba(107, 114, 128, 0.1);
    font-weight: 600;
    text-align: center;
}

/* Dark mode support for column groups */
.dark .apex-datatable :deep(.p-datatable-thead-group) th {
    background-color: rgba(59, 130, 246, 0.2);
}

.dark .apex-datatable :deep(.p-datatable-tfoot-group) td {
    background-color: rgba(107, 114, 128, 0.2);
}
/*DD 20250720:2100 - END*/
</style>