// resources/js/components/apex/widgets/CheckboxWidget.vue
<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface Props {
    widgetId: string;
    label: string;
    checked?: boolean;
    disabled?: boolean;
    binary?: boolean;
    invalid?: boolean;
    indeterminate?: boolean;
    variant?: 'filled' | 'outlined';
    size?: 'small' | 'medium' | 'large';
    name?: string;
    value?: any;
    trueValue?: any;
    falseValue?: any;
}

const props = withDefaults(defineProps<Props>(), {
    checked: false,
    disabled: false,
    binary: true,
    invalid: false,
    indeterminate: false,
    variant: 'outlined',
    size: 'medium',
    trueValue: true,
    falseValue: false
});

// Create a reactive reference for the checkbox state
const checkboxValue = ref(props.checked ? props.trueValue : props.falseValue);

// Watch for changes in the checked prop
watch(() => props.checked, (newValue) => {
    checkboxValue.value = newValue ? props.trueValue : props.falseValue;
});

// Compute classes based on size and variant
const checkboxClasses = computed(() => {
    const classes = ['apex-checkbox-widget'];
    
    // Size classes
    if (props.size === 'small') {
        classes.push('apex-checkbox-small');
    } else if (props.size === 'large') {
        classes.push('apex-checkbox-large');
    }
    
    // Variant classes
    if (props.variant === 'filled') {
        classes.push('apex-checkbox-filled');
    }
    
    // State classes
    if (props.invalid) {
        classes.push('apex-checkbox-invalid');
    }
    
    return classes.join(' ');
});

// Handle checkbox change
const handleChange = (event: any) => {
    const newValue = event.checked ? props.trueValue : props.falseValue;
    checkboxValue.value = newValue;
    
    // You can emit an event here if needed for parent component communication
    console.log(`Checkbox ${props.widgetId} changed to:`, newValue);
};
</script>

<template>
    <div :id="widgetId" :class="checkboxClasses">
        <div class="flex items-center gap-2">
            <PCheckbox
                v-model="checkboxValue"
                :binary="binary"
                :disabled="disabled"
                :invalid="invalid"
                :indeterminate="indeterminate"
                :variant="variant"
                :name="name"
                :value="value"
                :trueValue="trueValue"
                :falseValue="falseValue"
                :inputId="`${widgetId}-input`"
                @change="handleChange"
            />
            <label 
                :for="`${widgetId}-input`" 
                class="cursor-pointer select-none"
                :class="{ 'opacity-50 cursor-not-allowed': disabled }"
            >
                {{ label }}
            </label>
        </div>
    </div>
</template>

<style scoped>
.apex-checkbox-widget {
    margin-bottom: 0.5rem;
}

/* Size variations */
.apex-checkbox-small {
    font-size: 0.875rem;
}

.apex-checkbox-small :deep(.p-checkbox) {
    width: 1rem;
    height: 1rem;
}

.apex-checkbox-small :deep(.p-checkbox-icon) {
    font-size: 0.75rem;
}

.apex-checkbox-large {
    font-size: 1.125rem;
}

.apex-checkbox-large :deep(.p-checkbox) {
    width: 1.5rem;
    height: 1.5rem;
}

.apex-checkbox-large :deep(.p-checkbox-icon) {
    font-size: 1rem;
}

/* Variant styles */
.apex-checkbox-filled :deep(.p-checkbox.p-checkbox-checked .p-checkbox-box) {
    background-color: var(--p-checkbox-checked-background);
    border-color: var(--p-checkbox-checked-border-color);
}

/* Invalid state */
.apex-checkbox-invalid :deep(.p-checkbox-box) {
    border-color: var(--p-error-color);
}

.apex-checkbox-invalid label {
    color: var(--p-error-color);
}

/* Dark mode support */
.dark .apex-checkbox-widget label {
    color: var(--p-text-color);
}
</style>