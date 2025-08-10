// resources/js/components/apex/widgets/InputTextWidget.vue
<script setup lang="ts">
import { computed, ref, watch } from 'vue';

interface Props {
    widgetId: string;
    label?: string;
    placeholder?: string;
    value?: string;
    icon?: string;
    iconPosition?: 'left' | 'right';
    disabled?: boolean;
    required?: boolean;
    readonly?: boolean;
    size?: 'small' | 'medium' | 'large';
    feedback?: boolean;
    invalidMessage?: string;
    helpText?: string;
} 

const props = withDefaults(defineProps<Props>(), {
    label: '',
    placeholder: '',
    value: '',
    icon: '',
    iconPosition: 'left',
    disabled: false,
    required: false,
    readonly: false,
    size: 'medium',
    feedback: false,
    invalidMessage: '',
    helpText: ''
});

const emit = defineEmits<{
    (e: 'update:value', value: string): void;
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
const updateValue = (event: Event) => {
    const target = event.target as HTMLInputElement;
    inputValue.value = target.value;
    emit('update:value', target.value);
};
</script>

<template>
    <div :id="widgetId" class="apex-inputtext-widget mb-4">
        <!-- Label if provided -->
        <label v-if="label" :for="`${widgetId}-input`" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <!-- Input with icon -->
        <div class="relative">
            <span v-if="icon && iconPosition === 'left'" class="p-input-icon-left">
                <i :class="`pi ${icon}`"></i>
                <PInputText
                    :id="`${widgetId}-input`"
                    v-model="inputValue"
                    :placeholder="placeholder"
                    :disabled="disabled"
                    :readonly="readonly"
                    :required="required"
                    :class="sizeClass"
                    @input="updateValue"
                    class="w-full"
                />
            </span>
            
            <span v-else-if="icon && iconPosition === 'right'" class="p-input-icon-right">
                <i :class="`pi ${icon}`"></i>
                <PInputText
                    :id="`${widgetId}-input`"
                    v-model="inputValue"
                    :placeholder="placeholder"
                    :disabled="disabled"
                    :readonly="readonly"
                    :required="required"
                    :class="sizeClass"
                    @input="updateValue"
                    class="w-full"
                />
            </span>
            
            <PInputText
                v-else
                :id="`${widgetId}-input`"
                v-model="inputValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                :class="sizeClass"
                @input="updateValue"
                class="w-full"
            />
        </div>
        
        <!-- Help text if provided -->
        <div v-if="helpText" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ helpText }}
        </div>
        
        <!-- Invalid message if provided -->
        <div v-if="invalidMessage" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ invalidMessage }}
        </div>
    </div>
</template>

<style scoped>
.apex-inputtext-widget {
    margin-bottom: 1rem;
}
</style>