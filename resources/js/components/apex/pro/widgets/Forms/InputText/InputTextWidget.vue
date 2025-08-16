<!--
Copyright EXOR Group ltd 2025
Version 1.0.0.0
APEX Laravel PrimeVue Components
Description: PRO InputText Vue component with event handling, parameter injection, state management and advanced validation capabilities
File location: resources/js/components/apex/pro/widgets/forms/inputtext/InputTextWidget.vue
-->

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted, type PropType } from 'vue';
import { useEventHandling, type EventConfig } from '../../../Widget/PrimeVueBaseWidget/composables/useEventHandling';
import { useStateManagement } from '../../../Widget/PrimeVueBaseWidget/composables/useStateManagement';
import { useErrorDisplay } from '../../../Widget/PrimeVueBaseWidget/composables/useErrorDisplay';
import { useParameterInjection } from '../../../Widget/PrimeVueBaseWidget/composables/useParameterInjection';

// Props interface
interface Props {
  // Core InputText props (inherited from Core version)
  widgetId: string;
  label?: string;
  placeholder?: string;
  modelValue?: string;
  icon?: string;
  iconPosition?: 'left' | 'right';
  disabled?: boolean;
  required?: boolean;
  readonly?: boolean;
  size?: 'small' | 'medium' | 'large';
  feedback?: boolean;
  invalidMessage?: string;
  helpText?: string;

