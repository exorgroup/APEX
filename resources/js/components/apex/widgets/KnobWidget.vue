<script setup lang="ts">
import { ref, watch } from 'vue';

interface Props {
    widgetId: string;
    value?: number;
    min?: number;
    max?: number;
    step?: number;
    size?: number;
    strokeWidth?: number;
    showValue?: boolean;
    valueTemplate?: string;
    disabled?: boolean;
    readonly?: boolean;
    valueColor?: string;
    rangeColor?: string;
    textColor?: string;
}

const props = withDefaults(defineProps<Props>(), {
    value: 0,
    min: 0,
    max: 100,
    step: 1,
    size: 100,
    strokeWidth: 6,
    showValue: true,
    valueTemplate: '{value}',
    disabled: false,
    readonly: false,
});

// Create local reactive value
const localValue = ref(props.value);

// Watch for prop changes
watch(() => props.value, (newValue) => {
    localValue.value = newValue;
});

// Emit value changes (if we add v-model support later)
const emit = defineEmits<{
    'update:modelValue': [value: number];
}>();

watch(localValue, (newValue) => {
    emit('update:modelValue', newValue);
});
</script>

<template>
    <div :id="widgetId" class="apex-knob-widget">
        <PKnob
            v-model="localValue"
            :min="min"
            :max="max"
            :step="step"
            :size="size"
            :strokeWidth="strokeWidth"
            :showValue="showValue"
            :valueTemplate="valueTemplate"
            :disabled="disabled"
            :readonly="readonly"
            :valueColor="valueColor"
            :rangeColor="rangeColor"
            :textColor="textColor"
        />
    </div>
</template>

<style scoped>
.apex-knob-widget {
    display: inline-block;
    margin: 0.5rem;
}
</style>