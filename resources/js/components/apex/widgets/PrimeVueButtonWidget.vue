// resources/js/pages/PrimeVueTest.vue
<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import WidgetRenderer from '@/components/apex/WidgetRenderer.vue';

// Component state
const count = ref(0);
const temperature = ref(20);
const selectedDate = ref(null);

// Button click handlers
const increment = () => count.value++;
const decrement = () => count.value--;
const reset = () => count.value = 0;
const setTemperature = (temp: number) => temperature.value = temp;

// APEX Button widgets demonstration
const headerButtons = [
    {
        id: 'btn_home',
        type: 'button',
        props: {
            config: {
                label: 'Home',
                icon: 'pi pi-home',
                severity: 'secondary',
                outlined: true,
                href: '/',
            }
        }
    },
    {
        id: 'btn_refresh',
        type: 'button',
        props: {
            config: {
                label: 'Refresh',
                icon: 'pi pi-refresh',
                severity: 'info',
                rounded: true,
                onClick: 'location.reload()'
            }
        }
    }
];

const actionButtons = [
    {
        id: 'btn_save',
        type: 'button',
        props: {
            config: {
                label: 'Save',
                icon: 'pi pi-save',
                severity: 'success',
                raised: true,
                size: 'large'
            }
        }
    },
    {
        id: 'btn_delete',
        type: 'button',
        props: {
            config: {
                label: 'Delete',
                icon: 'pi pi-trash',
                severity: 'danger',
                outlined: true,
                size: 'small'
            }
        }
    }
];

const socialButtons = [
    {
        id: 'btn_github',
        type: 'button',
        props: {
            config: {
                label: 'GitHub',
                icon: 'pi pi-github',
                severity: 'contrast',
                rounded: true,
                text: true,
                href: 'https://github.com',
                target: '_blank'
            }
        }
    },
    {
        id: 'btn_twitter',
        type: 'button',
        props: {
            config: {
                label: 'Twitter',
                icon: 'pi pi-twitter',
                severity: 'info',
                rounded: true,
                badge: '5',
                badgeSeverity: 'danger'
            }
        }
    }
];

