// resources/js/components/apex/widgets/DataTableWidget.vue
<script setup lang="ts">
import { ref, onMounted, computed, watch, defineAsyncComponent } from 'vue';
import axios from 'axios';
import vTooltip from 'primevue/tooltip';

interface Column {
    field: string;
    header: string;
    sortable?: boolean;
    filter?: boolean;
    filterType?: 'text' | 'numeric' | 'date' | 'dropdown' | 'multiselect';
    filterOptions?: Array<{ label: string; value: string }>;
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
    multiSortMeta?: Array<{ field: string; order: number }>;
    // Selection
    selectionMode?: 'single' | 'multiple' | 'checkbox' | null;
    selection?: any[];
    metaKeySelection?: boolean;
    selectAll?: boolean;
    // Group Actions
    groupActions?: GroupAction[];
    // Filters
    filters?: any;
    filterDisplay?: 'menu' | 'row';
    globalFilter?: boolean;
    globalFilterFields?: string[];
    filterMatchModeOptions?: any;
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
    stateStorage?: 'session' | 'local' | null;
    stateKey?: string;
    // DD20250710-1240 - Add conditional styling props
    conditionalStyles?: ConditionalStyle[];
}

const props = withDefaults(defineProps<Props>(), {
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
    selectionMode: null,
    selection: () => [],
    metaKeySelection: true,
    selectAll: false,
    groupActions: () => [],
    filters: () => ({}),
    filterDisplay: 'row',
    globalFilter: false,
    globalFilterFields: () => [],
    filterMatchModeOptions: () => ({}),
    scrollable: false,
    scrollHeight: 'flex',
    virtualScroll: false,
    frozenColumns: 0,
    showView: false,
    showEdit: false,
    showDelete: false,
    showHistory: false,
    showPrint: false,
    crudActions: () => ({
        idField: 'id',
        permissions: {
            view: true,
            edit: true,
            delete: true,
            history: true,
            print: true
        }
    }),
    columnToggle: false,
    columnTogglePosition: 'right',
    resizableColumns: false,
    columnResizeMode: 'fit',
    reorderableColumns: false,
    reorderableRows: false,
    exportable: false,
    exportFormats: () => ['csv', 'excel', 'pdf'],
    exportFilename: 'data-export',
    loading: false,
    emptyMessage: 'No records found',
    tableStyle: 'min-width: 50rem',
    tableClass: '',
    responsiveLayout: 'scroll',
    stateStorage: undefined,
    stateKey: undefined,
    // DD20250710-1240 - Add conditional styling defaults
    conditionalStyles: () => []
});

const emit = defineEmits(['crud-action', 'action']);

// Data management
const data = ref<any[]>([]);
const totalRecords = ref(0);
const loading = ref(false);
const filters = ref({});
const visibleColumns = ref<string[]>([]);
const selectedItems = ref<any[]>([]);
const isLazyMode = ref(false);
const lazyThreshold = ref(props.dataSource?.lazyThreshold || 1000);

// Computed properties
const filteredData = computed(() => {
    if (!data.value) return [];
    return data.value;
});

const selectedCount = computed(() => {
    return selectedItems.value?.length || 0;
});

const recordCount = computed(() => {
    return isLazyMode.value ? totalRecords.value : filteredData.value.length;
});

// Initialize visible columns
const initVisibleColumns = () => {
    visibleColumns.value = props.columns
        .filter(col => !col.hidden)
        .map(col => col.field);
};

// Permissions handling
const permissions = computed(() => {
    return props.crudActions?.permissions || {
        view: true,
        edit: true,
        delete: true,
        history: true,
        print: true
    };
});

// All columns including data and action columns
const allColumns = computed(() => {
    return props.columns.filter(col => !col.hidden);
});

