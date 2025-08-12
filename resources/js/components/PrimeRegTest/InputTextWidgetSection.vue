<!--
File location: resources/js/components/PrimeRegTest/InputTextWidgetSection.vue
Description: InputText widgets test section component
-->

<script setup lang="ts">
import WidgetRenderer from '@/components/apex/WidgetRenderer.vue';

interface Props {
    widgets: any[];
}

const props = defineProps<Props>();
</script>

<template>
    <div v-if="widgets && widgets.length > 0">
        <h3 class="mb-6 text-lg font-semibold text-gray-700 dark:text-gray-300">
            InputText Widgets
        </h3>
        
        <!-- Basic InputText -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Basic text input with label and placeholder:
            </p>
            <WidgetRenderer :widgets="[widgets[0]]" />
        </div>

        <!-- Required InputText -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Required field with validation message:
            </p>
            <WidgetRenderer :widgets="[widgets[1]]" />
        </div>

        <!-- Disabled InputText -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Disabled input field:
            </p>
            <WidgetRenderer :widgets="[widgets[2]]" />
        </div>

        <!-- Readonly InputText -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Readonly input field:
            </p>
            <WidgetRenderer :widgets="[widgets[3]]" />
        </div>

        <!-- InputText with Left Icon -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Input with search icon on the left:
            </p>
            <WidgetRenderer :widgets="[widgets[4]]" />
        </div>

        <!-- InputText with Right Icon -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Input with email icon on the right:
            </p>
            <WidgetRenderer :widgets="[widgets[5]]" />
        </div>

        <!-- Small Size InputText -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Small size input field:
            </p>
            <WidgetRenderer :widgets="[widgets[6]]" />
        </div>

        <!-- Large Size InputText -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Large size input field:
            </p>
            <WidgetRenderer :widgets="[widgets[7]]" />
        </div>

        <!-- InputText with Help Text -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Input with help text below:
            </p>
            <WidgetRenderer :widgets="[widgets[8]]" />
        </div>

        <!-- InputText with Validation Feedback -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                Input with validation feedback enabled:
            </p>
            <WidgetRenderer :widgets="[widgets[9]]" />
        </div>
    </div>
</template>