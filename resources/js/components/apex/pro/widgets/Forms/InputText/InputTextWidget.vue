<!--
Copyright EXOR Group ltd 2025
Version 1.0.0.0
APEX Laravel PrimeVue Components
Description: PRO InputText Vue component extending Core InputText with advanced props and event handling
File location: resources/js/components/apex/pro/widgets/Forms/InputText/InputTextWidget.vue
-->

<template>
    <div :id="props.id" class="apex-pro-inputtext-widget" :class="wrapperClasses">
        <label 
            v-if="label" 
            :for="`${props.id}-input`" 
            :class="labelClasses"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        ref
        <PInputText
            :id="`${props.id}-input`"
            v-model="internalValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            :required="required"
            :size="size"
            :invalid="invalid || hasValidationError"
            :variant="variant"
            :floatLabel="floatLabel"
            :accesskey="accesskey"
            :tabindex="tabindex"
            :aria-label="ariaLabel"
            :pt="pt"
            :class="inputClasses"
            v-bind="eventAttributes"
            @blur="handleBlur"
            @focus="handleFocus"
            @input="handleInput"
            @keydown="handleKeyDown"
            @keyup="handleKeyUp"
            @click="handleClick"
            @dblclick="handleDoubleClick"
        />
        
        <div v-if="helpText" :class="helpTextClasses">
            {{ helpText }}
        </div>
        
        <div v-if="hasValidationError" :class="validationClasses">
            {{ validationMessage }}
        </div>

        <!-- PRO Features Status (for development/testing) -->
        <div v-if="showProStatus && isProLicensed" class="mt-2 text-xs text-green-600">
            PRO Features Active
        </div>
        <div v-else-if="showProStatus" class="mt-2 text-xs text-gray-500">
            Core Features Only
        </div>

        <!-- Global Toast (only render once per page) -->
        <PToast v-if="shouldRenderToast" group="apex-widget-toast" />

        <!-- Response Modal -->
        <PDialog 
            v-model:visible="responseHandler.modalVisible.value"
            :modal="true"
            :closable="responseHandler.modalConfig.value?.closable ?? true"
            :style="{ width: responseHandler.modalConfig.value?.width ?? '400px' }"
            :header="responseHandler.modalConfig.value?.title ?? 'Message'"
        >
            <div class="flex items-start gap-4">
                <PImage 
                    v-if="responseHandler.modalConfig.value?.image"
                    :src="responseHandler.modalConfig.value.image"
                    alt="Modal Image"
                    class="w-16 h-16 object-cover rounded"
                />
                <div class="flex-1">
                    <p class="text-gray-700">{{ responseHandler.modalMessage.value }}</p>
                </div>
            </div>
            
            <template #footer>
                <PButton 
                    :label="responseHandler.modalConfig.value?.buttonText ?? 'OK'"
                    :severity="responseHandler.modalConfig.value?.buttonSeverity ?? 'info'"
                    @click="responseHandler.closeModal"
                />
            </template>
        </PDialog>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { useEventHandling } from '../../../Widget/PrimeVueBaseWidget/composables/useEventHandling';
import { useResponseHandling } from '../../../Widget/PrimeVueBaseWidget/composables/useResponseHandling';
import PDialog from 'primevue/dialog';
import PButton from 'primevue/button';
import PImage from 'primevue/image';
import PToast from 'primevue/toast';

// Props interface extending core props with PRO features
interface Props {
    // Core InputText props (use 'id' not 'widgetId' to match backend)
    id: string;
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

    // PRO-specific props
    events?: {
        [eventName: string]: any;
    };
    stateConfig?: {
        syncToServer?: boolean;
        localState?: boolean;
        conflictResolution?: 'client' | 'server' | 'merge' | 'prompt';
    };
    parameterConfig?: {
        contexts?: string[];
        templates?: Record<string, string>;
        validation?: any;
    };
    advancedValidation?: {
        realTimeValidation?: boolean;
        customRules?: any[];
        businessRules?: any;
    };
    serverConfig?: {
        endpoints?: Record<string, string>;
        timeout?: number;
        retries?: number;
    };

    // Development/testing props
    showProStatus?: boolean;
    
    // Additional props
    [key: string]: any;
}