// Action columns
const actionColumns = computed(() => {
    const actions: Column[] = [];
    
    if (props.showView && permissions.value.view !== false) {
        actions.push({
            field: '_action_view',
            header: '',
            sortable: false,
            filter: false,
            filterType: 'text',
            filterOptions: undefined,
            style: 'width: 50px',
            bodyStyle: undefined,
            headerStyle: undefined,
            hidden: false,
            resizable: false,
            minWidth: undefined,
            maxWidth: undefined,
            dataType: 'text',
            format: undefined,
            leadText: '',
            trailText: '',
            widgetConfig: undefined,
            url: undefined,
            urlTarget: '_self',
            clickable: false,
            action: undefined,
            actionField: undefined,
            searchExclude: false,
            exportable: false,
            reorderable: false,
            frozen: true
        });
    }
    
    if (props.showEdit && permissions.value.edit !== false) {
        actions.push({
            field: '_action_edit',
            header: '',
            sortable: false,
            filter: false,
            filterType: 'text',
            filterOptions: undefined,
            style: 'width: 50px',
            bodyStyle: undefined,
            headerStyle: undefined,
            hidden: false,
            resizable: false,
            minWidth: undefined,
            maxWidth: undefined,
            dataType: 'text',
            format: undefined,
            leadText: '',
            trailText: '',
            widgetConfig: undefined,
            url: undefined,
            urlTarget: '_self',
            clickable: false,
            action: undefined,
            actionField: undefined,
            searchExclude: false,
            exportable: false,
            reorderable: false,
            frozen: true
        });
    }
    
    if (props.showDelete && permissions.value.delete !== false) {
        actions.push({
            field: '_action_delete',
            header: '',
            sortable: false,
            filter: false,
            filterType: 'text',
            filterOptions: undefined,
            style: 'width: 50px',
            bodyStyle: undefined,
            headerStyle: undefined,
            hidden: false,
            resizable: false,
            minWidth: undefined,
            maxWidth: undefined,
            dataType: 'text',
            format: undefined,
            leadText: '',
            trailText: '',
            widgetConfig: undefined,
            url: undefined,
            urlTarget: '_self',
            clickable: false,
            action: undefined,
            actionField: undefined,
            searchExclude: false,
            exportable: false,
            reorderable: false,
            frozen: true
        });
    }
    
    if (props.showHistory && permissions.value.history !== false) {
        actions.push({
            field: '_action_history',
            header: '',
            sortable: false,
            filter: false,
            filterType: 'text',
            filterOptions: undefined,
            style: 'width: 50px',
            bodyStyle: undefined,
            headerStyle: undefined,
            hidden: false,
            resizable: false,
            minWidth: undefined,
            maxWidth: undefined,
            dataType: 'text',
            format: undefined,
            leadText: '',
            trailText: '',
            widgetConfig: undefined,
            url: undefined,
            urlTarget: '_self',
            clickable: false,
            action: undefined,
            actionField: undefined,
            searchExclude: false,
            exportable: false,
            reorderable: false,
            frozen: true
        });
    }
    
    if (props.showPrint && permissions.value.print !== false) {
        actions.push({
            field: '_action_print',
            header: '',
            sortable: false,
            filter: false,
            filterType: 'text',
            filterOptions: undefined,
            style: 'width: 50px',
            bodyStyle: undefined,
            headerStyle: undefined,
            hidden: false,
            resizable: false,
            minWidth: undefined,
            maxWidth: undefined,
            dataType: 'text',
            format: undefined,
            leadText: '',
            trailText: '',
            widgetConfig: undefined,
            url: undefined,
            urlTarget: '_self',
            clickable: false,
            action: undefined,
            actionField: undefined,
            searchExclude: false,
            exportable: false,
            reorderable: false,
            frozen: true
        });
    }
    
    return actions;
});

// Combined columns with action columns at the end
const columnsWithActions = computed(() => [...allColumns.value, ...actionColumns.value]);

// Get widget component for ApexWidget type
const getWidgetComponent = (widgetType: string) => {
    return defineAsyncComponent(() => import(`./widgets/${widgetType}Widget.vue`));
};

// Action handlers
const handleCrudAction = (action: string, rowData: any) => {
    const payload = {
        action,
        id: rowData[props.crudActions?.idField || 'id'],
        data: rowData
    };
    emit('crud-action', payload);
};

const handleHeaderAction = (action: string) => {
    emit('action', { action });
};

