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

// Use response handling composable
const responseHandler = useResponseHandling();

// Internal reactive state
const internalValue = ref(props.modelValue);
const isProLicensed = ref(false);
const hasValidationError = ref(false);
const validationMessage = ref('');

// Common events that we'll listen for
const commonEvents = [
    'blur', 'focus', 'click', 'dblclick', 'mouseover', 'mouseout',
    'mousedown', 'mouseup', 'keydown', 'keyup', 'keypress',
    'change', 'input'
];

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

// Event handlers (simplified)
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

// Generic event processor
const handleGenericEvent = (event: Event) => {
    try {
        const target = event.target as HTMLElement;
        
        // Only process events for THIS widget's input element
        if (!target.id.startsWith(props.id + '-')) {
            return; // Not our widget, ignore
        }
        
        const attrName = `on${event.type}`;
        const attrValue = target.getAttribute(attrName);
        
        if (attrValue?.includes(':')) {
            event.preventDefault();
            event.stopPropagation();
            
            const [type, rest] = attrValue.split(':', 2);
            
            switch(type) {
                case 'js':
                    handleJSEvent(rest, event);
                    break;
                case 'server':
                    handleServerEvent(rest, event);
                    break;
                case 'vue':
                    handleVueEvent(rest, event);
                    break;
                default:
                    console.log('Unknown event type:', type);
            }
        }
    } catch (error) {
        console.error('Error in generic event handler:', error);
    }
};

// JavaScript event handler
const handleJSEvent = (jsCode: string, event: Event) => {
    try {
        console.log('Executing JS event:', jsCode);
        eval(jsCode);
    } catch (error) {
        console.error('Error executing JS event:', error);
    }
};

// Debouncing state
const debounceTimers = ref<Map<string, number>>(new Map());

// Server event handler with debouncing
const handleServerEvent = async (serverCommand: string, event: Event) => {
    try {
        console.log('Executing Server event:', serverCommand);
        
        // Parse server command with optional response config and debounce
        const parts = serverCommand.split('|');
        const commandPart = parts[0];
        const responsePart = parts[1];
        const debouncePart = parts[2];
        
        // Parse the main command
        const match = commandPart.match(/^(.+?)\/(\w+)\((.+)\)$/);
        if (!match) {
            console.error('Invalid server command format:', serverCommand);
            return;
        }
        
        const [, endpoint, handler, fullParamsStr] = match;
        
        // Handle debouncing
        if (debouncePart && parseInt(debouncePart) > 0) {
            const debounceMs = parseInt(debouncePart);
            const eventKey = `${props.id}-${event.type}`;
            
            // Clear existing timer
            if (debounceTimers.value.has(eventKey)) {
                clearTimeout(debounceTimers.value.get(eventKey));
            }
            
            // Set new timer
            const timerId = window.setTimeout(() => {
                executeServerCall(commandPart, responsePart, event);
                debounceTimers.value.delete(eventKey);
            }, debounceMs);
            
            debounceTimers.value.set(eventKey, timerId);
            return;
        }
        
        // Execute immediately if no debounce
        await executeServerCall(commandPart, responsePart, event);
    } catch (error) {
        console.error('Error executing server event:', error);
    }
};