// Make functions available globally for button onClick
window.increment = increment;
window.decrement = decrement;
window.reset = reset;
window.temperature = temperature;
window.selectedDate = selectedDate;
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Header Section with APEX Buttons -->
            <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 mb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">PrimeVue Component Testing</h1>
                        <div class="flex gap-2">
                            <WidgetRenderer :widgets="headerButtons" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                <!-- Action Buttons Section -->
                <div class="mb-8">
                    <PCard>
                        <template #header>
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                                <h2 class="text-xl font-semibold text-white">APEX Button Widget Examples</h2>
                            </div>
                        </template>
                        <template #content>
                            <div class="p-6 space-y-6">
                                <div>
                                    <h3 class="text-lg font-medium mb-3">Action Buttons</h3>
                                    <div class="flex gap-3">
                                        <WidgetRenderer :widgets="actionButtons" />
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-medium mb-3">Social Buttons</h3>
                                    <div class="flex gap-3">
                                        <WidgetRenderer :widgets="socialButtons" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </PCard>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Counter Test Card with APEX Buttons -->
                    <PCard class="shadow-lg">
                        <template #header>
                            <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-4">
                                <h2 class="text-xl font-semibold text-white">Counter Test</h2>
                            </div>
                        </template>
                        
                        <template #content>
                            <div class="space-y-6 p-6">
                                <!-- Counter Display -->
                                <div class="text-center">
                                    <PInputText
                                        v-model="count"
                                        readonly
                                        class="w-full text-center text-2xl font-bold"
                                        placeholder="Counter value"
                                    />
                                </div>

                                <!-- Counter Buttons using APEX Button Widgets -->
                                <div class="flex gap-3 justify-center">
                                    <WidgetRenderer :widgets="[
                                        {
                                            id: 'btn_decrement',
                                            type: 'button',
                                            props: {
                                                config: {
                                                    label: 'Decrement',
                                                    icon: 'pi pi-minus',
                                                    severity: 'danger',
                                                    onClick: 'decrement'
                                                }
                                            }
                                        },
                                        {
                                            id: 'btn_increment',
                                            type: 'button',
                                            props: {
                                                config: {
                                                    label: 'Increment',
                                                    icon: 'pi pi-plus',
                                                    severity: 'success',
                                                    onClick: 'increment'
                                                }
                                            }
                                        },
                                        {
                                            id: 'btn_reset',
                                            type: 'button',
                                            props: {
                                                config: {
                                                    label: 'Reset',
                                                    icon: 'pi pi-refresh',
                                                    severity: 'secondary',
                                                    outlined: true,
                                                    onClick: 'reset'
                                                }
                                            }
                                        }
                                    ]" />
                                </div>
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

                                <!-- Temperature Presets with APEX Buttons -->
                                <div class="grid grid-cols-3 gap-2">
                                    <WidgetRenderer :widgets="[
                                        {
                                            id: 'btn_cold',
                                            type: 'button',
                                            props: {
                                                config: {
                                                    label: 'Cold',
                                                    icon: 'pi pi-cloud',
                                                    severity: 'info',
                                                    size: 'small',
                                                    onClick: 'temperature.value = 5'
                                                }
                                            }
                                        },
                                        {
                                            id: 'btn_normal',
                                            type: 'button',
                                            props: {
                                                config: {
                                                    label: 'Normal',
                                                    icon: 'pi pi-sun',
                                                    severity: 'warning',
                                                    size: 'small',
                                                    onClick: 'temperature.value = 20'
                                                }
                                            }
                                        },
                                        {
                                            id: 'btn_hot',
                                            type: 'button',
                                            props: {
                                                config: {
                                                    label: 'Hot',
                                                    icon: 'pi pi-bolt',
                                                    severity: 'danger',
                                                    size: 'small',
                                                    onClick: 'temperature.value = 35'
                                                }
                                            }
                                        }
                                    ]" />
                                </div>
                            </div>
                        </template>
                    </PCard>

                    <!-- Date Picker Test -->
                    <PCard class="shadow-lg">
                        <template #header>
                            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                                <h2 class="text-xl font-semibold text-white">Date Picker Test</h2>
                            </div>
                        </template>
                        
                        <template #content>
                            <div class="space-y-6 p-6">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Select Date
                                    </label>
                                    <PDatePicker
                                        v-model="selectedDate"
                                        dateFormat="dd/mm/yy"
                                        :manualInput="false"
                                        showIcon
                                        iconDisplay="input"
                                        class="w-full"
                                    />
                                </div>

                                <div v-if="selectedDate" class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Selected: <strong>cvc</strong>
                                    </p>
                                </div>

                                <!-- Clear Button using APEX Widget -->
                                <div class="flex justify-end">
                                    <WidgetRenderer :widgets="[
                                        {
                                            id: 'btn_clear_date',
                                            type: 'button',
                                            props: {
                                                config: {
                                                    label: 'Clear Date',
                                                    icon: 'pi pi-times',
                                                    severity: 'secondary',
                                                    text: true,
                                                    onClick: 'selectedDate.value = null'
                                                }
                                            }
                                        }
                                    ]" />
                                </div>
                            </div>
                        </template>
                    </PCard>

                    <!-- Breadcrumb Test -->
                    <PCard class="shadow-lg">
                        <template #header>
                            <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                                <h2 class="text-xl font-semibold text-white">Breadcrumb Navigation</h2>
                            </div>
                        </template>
                        
                        <template #content>
                            <div class="p-6">
                                <PBreadcrumb :model="[
                                    { label: 'Home', icon: 'pi pi-home', url: '/' },
                                    { label: 'Components', url: '#' },
                                    { label: 'PrimeVue Test', url: '#' }
                                ]" />
                            </div>
                        </template>
                    </PCard>
                </div>

                <!-- Additional Button Examples -->
                <div class="mt-8">
                    <PCard>
                        <template #header>
                            <div class="bg-gradient-to-r from-gray-600 to-gray-800 px-6 py-4">
                                <h2 class="text-xl font-semibold text-white">More Button Variations</h2>
                            </div>
                        </template>
                        <template #content>
                            <div class="p-6 space-y-4">
                                <div>
                                    <h3 class="text-sm font-medium mb-2">Loading States</h3>
                                    <div class="flex gap-2">
                                        <WidgetRenderer :widgets="[
                                            {
                                                id: 'btn_loading1',
                                                type: 'button',
                                                props: {
                                                    config: {
                                                        label: 'Processing',
                                                        loading: true,
                                                        severity: 'primary'
                                                    }
                                                }
                                            },
                                            {
                                                id: 'btn_loading2',
                                                type: 'button',
                                                props: {
                                                    config: {
                                                        label: 'Please Wait',
                                                        loading: true,
                                                        severity: 'success',
                                                        outlined: true
                                                    }
                                                }
                                            }
                                        ]" />
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium mb-2">Icon Positions</h3>
                                    <div class="flex gap-2 flex-wrap">
                                        <WidgetRenderer :widgets="[
                                            {
                                                id: 'btn_icon_left',
                                                type: 'button',
                                                props: {
                                                    config: {
                                                        label: 'Left Icon',
                                                        icon: 'pi pi-check',
                                                        iconPos: 'left'
                                                    }
                                                }
                                            },
                                            {
                                                id: 'btn_icon_right',
                                                type: 'button',
                                                props: {
                                                    config: {
                                                        label: 'Right Icon',
                                                        icon: 'pi pi-send',
                                                        iconPos: 'right'
                                                    }
                                                }
                                            },
                                            {
                                                id: 'btn_icon_top',
                                                type: 'button',
                                                props: {
                                                    config: {
                                                        label: 'Top',
                                                        icon: 'pi pi-upload',
                                                        iconPos: 'top'
                                                    }
                                                }
                                            },
                                            {
                                                id: 'btn_icon_bottom',
                                                type: 'button',
                                                props: {
                                                    config: {
                                                        label: 'Bottom',
                                                        icon: 'pi pi-download',
                                                        iconPos: 'bottom'
                                                    }
                                                }
                                            }
                                        ]" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </PCard>
                </div>
            </div>
        </div>
    </AppLayout>
</template>