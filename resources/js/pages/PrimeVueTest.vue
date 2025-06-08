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
const searchText = ref('');

// Methods
const increment = () => {
    counter.value++;
};

const decrement = () => {
    counter.value--;
};

const handleSearch = () => {
    console.log('Searching for:', searchText.value);
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

const inputTextWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'inputtext')
);

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
            <div class="mx-auto max-w-6xl">
                <h1 class="mb-8 text-3xl font-bold text-gray-900 dark:text-white">
                    PrimeVue Component Test
                </h1>

                <!-- Search Bar -->
                <div class="mb-6 flex items-center">
                    <span class="p-input-icon-left w-full">
                        <i class="pi pi-search"></i>
                        <PInputText
                            v-model="searchText"
                            placeholder="Search..."
                            class="w-full"
                        />
                    </span>
                    <PButton
                        @click="handleSearch"
                        label="Search"
                        icon="pi pi-search"
                        class="ml-2"
                    />
                </div>

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
                            
                            <!-- InputText Widgets -->
                            <div v-if="inputTextWidgets.length > 0">
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Input Text Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Text input fields rendered from JSON configuration:
                                </p>
                                <WidgetRenderer :widgets="inputTextWidgets" />
                            </div>
                            
                            <!-- Knob Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Knob Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Circular value selectors rendered from JSON configuration:
                                </p>
                                <WidgetRenderer :widgets="knobWidgets" />
                            </div>
                            
                            <!-- DatePicker Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Date Picker Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Date selector components rendered from JSON configuration:
                                </p>
                                <WidgetRenderer :widgets="datePickerWidgets" />
                                
                                <!-- Date Picker Details -->
                                <div class="mt-4 grid gap-4 text-sm md:grid-cols-2 lg:grid-cols-3">
                                    <div class="rounded-lg bg-indigo-50 p-3 dark:bg-indigo-900/20">
                                        <strong class="text-indigo-700 dark:text-indigo-300">Basic:</strong>
                                        <span class="text-indigo-600 dark:text-indigo-400"> Standard date selection</span>
                                    </div>
                                    <div class="rounded-lg bg-purple-50 p-3 dark:bg-purple-900/20">
                                        <strong class="text-purple-700 dark:text-purple-300">DateTime:</strong>
                                        <span class="text-purple-600 dark:text-purple-400"> With time picker</span>
                                    </div>
                                    <div class="rounded-lg bg-pink-50 p-3 dark:bg-pink-900/20">
                                        <strong class="text-pink-700 dark:text-pink-300">Restricted:</strong>
                                        <span class="text-pink-600 dark:text-pink-400"> Next 30 days only</span>
                                    </div>
                                    <div class="rounded-lg bg-teal-50 p-3 dark:bg-teal-900/20">
                                        <strong class="text-teal-700 dark:text-teal-300">Range:</strong>
                                        <span class="text-teal-600 dark:text-teal-400"> Date range selection</span>
                                    </div>
                                    <div class="rounded-lg bg-cyan-50 p-3 dark:bg-cyan-900/20">
                                        <strong class="text-cyan-700 dark:text-cyan-300">Inline:</strong>
                                        <span class="text-cyan-600 dark:text-cyan-400"> Always visible calendar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>

                <div class="grid gap-8 md:grid-cols-2">
                    <!-- Counter Test Card -->
                    <PCard class="shadow-lg">
                        <template #header>
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                                <h2 class="text-xl font-semibold text-white">Counter Test</h2>
                            </div>
                        </template>
                        
                        <template #content>
                            <div class="space-y-6 p-6">
                                <!-- Counter Display -->
                                <div class="text-center">
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Current Value
                                    </label>
                                    <PInputText
                                        v-model="counter"
                                        readonly
                                        class="w-full text-center text-lg font-bold"
                                    />
                                </div>

                                <!-- Control Buttons -->
                                <div class="flex space-x-4">
                                    <PButton
                                        @click="increment"
                                        label="Increment (+)"
                                        icon="pi pi-plus"
                                        class="flex-1"
                                        severity="success"
                                    />
                                    <PButton
                                        @click="decrement"
                                        label="Decrement (-)"
                                        icon="pi pi-minus"
                                        class="flex-1"
                                        severity="danger"
                                    />
                                </div>

                                <!-- Reset Button -->
                                <PButton
                                    @click="counter = 0"
                                    label="Reset"
                                    icon="pi pi-refresh"
                                    class="w-full"
                                    severity="secondary"
                                    outlined
                                />
                            </div>
                        </template>
                    </PCard>

                    <!-- Temperature Knob Test Card -->
                    <PCard class="shadow-lg">
                        <template #header>
                            <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
                                <h2 class="text-xl font-semibold text-white">Temperature Control</h2>
                            </div>
                        </template>
                        
                        <template #content>
                            <div class="space-y-6 p-6">
                                <!-- Temperature Display -->
                                <div class="text-center">
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Temperature (°C)
                                    </label>
                                    <PInputText
                                        v-model="temperature"
                                        readonly
                                        class="w-full text-center text-lg font-bold"
                                    />
                                </div>

                                <!-- Temperature Knob -->
                                <div class="flex justify-center">
                                    <PKnob
                                        v-model="temperature"
                                        :min="0"
                                        :max="100"
                                        :step="1"
                                        :size="120"
                                        :stroke-width="8"
                                        show-value
                                        value-template="{value}°C"
                                    />
                                </div>

                                <!-- Temperature Presets -->
                                <div class="grid grid-cols-3 gap-2">
                                    <PButton
                                        @click="temperature = 0"
                                        label="Cold"
                                        icon="pi pi-minus-circle"
                                        severity="info"
                                        size="small"
                                        outlined
                                    />
                                    <PButton
                                        @click="temperature = 20"
                                        label="Room"
                                        icon="pi pi-home"
                                        severity="secondary"
                                        size="small"
                                        outlined
                                    />
                                    <PButton
                                        @click="temperature = 100"
                                        label="Hot"
                                        icon="pi pi-sun"
                                        severity="warning"
                                        size="small"
                                        outlined
                                    />
                                </div>
                            </div>
                        </template>
                    </PCard>
                </div>

                <!-- Status Information -->
                <PCard class="mt-8 shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">Component Status</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="grid gap-4 p-6 md:grid-cols-2">
                            <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                <h3 class="mb-2 font-semibold text-blue-800 dark:text-blue-200">
                                    <i class="pi pi-check-circle mr-2"></i>
                                    Counter Component
                                </h3>
                                <p class="text-sm text-blue-600 dark:text-blue-300">
                                    Current value: <strong>{{ counter }}</strong><br>
                                    Status: {{ counter === 0 ? 'Reset' : counter > 0 ? 'Positive' : 'Negative' }}
                                </p>
                            </div>
                            
                            <div class="rounded-lg bg-orange-50 p-4 dark:bg-orange-900/20">
                                <h3 class="mb-2 font-semibold text-orange-800 dark:text-orange-200">
                                    <i class="pi pi-thermometer mr-2"></i>
                                    Temperature Control
                                </h3>
                                <p class="text-sm text-orange-600 dark:text-orange-300">
                                    Current temperature: <strong>{{ temperature }}°C</strong><br>
                                    Status: {{ temperature < 10 ? 'Cold' : temperature < 30 ? 'Comfortable' : 'Hot' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-4 rounded-lg bg-purple-50 p-4 dark:bg-purple-900/20">
                            <h3 class="mb-2 font-semibold text-purple-800 dark:text-purple-200">
                                <i class="pi pi-sitemap mr-2"></i>
                                APEX Widget System
                            </h3>
                            <p class="text-sm text-purple-600 dark:text-purple-300">
                                Total widgets rendered: <strong>{{ widgets.length }}</strong><br>
                                Breadcrumb widgets: <strong>{{ breadcrumbWidgets.length }}</strong><br>
                                Knob widgets: <strong>{{ knobWidgets.length }}</strong><br>
                                DatePicker widgets: <strong>{{ datePickerWidgets.length }}</strong><br>
                                InputText widgets: <strong>{{ inputTextWidgets.length }}</strong><br>
                                Widget types: breadcrumb, knob, datepicker, inputtext
                            </p>
                        </div>
                    </template>
                </PCard>
            </div>
        </div>
    </AppLayout>
</template>