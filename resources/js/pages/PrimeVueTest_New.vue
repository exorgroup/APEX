<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import WidgetRenderer from '@/components/apex/WidgetRenderer.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// Import composables
import { useBreadcrumbTest } from '@/composables/PrimeVueTest/useBreadcrumbTest';
import { useKnobTest } from '@/composables/PrimeVueTest/useKnobTest';
import { useDatePickerTest } from '@/composables/PrimeVueTest/useDatePickerTest';
import { useDataTableBasicTest } from '@/composables/PrimeVueTest/useDataTableBasicTest';
import { useDataTableAdvancedTest } from '@/composables/PrimeVueTest/useDataTableAdvancedTest';
import { useDataTableRowFeaturesTest } from '@/composables/PrimeVueTest/useDataTableRowFeaturesTest';
import { useDataTableColumnFeaturesTest } from '@/composables/PrimeVueTest/useDataTableColumnFeaturesTest';
import { useDataTableGroupingTest } from '@/composables/PrimeVueTest/useDataTableGroupingTest';
import { useDataTableDataTypesTest } from '@/composables/PrimeVueTest/useDataTableDataTypesTest';
import { useDataTableReorderTest } from '@/composables/PrimeVueTest/useDataTableReorderTest';
import { useEventHandlers } from '@/composables/PrimeVueTest/useEventHandlers';
import { useStaticComponents } from '@/composables/PrimeVueTest/useStaticComponents';

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

// Use composables
const { breadcrumbWidgets } = useBreadcrumbTest(props.widgets);
const { knobWidgets } = useKnobTest(props.widgets);
const { datePickerWidgets } = useDatePickerTest(props.widgets);
const { basicDataTableWidgets } = useDataTableBasicTest(props.widgets);
const { 
    reOrderDataTable,
    conditionalStylingDataTable,
    clickableDataTable,
    filterDataTable,
    autoModeDataTable
} = useDataTableAdvancedTest(props.widgets);

const {
    rowExpansionDataTable,
    rowLockingDataTable
} = useDataTableRowFeaturesTest(props.widgets);

const {
    columnLockingDataTable
} = useDataTableColumnFeaturesTest(props.widgets);

const {
    subheaderRowGroupingDataTable,
    columnGroupingDataTable,
    rowspanRowGroupingDataTable
} = useDataTableGroupingTest(props.widgets);

const {
    dataTypesDataTable
} = useDataTableDataTypesTest(props.widgets);

const {
    handleColumnLock,
    handleRowLock,
    handleRowUnlock,
    handleRowExpansion,
    handleRowCollapse,
    handleHeaderAction
} = useEventHandlers();

const {
    staticComponentData
} = useStaticComponents();

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
                    PrimeVue Component Test Traits 2
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
                                    Date picker controls rendered from JSON configuration:
                                </p>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <WidgetRenderer :widgets="datePickerWidgets" />
                                </div>
                            </div>

                            <!-- Basic DataTable Demos -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Basic DataTable Demos
                                </h3>
                                <div class="space-y-8">
                                    <WidgetRenderer :widgets="basicDataTableWidgets" />
                                </div>
                            </div>

                            <!-- Advanced DataTable Demos -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Advanced DataTable Features
                                </h3>
                                <div class="space-y-8">
                                    <!-- ReOrder Demo -->
                                    <div v-if="reOrderDataTable">
                                        <WidgetRenderer 
                                            :widgets="[reOrderDataTable]" 
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>

                                    <!-- Row Expansion Demo -->
                                    <div v-if="rowExpansionDataTable">
                                        <WidgetRenderer 
                                            :widgets="[rowExpansionDataTable]" 
                                            @row-expand="handleRowExpansion"
                                            @row-collapse="handleRowCollapse"
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>

                                    <!-- Row Locking Demo -->
                                    <div v-if="rowLockingDataTable">
                                        <WidgetRenderer 
                                            :widgets="[rowLockingDataTable]" 
                                            @row-lock="handleRowLock"
                                            @row-unlock="handleRowUnlock"
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>

                                    <!-- Column Locking Demo -->
                                    <div v-if="columnLockingDataTable">
                                        <WidgetRenderer 
                                            :widgets="[columnLockingDataTable]" 
                                            @column-lock="handleColumnLock"
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>

                                    <!-- Row Grouping Demos -->
                                    <div v-if="subheaderRowGroupingDataTable">
                                        <WidgetRenderer :widgets="[subheaderRowGroupingDataTable]" />
                                    </div>

                                    <div v-if="columnGroupingDataTable">
                                        <WidgetRenderer 
                                            :widgets="[columnGroupingDataTable]" 
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>

                                    <div v-if="rowspanRowGroupingDataTable">
                                        <WidgetRenderer :widgets="[rowspanRowGroupingDataTable]" />
                                    </div>

                                    <!-- Data Types Demo -->
                                    <div v-if="dataTypesDataTable">
                                        <WidgetRenderer :widgets="[dataTypesDataTable]" />
                                    </div>

                                    <!-- Other Advanced Features -->
                                    <div v-if="conditionalStylingDataTable">
                                        <WidgetRenderer :widgets="[conditionalStylingDataTable]" />
                                    </div>

                                    <div v-if="clickableDataTable">
                                        <WidgetRenderer 
                                            :widgets="[clickableDataTable]" 
                                            @header-action="handleHeaderAction"
                                        />
                                    </div>

                                    <div v-if="filterDataTable">
                                        <WidgetRenderer :widgets="[filterDataTable]" />
                                    </div>

                                    <div v-if="autoModeDataTable">
                                        <WidgetRenderer :widgets="[autoModeDataTable]" />
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
                                            <PButton label="Info" severity="info" />
                                            <PButton label="Warning" severity="warning" />
                                            <PButton label="Help" severity="help" />
                                            <PButton label="Danger" severity="danger" />
                                        </div>
                                    </div>

                                    <!-- Form Controls -->
                                    <div>
                                        <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">Form Controls</h4>
                                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                            <div>
                                                <label class="mb-2 block text-sm font-medium">Text Input</label>
                                                <PInputText placeholder="Enter text" class="w-full" />
                                            </div>
                                            <div>
                                                <label class="mb-2 block text-sm font-medium">Number Input</label>
                                                <PInputNumber placeholder="Enter number" class="w-full" />
                                            </div>
                                            <div>
                                                <label class="mb-2 block text-sm font-medium">Select</label>
                                                <PSelect 
                                                    :options="staticComponentData.selectOptions"
                                                    option-label="label"
                                                    option-value="value"
                                                    placeholder="Choose option"
                                                    class="w-full"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Interactive Demo -->
                                    <div>
                                        <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">Interactive Counter Demo</h4>
                                        <div class="flex items-center gap-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                            <PButton icon="pi pi-minus" @click="decrement" severity="danger" outlined />
                                            <span class="text-2xl font-bold">{{ counter }}</span>
                                            <PButton icon="pi pi-plus" @click="increment" severity="success" outlined />
                                        </div>
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