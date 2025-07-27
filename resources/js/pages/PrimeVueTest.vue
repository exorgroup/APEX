// resources/js/pages/PrimeVueTest.vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import WidgetRenderer from '@/components/apex/WidgetRenderer.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Props {
    widgets: any[];
}

const props = defineProps<Props>();

// Reactive data 
const counter = ref(0);
const temperature = ref(20);

// Methods
const increment = () => {
    counter.value++;
};

const decrement = () => {
    counter.value--;
};

// Separate widgets by type
const breadcrumbWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'breadcrumb')
);

const knobWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'knob')
);

const datePickerWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'datepicker')
);

const dataTableWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'datatable')
);

//DD 20250720:2115 - BEGIN - Add ReOrder demo widget
const reOrderDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'ReOrder Feature Demo - NEWEST FUNCTIONALITY!')
);
//DD 20250720:2115 - END

//DD1 - Row Grouping DataTable - Subheader Mode
const subheaderRowGroupingDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Row Grouping Demo - Subheader Mode')
);

//DD2 - Column Grouping DataTable Demo
const columnGroupingDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Column Grouping Demo - Header & Footer Groups')
);

//DD3 - Row Grouping DataTable - Rowspan Mode
const rowspanRowGroupingDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Row Grouping Demo - Rowspan Mode')
);

//DD 20250714:1400 - BEGIN - Add column locking demo widget
const columnLockingDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Products with Column Locking - NEWEST FEATURE DEMO')
);
//DD 20250714:1400 - END

//DD 20250713:2021 - BEGIN - Add row locking demo widget
const rowLockingDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Products with Row Locking - Latest Feature Demo')
);
//DD 20250713:2021 - END

// DD20250712-1930 BEGIN - Add row expansion demo widget
const rowExpansionDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Products with Order History - Row Expansion Demo')
);
// DD20250712-1930 END

// DD20250710-1240 - Add conditional styling DataTable
const conditionalStylingDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Conditional Row Styling Demo')
);

// Separate DataTables by their characteristics for better organization
const serverSideDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Product Inventory Management')
);

const clientSideDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Client-Side DataTable')
);

const autoModeDataTables = computed(() => 
    dataTableWidgets.value.filter(w => w.props.header?.title?.includes('Auto Mode'))
);

const advancedColumnDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Advanced Column Features')
);

const actionDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Products with Actions')
);

const dataTypesDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Data Type Formatting Demo')
);

// Handle custom actions from DataTable
const handleCustomAction = (payload: any) => {
    console.log('Custom Action:', payload);
    alert(`Action: ${payload.action}\nValue: ${payload.value}\nData: ${JSON.stringify(payload.data, null, 2)}`);
};

// Handle CRUD actions from DataTable
const handleCrudAction = (payload: any) => {
    console.log('CRUD Action:', payload);
    
    switch (payload.action) {
        case 'view':
            alert(`View Product ID: ${payload.id}`);
            break;
        case 'edit':
            alert(`Edit Product ID: ${payload.id}`);
            break;
        case 'delete':
            if (confirm(`Are you sure you want to delete product ID: ${payload.id}?`)) {
                alert(`Delete Product ID: ${payload.id}`);
            }
            break;
        case 'history':
            alert(`View History for Product ID: ${payload.id}`);
            break;
        case 'print':
            alert(`Print Product ID: ${payload.id}`);
            window.print();
            break;
    }
};

//DD 20250720:2115 - BEGIN - Handle ReOrder events
const handleColumnReorder = (payload: any) => {
    console.log('Column Reordered:', payload);
    // Optional: Show notification or perform analytics
    // toast.add({ 
    //     severity: 'info', 
    //     summary: 'Column Reordered', 
    //     detail: `Columns have been reordered`, 
    //     life: 3000 
    // });
};

const handleRowReorder = (payload: any) => {
    console.log('Row Reordered:', payload);
    // Optional: Show notification or save new order
    // toast.add({ 
    //     severity: 'info', 
    //     summary: 'Row Reordered', 
    //     detail: `Rows have been reordered`, 
    //     life: 3000 
    // });
};
//DD 20250720:2115 - END

//DD 20250714:1400 - BEGIN - Handle column locking events
const handleColumnLockChange = (payload: any) => {
    console.log('Column Lock Changed:', payload);
    const action = payload.locked ? 'locked' : 'unlocked';
    const message = `Column "${payload.field}" has been ${action}. Total locked columns: ${payload.allLockedFields.length}`;
    
    // Optional: Show toast notification
    // toast.add({ 
    //     severity: payload.locked ? 'info' : 'success', 
    //     summary: `Column ${payload.locked ? 'Locked' : 'Unlocked'}`, 
    //     detail: message, 
    //     life: 3000 
    // });
};
//DD 20250714:1400 - END