  // PRO-specific props
  events?: EventConfig;
  stateConfig?: {
    syncToServer?: boolean;
    localState?: boolean;
    conflictResolution?: 'client' | 'server' | 'merge';
  };
  errorConfig?: {
    displayType?: 'inline' | 'toast' | 'dialog';
    position?: 'top' | 'bottom';
    timeout?: number;
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

  // Form context
  formData?: Record<string, any>;
  widgetData?: Record<string, any>;
}

// Define props
const props = defineProps<Props>();

// Define emits
const emit = defineEmits<{
  'update:modelValue': [value: string];
  'blur': [event: Event];
  'focus': [event: Event];
  'input': [event: Event];
  'change': [event: Event];
  'keydown': [event: Event];
  'keyup': [event: Event];
  'click': [event: Event];
  'server-response': [data: any];
  'state-change': [state: any];
  'validation-result': [result: any];
}>();

// Reactive data
const inputRef = ref<HTMLInputElement>();
const localValue = ref(props.modelValue || '');
const isValidating = ref(false);
const validationResult = ref<any>(null);

// Convert events to reactive ref
const eventConfig = ref<EventConfig>(props.events || {});
const formContext = ref(props.formData || {});

// Initialize composables
const {
  getEventHandlers,
  isLoading: eventLoading,
  registerEvents
} = useEventHandling(props.widgetId, eventConfig, formContext);

const {
  state,
  updateState,
  syncToServer,
  isLoading: stateLoading
} = useStateManagement(props.widgetId, props.stateConfig);

const {
  showError,
  clearErrors,
  hasErrors,
  errorMessage,
  errorType
} = useErrorDisplay(props.errorConfig);

const {
  resolveParameters,
  updateContext
} = useParameterInjection(props.parameterConfig);

// Computed properties
const isLoading = computed(() => 
  eventLoading.value || stateLoading.value || isValidating.value
);

const hasValidationError = computed(() => 
  hasErrors.value || (validationResult.value && !validationResult.value.valid)
);

const currentErrorMessage = computed(() => {
  if (validationResult.value && !validationResult.value.valid) {
    return validationResult.value.message;
  }
  return errorMessage.value || props.invalidMessage;
});

const inputClasses = computed(() => {
  const classes = [];
  
  if (props.size) {
    classes.push(`p-inputtext-${props.size}`);
  }
  
  if (hasValidationError.value) {
    classes.push('p-invalid');
  }
  
  if (isLoading.value) {
    classes.push('p-disabled');
  }
  
  return classes.join(' ');
});

// Get event handlers from composable
const eventHandlers = computed(() => getEventHandlers());

// Watch for modelValue changes from parent
watch(() => props.modelValue, (newValue) => {
  try {
    if (newValue !== localValue.value) {
      localValue.value = newValue || '';
      updateState({ value: localValue.value });
    }
  } catch (error) {
    console.error('Error watching modelValue:', error);
  }
});

// Watch for local value changes
watch(localValue, (newValue) => {
  try {
    emit('update:modelValue', newValue);
    updateState({ value: newValue });
    
    // Real-time validation if enabled
    if (props.advancedValidation?.realTimeValidation) {
      performValidation(newValue);
    }
  } catch (error) {
    console.error('Error watching localValue:', error);
  }
});

// Watch for form data changes
watch(() => props.formData, (newFormData) => {
  try {
    if (newFormData) {
      formContext.value = newFormData;
      updateContext({ form: newFormData });
    }
  } catch (error) {
    console.error('Error watching formData:', error);
  }
}, { deep: true });

// Watch for widget data changes
watch(() => props.widgetData, (newWidgetData) => {
  try {
    if (newWidgetData) {
      updateContext({ widget: newWidgetData });
    }
  } catch (error) {
    console.error('Error watching widgetData:', error);
  }
}, { deep: true });

/**
 * Perform validation on input value
 */
const performValidation = async (value: string): Promise<void> => {
  try {
    isValidating.value = true;
    clearErrors();

    // Basic required validation
    if (props.required && !value.trim()) {
      validationResult.value = {
        valid: false,
        message: 'This field is required',
        field: props.widgetId,
        code: 'REQUIRED'
      };
      emit('validation-result', validationResult.value);
      return;
    }

    // Custom validation rules
    if (props.advancedValidation?.customRules) {
      for (const rule of props.advancedValidation.customRules) {
        const result = await validateCustomRule(value, rule);
        if (!result.valid) {
          validationResult.value = result;
          emit('validation-result', validationResult.value);
          return;
        }
      }
    }

    // All validations passed
    validationResult.value = {
      valid: true,
      message: 'Valid',
      field: props.widgetId,
      code: 'VALID'
    };
    emit('validation-result', validationResult.value);
  } catch (error) {
    console.error('Error performing validation:', error);
    validationResult.value = {
      valid: false,
      message: 'Validation error occurred',
      field: props.widgetId,
      code: 'VALIDATION_ERROR'
    };
    emit('validation-result', validationResult.value);
  } finally {
    isValidating.value = false;
  }
};

/**
 * Validate custom rule
 */
const validateCustomRule = async (value: string, rule: any): Promise<any> => {
  try {
    // Handle different rule types
    switch (rule.type) {
      case 'email':
        return validateEmail(value);
      case 'phone':
        return validatePhone(value);
      case 'creditCard':
        return validateCreditCard(value);
      case 'pattern':
        return validatePattern(value, rule.pattern);
      case 'length':
        return validateLength(value, rule.min, rule.max);
      case 'server':
        return await validateOnServer(value, rule);
      default:
        return { valid: true };
    }
  } catch (error) {
    console.error('Error validating custom rule:', error);
    return {
      valid: false,
      message: rule.message || 'Validation failed',
      code: 'RULE_ERROR'
    };
  }
};

/**
 * Email validation
 */
const validateEmail = (value: string): any => {
  try {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = emailPattern.test(value);
    
    return {
      valid: isValid,
      message: isValid ? 'Valid email' : 'Invalid email format',
      code: isValid ? 'VALID' : 'INVALID_EMAIL'
    };
  } catch (error) {
    console.error('Error validating email:', error);
    return { valid: false, message: 'Email validation error', code: 'VALIDATION_ERROR' };
  }
};

/**
 * Phone validation
 */
const validatePhone = (value: string): any => {
  try {
    // Remove all non-numeric characters
    const cleaned = value.replace(/\D/g, '');
    const isValid = cleaned.length >= 10 && cleaned.length <= 15;
    
    return {
      valid: isValid,
      message: isValid ? 'Valid phone number' : 'Invalid phone number',
      code: isValid ? 'VALID' : 'INVALID_PHONE'
    };
  } catch (error) {
    console.error('Error validating phone:', error);
    return { valid: false, message: 'Phone validation error', code: 'VALIDATION_ERROR' };
  }
};

/**
 * Credit card validation (Luhn algorithm)
 */
const validateCreditCard = (value: string): any => {
  try {
    // Remove spaces and dashes
    const cleaned = value.replace(/[\s\-]/g, '');
    
    if (!cleaned || !/^\d+$/.test(cleaned)) {
      return { valid: false, message: 'Invalid card number format', code: 'INVALID_FORMAT' };
    }

    // Luhn algorithm
    let sum = 0;
    let alternate = false;
    
    for (let i = cleaned.length - 1; i >= 0; i--) {
      let n = parseInt(cleaned.charAt(i), 10);
      
      if (alternate) {
        n *= 2;
        if (n > 9) {
          n = (n % 10) + 1;
        }
      }
      
      sum += n;
      alternate = !alternate;
    }
    
    const isValid = sum % 10 === 0;
    
    return {
      valid: isValid,
      message: isValid ? 'Valid card number' : 'Invalid card number',
      code: isValid ? 'VALID' : 'INVALID_CARD'
    };
  } catch (error) {
    console.error('Error validating credit card:', error);
    return { valid: false, message: 'Card validation error', code: 'VALIDATION_ERROR' };
  }
};

/**
 * Pattern validation
 */
const validatePattern = (value: string, pattern: string): any => {
  try {
    const regex = new RegExp(pattern);
    const isValid = regex.test(value);
    
    return {
      valid: isValid,
      message: isValid ? 'Pattern matches' : 'Pattern does not match',
      code: isValid ? 'VALID' : 'PATTERN_MISMATCH'
    };
  } catch (error) {
    console.error('Error validating pattern:', error);
    return { valid: false, message: 'Pattern validation error', code: 'VALIDATION_ERROR' };
  }
};

/**
 * Length validation
 */
const validateLength = (value: string, min?: number, max?: number): any => {
  try {
    const length = value.length;
    
    if (min !== undefined && length < min) {
      return {
        valid: false,
        message: `Minimum length is ${min} characters`,
        code: 'TOO_SHORT'
      };
    }
    
    if (max !== undefined && length > max) {
      return {
        valid: false,
        message: `Maximum length is ${max} characters`,
        code: 'TOO_LONG'
      };
    }
    
    return {
      valid: true,
      message: 'Length is valid',
      code: 'VALID'
    };
  } catch (error) {
    console.error('Error validating length:', error);
    return { valid: false, message: 'Length validation error', code: 'VALIDATION_ERROR' };
  }
};

/**
 * Server-side validation
 */
const validateOnServer = async (value: string, rule: any): Promise<any> => {
  try {
    // This would typically call the server validation endpoint
    // For now, return a mock validation
    return {
      valid: true,
      message: 'Server validation passed',
      code: 'VALID'
    };
  } catch (error) {
    console.error('Error in server validation:', error);
    return {
      valid: false,
      message: 'Server validation failed',
      code: 'SERVER_ERROR'
    };
  }
};

/**
 * Handle input events
 */
const handleInput = (event: Event) => {
  try {
    const target = event.target as HTMLInputElement;
    localValue.value = target.value;
    emit('input', event);
    
    // Execute PRO event handler if configured
    if (eventHandlers.value.onInput) {
      eventHandlers.value.onInput(event, target.value);
    }
  } catch (error) {
    console.error('Error handling input:', error);
  }
};

/**
 * Handle blur events
 */
const handleBlur = (event: Event) => {
  try {
    emit('blur', event);
    
    // Perform validation on blur
    if (props.advancedValidation?.realTimeValidation) {
      performValidation(localValue.value);
    }
    
    // Execute PRO event handler if configured
    if (eventHandlers.value.onBlur) {
      eventHandlers.value.onBlur(event, localValue.value);
    }
  } catch (error) {
    console.error('Error handling blur:', error);
  }
};

/**
 * Handle focus events
 */
const handleFocus = (event: Event) => {
  try {
    emit('focus', event);
    clearErrors();
    
    // Execute PRO event handler if configured
    if (eventHandlers.value.onFocus) {
      eventHandlers.value.onFocus(event, localValue.value);
    }
  } catch (error) {
    console.error('Error handling focus:', error);
  }
};

/**
 * Handle click events
 */
const handleClick = (event: Event) => {
  try {
    emit('click', event);
    
    // Execute PRO event handler if configured
    if (eventHandlers.value.onClick) {
      eventHandlers.value.onClick(event, localValue.value);
    }
  } catch (error) {
    console.error('Error handling click:', error);
  }
};

/**
 * Handle keydown events
 */
const handleKeydown = (event: KeyboardEvent) => {
  try {
    emit('keydown', event);
    
    // Execute PRO event handler if configured
    if (eventHandlers.value.onKeydown) {
      eventHandlers.value.onKeydown(event, localValue.value);
    }
  } catch (error) {
    console.error('Error handling keydown:', error);
  }
};

/**
 * Handle keyup events
 */
const handleKeyup = (event: KeyboardEvent) => {
  try {
    emit('keyup', event);
    
    // Execute PRO event handler if configured
    if (eventHandlers.value.onKeyup) {
      eventHandlers.value.onKeyup(event, localValue.value);
    }
  } catch (error) {
    console.error('Error handling keyup:', error);
  }
};

/**
 * Focus input programmatically
 */
const focus = (): void => {
  try {
    inputRef.value?.focus();
  } catch (error) {
    console.error('Error focusing input:', error);
  }
};

/**
 * Clear input value
 */
const clear = (): void => {
  try {
    localValue.value = '';
    clearErrors();
    validationResult.value = null;
  } catch (error) {
    console.error('Error clearing input:', error);
  }
};

// Initialize component
onMounted(() => {
  try {
    // Register events
    registerEvents();
    
    // Initialize state
    updateState({ value: localValue.value });
    
    // Update context with initial data
    updateContext({
      widget: props.widgetData || {},
      form: props.formData || {}
    });
  } catch (error) {
    console.error('Error mounting PRO InputText widget:', error);
  }
});

// Cleanup
onUnmounted(() => {
  try {
    clearErrors();
  } catch (error) {
    console.error('Error unmounting PRO InputText widget:', error);
  }
});

// Expose methods for parent components
defineExpose({
  focus,
  clear,
  validate: () => performValidation(localValue.value),
  getValue: () => localValue.value,
  setValue: (value: string) => { localValue.value = value; }
});
</script>

<template>
  <div class="apex-pro-inputtext-widget">
    <!-- Label -->
    <label 
      v-if="label" 
      :for="widgetId"
      class="apex-inputtext-label"
      :class="{ 'required': required }"
    >
      {{ label }}
      <span v-if="required" class="required-indicator">*</span>
    </label>

