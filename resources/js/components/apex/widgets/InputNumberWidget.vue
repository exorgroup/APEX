// resources/js/components/apex/widgets/InputNumberWidget.vue
<script setup lang="ts">
import { ref, watch, computed } from 'vue';

interface Props {
    widgetId: string;
    label?: string;
    value?: number;
    min?: number;
    max?: number;
    step?: number;
    format?: boolean;
    showButtons?: boolean;
    buttonLayout?: 'stacked' | 'horizontal' | 'vertical';
    incrementButtonIcon?: string;
    decrementButtonIcon?: string;
    prefix?: string;
    suffix?: string;
    currency?: string;
    currencyDisplay?: string;
    locale?: string;
    placeholder?: string;
    disabled?: boolean;
    readonly?: boolean;
    size?: 'small' | 'medium' | 'large';
    helpText?: string;
    mode?: 'decimal' | 'currency' | 'percentage';
    minFractionDigits?: number;
    maxFractionDigits?: number;
}

const props = withDefaults(defineProps<Props>(), {
    label: '',
    value: 0,
    min: undefined,
    max: undefined,
    step: 1,
    format: false,
    showButtons: false,
    buttonLayout: 'stacked',
    incrementButtonIcon: 'pi pi-plus',
    decrementButtonIcon: 'pi pi-minus',
    prefix: '',
    suffix: '',
    currency: undefined,
    currencyDisplay: undefined,
    locale: undefined,
    placeholder: '',
    disabled: false,
    readonly: false,
    size: 'medium',
    helpText: '',
    mode: 'decimal',
    minFractionDigits: undefined,
    maxFractionDigits: undefined
});

const emit = defineEmits<{
    (e: 'update:value', value: number): void;
}>();

// Local state to manage input value
const inputValue = ref(props.value);

// Watch for changes in value prop
watch(() => props.value, (newValue) => {
    inputValue.value = newValue;
});

// Computed classes based on props
const sizeClass = computed(() => {
    return {
        'p-inputtext-sm': props.size === 'small',
        'p-inputtext-lg': props.size === 'large'
    };
});

// Update value and emit changes
const updateValue = (value: number) => {
    inputValue.value = value;
    emit('update:value', value);
};
</script>

<template>
    <div :id="widgetId" class="apex-inputnumber-widget mb-4">
        <!-- Label if provided -->
        <label v-if="label" :for="`${widgetId}-input`" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
        </label>
        
        <div class="relative">
            <PInputNumber
                :id="`${widgetId}-input`"
                v-model="inputValue"
                :min="min"
                :max="max"
                :step="step"
                :format="format"
                :showButtons="showButtons"
                :buttonLayout="buttonLayout"
                :incrementButtonIcon="incrementButtonIcon"
                :decrementButtonIcon="decrementButtonIcon"
                :prefix="prefix"
                :suffix="suffix"
                :currency="currency"
                :currencyDisplay="currencyDisplay"
                :locale="locale"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :mode="mode"
                :minFractionDigits="minFractionDigits"
                :maxFractionDigits="maxFractionDigits"
                :class="[sizeClass, 'w-full']"
                @update:modelValue="updateValue"
            />
        </div>
        
        <!-- Help text if provided -->
        <p v-if="helpText" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ helpText }}
        </p>
    </div>
</template>

<style scoped>
.apex-inputnumber-widget {
    margin-bottom: 1rem;
}
</style>