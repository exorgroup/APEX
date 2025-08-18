<!--
File location: resources/js/components/PrimeRegTest/InputTextProWidgetSection.vue
URL: /primereg-pro-test/
Description: InputText Pro edition widgets test section component - displays core inheritance validation and pro features
-->

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import WidgetRenderer from '@/components/apex/WidgetRenderer.vue';

interface Props {
    widgets: any[];
}

const props = defineProps<Props>();

// Split widgets into core inherited (0-9) and pro specific (10+)
const coreInheritedWidgets = computed(() => props.widgets.slice(0, 10));
const proSpecificWidgets = computed(() => props.widgets.slice(10));

// Debug logging
onMounted(() => {
    console.log('=== InputTextProWidgetSection Debug ===');
    console.log('Received widgets:', props.widgets);
    console.log('Widgets length:', props.widgets.length);
    console.log('Core inherited count:', coreInheritedWidgets.value.length);
    console.log('Pro specific count:', proSpecificWidgets.value.length);
    console.log('======================================');
});
</script>

<template>
    <div v-if="widgets && widgets.length > 0">
        <!-- Core Features Inherited by Pro Edition -->
        <div class="mb-12">
            <h3 class="mb-6 text-lg font-semibold text-blue-700 dark:text-blue-300 border-b border-blue-200 pb-2">
                Core Features Inherited by Pro Edition
            </h3>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                These widgets test that Pro edition correctly inherits and renders all Core edition features.
            </p>
            
            <!-- Basic InputText Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Basic text input with label and placeholder:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[0]]" />
            </div>

            <!-- Required InputText Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Required field with validation message:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[1]]" />
            </div>

            <!-- Disabled InputText Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Disabled input field:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[2]]" />
            </div>

            <!-- Readonly InputText Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Readonly input field:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[3]]" />
            </div>

            <!-- InputText with Left Icon Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Input with search icon on the left:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[4]]" />
            </div>

            <!-- InputText with Right Icon Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Input with email icon on the right:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[5]]" />
            </div>

            <!-- Small Size InputText Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Small size input field:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[6]]" />
            </div>

            <!-- Large Size InputText Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Large size input field:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[7]]" />
            </div>

            <!-- InputText with Help Text Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Input with help text below:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[8]]" />
            </div>

            <!-- InputText with Validation Feedback Pro -->
            <div class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Core → Pro:</span> Input with validation feedback enabled:
                </p>
                <WidgetRenderer :widgets="[coreInheritedWidgets[9]]" />
            </div>
        </div>

        <!-- Pro Edition Specific Features -->
        <div class="mb-12">
            <h3 class="mb-6 text-lg font-semibold text-purple-700 dark:text-purple-300 border-b border-purple-200 pb-2">
                Pro Edition Specific Features
            </h3>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                These widgets test Pro-only capabilities including advanced events, state management, and server integration.
            </p>

            <!-- Pro Event Handling -->
            <div v-if="proSpecificWidgets[0]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> Event handling (blur, focus, error) - Check console:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[0]]" />
            </div>

            <!-- Pro Advanced Validation -->
            <div v-if="proSpecificWidgets[1]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> Advanced validation with real-time rules:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[1]]" />
            </div>

            <!-- Pro State Management -->
            <div v-if="proSpecificWidgets[2]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> State management with server sync:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[2]]" />
            </div>

            <!-- Pro Error Handling -->
            <div v-if="proSpecificWidgets[3]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> Error display as toast notification:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[3]]" />
            </div>

            <!-- Pro Parameter Injection -->
            <div v-if="proSpecificWidgets[4]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> Parameter injection with context templates:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[4]]" />
            </div>

            <!-- Pro Clipboard Events -->
            <div v-if="proSpecificWidgets[5]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> Clipboard events (copy, cut, paste) - Check console:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[5]]" />
            </div>

            <!-- Pro Loading States -->
            <div v-if="proSpecificWidgets[6]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> Loading states with server communication:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[6]]" />
            </div>

            <!-- Pro Resize/Scroll Events -->
            <div v-if="proSpecificWidgets[7]" class="mb-6">
                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-purple-600">Pro Feature:</span> Resize and scroll event handling - Check console:
                </p>
                <WidgetRenderer :widgets="[proSpecificWidgets[7]]" />
            </div>
        </div>

        <!-- Test Summary -->
        <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Test Summary</h4>
            <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="font-medium">Core Inherited:</span> {{ coreInheritedWidgets.length }} widgets</p>
                <p><span class="font-medium">Pro Specific:</span> {{ proSpecificWidgets.length }} widgets</p>
                <p><span class="font-medium">Total Widgets:</span> {{ widgets.length }} widgets</p>
                <p><span class="font-medium">Edition:</span> Pro edition with core inheritance</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional styling for pro edition indicators */
.font-medium {
    font-weight: 500;
}
</style>