// Group actions
const handleGroupAction = (action: GroupAction) => {
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

// Export functionality
const exportData = (format: 'csv' | 'excel' | 'pdf') => {
    console.log(`Exporting as ${format}`, {
        filename: props.exportFilename,
        data: data.value,
        columns: props.columns.filter(col => col.exportable !== false)
    });
    // Implement actual export logic here
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

// Initialize filters
const initFilters = () => {
    if (!props.filters) {
        filters.value = {};
    } else {
        filters.value = props.filters;
    }
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
                sortField: event?.sortField ?? null,
                sortOrder: event?.sortOrder ?? 1,
                filters: event?.filters ?? filters.value,
                globalFilter: event?.globalFilter ?? null,
                columns: props.columns
            };

            const response = await axios({
                method: props.dataSource.method || 'GET',
                url: props.dataSource.url,
                params: props.dataSource.method === 'GET' ? params : undefined,
                data: props.dataSource.method === 'POST' ? params : undefined
            });

            data.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
            totalRecords.value = response.data.total || response.data.totalRecords || data.value.length;
        }
    } catch (error) {
        console.error('Error loading data:', error);
        data.value = [];
        totalRecords.value = 0;
    } finally {
        loading.value = false;
    }
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
            await determineLazyMode();
            
            // If we determined lazy mode, load data
            if (isLazyMode.value) {
                await loadData();
            }
            return;
        }
        
        // Default behavior - load all data
        await loadData();
    } catch (error) {
        console.error('Error during initialization:', error);
    }
});

// Watch for changes in selection
watch(() => props.selection, (newSelection) => {
    if (newSelection) {
        selectedItems.value = newSelection;
    }
}, { deep: true });

// Watch for changes in data source
watch(() => props.dataSource, async (newDataSource) => {
    if (newDataSource?.url) {
        await loadData();
    }
}, { deep: true });
</script>

<template>
    <div class="apex-datatable-widget" :class="tableClasses">
        <!-- Header -->
        <div v-if="header" class="datatable-header">
            <div class="header-content">
                <div class="header-text">
                    <h3 v-if="header.title" class="header-title">{{ header.title }}</h3>
                    <p v-if="header.subtitle" class="header-subtitle">{{ header.subtitle }}</p>
                </div>
                <div v-if="header.actions" class="header-actions">
                    <PButton 
                        v-for="action in header.actions" 
                        :key="action.action"
                        :label="action.label"
                        :icon="action.icon"
                        :severity="action.severity"
                        @click="handleHeaderAction(action.action)"
                    />
                </div>
            </div>
        </div>

        <!-- Group Actions -->
        <div v-if="groupActions && groupActions.length > 0 && selectedCount > 0" class="group-actions">
            <div class="selected-info">
                <span>{{ selectedCount }} item(s) selected</span>
            </div>
            <div class="group-buttons">
                <PButton 
                    v-for="action in groupActions" 
                    :key="action.action"
                    :label="action.label"
                    :icon="action.icon"
                    :severity="action.severity"
                    size="small"
                    @click="handleGroupAction(action)"
                />
            </div>
        </div>

        <!-- Column Toggle -->
        <div v-if="columnToggle" class="column-toggle">
            <PMultiSelect 
                v-model="visibleColumns" 
                :options="allColumns"
                option-label="header"
                option-value="field"
                display="chip"
                placeholder="Select Columns"
                :show-clear="true"
                class="column-selector"
            />
        </div>

        <!-- Data Table -->
        <PDataTable
            v-model:selection="selectedItems"
            v-model:filters="filters"
            :value="data"
            :data-key="dataKey"
            :lazy="isLazyMode"
            :loading="loading"
            :total-records="totalRecords"
            :rows="rows"
            :rows-per-page-options="rowsPerPageOptions"
            :paginator="paginator"
            :paginator-position="paginatorPosition"
            :current-page-report-template="currentPageReportTemplate"
            :sort-mode="sortMode"
            :removable-sort="removableSort"
            :default-sort-order="defaultSortOrder"
            :multi-sort-meta="multiSortMeta"
            :selection-mode="selectionMode"
            :meta-key-selection="metaKeySelection"
            :select-all="selectAll"
            :filter-display="filterDisplay"
            :global-filter-fields="globalFilterFields"
            :scrollable="scrollable"
            :scroll-height="scrollHeight"
            :virtual-scroll="virtualScroll"
            :frozen-columns="frozenColumns"
            :resizable-columns="resizableColumns"
            :column-resize-mode="columnResizeMode"
            :reorderable-columns="reorderableColumns"
            :reorderable-rows="reorderableRows"
            :table-style="tableStyle"
            :responsive-layout="responsiveLayout"
            :state-storage="stateStorage"
            :state-key="stateKey"
            :striped-rows="stripedRows"
            :show-gridlines="showGridlines"
            :size="size"
            :empty-message="emptyMessage"
            :row-class="getRowClass"
            :row-style="getRowStyle"
            @page="loadData"
            @sort="loadData"
            @filter="loadData"
        >
            <!-- Columns -->
            <PColumn 
                v-for="column in columnsWithActions" 
                :key="column.field"
                :field="column.field"
                :header="column.header"
                :sortable="column.sortable"
                :filter="column.filter"
                :filter-type="(column as Column).filterType"
                :filter-options="(column as Column).filterOptions"
                :style="column.style"
                :body-style="(column as Column).bodyStyle"
                :header-style="(column as Column).headerStyle"
                :resizable="column.resizable"
                :min-width="(column as Column).minWidth"
                :max-width="(column as Column).maxWidth"
                :exportable="column.exportable"
                :reorderable="column.reorderable"
                :frozen="column.frozen"
                :class="{ 'hidden-column': !visibleColumns.includes(column.field) }"
            >
                <!-- Body template for different data types and actions -->
                <template #body="{ data: rowData, field }">
                    <!-- Action buttons -->
                    <div v-if="field.startsWith('_action_')" class="action-buttons">
                        <PButton 
                            v-if="field === '_action_view'"
                            icon="pi pi-eye"
                            size="small"
                            severity="info"
                            @click="handleCrudAction('view', rowData)"
                            v-tooltip="'View'"
                        />
                        <PButton 
                            v-else-if="field === '_action_edit'"
                            icon="pi pi-pencil"
                            size="small"
                            severity="warning"
                            @click="handleCrudAction('edit', rowData)"
                            v-tooltip="'Edit'"
                        />
                        <PButton 
                            v-else-if="field === '_action_delete'"
                            icon="pi pi-trash"
                            size="small"
                            severity="danger"
                            @click="handleCrudAction('delete', rowData)"
                            v-tooltip="'Delete'"
                        />
                        <PButton 
                            v-else-if="field === '_action_history'"
                            icon="pi pi-history"
                            size="small"
                            severity="secondary"
                            @click="handleCrudAction('history', rowData)"
                            v-tooltip="'History'"
                        />
                        <PButton 
                            v-else-if="field === '_action_print'"
                            icon="pi pi-print"
                            size="small"
                            severity="secondary"
                            @click="handleCrudAction('print', rowData)"
                            v-tooltip="'Print'"
                        />
                    </div>
                    
                    <!-- Regular data display -->
                    <div v-else class="cell-content">
                        {{ rowData[field] }}
                    </div>
                </template>
            </PColumn>
        </PDataTable>

        <!-- Footer -->
        <div v-if="footer" class="datatable-footer">
            <div class="footer-content">
                <div class="footer-text">
                    <span v-if="footer.text">{{ footer.text }}</span>
                    <span v-if="footer.showRecordCount" class="record-count">
                        Total: {{ recordCount }} records
                    </span>
                    <span v-if="footer.showSelectedCount && selectedCount > 0" class="selected-count">
                        Selected: {{ selectedCount }} items
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.apex-datatable-widget {
    width: 100%;
}

