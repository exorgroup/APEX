<!-- 
Copyright EXOR Group ltd 2025
Version 1.0.0.0
APEX Laravel PrimeVue Components
Description: Core InputText Vue component with registry-based props
File location: resources/js/components/apex/core/widgets/forms/inputtext/InputTextWidget.vue
-->

<template>
    <div :id="widgetId" class="apex-inputtext-widget" :class="wrapperClasses">
        <label 
            v-if="label" 
            :for="`${widgetId}-input`" 
            :class="labelClasses"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <PInputText
            :id="`${widgetId}-input`"
            v-model="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            :required="required"
            :class="inputClasses"
            @blur="handleBlur"
            @focus="handleFocus"
            @input="handleInput"
        />
        
        <div v-if="helpText" :class="helpTextClasses">
            {{ helpText }}
        </div>
        
        <div v-if="hasValidationError" :class="validationClasses">
            {{ validationMessage }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

interface Props {
    widgetId: string;
    modelValue?: string;
    placeholder?: string;
    disabled?: boolean;
    readonly?: boolean;
    required?: boolean;
    size?: 'small' | 'large';
    invalidMessage?: string;
    label?: string;
    helpText?: string;
    variant?: 'filled' | 'outlined';
    floatLabel?: boolean;
    accesskey?: string;
    tabindex?: number;
    ariaLabel?: string;
    invalid?: boolean;
    pt?: object;
    [key: string]: any;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    placeholder: '',
    disabled: false,
    readonly: false,
    required: false,
    size: undefined,
    invalidMessage: '',
    label: '',
    helpText: '',
    variant: undefined,
    floatLabel: false,
    accesskey: undefined,
    tabindex: undefined,
    ariaLabel: undefined,
    invalid: false,
    pt: undefined
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
    blur: [event: Event];
    focus: [event: Event];
    input: [event: Event];
    keydown: [event: KeyboardEvent];
    invalid: [error: any];
}>();

const modelValue = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
    modelValue.value = newValue;
});

const wrapperClasses = computed(() => {
    const classes = ['apex-inputtext-widget'];
    
    if (props.size) {
        classes.push(`apex-size-${props.size}`);
    }
    
    if (props.variant) {
        classes.push(`apex-variant-${props.variant}`);
    }
    
    if (props.floatLabel) {
        classes.push('apex-float-label');
    }
    
    if (hasValidationError.value) {
        classes.push('apex-invalid');
    }
    
    return classes;
});

const labelClasses = computed(() => {
    const classes = ['apex-label', 'mb-2', 'block', 'text-sm', 'font-medium', 'text-gray-700', 'dark:text-gray-300'];
    
    if (props.floatLabel) {
        classes.push('apex-float-label-text');
    }
    
    return classes;
});

const inputClasses = computed(() => {
    const classes = ['w-full'];
    
    if (props.size === 'small') {
        classes.push('p-inputtext-sm');
    } else if (props.size === 'large') {
        classes.push('p-inputtext-lg');
    }
    
    if (props.invalid || hasValidationError.value) {
        classes.push('p-invalid');
    }
    
    return classes;
});

const helpTextClasses = computed(() => [
    'apex-help-text',
    'mt-1',
    'text-sm',
    'text-gray-500',
    'dark:text-gray-400'
]);

const validationClasses = computed(() => [
    'apex-validation-message',
    'mt-1',
    'text-sm',
    'text-red-600',
    'dark:text-red-400'
]);

const hasValidationError = computed(() => {
    return props.invalid || (props.invalidMessage && props.invalidMessage.length > 0);
});

const validationMessage = computed(() => {
    return props.invalidMessage || 'Invalid input';
});

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    modelValue.value = target.value;
    emit('update:modelValue', target.value);
    emit('input', event);
};

const handleBlur = (event: Event) => {
    emit('blur', event);
};

const handleFocus = (event: Event) => {
    emit('focus', event);
};
</script>

<style scoped>
.apex-inputtext-widget {
    margin-bottom: 1rem;
}

.apex-size-small {
    font-size: 0.875rem;
}

.apex-size-large {
    font-size: 1.125rem;
}

.apex-variant-filled .p-inputtext {
    background-color: var(--surface-50);
}

.apex-float-label {
    position: relative;
}

.apex-float-label .apex-float-label-text {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    transition: all 0.2s ease;
    pointer-events: none;
    color: var(--text-color-secondary);
}

.apex-float-label.has-value .apex-float-label-text,
.apex-float-label.has-focus .apex-float-label-text {
    top: -0.25rem;
    left: 0.5rem;
    font-size: 0.75rem;
    background: var(--surface-0);
    padding: 0 0.25rem;
}

.apex-invalid .p-inputtext {
    border-color: var(--red-500);
}
</style>