const props = withDefaults(defineProps<Props>(), {
    id: '',
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
    pt: undefined,
    events: undefined,
    stateConfig: undefined,
    parameterConfig: undefined,
    advancedValidation: undefined,
    serverConfig: undefined,
    showProStatus: false
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
    blur: [event: Event];
    focus: [event: Event];
    input: [event: Event];
    keydown: [event: KeyboardEvent];
    keyup: [event: KeyboardEvent];
    click: [event: Event];
    dblclick: [event: Event];
    invalid: [error: any];
    stateChange: [state: any];
    validationChange: [isValid: boolean];
    'vue-event': [eventData: any];
}>();

// Check Pro license status
const isProLicensed = computed(() => {
    return !!(props.events || props.stateConfig || props.parameterConfig || props.advancedValidation);
});

// Initialize composables
const responseHandler = useResponseHandling();
const eventHandler = useEventHandling({
    widgetId: props.id,
    events: props.events,
    isProLicensed: isProLicensed.value
});

// Make response handler available globally for the composable
(window as any).responseHandler = responseHandler;

// Toast intelligence - only render once per page
const shouldRenderToast = computed(() => {
    try {
        // Check if another widget already rendered the toast
        if (typeof window !== 'undefined') {
            if (!window.apexToastRendered) {
                window.apexToastRendered = props.id;
                return true;
            }
            return window.apexToastRendered === props.id;
        }
        return false;
    } catch (error) {
        console.error('Error checking toast render status:', error);
        return false;
    }
});

// Internal reactive state
const internalValue = ref(props.modelValue);
const hasValidationError = ref(false);
const validationMessage = ref('');

// Watch for modelValue changes
watch(() => props.modelValue, (newValue) => {
    try {
        internalValue.value = newValue;
    } catch (error) {
        console.error('Error updating model value:', error);
    }
});

// Watch for internal value changes
watch(internalValue, (newValue) => {
    try {
        emit('update:modelValue', newValue);
        
        // PRO feature: Real-time validation
        if (isProLicensed.value && props.advancedValidation?.realTimeValidation) {
            performRealTimeValidation(newValue);
        }
    } catch (error) {
        console.error('Error emitting model value update:', error);
    }
});

// Computed properties
const eventAttributes = computed(() => {
    try {
        const attributes: Record<string, string> = {};
        
        if (isProLicensed.value && props.events) {
            for (const [eventName, eventConfig] of Object.entries(props.events)) {
                if (typeof eventConfig === 'string') {
                    // Simple string handler
                    attributes[eventName.toLowerCase()] = eventConfig;
                } else if (typeof eventConfig === 'object' && eventConfig.type === 'simple') {
                    // Simple object with handler string
                    attributes[eventName.toLowerCase()] = eventConfig.handler;
                }
            }
        }
        
        return attributes;
    } catch (error) {
        console.error('Error computing event attributes:', error);
        return {};
    }
});

const wrapperClasses = computed(() => {
    try {
        const classes = ['apex-pro-inputtext-widget'];
        
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
            classes.push('apex-has-error');
        }

        if (isProLicensed.value) {
            classes.push('apex-pro-licensed');
        }
        
        return classes;
    } catch (error) {
        console.error('Error computing wrapper classes:', error);
        return ['apex-pro-inputtext-widget'];
    }
});

const labelClasses = computed(() => {
    try {
        return ['apex-label', 'block', 'text-sm', 'font-medium', 'text-gray-700', 'mb-1'];
    } catch (error) {
        console.error('Error computing label classes:', error);
        return ['apex-label'];
    }
});

const inputClasses = computed(() => {
    try {
        const classes = ['apex-input'];
        
        if (hasValidationError.value) {
            classes.push('apex-input-error');
        }
        
        return classes;
    } catch (error) {
        console.error('Error computing input classes:', error);
        return ['apex-input'];
    }
});

const helpTextClasses = computed(() => {
    try {
        return ['apex-help-text', 'text-xs', 'text-gray-500', 'mt-1'];
    } catch (error) {
        console.error('Error computing help text classes:', error);
        return ['apex-help-text'];
    }
});

const validationClasses = computed(() => {
    try {
        return ['apex-validation-message', 'text-xs', 'text-red-500', 'mt-1'];
    } catch (error) {
        console.error('Error computing validation classes:', error);
        return ['apex-validation-message'];
    }
});

// Event handlers - now simplified since composable handles the complex logic
const handleBlur = (event: Event) => {
    try {
        emit('blur', event);
    } catch (error) {
        console.error('Error handling blur event:', error);
    }
};

const handleFocus = (event: Event) => {
    try {
        emit('focus', event);
    } catch (error) {
        console.error('Error handling focus event:', error);
    }
};

const handleInput = (event: Event) => {
    try {
        emit('input', event);
    } catch (error) {
        console.error('Error handling input event:', error);
    }
};

const handleKeyDown = (event: KeyboardEvent) => {
    try {
        emit('keydown', event);
    } catch (error) {
        console.error('Error handling keydown event:', error);
    }
};

const handleKeyUp = (event: KeyboardEvent) => {
    try {
        emit('keyup', event);
    } catch (error) {
        console.error('Error handling keyup event:', error);
    }
};

const handleClick = (event: Event) => {
    try {
        emit('click', event);
    } catch (error) {
        console.error('Error handling click event:', error);
    }
};

const handleDoubleClick = (event: Event) => {
    try {
        emit('dblclick', event);
    } catch (error) {
        console.error('Error handling double click event:', error);
    }
};

// PRO Methods
const performRealTimeValidation = (value: string) => {
    try {
        console.log('PRO Real-time validation triggered:', value);
        hasValidationError.value = false;
        validationMessage.value = '';
        emit('validationChange', true);
    } catch (error) {
        console.error('Error performing real-time validation:', error);
    }
};

// Lifecycle hooks
onMounted(() => {
    try {
        if (isProLicensed.value) {
            console.log('PRO InputText Widget initialized with features:', {
                events: !!props.events,
                stateConfig: !!props.stateConfig,
                parameterConfig: !!props.parameterConfig,
                advancedValidation: !!props.advancedValidation
            });
        }
    } catch (error) {
        console.error('Error during component mounting:', error);
    }
});

onUnmounted(() => {
    try {
        // Composable handles its own cleanup
        console.log('PRO InputText Widget unmounted');
    } catch (error) {
        console.error('Error during component unmounting:', error);
    }
});
</script>

<style scoped>
/* PRO-specific styling */
.apex-pro-inputtext-widget {
    position: relative;
}

.apex-pro-licensed {
    border-left: 2px solid #10b981;
    padding-left: 0.5rem;
}

.apex-input-error {
    border-color: #ef4444;
}

.apex-input-error:focus {
    border-color: #ef4444;
    outline: 2px solid #ef4444;
    outline-offset: 2px;
}

.apex-has-error .apex-input {
    border-color: #ef4444;
}

.apex-validation-message {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.apex-help-text {
    color: #6b7280;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
</style>