    <!-- Input Container -->
    <div class="apex-inputtext-container" :class="{ 'has-icon': icon }">
      <!-- Left Icon -->
      <i 
        v-if="icon && iconPosition === 'left'" 
        :class="`pi pi-${icon} apex-inputtext-icon-left`"
      ></i>

      <!-- Loading Spinner -->
      <ProgressSpinner 
        v-if="isLoading"
        class="apex-inputtext-spinner"
        style="width: 20px; height: 20px;"
      />

      <!-- Input Element -->
      <InputText
        :id="widgetId"
        ref="inputRef"
        v-model="localValue"
        :placeholder="placeholder"
        :disabled="disabled || isLoading"
        :readonly="readonly"
        :class="inputClasses"
        :invalid="hasValidationError"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        @click="handleClick"
        @keydown="handleKeydown"
        @keyup="handleKeyup"
      />

      <!-- Right Icon -->
      <i 
        v-if="icon && iconPosition === 'right'" 
        :class="`pi pi-${icon} apex-inputtext-icon-right`"
      ></i>
    </div>

    <!-- Error Message (Inline) -->
    <PMessage 
      v-if="hasValidationError && errorType === 'inline'"
      severity="error"
      :closable="false"
      class="apex-inputtext-error"
    >
      {{ currentErrorMessage }}
    </PMessage>

