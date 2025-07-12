// resources/js/components/apex/widgets/DataTableWidget.vue
<script setup lang="ts">
import { ref, onMounted, computed, watch, defineAsyncComponent } from 'vue';
import axios from 'axios';
import PMultiSelect from 'primevue/multiselect';
import PButton from 'primevue/button';
import PDataTable from 'primevue/datatable';
import PColumn from 'primevue/column';
import PInputText from 'primevue/inputtext';
import PDivider from 'primevue/divider';
import vTooltip from 'primevue/tooltip';

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

// DD20250710-1240 - Add conditional styling interfaces
interface ConditionalStyle {
    column: string;
    value: any;
    operator?: 'eq' | 'ne' | 'lt' | 'lte' | 'gt' | 'gte' | 'contains' | 'startsWith' | 'endsWith' | 'in' | 'notIn';
    priority?: number; // Priority level (1 = highest priority, 9999 = default for no priority)
    cssClasses?: string;
    inlineStyles?: string;
    styleObject?: Record<string, any>;
}

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
    // DD20250710-1240 - Add conditional styling props
    conditionalStyles?: ConditionalStyle[];
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
    // DD20250710-1240 - Add conditional styling defaults
    conditionalStyles: () => []
});

// DD20250707-2330 BEGIN - Add DataTable ref for export functionality
const dt = ref();
const loading = ref(props.loading);
const data = ref<any[]>([]);
const totalRecords = ref(0);
const filters = ref(props.filters || {});
const lazyParams = ref({});
const selectedItems = ref<any[]>([...props.selection]);
const globalFilterValue = ref('');
const sortField = ref<string | undefined>();
const sortOrder = ref<1 | -1 | 0 | undefined>();
const multiSortMeta = ref<Array<{ field: string; order: 1 | -1 | 0 }>>(props.multiSortMeta || []);
const first = ref(0);
const isLazyMode = ref<boolean>(false); // Determined at runtime
const visibleColumns = ref<Column[]>([]); // Track visible columns as objects

// Initialize visible columns based on hidden property
const initVisibleColumns = () => {
    // Include all data columns that are not marked as hidden
    const dataColumns = props.columns.filter(col => !col.hidden);
    // Always include action columns in visible columns
    visibleColumns.value = [...dataColumns, ...actionColumns.value];
};

// Handle column toggle
const onColumnToggle = (val: Column[]) => {
    // Filter to only data columns (exclude action columns from toggle)
    const selectedDataColumns = val.filter(col => !col.field.startsWith('_action_'));
    // Always keep action columns visible
    visibleColumns.value = [...selectedDataColumns, ...actionColumns.value];
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

// Define emits
const emit = defineEmits<{
    action: [payload: { action: string; data: any; value: any }];
    'crud-action': [payload: { action: string; id: any; data: any }];
    headerAction: [action: string];
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
const columnsWithActions = computed(() => [...allColumns.value, ...actionColumns.value]);

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

// Column options for toggle dropdown (exclude hidden, frozen, and action columns)
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
    // No column filters to initialize
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

// DD20250707-2330 BEGIN - Replace export functionality with PrimeVue native export
const exportData = (format: 'csv' | 'excel' | 'pdf') => {
    if (!dt.value) {
        console.error('DataTable ref not available for export');
        return;
    }

    try {
        // Get visible and exportable columns
        const exportableColumns = visibleColumns.value.filter(col => 
            col.exportable !== false && !col.field.startsWith('_action_')
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
// DD20250707-2330 END

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

// DD20250710-1240 - Add conditional styling functionality
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
    if (!props.conditionalStyles || props.conditionalStyles.length === 0) {
        return '';
    }
    
    // Sort styles by priority (1 = highest priority, 9999 = default)
    const sortedStyles = [...props.conditionalStyles].sort((a, b) => {
        const priorityA = a.priority || 9999;
        const priorityB = b.priority || 9999;
        return priorityA - priorityB; // Ascending order (1 first, 9999 last)
    });
    
    const classes: Record<string, boolean> = {};
    
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
    
    return classes;
};

const getRowStyle = (rowData: any): Record<string, any> => {
    if (!props.conditionalStyles || props.conditionalStyles.length === 0) {
        return {};
    }
    
    // Sort styles by priority (1 = highest priority, 9999 = default)
    const sortedStyles = [...props.conditionalStyles].sort((a, b) => {
        const priorityA = a.priority || 9999;
        const priorityB = b.priority || 9999;
        return priorityA - priorityB; // Ascending order (1 first, 9999 last)
    });
    
    let styleObject = {};
    
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

// Watch for external selection changes
watch(() => props.selection, (newVal) => {
    selectedItems.value = [...newVal];
});
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
        <div v-if="globalFilter || exportable || (hasSelectedItems && groupActions.length > 0)" 
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
            :loading="loading"
            :dataKey="dataKey"
            v-model:selection="selectedItems"
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
            @page="onPage"
            @sort="onSort"
            @row-reorder="onRowReorder"
            @col-reorder="onColReorder"
        >
            <!-- Column Toggle in Header -->
            <template v-if="columnToggle" #header>
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
                :frozen="col.frozen"
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
                    <!-- Action Buttons -->
                    <template v-if="col.field.startsWith('_action_')">
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
</style>