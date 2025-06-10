// resources/js/components/apex/widgets/SelectWidget.vue
<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface SelectOption {
    label: string;
    value: string | number;
    disabled?: boolean;
    icon?: string;
}

interface Props {
    widgetId: string;
    value?: string | number | (string | number)[] | null;
    options: SelectOption[];
    optionLabel?: string;
    optionValue?: string;
    optionDisabled?: string;
    placeholder?: string;
    multiple?: boolean;
    disabled?: boolean;
    filter?: boolean;
    filterPlaceholder?: string;
    showClear?: boolean;
    editable?: boolean;
    checkmark?: boolean;
    highlightOnSelect?: boolean;
    display?: 'comma' | 'chip';
    required?: boolean;
    invalid?: boolean;
    label?: string;
    helpText?: string;
    loading?: boolean;
    loadingIcon?: string;
    variant?: 'filled' | 'outlined';
    size?: 'small' | 'normal' | 'large';
}

const props = withDefaults(defineProps<Props>(), {
    value: null,
    options: () => [],
    optionLabel: 'label',
    optionValue: 'value',
    optionDisabled: 'disabled',
    placeholder: 'Select an option',
    multiple: false,
    disabled: false,
    filter: false,
    filterPlaceholder: 'Search',
    showClear: false,
    editable: false,
    checkmark: false,
    highlightOnSelect: true,
    display: 'comma',
    required: false,
    invalid: false,
    loading: false,
    loadingIcon: 'pi pi-spinner',
    variant: 'outlined',
    size: 'normal'
});

// Local value state
const localValue = ref<any>(props.value);

// Watch for prop changes
watch(() => props.value, (newValue) => {
    localValue.value = newValue;
});

// Emit value changes
const emit = defineEmits<{
    'update:modelValue': [value: any];
    'change': [value: any];
    'filter': [value: string];
}>();

watch(localValue, (newValue) => {
    emit('update:modelValue', newValue);
    emit('change', newValue);
});

// Computed classes based on size
const selectClass = computed(() => {
    const classes = ['apex-select-widget'];
    
    if (props.size === 'small') {
        classes.push('p-select-sm');
    } else if (props.size === 'large') {
        classes.push('p-select-lg');
    }
    
    if (props.variant === 'filled') {
        classes.push('p-variant-filled');
    }
    
    if (props.invalid) {
        classes.push('p-invalid');
    }
    
    return classes.join(' ');
});

// Handle filter event
const handleFilter = (event: any) => {
    emit('filter', event.value);
};
</script>

<template>
    <div :id="widgetId" class="apex-select-container">
        <!-- Label -->
        <label v-if="label" :for="`${widgetId}-select`" class="apex-select-label">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <!-- Select Component -->
        <PSelect
            v-model="localValue"
            :inputId="`${widgetId}-select`"
            :options="options"
            :optionLabel="optionLabel"
            :optionValue="optionValue"
            :optionDisabled="optionDisabled"
            :placeholder="placeholder"
            :multiple="multiple"
            :disabled="disabled"
            :filter="filter"
            :filterPlaceholder="filterPlaceholder"
            :showClear="showClear"
            :editable="editable"
            :checkmark="checkmark"
            :highlightOnSelect="highlightOnSelect"
            :display="display"
            :loading="loading"
            :loadingIcon="loadingIcon"
            :class="selectClass"
            @filter="handleFilter"
        >
            <!-- Custom option template with icons -->
            <template #option="slotProps">
                <div class="flex align-items-center">
                    <i v-if="slotProps.option.icon" :class="[slotProps.option.icon, 'mr-2']"></i>
                    <span>{{ slotProps.option[optionLabel] }}</span>
                </div>
            </template>
            
            <!-- Custom value template for single selection with icon -->
            <template v-if="!multiple && localValue" #value="slotProps">
                <div v-if="slotProps.value && typeof slotProps.value === 'object'" class="flex align-items-center">
                    <i v-if="slotProps.value.icon" :class="[slotProps.value.icon, 'mr-2']"></i>
                    <span>{{ slotProps.value[optionLabel] }}</span>
                </div>
                <span v-else>{{ slotProps.value }}</span>
            </template>
        </PSelect>
        
        <!-- Help Text -->
        <small v-if="helpText" class="apex-select-help">
            {{ helpText }}
        </small>
    </div>
</template>

<style scoped>
.apex-select-container {
    display: flex;
    flex-direction: column;
    margin-bottom: 1rem;
}

.apex-select-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-color);
}

.apex-select-widget {
    width: 100%;
}

.apex-select-help {
    margin-top: 0.25rem;
    color: var(--text-color-secondary);
}

/* Size variations */
:deep(.p-select-sm) {
    font-size: 0.875rem;
}

:deep(.p-select-sm .p-select-label) {
    padding: 0.375rem 0.625rem;
}

:deep(.p-select-lg) {
    font-size: 1.125rem;
}

:deep(.p-select-lg .p-select-label) {
    padding: 0.625rem 0.875rem;
}

/* Filled variant */
:deep(.p-variant-filled) {
    background-color: var(--surface-100);
}

:deep(.p-variant-filled:enabled:hover) {
    background-color: var(--surface-200);
}

/* Loading state */
:deep(.p-select-loading-icon) {
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>