<script setup lang="ts">
import { ref, watch, computed } from 'vue';

interface Props {
    widgetId: string;
    value?: string | null;
    placeholder?: string;
    dateFormat?: string;
    inline?: boolean;
    showIcon?: boolean;
    showButtonBar?: boolean;
    showTime?: boolean;
    timeOnly?: boolean;
    hourFormat?: string;
    disabled?: boolean;
    readonly?: boolean;
    minDate?: string | null;
    maxDate?: string | null;
    disabledDates?: string[];
    selectionMode?: 'single' | 'multiple' | 'range';
    numberOfMonths?: number;
    view?: 'date' | 'month' | 'year';
    touchUI?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    value: null,
    placeholder: 'Select date',
    dateFormat: 'mm/dd/yy',
    inline: false,
    showIcon: true,
    showButtonBar: false,
    showTime: false,
    timeOnly: false,
    hourFormat: '24',
    disabled: false,
    readonly: false,
    minDate: null,
    maxDate: null,
    disabledDates: () => [],
    selectionMode: 'single',
    numberOfMonths: 1,
    view: 'date',
    touchUI: false,
});

// Convert string dates to Date objects
const localValue = ref<Date | Date[] | null>(null);

// Initialize value
if (props.value) {
    if (props.selectionMode === 'multiple' && Array.isArray(props.value)) {
        localValue.value = props.value.map(d => new Date(d));
    } else if (props.selectionMode === 'range' && Array.isArray(props.value)) {
        localValue.value = props.value.map(d => d ? new Date(d) : null) as Date[];
    } else if (typeof props.value === 'string') {
        localValue.value = new Date(props.value);
    }
}

// Convert min/max dates
const minDateComputed = computed(() => 
    props.minDate ? new Date(props.minDate) : undefined
);

const maxDateComputed = computed(() => 
    props.maxDate ? new Date(props.maxDate) : undefined
);

// Convert disabled dates
const disabledDatesComputed = computed(() => 
    props.disabledDates.map(d => new Date(d))
);

// Watch for prop changes
watch(() => props.value, (newValue) => {
    if (newValue) {
        if (props.selectionMode === 'multiple' && Array.isArray(newValue)) {
            localValue.value = newValue.map(d => new Date(d));
        } else if (props.selectionMode === 'range' && Array.isArray(newValue)) {
            localValue.value = newValue.map(d => d ? new Date(d) : null) as Date[];
        } else if (typeof newValue === 'string') {
            localValue.value = new Date(newValue);
        }
    } else {
        localValue.value = null;
    }
});

// Emit value changes
const emit = defineEmits<{
    'update:modelValue': [value: Date | Date[] | null];
}>();

watch(localValue, (newValue) => {
    emit('update:modelValue', newValue);
});
</script>

<template>
    <div :id="widgetId" class="apex-datepicker-widget">
        <PDatePicker
            v-model="localValue"
            :placeholder="placeholder"
            :dateFormat="dateFormat"
            :inline="inline"
            :showIcon="showIcon"
            :showButtonBar="showButtonBar"
            :showTime="showTime"
            :timeOnly="timeOnly"
            :hourFormat="hourFormat"
            :disabled="disabled"
            :readonly="readonly"
            :minDate="minDateComputed"
            :maxDate="maxDateComputed"
            :disabledDates="disabledDatesComputed"
            :selectionMode="selectionMode"
            :numberOfMonths="numberOfMonths"
            :view="view"
            :touchUI="touchUI"
        />
    </div>
</template>

<style scoped>
.apex-datepicker-widget {
    display: inline-block;
    margin: 0.5rem;
}

/* Ensure proper width for non-inline datepickers */
.apex-datepicker-widget :deep(.p-datepicker:not(.p-datepicker-inline)) {
    min-width: 250px;
}
</style>