    <!-- Help Text -->
    <small 
      v-if="helpText && !hasValidationError" 
      class="apex-inputtext-help"
    >
      {{ helpText }}
    </small>

    <!-- Validation Result (Development Mode) -->
    <div 
      v-if="validationResult && import.meta.env.DEV"
      class="apex-inputtext-debug"
    >
      <small>Debug: {{ validationResult.code }} - {{ validationResult.message }}</small>
    </div>
  </div>
</template>

<style scoped>
.apex-pro-inputtext-widget {
  @apply w-full;
}

.apex-inputtext-label {
  @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1;
}

.apex-inputtext-label.required {
  @apply font-semibold;
}

.required-indicator {
  @apply text-red-500 ml-1;
}

.apex-inputtext-container {
  @apply relative;
}

.apex-inputtext-container.has-icon .p-inputtext {
  @apply pl-10;
}

.apex-inputtext-icon-left {
  @apply absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400;
}

.apex-inputtext-icon-right {
  @apply absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400;
}

.apex-inputtext-spinner {
  @apply absolute right-3 top-1/2 transform -translate-y-1/2;
}

.apex-inputtext-error {
  @apply mt-1 text-sm;
}

.apex-inputtext-help {
  @apply block mt-1 text-gray-500 dark:text-gray-400;
}

.apex-inputtext-debug {
  @apply mt-1 p-2 bg-gray-100 dark:bg-gray-800 rounded text-xs;
}

/* PrimeVue overrides */
.p-inputtext-small {
  @apply text-sm px-2 py-1;
}

.p-inputtext-medium {
  @apply text-base px-3 py-2;
}

.p-inputtext-large {
  @apply text-lg px-4 py-3;
}
</style>