// Extract server call execution
const executeServerCall = async (commandPart: string, responsePart: string, event: Event) => {
    try {
        const match = commandPart.match(/^(.+?)\/(\w+)\((.+)\)$/);
        if (!match) return;
        
        const [, endpoint, handler, fullParamsStr] = match;
        
        // Decode response configuration if present
        let responseConfig = null;
        if (responsePart) {
            responseConfig = responseHandler.decodeResponseConfig(responsePart);
        }
        
        // Evaluate parameters
        const paramRegex = /(?:document\.getElementById\('[^']+'\)\.value|'[^']*'|\w+)/g;
        const paramsList = fullParamsStr.match(paramRegex) || [];
        
        let evaluatedParams: any[] = [];
        for (const param of paramsList) {
            const trimmedParam = param.trim();
            if (trimmedParam.includes('document.getElementById')) {
                evaluatedParams.push(eval(trimmedParam));
            } else if (trimmedParam.startsWith("'") && trimmedParam.endsWith("'")) {
                evaluatedParams.push(trimmedParam.slice(1, -1));
            } else {
                evaluatedParams.push(trimmedParam);
            }
        }
        
        const url = endpoint.startsWith('/') ? endpoint : '/' + endpoint;
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                handler: handler,
                params: evaluatedParams,
                event: event.type,
                value: (event.target as HTMLInputElement)?.value || '',
                widgetId: props.id
            })
        });
        
        if (response.ok) {
            const result = await response.json();
            console.log('Server response:', result);
            
            if (responseConfig) {
                await responseHandler.processServerResponse(result, responseConfig, event);
            }
            
            if (handler && window[handler as keyof Window]) {
                (window[handler as keyof Window] as Function)(result, event);
            }
        }
    } catch (error) {
        console.error('Error in server call execution:', error);
    }
};

// Vue event handler with enhanced parent communication
const handleVueEvent = (vueCommand: string, event: Event) => {
    try {
        console.log('Executing Vue event:', vueCommand);
        
        // Parse: "methodName(param1, param2)"
        const [method, paramsStr] = vueCommand.split('(');
        const params = paramsStr ? paramsStr.replace(')', '').split(',').map(p => p.trim()) : [];
        
        // Evaluate parameters similar to other event types
        const evaluatedParams: any[] = [];
        for (const param of params) {
            try {
                if (param.includes('document.getElementById')) {
                    evaluatedParams.push(eval(param));
                } else if (param.startsWith("'") && param.endsWith("'")) {
                    evaluatedParams.push(param.slice(1, -1));
                } else {
                    evaluatedParams.push(param);
                }
            } catch (evalError) {
                console.warn(`Could not evaluate Vue parameter "${param}":`, evalError);
                evaluatedParams.push(param);
            }
        }
        
        // Enhanced emission with more context
        emit('vue-event', {
            method: method,
            params: evaluatedParams,
            originalParams: params,
            event: {
                type: event.type,
                target: event.target,
                timestamp: Date.now()
            },
            widget: {
                id: props.id,
                value: (event.target as HTMLInputElement)?.value || '',
                type: 'inputtext'
            },
            context: {
                isProLicensed: isProLicensed.value,
                hasValidationError: hasValidationError.value
            }
        });
    } catch (error) {
        console.error('Error executing Vue event:', error);
    }
};

// Setup generic event handler
const setupGenericEventHandler = () => {
    try {
        if (!isProLicensed.value) return;

        commonEvents.forEach(eventType => {
            document.addEventListener(eventType, handleGenericEvent, true);
        });
    } catch (error) {
        console.error('Error setting up generic event handler:', error);
    }
};

// Cleanup event handlers
const cleanupEventHandlers = () => {
    try {
        commonEvents.forEach(eventType => {
            document.removeEventListener(eventType, handleGenericEvent, true);
        });
    } catch (error) {
        console.error('Error cleaning up event handlers:', error);
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

const checkProLicense = () => {
    try {
        isProLicensed.value = !!(props.events || props.stateConfig || props.parameterConfig || props.advancedValidation);
        console.log('PRO License Status:', isProLicensed.value);
    } catch (error) {
        console.error('Error checking PRO license:', error);
        isProLicensed.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    try {
        checkProLicense();
        
        if (isProLicensed.value) {
            console.log('PRO InputText Widget initialized with features:', {
                events: !!props.events,
                stateConfig: !!props.stateConfig,
                parameterConfig: !!props.parameterConfig,
                advancedValidation: !!props.advancedValidation
            });
            
            setupGenericEventHandler();
        }
    } catch (error) {
        console.error('Error during component mounting:', error);
    }
});

onUnmounted(() => {
    try {
        cleanupEventHandlers();
        
        // Clear any pending debounce timers
        debounceTimers.value.forEach(timerId => {
            clearTimeout(timerId);
        });
        debounceTimers.value.clear();
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