//DD 20250713:2021 - BEGIN - Handle row locking events
const handleRowLock = (payload: any) => {
    console.log('Row Locked:', payload);
    // Optional: Show toast notification
    // toast.add({ severity: 'info', summary: 'Row Locked', detail: `Locked row with ID: ${payload.row.id}`, life: 3000 });
};

const handleRowUnlock = (payload: any) => {
    console.log('Row Unlocked:', payload);
    // Optional: Show toast notification
    // toast.add({ severity: 'success', summary: 'Row Unlocked', detail: `Unlocked row with ID: ${payload.row.id}`, life: 3000 });
};
//DD 20250713:2021 - END

// DD20250712-1930 BEGIN - Handle row expansion events
const handleRowExpansion = (event: any) => {
    console.log('Row Expanded:', event);
    // Optional: Show toast notification or perform other actions
};

const handleRowCollapse = (event: any) => {
    console.log('Row Collapsed:', event);
    // Optional: Show toast notification or perform other actions
};

const handleHeaderAction = (action: string) => {
    console.log('Header Action:', action);
    
    switch (action) {
        case 'add':
            alert('Add new product clicked');
            break;
        case 'import':
            alert('Import products clicked');
            break;
        case 'refresh':
            alert('Refresh data clicked');
            break;
        //DD 20250720:2115 - BEGIN
        case 'reset-order':
            alert('Reset Order clicked - this would reset column and row order to defaults');
            break;
        case 'export-reordered':
            alert('Export Reordered clicked - this would export data in current display order');
            break;
        //DD 20250720:2115 - END
        //DD2 - Column Grouping actions
        case 'export-analysis':
            alert('Export Analysis clicked - this would export the column grouped data with totals');
            break;
        //DD 20250714:1400 - BEGIN
        case 'lock-important':
            alert('Lock Important Columns clicked - this would lock key columns for analysis');
            break;
        //DD 20250714:1400 - END
        //DD 20250713:2021 - BEGIN
        case 'lock-all':
            alert('Lock All Available clicked - this would lock the maximum allowed rows');
            break;
        //DD 20250713:2021 - END
        default:
            alert(`Header action: ${action}`);
    }
};
// DD20250712-1930 END

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'PrimeVue Test',
        href: '/primevue-test',
    },
];
</script>

