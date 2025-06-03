<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

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
            <div class="mx-auto max-w-4xl">
                <h1 class="mb-8 text-3xl font-bold text-gray-900 dark:text-white">
                    PrimeVue Component Test
                </h1>

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
                    </template>
                </PCard>
            </div>
        </div>
    </AppLayout>
</template>