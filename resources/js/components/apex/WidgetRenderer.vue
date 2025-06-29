<script setup lang="ts">
import { computed, markRaw, type Component } from 'vue';
import BreadcrumbWidget from './widgets/BreadcrumbWidget.vue';
import TextareaWidget from './widgets/TextareaWidget.vue'; 
import KnobWidget from './widgets/KnobWidget.vue';
import DatePickerWidget from './widgets/DatePickerWidget.vue';
import InputTextWidget from './widgets/InputTextWidget.vue';
import InputNumberWidget from './widgets/InputNumberWidget.vue';
import SelectWidget from './widgets/SelectWidget.vue';
import CheckboxWidget from './widgets/CheckboxWidget.vue';
import ButtonWidget from './widgets/ButtonWidget.vue';
import DataTableWidget from './widgets/DataTableWidget.vue';


interface WidgetConfig {
    id: string;
    type: string;
    props: Record<string, any>;
}

interface Props {
    widgets: WidgetConfig[];
}

const props = defineProps<Props>();

// Use Record type for better TypeScript support
const widgetComponents: Record<string, Component> = {
    breadcrumb: markRaw(BreadcrumbWidget),
    knob: markRaw(KnobWidget),
    datepicker: markRaw(DatePickerWidget),
    inputtext: markRaw(InputTextWidget),
    inputnumber: markRaw(InputNumberWidget),
    textarea: markRaw(TextareaWidget),
    select: markRaw(SelectWidget),
    checkbox: markRaw(CheckboxWidget),
    button: markRaw(ButtonWidget),
    datatable: markRaw(DataTableWidget),
    // Add more widget types here as they are created
};

// Get component for widget type with proper typing
const getWidgetComponent = (type: string): Component | null => {
    return widgetComponents[type] ?? null;
};

// Process widgets to include component references
const processedWidgets = computed(() => {
    return props.widgets.map(widget => ({
        ...widget,
        component: getWidgetComponent(widget.type)
    }));
});

const emit = defineEmits<{
    action: [payload: any];
    'crud-action': [payload: any];
    headerAction: [action: string];
}>();
</script>

<template>
    <div class="apex-widget-container">
        <component
            v-for="widget in processedWidgets"
            :key="widget.id"
            :is="widget.component"
            v-bind="widget.props"
            :widget-id="widget.id"
            @action="$emit('action', $event)"
            @crud-action="$emit('crud-action', $event)"
            @headerAction="$emit('headerAction', $event)"
        />
    </div>
</template>