.datatable-header {
    margin-bottom: 1rem;
    padding: 1rem;
    background-color: #f9fafb;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

:global(.dark) .datatable-header {
    background-color: #1f2937;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-text {
    flex: 1;
}

.header-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

:global(.dark) .header-title {
    color: #ffffff;
}

.header-subtitle {
    font-size: 0.875rem;
    color: #4b5563;
}

:global(.dark) .header-subtitle {
    color: #9ca3af;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

.group-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background-color: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

:global(.dark) .group-actions {
    background-color: rgba(30, 58, 138, 0.2);
    border-color: #1e40af;
}

.selected-info {
    color: #1d4ed8;
    font-weight: 500;
}

:global(.dark) .selected-info {
    color: #93c5fd;
}

.group-buttons {
    display: flex;
    gap: 0.5rem;
}

.column-toggle {
    margin-bottom: 1rem;
}

.column-selector {
    width: 100%;
    max-width: 28rem;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}

.cell-content {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.datatable-footer {
    margin-top: 1rem;
    padding: 1rem;
    background-color: #f9fafb;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

:global(.dark) .datatable-footer {
    background-color: #1f2937;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-text {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #4b5563;
}

:global(.dark) .footer-text {
    color: #9ca3af;
}

.record-count {
    font-weight: 500;
}

.selected-count {
    font-weight: 500;
    color: #2563eb;
}

:global(.dark) .selected-count {
    color: #60a5fa;
}

.hidden-column {
    display: none;
}
</style>