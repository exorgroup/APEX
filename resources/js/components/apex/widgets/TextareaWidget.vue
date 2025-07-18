// resources/js/components/apex/widgets/TextareaWidget.vue
<script setup lang="ts">
import { computed, ref, watch } from 'vue';

interface Props {
    widgetId: string;
    label?: string;
    placeholder?: string;
    value?: string;
    rows?: number;
    cols?: number;
    autoResize?: boolean;
    disabled?: boolean;
    required?: boolean;
    readonly?: boolean;
    maxLength?: number;
    feedback?: boolean;
    invalidMessage?: string;
    helpText?: string;
}

const props = withDefaults(defineProps<Props>(), {
    label: '',
    placeholder: '',
    value: '',
    rows: 5,
    cols: undefined,
    autoResize: false,
    disabled: false,
    required: false,
    readonly: false,
    maxLength: undefined,
    feedback: false,
    invalidMessage: '',
    helpText: ''
});

const emit = defineEmits<{
    (e: 'update:value', value: string): void;
}>();

// Local state to manage textarea value
const textareaValue = ref(props.value);

// Watch for changes in value prop
watch(() => props.value, (newValue) => {
    textareaValue.value = newValue;
});

// Update value and emit changes
const updateValue = (event: Event) => {
    const target = event.target as HTMLTextAreaElement;
    textareaValue.value = target.value;
    emit('update:value', target.value);
};
</script>

<template>
    <div :id="widgetId" class="apex-textarea-widget mb-4">
        <!-- Label if provided -->
        <label v-if="label" :for="`${widgetId}-textarea`" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        
        <!-- Textarea component -->
        <PTextarea
            :id="`${widgetId}-textarea`"
            v-model="textareaValue"
            :placeholder="placeholder"
            :rows="rows"
            :cols="cols"
            :autoResize="autoResize"
            :disabled="disabled"
            :readonly="readonly"
            :required="required"
            :maxlength="maxLength"
            @input="updateValue"
            class="w-full"
        />
        
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
.apex-textarea-widget {
    margin-bottom: 1rem;
}
</style>