<template>
    <Head title="PrimeVue Test" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="mx-auto max-w-7xl">
                <h1 class="mb-8 text-3xl font-bold text-gray-900 dark:text-white">
                    PrimeVue Component Test
                </h1>

                <!-- APEX Widget System Section -->
                <PCard class="mb-8 shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">APEX JSON Widget System</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-8 p-6">
                            <!-- Breadcrumb Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Breadcrumb Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Navigation breadcrumbs rendered from JSON configuration:
                                </p>
                                <WidgetRenderer :widgets="breadcrumbWidgets" />
                            </div>

                            <!-- Knob Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Knob Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Interactive knob controls rendered from JSON configuration:
                                </p>
                                <div class="flex flex-wrap items-center justify-around gap-6 rounded-lg bg-gray-50 p-6 dark:bg-gray-800">
                                    <WidgetRenderer :widgets="knobWidgets" />
                                </div>
                            </div>

                            <!-- DatePicker Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    DatePicker Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Date selection widgets with various configurations:
                                </p>
                                <div class="grid gap-6 md:grid-cols-3">
                                    <WidgetRenderer :widgets="datePickerWidgets" />
                                </div>
                            </div>

                            <!-- DataTable Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    DataTable Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Server-side data tables with sorting, filtering, and pagination:
                                </p>

                                <!--DD 20250720:2115 - BEGIN - ReOrder Feature Demo Section -->
                                <div v-if="reOrderDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🆕 ReOrder Feature Demo - BRAND NEW FUNCTIONALITY!
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-amber-50 to-orange-50 p-4 text-sm dark:from-amber-900/20 dark:to-orange-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                🔄 Advanced Column & Row Reordering System
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-1">
                                                <div class="space-y-2">
                                                    <div class="text-amber-800 dark:text-amber-200">
                                                        <strong>✨ ReOrder Features:</strong>
                                                    </div>
                                                    <ul class="list-inside list-disc space-y-1 text-sm text-amber-700 dark:text-amber-300">
                                                        <li><strong>Column Reordering:</strong> Drag column headers to reorder columns (Price and Rating excluded)</li>
                                                        <li><strong>Row Reordering:</strong> Use drag handles (≡) to reorder rows by dragging them up or down</li>
                                                        <li><strong>Exclusion Control:</strong> Specific columns can be excluded from reordering via configuration</li>
                                                        <li><strong>Visual Feedback:</strong> Drag cursors and hover effects provide clear user guidance</li>
                                                        <li><strong>Event Tracking:</strong> All reorder actions are logged and can trigger custom behaviors</li>
                                                        <li><strong>Status Display:</strong> Toolbar and footer show current ReOrder configuration status</li>
                                                        <li><strong>Smart Integration:</strong> Works seamlessly with sorting, filtering, and pagination</li>
                                                        <li><strong>Export Support:</strong> Reordered data can be exported in the new display order</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Use Case:</strong> Perfect for custom dashboards, personalized data views, and user-defined report layouts where column and row order matters.
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Try It:</strong> Drag "Category" column header to reorder columns, then use the ≡ handles to reorder rows!
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[reOrderDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                            @header-action="handleHeaderAction"
                                            @column-reorder="handleColumnReorder"
                                            @row-reorder="handleRowReorder"
                                        />
                                    </div>
                                </div>
                                <!--DD 20250720:2115 - END -->
                                <!--DD1 - BEGIN - Subheader Row Grouping Demo Section -->
                                <div v-if="subheaderRowGroupingDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🆕 Row Grouping Feature Demo - Subheader Mode
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-violet-50 to-purple-50 p-4 text-sm dark:from-violet-900/20 dark:to-purple-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                📊 Row Grouping with Subheader Mode
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-1">
                                                <div class="space-y-2">
                                                    <div class="text-violet-800 dark:text-violet-200">
                                                        <strong>✨ Subheader Features:</strong>
                                                    </div>
                                                    <ul class="list-inside list-disc space-y-1 text-sm text-violet-700 dark:text-violet-300">
                                                        <li><strong>Group Headers:</strong> Clear visual grouping with dedicated header rows</li>
                                                        <li><strong>Group Footers:</strong> Summary information at the end of each group</li>
                                                        <li><strong>Multi-level Grouping:</strong> Group by Category and then by Inventory Status</li>
                                                        <li><strong>Total Calculations:</strong> Automatic totals for Price and Quantity columns</li>
                                                        <li><strong>Group Counts:</strong> Display count of items in each group</li>
                                                        <li><strong>Customizable Text:</strong> Configure header and footer text templates</li>
                                                        <li><strong>Visual Hierarchy:</strong> Clear separation between groups and data</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Use Case:</strong> Perfect for financial reports, inventory summaries, and any data that needs clear group separation with totals.
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Best For:</strong> Reports that need to show group totals and clear visual separation between categories!
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[subheaderRowGroupingDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>
                                </div>
                                <!--DD1 - END -->                                

                                <!--DD3 - BEGIN - Rowspan Row Grouping Demo Section -->
                                <div v-if="rowspanRowGroupingDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🆕 Row Grouping Feature Demo - Rowspan Mode
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-teal-50 to-cyan-50 p-4 text-sm dark:from-teal-900/20 dark:to-cyan-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                📋 Row Grouping with Rowspan Mode
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-1">
                                                <div class="space-y-2">
                                                    <div class="text-teal-800 dark:text-teal-200">
                                                        <strong>✨ Rowspan Features:</strong>
                                                    </div>
                                                    <ul class="list-inside list-disc space-y-1 text-sm text-teal-700 dark:text-teal-300">
                                                        <li><strong>Spanning Cells:</strong> Group cells span multiple rows for clean visual grouping</li>
                                                        <li><strong>Excel-like Appearance:</strong> Familiar spreadsheet-style grouped display</li>
                                                        <li><strong>Vertical Alignment:</strong> Group cells aligned at top for better readability</li>
                                                        <li><strong>Reduced Repetition:</strong> Group values shown once instead of repeated</li>
                                                        <li><strong>Print Friendly:</strong> Great for printed reports and documentation</li>
                                                        <li><strong>Visual Grouping:</strong> Clear visual association between related rows</li>
                                                        <li><strong>No Extra Headers:</strong> Groups are defined by spanning cells, not additional rows</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Use Case:</strong> Perfect for printed reports, export to Excel, and any interface where traditional spreadsheet-like grouping is preferred.
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Best For:</strong> Professional reports and traditional business applications that need familiar Excel-style grouping!
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[rowspanRowGroupingDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>
                                </div>
                                <!--DD3 - END -->





                                <!--DD 20250714:1400 - BEGIN - Column Locking Demo Section -->
                                <div v-if="columnLockingDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🆕 Column Locking Feature Demo - NEWEST FEATURE!
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-emerald-50 to-teal-50 p-4 text-sm dark:from-emerald-900/20 dark:to-teal-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                🔒 Column Locking with Horizontal Scroll Prevention
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-1">
                                                <div class="space-y-2">
                                                    <div class="text-emerald-800 dark:text-emerald-200">
                                                        <strong>✨ Newest Features:</strong>
                                                    </div>
                                                    <ul class="list-inside list-disc space-y-1 text-sm text-emerald-700 dark:text-emerald-300">
                                                        <li><strong>Lock Columns:</strong> Click column lock buttons (🔒) in toolbar to freeze columns from horizontal scrolling</li>
                                                        <li><strong>Unlock Columns:</strong> Click unlock buttons (🔓) to release locked columns back to normal scrolling</li>
                                                        <li><strong>Initial State:</strong> "Product Code" and "Price" columns start locked (configurable)</li>
                                                        <li><strong>User Control:</strong> Users can lock/unlock "Product Name", "Category", "Stock", and "Rating" columns</li>
                                                        <li><strong>Visual Feedback:</strong> Lock buttons show current state with different icons and colors</li>
                                                        <li><strong>Selective Control:</strong> Some columns (like "Status") have no lock button (developer controlled)</li>
                                                        <li><strong>Wide Tables:</strong> Essential for tables with many columns that extend beyond viewport</li>
                                                        <li><strong>Status Display:</strong> Footer shows count of currently locked columns</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Use Case:</strong> Perfect for financial dashboards, spreadsheet-like data, or any wide table where key columns need to stay visible while exploring other data horizontally.
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Try It:</strong> Lock "Product Name" column, then scroll horizontally to see how it stays visible with "Product Code" and "Price"!
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[columnLockingDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                            @header-action="handleHeaderAction"
                                            @column-lock-change="handleColumnLockChange"
                                        />
                                    </div>
                                </div>
                                <!--DD 20250714:1400 - END -->

                                <!--DD 20250713:2021 - BEGIN - Row Locking Demo Section -->
                                <div v-if="rowLockingDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🆕 Row Locking Feature Demo - BRAND NEW FEATURE!
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-indigo-50 to-purple-50 p-4 text-sm dark:from-indigo-900/20 dark:to-purple-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                🔒 Row Locking with Vertical Scroll Prevention
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-1">
                                                <div class="space-y-2">
                                                    <div class="text-indigo-800 dark:text-indigo-200">
                                                        <strong>✨ Latest Features:</strong>
                                                    </div>
                                                    <ul class="list-inside list-disc space-y-1 text-sm text-indigo-700 dark:text-indigo-300">
                                                        <li><strong>Lock Rows:</strong> Click the lock icon (🔒) to lock up to 3 rows from vertical scrolling</li>
                                                        <li><strong>Unlock Rows:</strong> Click the unlock icon (🔓) to release locked rows back to normal scrolling</li>
                                                        <li><strong>Limit Control:</strong> Maximum of 3 rows can be locked simultaneously (configurable)</li>
                                                        <li><strong>Visual Feedback:</strong> Locked rows have blue styling and appear frozen at top</li>
                                                        <li><strong>Status Display:</strong> Current locked count vs maximum shown in toolbar and footer</li>
                                                        <li><strong>Smart Disable:</strong> Lock button disabled when maximum limit reached</li>
                                                        <li><strong>Event Handling:</strong> Row lock/unlock events (check console for details)</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Use Case:</strong> Perfect for keeping important rows visible while browsing through large datasets. Great for comparison, reference data, or priority items.
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Try It:</strong> Lock some expensive products, then scroll down to compare with others!
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[rowLockingDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                            @header-action="handleHeaderAction"
                                            @row-lock="handleRowLock"
                                            @row-unlock="handleRowUnlock"
                                        />
                                    </div>
                                </div>
                                <!--DD 20250713:2021 - END -->

                                <!-- DD20250712-1930 BEGIN - Row Expansion Demo Section -->
                                <div v-if="rowExpansionDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🆕 Row Expansion Feature Demo - LATEST NEW FEATURE!
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 p-4 text-sm dark:from-blue-900/20 dark:to-purple-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                🚀 Row Expansion with Nested DataTables
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-1">
                                                <div class="space-y-2">
                                                    <div class="text-blue-800 dark:text-blue-200">
                                                        <strong>✨ New Features:</strong>
                                                    </div>
                                                    <ul class="list-inside list-disc space-y-1 text-sm text-blue-700 dark:text-blue-300">
                                                        <li><strong>Expandable Rows:</strong> Click the expand icon (▶) to view nested order history</li>
                                                        <li><strong>Nested DataTables:</strong> Full DataTableWidget with sorting, formatting, and styling</li>
                                                        <li><strong>Expand/Collapse All:</strong> Header buttons to expand or collapse all rows at once</li>
                                                        <li><strong>Dynamic Titles:</strong> Custom titles like "Order History for [Product Name]"</li>
                                                        <li><strong>Event Handling:</strong> Row expand/collapse events (check console)</li>
                                                        <li><strong>Conditional Styling:</strong> Color-coded rows based on inventory status</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Implementation:</strong> Uses nested DataTableWidgets with full functionality - export, search, styling all work in nested tables too!
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Future-Proof:</strong> All future DataTableWidget features automatically work in expanded rows
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[rowExpansionDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                            @header-action="handleHeaderAction"
                                            @row-expand="handleRowExpansion"
                                            @row-collapse="handleRowCollapse"
                                        />
                                    </div>
                                </div>
                                <!-- DD20250712-1930 END -->

                                 <!-- DD20250710-1240 - Conditional Styling DataTable -->
                                 <div v-if="conditionalStylingDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🎨 Conditional Row Styling Demo
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-green-50 to-blue-50 p-4 text-sm dark:from-green-900/20 dark:to-blue-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                ✨ Conditional Styling Rules Demonstration
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-red-400 bg-red-200"></div>
                                                    <span class="text-red-800 dark:text-red-200"><strong>Red:</strong> Out of Stock</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-orange-400 bg-orange-200"></div>
                                                    <span class="text-orange-800 dark:text-orange-200"><strong>Orange:</strong> Low Stock</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-green-400 bg-green-200"></div>
                                                    <span class="text-green-800 dark:text-green-200"><strong>Green:</strong> In Stock</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-blue-400 bg-blue-200"></div>
                                                    <span class="text-blue-800 dark:text-blue-200"><strong>Blue:</strong> Price > $100 (italic)</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-yellow-400 bg-yellow-200"></div>
                                                    <span class="text-yellow-800 dark:text-yellow-200"><strong>Yellow:</strong> Quantity < 5 (left border)</span>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Implementation:</strong> Uses column-based conditions with operators (eq, gt, lt), priority levels (1=highest), and user-configurable styling (CSS classes, inline styles, style objects)
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Priority System:</strong> Out of Stock (Priority 1) overrides Low Quantity (Priority 5) - that's why items with 0 stock show red instead of yellow
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[conditionalStylingDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Server-side DataTable -->
                                <div v-if="serverSideDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Full-featured DataTable with Server-side Processing
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <WidgetRenderer :widgets="[serverSideDataTable]" />
                                    </div>
                                </div>

                                <!-- Client-side DataTable -->
                                <div v-if="clientSideDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Simple Client-side DataTable
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <WidgetRenderer :widgets="[clientSideDataTable]" />
                                    </div>
                                </div>

                                <!-- Auto Mode DataTables -->
                                <div v-if="autoModeDataTables.length > 0" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Auto Mode DataTables - Intelligent Loading Strategy
                                    </h4>
                                    <div v-for="(table, index) in autoModeDataTables" :key="`auto-${index}`" 
                                         class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <WidgetRenderer :widgets="[table]" />
                                    </div>
                                </div>

                                <!-- Advanced Column Features DataTable -->
                                <div v-if="advancedColumnDataTable" class="mt-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Advanced Column Features Demo
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
                                            <div class="mb-2 font-semibold">Try these features:</div>
                                            <ul class="list-inside list-disc space-y-1">
                                                <li>Click "Columns" button to show/hide columns</li>
                                                <li>Drag column borders to resize (except Price column)</li>
                                                <li>Drag column headers to reorder</li>
                                                <li>ID and Stock columns are hidden by default</li>
                                                <li>Code column is frozen and cannot be hidden</li>
                                            </ul>
                                        </div>
                                        <WidgetRenderer :widgets="[advancedColumnDataTable]" />
                                    </div>
                                </div>

                                <!-- Action Handler DataTable -->
                                <div v-if="actionDataTable" class="mt-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        DataTable with Action Handlers
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-purple-50 p-4 text-sm text-purple-800 dark:bg-purple-900/20 dark:text-purple-200">
                                            <div class="mb-2 font-semibold">Interactive Features:</div>
                                            <ul class="list-inside list-disc space-y-1">
                                                <li><strong>Product Names:</strong> Click to open details in new tab</li>
                                                <li><strong>Categories:</strong> Click to trigger filter action</li>
                                                <li><strong>Action Buttons:</strong> View, Edit, History, Print (Delete disabled by permission)</li>
                                                <li><strong>Events:</strong> Check console for action events</li>
                                            </ul>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[actionDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                        />
                                    </div>
                                </div>

                                <!-- Data Types DataTable -->
                                <div v-if="dataTypesDataTable" class="mt-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Data Type Formatting Demo
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-200">
                                            <div class="mb-2 font-semibold">Data Type Examples:</div>
                                            <ul class="list-inside list-disc space-y-1">
                                                <li><strong>Currency:</strong> Multiple formats with $ and € symbols</li>
                                                <li><strong>Dates:</strong> Short/Long formats, US/EU cultures</li>
                                                <li><strong>DateTime:</strong> Combined date and time with 12/24 hour formats</li>
                                                <li><strong>Percentage & Numbers:</strong> Decimal place control</li>
                                                <li><strong>Text:</strong> Truncation with ellipsis</li>
                                                <li><strong>Images:</strong> Inline images with controlled width</li>
                                                <li><strong>Widgets:</strong> Embedded knob widgets for ratings</li>
                                            </ul>
                                        </div>
                                        <WidgetRenderer :widgets="[dataTypesDataTable]" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>

                <!-- Static PrimeVue Components Section -->
                <PCard class="shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">Static PrimeVue Components</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-8 p-6">
                            <!-- Basic Components -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Basic Components</h3>
                                
                                <div class="space-y-6">
                                    <!-- Buttons -->
                                    <div>
                                        <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">Buttons</h4>
                                        <div class="flex flex-wrap gap-3">
                                            <PButton label="Primary" />
                                            <PButton label="Secondary" severity="secondary" />
                                            <PButton label="Success" severity="success" />
                                            <PButton label="Warning" severity="warning" />
                                            <PButton label="Danger" severity="danger" />
                                            <PButton label="Help" severity="help" />
                                            <PButton label="Info" severity="info" />
                                        </div>
                                    </div>

                                    <!-- Input Fields -->
                                    <div>
                                        <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">Input Fields</h4>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <PInputText placeholder="Enter text..." />
                                            <PInputNumber v-model="counter" showButtons :min="0" :max="100" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>

                <!--DD 20250714:1400 - BEGIN - Add Documentation Section for Column Locking -->
                <PCard class="mt-8 shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">🔒 Column Locking Documentation</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-6 p-6">
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Implementation Guide
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    The DataTable widget now supports column locking to prevent selected columns from horizontal scrolling. Configure this feature using individual column properties and the <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-gray-800">columnLocking</code> configuration:
                                </p>
                                
                                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Configuration Example:</h4>
                                    <pre class="overflow-x-auto text-xs text-gray-700 dark:text-gray-300"><code>{
  "columns": [
    {
      "field": "code",
      "header": "Product Code", 
      "lockColumn": true,     // Initially locked
      "lockButton": true      // Show lock/unlock button
    },
    {
      "field": "name",
      "header": "Product Name",
      "lockColumn": false,    // Not initially locked
      "lockButton": true      // But user can lock it
    },
    {
      "field": "status", 
      "header": "Status"
      // No locking functionality for this column
    }
  ],
  "columnLocking": {
    "enabled": true,
    "buttonPosition": "toolbar",  // or "header"
    "buttonStyle": "margin: 0 2px;",
    "buttonClass": "custom-lock-btn"
  }
}</code></pre>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-lg bg-emerald-50 p-4 dark:bg-emerald-900/20">
                                        <h4 class="mb-2 font-semibold text-emerald-800 dark:text-emerald-200">Column Properties:</h4>
                                        <ul class="list-inside list-disc text-sm text-emerald-700 dark:text-emerald-300">
                                            <li><code>lockColumn: true</code> - Column is initially locked</li>
                                            <li><code>lockButton: true</code> - Show lock/unlock button</li>
                                            <li>Lock buttons show column header text</li>
                                            <li>Padlock icon changes with state</li>
                                            <li>Tooltips indicate current action</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-teal-50 p-4 dark:bg-teal-900/20">
                                        <h4 class="mb-2 font-semibold text-teal-800 dark:text-teal-200">Global Configuration:</h4>
                                        <ul class="list-inside list-disc text-sm text-teal-700 dark:text-teal-300">
                                            <li><code>columnLocking.enabled</code> - Master switch</li>
                                            <li><code>buttonPosition</code> - "toolbar" or "header"</li>
                                            <li><code>buttonStyle</code> - Custom CSS styles</li>
                                            <li><code>buttonClass</code> - Custom CSS classes</li>
                                            <li>Works with scrollable tables</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-cyan-50 p-4 dark:bg-cyan-900/20">
                                        <h4 class="mb-2 font-semibold text-cyan-800 dark:text-cyan-200">Event Handling:</h4>
                                        <ul class="list-inside list-disc text-sm text-cyan-700 dark:text-cyan-300">
                                            <li><code>@column-lock-change</code> - Fired on lock/unlock</li>
                                            <li>Event includes field name and locked state</li>
                                            <li>Array of all currently locked fields</li>
                                            <li>Perfect for analytics or state persistence</li>
                                            <li>Console logging for debugging</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                    <h4 class="mb-2 font-semibold text-blue-800 dark:text-blue-200">Use Cases & Benefits:</h4>
                                    <div class="grid gap-3 md:grid-cols-2">
                                        <ul class="list-inside list-disc text-sm text-blue-700 dark:text-blue-300">
                                            <li><strong>Financial Dashboards:</strong> Lock key metrics while exploring data</li>
                                            <li><strong>Spreadsheet Views:</strong> Keep identifier columns visible</li>
                                            <li><strong>Comparison Tables:</strong> Lock reference data for analysis</li>
                                        </ul>
                                        <ul class="list-inside list-disc text-sm text-blue-700 dark:text-blue-300">
                                            <li><strong>Wide Data Sets:</strong> Navigate horizontally without losing context</li>
                                            <li><strong>User Experience:</strong> Customizable column visibility</li>
                                            <li><strong>Responsive Design:</strong> Essential columns always accessible</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>
                <!--DD 20250714:1400 - END -->

                <!--DD 20250713:2021 - BEGIN - Add Documentation Section for Row Locking -->
                <PCard class="mt-8 shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">🔒 Row Locking Documentation</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-6 p-6">
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Implementation Guide
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    The DataTable widget now supports row locking to prevent selected rows from scrolling. Configure this feature using the <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-gray-800">rowLocking</code> property:
                                </p>
                                
                                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Configuration Example:</h4>
                                    <pre class="overflow-x-auto text-xs text-gray-700 dark:text-gray-300"><code>{
                                        "rowLocking": {
                                            "enabled": true,
                                            "maxLockedRows": 3,
                                            "lockColumn": {
                                            "style": "width: 4rem",
                                            "frozen": true,
                                            "header": "Lock"
                                            },
                                            "lockedRowClasses": "font-bold bg-blue-50",
                                            "lockedRowStyles": {
                                            "backgroundColor": "#eff6ff",
                                            "borderLeft": "4px solid #3b82f6"
                                            }
                                        }
                                        }</code>
                                    </pre>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                        <h4 class="mb-2 font-semibold text-blue-800 dark:text-blue-200">Core Features:</h4>
                                        <ul class="list-inside list-disc text-sm text-blue-700 dark:text-blue-300">
                                            <li><code>enabled: true</code> - Activates row locking</li>
                                            <li><code>maxLockedRows: 3</code> - Maximum lockable rows</li>
                                            <li>Lock button automatically disabled at limit</li>
                                            <li>Locked rows appear at top (frozen)</li>
                                            <li>Locked rows don't scroll vertically</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                                        <h4 class="mb-2 font-semibold text-green-800 dark:text-green-200">Visual Customization:</h4>
                                        <ul class="list-inside list-disc text-sm text-green-700 dark:text-green-300">
                                            <li><code>lockColumn</code> - Lock button column styling</li>
                                            <li><code>lockedRowClasses</code> - CSS classes for locked rows</li>
                                            <li><code>lockedRowStyles</code> - Inline styles object</li>
                                            <li>Frozen column support for lock button</li>
                                            <li>Custom header text for lock column</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-purple-50 p-4 dark:bg-purple-900/20">
                                        <h4 class="mb-2 font-semibold text-purple-800 dark:text-purple-200">Event Handling:</h4>
                                        <ul class="list-inside list-disc text-sm text-purple-700 dark:text-purple-300">
                                            <li><code>@row-lock</code> - Fired when row is locked</li>
                                            <li><code>@row-unlock</code> - Fired when row is unlocked</li>
                                            <li>Events include row data and index</li>
                                            <li>Perfect for toast notifications</li>
                                            <li>Integrate with state management</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                                    <h4 class="mb-2 font-semibold text-yellow-800 dark:text-yellow-200">Use Cases & Benefits:</h4>
                                    <div class="grid gap-3 md:grid-cols-2">
                                        <ul class="list-inside list-disc text-sm text-yellow-700 dark:text-yellow-300">
                                            <li><strong>Data Comparison:</strong> Lock reference rows while browsing</li>
                                            <li><strong>Priority Items:</strong> Keep important data visible</li>
                                            <li><strong>Shopping Lists:</strong> Lock selected items for review</li>
                                        </ul>
                                        <ul class="list-inside list-disc text-sm text-yellow-700 dark:text-yellow-300">
                                            <li><strong>Financial Analysis:</strong> Lock baseline data for comparison</li>
                                            <li><strong>Inventory Management:</strong> Keep critical stock visible</li>
                                            <li><strong>User Experience:</strong> Reduce scrolling and improve workflow</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>
                <!--DD 20250713:2021 - END -->

                <!-- DD20250710-1240 - Add Documentation Section for Conditional Styling -->
                <PCard class="mt-8 shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">🎨 Conditional Styling Documentation</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-6 p-6">
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Implementation Guide
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    The DataTable widget now supports conditional row styling based on column values. Configure styles using the <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-gray-800">conditionalStyles</code> property:
                                </p>
                                
                                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Configuration Example:</h4>
                                    <pre class="overflow-x-auto text-xs text-gray-700 dark:text-gray-300"><code>{
                                        "conditionalStyles": [
                                            {
                                            "column": "inventoryStatus",
                                            "value": "OUTOFSTOCK", 
                                            "operator": "eq",
                                            "priority": 1,
                                            "styleObject": {
                                                "backgroundColor": "#fee2e2",
                                                "color": "#7f1d1d",
                                                "fontWeight": "bold"
                                            }
                                            },
                                            {
                                            "column": "quantity",
                                            "value": 5,
                                            "operator": "lt",
                                            "priority": 5,
                                            "styleObject": {
                                                "backgroundColor": "#fef3c7",
                                                "color": "#92400e"
                                            }
                                            }
                                        ]
                                        }</code></pre>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                                        <h4 class="mb-2 font-semibold text-yellow-800 dark:text-yellow-200">Priority System:</h4>
                                        <ul class="list-inside list-disc text-sm text-yellow-700 dark:text-yellow-300">
                                            <li><code>priority: 1</code> - Highest priority</li>
                                            <li><code>priority: 2-998</code> - Medium priority</li>
                                            <li><code>priority: 9999</code> - Default (lowest)</li>
                                            <li>Lower numbers override higher numbers</li>
                                            <li>Rules without priority get 9999</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                        <h4 class="mb-2 font-semibold text-blue-800 dark:text-blue-200">Available Operators:</h4>
                                        <ul class="list-inside list-disc text-sm text-blue-700 dark:text-blue-300">
                                            <li><code>eq</code> - equals</li>
                                            <li><code>ne</code> - not equals</li>
                                            <li><code>lt / lte</code> - less than (or equal)</li>
                                            <li><code>gt / gte</code> - greater than (or equal)</li>
                                            <li><code>contains</code> - string contains</li>
                                            <li><code>startsWith / endsWith</code> - string patterns</li>
                                            <li><code>in / notIn</code> - array membership</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-purple-50 p-4 dark:bg-purple-900/20">
                                        <h4 class="mb-2 font-semibold text-purple-800 dark:text-purple-200">Styling Methods:</h4>
                                        <ul class="list-inside list-disc text-sm text-purple-700 dark:text-purple-300">
                                            <li><code>cssClasses</code> - Apply CSS class names</li>
                                            <li><code>inlineStyles</code> - CSS string format</li>
                                            <li><code>styleObject</code> - JavaScript object format</li>
                                        </ul>
                                        <p class="mt-2 text-xs text-purple-600 dark:text-purple-400">
                                            💡 All three methods can be combined for complex styling
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>
            </div>
        </div>
    </AppLayout>
</template>