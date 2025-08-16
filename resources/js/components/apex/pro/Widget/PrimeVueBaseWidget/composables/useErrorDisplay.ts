/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Error display composable for PRO widgets providing configurable error display strategies (inline, toast, dialog) with PrimeVue integration and auto-dismiss functionality
 * File location: resources/js/components/apex/pro/Widget/PrimeVueBaseWidget/composables/useErrorDisplay.ts
 */

import { ref, reactive, computed, onMounted, onUnmounted, readonly, type Ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

export interface ErrorConfig {
  displayType?: 'inline' | 'toast' | 'dialog';
  position?: 'top' | 'bottom' | 'left' | 'right';
  timeout?: number;
  autoClose?: boolean;
  showIcon?: boolean;
  allowDismiss?: boolean;
  stackErrors?: boolean;
  customStyling?: {
    className?: string;
    style?: Record<string, string>;
  };
  fieldMapping?: Record<string, string>;
  localization?: {
    defaultLocale?: string;
    messages?: Record<string, Record<string, string>>;
  };
}

export interface ErrorItem {
  id: string;
  message: string;
  severity: 'info' | 'success' | 'warn' | 'error';
  field?: string;
  code?: string;
  displayType: 'inline' | 'toast' | 'dialog';
  autoClose: boolean;
  timeout: number;
  showIcon: boolean;
  allowDismiss: boolean;
  timestamp: string;
  dismissed: boolean;
  context?: Record<string, any>;
}

export function useErrorDisplay(config: ErrorConfig = {}) {
  const toast = useToast();
  const confirm = useConfirm();

  // Configuration with defaults
  const errorConfig = reactive<ErrorConfig>({
    displayType: 'inline',
    position: 'bottom',
    timeout: 5000,
    autoClose: true,
    showIcon: true,
    allowDismiss: true,
    stackErrors: false,
    customStyling: {},
    fieldMapping: {},
    localization: {
      defaultLocale: 'en',
      messages: {}
    },
    ...config
  });

  // Error storage
  const errors = ref<Map<string, ErrorItem>>(new Map());
  const autoCloseTimers = ref<Map<string, number>>(new Map());

  /**
   * Show error with specified configuration
   */
  const showError = (
    message: string,
    displayType?: 'inline' | 'toast' | 'dialog',
    field?: string,
    severity: 'info' | 'success' | 'warn' | 'error' = 'error',
    code?: string,
    context?: Record<string, any>
  ): string => {
    try {
      const errorId = generateErrorId();
      const resolvedDisplayType = displayType || determineDisplayType(severity, field);
      const localizedMessage = localizeMessage(message, code);

      const error: ErrorItem = {
        id: errorId,
        message: localizedMessage,
        severity,
        field: mapField(field),
        code,
        displayType: resolvedDisplayType,
        autoClose: shouldAutoClose(severity),
        timeout: errorConfig.timeout || 5000,
        showIcon: errorConfig.showIcon || true,
        allowDismiss: errorConfig.allowDismiss || true,
        timestamp: new Date().toISOString(),
        dismissed: false,
        context
      };

      // Handle stacking vs replacement
      if (!errorConfig.stackErrors) {
        clearErrors(error.field);
      }

      // Store error
      errors.value.set(errorId, error);

      // Display error based on type
      displayError(error);

      // Setup auto-close if configured
      if (error.autoClose && error.timeout > 0) {
        setupAutoClose(errorId, error.timeout);
      }

      return errorId;
    } catch (error) {
      console.error('Error showing error:', error);
      return '';
    }
  };

  /**
   * Display error using appropriate method
   */
  const displayError = (error: ErrorItem): void => {
    try {
      switch (error.displayType) {
        case 'toast':
          displayToastError(error);
          break;
        case 'dialog':
          displayDialogError(error);
          break;
        case 'inline':
        default:
          // Inline errors are handled by the component itself
          // Just emit event for component to handle
          emitErrorEvent('error-added', error);
          break;
      }
    } catch (err) {
      console.error('Error displaying error:', err);
    }
  };

  /**
   * Display toast error using PrimeVue Toast
   */
  const displayToastError = (error: ErrorItem): void => {
    try {
      const toastConfig: any = {
        severity: error.severity,
        summary: getSeverityLabel(error.severity),
        detail: error.message,
        life: error.autoClose ? error.timeout : 0,
        closable: error.allowDismiss,
        group: error.field ? `field-${error.field}` : 'global'
      };

      // Add custom styling if configured
      if (errorConfig.customStyling?.className) {
        toastConfig.styleClass = errorConfig.customStyling.className;
      }

      // Position configuration
      if (errorConfig.position) {
        toastConfig.position = mapToastPosition(errorConfig.position);
      }

      toast.add(toastConfig);

      // Mark as displayed
      emitErrorEvent('error-displayed', error);
    } catch (err) {
      console.error('Error displaying toast:', err);
    }
  };

  /**
   * Display dialog error using PrimeVue Confirm Dialog
   */
  const displayDialogError = (error: ErrorItem): void => {
    try {
      const dialogConfig: any = {
        message: error.message,
        header: getSeverityLabel(error.severity),
        icon: getDialogIcon(error.severity),
        acceptLabel: 'OK',
        rejectLabel: error.allowDismiss ? 'Dismiss' : undefined,
        accept: () => {
          dismissError(error.id);
        }
      };

      // Only show reject button if dismissal is allowed
      if (error.allowDismiss) {
        dialogConfig.reject = () => {
          dismissError(error.id);
        };
      }

      confirm.require(dialogConfig);

      // Mark as displayed
      emitErrorEvent('error-displayed', error);
    } catch (err) {
      console.error('Error displaying dialog:', err);
    }
  };

  /**
   * Dismiss specific error
   */
  const dismissError = (errorId: string): boolean => {
    try {
      const error = errors.value.get(errorId);
      if (!error) {
        return false;
      }

      // Mark as dismissed
      error.dismissed = true;

      // Clear auto-close timer
      const timer = autoCloseTimers.value.get(errorId);
      if (timer) {
        window.clearTimeout(timer);
        autoCloseTimers.value.delete(errorId);
      }

      // Remove from storage
      errors.value.delete(errorId);

      // Emit event
      emitErrorEvent('error-dismissed', error);

      return true;
    } catch (error) {
      console.error('Error dismissing error:', error);
      return false;
    }
  };

  /**
   * Clear errors for specific field or all errors
   */
  const clearErrors = (field?: string): number => {
    try {
      let clearedCount = 0;

      if (field) {
        // Clear errors for specific field
        for (const [errorId, error] of errors.value.entries()) {
          if (error.field === field) {
            dismissError(errorId);
            clearedCount++;
          }
        }
      } else {
        // Clear all errors
        const errorIds = Array.from(errors.value.keys());
        errorIds.forEach(errorId => {
          dismissError(errorId);
          clearedCount++;
        });
      }

      if (clearedCount > 0) {
        emitErrorEvent('errors-cleared', { field, count: clearedCount });
      }

      return clearedCount;
    } catch (error) {
      console.error('Error clearing errors:', error);
      return 0;
    }
  };

  /**
   * Process server error response
   */
  const processServerError = (serverResponse: any): string | null => {
    try {
      if (!serverResponse || serverResponse.success) {
        return null;
      }

      const message = serverResponse.message || 'An error occurred';
      const field = serverResponse.field || null;
      const code = serverResponse.code || 'SERVER_ERROR';
      const severity = mapServerSeverity(serverResponse.severity || 'error');

      return showError(message, undefined, field, severity, code, {
        source: 'server',
        response: serverResponse
      });
    } catch (error) {
      console.error('Error processing server error:', error);
      return null;
    }
  };

  /**
   * Setup auto-close timer for error
   */
  const setupAutoClose = (errorId: string, timeout: number): void => {
    try {
      const timer = window.setTimeout(() => {
        dismissError(errorId);
      }, timeout);

      autoCloseTimers.value.set(errorId, timer);
    } catch (error) {
      console.error('Error setting up auto-close:', error);
    }
  };

  /**
   * Generate unique error ID
   */
  const generateErrorId = (): string => {
    try {
      return `error_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    } catch (error) {
      console.error('Error generating error ID:', error);
      return `error_${Date.now()}`;
    }
  };

  /**
   * Determine display type based on severity and field
   */
  const determineDisplayType = (
    severity: string,
    field?: string
  ): 'inline' | 'toast' | 'dialog' => {
    try {
      // Field-specific errors are typically inline
      if (field) {
        return 'inline';
      }

      // System errors based on severity
      switch (severity) {
        case 'error':
          return errorConfig.displayType || 'inline';
        case 'warn':
          return 'toast';
        case 'info':
        case 'success':
          return 'toast';
        default:
          return errorConfig.displayType || 'inline';
      }
    } catch (error) {
      console.error('Error determining display type:', error);
      return 'inline';
    }
  };

  /**
   * Check if error should auto-close
   */
  const shouldAutoClose = (severity: string): boolean => {
    try {
      // Critical errors should not auto-close
      if (severity === 'error') {
        return false;
      }

      return errorConfig.autoClose || true;
    } catch (error) {
      console.error('Error determining auto-close:', error);
      return true;
    }
  };

  /**
   * Localize error message
   */
  const localizeMessage = (message: string, code?: string): string => {
    try {
      if (!code || !errorConfig.localization?.messages) {
        return message;
      }

      const locale = errorConfig.localization.defaultLocale || 'en';
      const messages = errorConfig.localization.messages[locale];

      if (messages && messages[code]) {
        return messages[code];
      }

      return message;
    } catch (error) {
      console.error('Error localizing message:', error);
      return message;
    }
  };

  /**
   * Map field using field mapping configuration
   */
  const mapField = (field?: string): string | undefined => {
    try {
      if (!field || !errorConfig.fieldMapping) {
        return field;
      }

      return errorConfig.fieldMapping[field] || field;
    } catch (error) {
      console.error('Error mapping field:', error);
      return field;
    }
  };

  /**
   * Map server severity to client severity
   */
  const mapServerSeverity = (serverSeverity: string): 'info' | 'success' | 'warn' | 'error' => {
    try {
      const mapping: Record<string, 'info' | 'success' | 'warn' | 'error'> = {
        'critical': 'error',
        'warning': 'warn',
        'notice': 'info',
        'information': 'info'
      };

      return mapping[serverSeverity] || (serverSeverity as any) || 'error';
    } catch (error) {
      console.error('Error mapping server severity:', error);
      return 'error';
    }
  };

  /**
   * Get severity label for display
   */
  const getSeverityLabel = (severity: string): string => {
    try {
      const labels: Record<string, string> = {
        'info': 'Information',
        'success': 'Success',
        'warn': 'Warning',
        'error': 'Error'
      };

      return labels[severity] || 'Message';
    } catch (error) {
      console.error('Error getting severity label:', error);
      return 'Message';
    }
  };

  /**
   * Get dialog icon for severity
   */
  const getDialogIcon = (severity: string): string => {
    try {
      const icons: Record<string, string> = {
        'info': 'pi pi-info-circle',
        'success': 'pi pi-check-circle',
        'warn': 'pi pi-exclamation-triangle',
        'error': 'pi pi-times-circle'
      };

      return icons[severity] || 'pi pi-info-circle';
    } catch (error) {
      console.error('Error getting dialog icon:', error);
      return 'pi pi-info-circle';
    }
  };

  /**
   * Map position to toast position
   */
  const mapToastPosition = (position: string): string => {
    try {
      const mapping: Record<string, string> = {
        'top': 'top-center',
        'bottom': 'bottom-center',
        'left': 'center-left',
        'right': 'center-right'
      };

      return mapping[position] || 'top-center';
    } catch (error) {
      console.error('Error mapping toast position:', error);
      return 'top-center';
    }
  };

  /**
   * Emit error event for parent components
   */
  const emitErrorEvent = (eventType: string, data: any): void => {
    try {
      const event = new CustomEvent(eventType, {
        detail: data
      });
      window.dispatchEvent(event);
    } catch (error) {
      console.error('Error emitting error event:', error);
    }
  };

  /**
   * Update error configuration
   */
  const updateConfig = (newConfig: Partial<ErrorConfig>): void => {
    try {
      Object.assign(errorConfig, newConfig);
    } catch (error) {
      console.error('Error updating error configuration:', error);
    }
  };

  /**
   * Cleanup resources
   */
  const cleanup = (): void => {
    try {
      // Clear all auto-close timers
      autoCloseTimers.value.forEach(timer => window.clearTimeout(timer));
      autoCloseTimers.value.clear();

      // Clear all errors
      errors.value.clear();
    } catch (error) {
      console.error('Error during error display cleanup:', error);
    }
  };

  // Computed properties
  const hasErrors = computed(() => errors.value.size > 0);
  const errorCount = computed(() => errors.value.size);
  const hasFieldErrors = (field: string) => 
    computed(() => Array.from(errors.value.values()).some(error => error.field === field));
  const getFieldErrors = (field: string) => 
    computed(() => Array.from(errors.value.values()).filter(error => error.field === field));
  const getAllErrors = computed(() => Array.from(errors.value.values()));
  const getInlineErrors = computed(() => 
    Array.from(errors.value.values()).filter(error => error.displayType === 'inline')
  );

  // Error message for single field
  const errorMessage = computed(() => {
    const allErrors = Array.from(errors.value.values());
    const latestError = allErrors
      .filter(error => !error.field || error.displayType === 'inline')
      .sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime())[0];
    
    return latestError?.message || '';
  });

  // Error type for styling
  const errorType = computed(() => {
    const allErrors = Array.from(errors.value.values());
    const latestError = allErrors
      .filter(error => !error.field || error.displayType === 'inline')
      .sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime())[0];
    
    return latestError?.displayType || 'inline';
  });

  // Lifecycle hooks
  onUnmounted(() => {
    cleanup();
  });

  return {
    // Core methods
    showError,
    dismissError,
    clearErrors,
    processServerError,
    
    // Configuration
    updateConfig,
    config: readonly(errorConfig),
    
    // State
    hasErrors,
    errorCount,
    errorMessage,
    errorType,
    
    // Field-specific helpers
    hasFieldErrors,
    getFieldErrors,
    
    // All errors
    getAllErrors,
    getInlineErrors,
    
    // Cleanup
    cleanup
  };
}