/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Event handling composable for PRO widgets providing Vue event binding, server communication, debouncing and async handler execution
 * File location: resources/js/components/apex/pro/widget/PrimeVueBaseWidget/composables/useEventHandling.ts
 */

import { ref, computed, onMounted, onUnmounted, type Ref } from 'vue';
import { useServerCommunication } from './useServerCommunication';
import { useErrorDisplay } from './useErrorDisplay';
import { useParameterInjection } from './useParameterInjection';

export interface EventHandler {
  type: 'single' | 'multiple' | 'advanced';
  handler?: string;
  server?: string;
  debounce?: number;
  throttle?: number;
  async?: boolean;
  params?: Record<string, any>;
  errorHandling?: 'default' | 'inline' | 'toast' | 'dialog';
  handlers?: EventHandler[];
  execution?: 'parallel' | 'sequential';
}

export interface EventConfig {
  [eventName: string]: EventHandler;
}

export interface EventContext {
  event: Event;
  widgetId: string;
  value: any;
  formData: Record<string, any>;
  widgetData: Record<string, any>;
}

export function useEventHandling(
  widgetId: string,
  eventConfig: Ref<EventConfig>,
  formContext?: Ref<Record<string, any>>
) {
  const { callServer } = useServerCommunication();
  const { showError, clearErrors } = useErrorDisplay();
  const { resolveParameters } = useParameterInjection();

  const registeredHandlers = ref<Map<string, EventHandler>>(new Map());
  const debounceTimers = ref<Map<string, number>>(new Map());
  const throttleTimers = ref<Map<string, boolean>>(new Map());

  const loadingEvents = ref<Set<string>>(new Set());

  /**
   * Register event handlers from configuration
   */
  const registerEvents = () => {
    try {
      if (!eventConfig.value) return;

      Object.entries(eventConfig.value).forEach(([eventName, handler]) => {
        registeredHandlers.value.set(eventName, handler);
      });
    } catch (error) {
      console.error('Error registering events:', error);
      showError('Failed to register event handlers', 'toast');
    }
  };

  const executeHandler = async (
    eventName: string,
    event: Event,
    value?: any
  ): Promise<void> => {
    try {
      const handler = registeredHandlers.value.get(eventName);
      if (!handler) return;

      const context: EventContext = {
        event,
        widgetId,
        value: value !== undefined ? value : (event.target as any)?.value,
        formData: formContext?.value || {},
        widgetData: {}
      };

      switch (handler.type) {
        case 'single':
          await executeSingleHandler(handler, context);
          break;
        case 'multiple':
          await executeMultipleHandlers(handler, context);
          break;
        case 'advanced':
          await executeAdvancedHandler(handler, context);
          break;
      }
    } catch (error: any) {
      const handler = registeredHandlers.value.get(eventName);
      const errorType = handler?.errorHandling === 'inline' || handler?.errorHandling === 'dialog' 
        ? handler.errorHandling 
        : 'toast';
      
      console.error(`Error executing ${eventName} handler:`, error);
      showError(
        `Error executing ${eventName} handler: ${error?.message || 'Unknown error'}`,
        errorType
      );
    }
  };

  /**
   * Execute single event handler
   */
  const executeSingleHandler = async (
    handler: EventHandler,
    context: EventContext
  ): Promise<void> => {
    try {
      if (handler.handler) {
        // Execute Vue method
        await executeVueMethod(handler.handler, context);
      }

      if (handler.server) {
        // Execute server method
        await executeServerMethod(handler, context);
      }
    } catch (error: any) {
      throw new Error(`Single handler execution failed: ${error?.message || 'Unknown error'}`);
    }
  };

  /**
   * Execute multiple event handlers
   */
  const executeMultipleHandlers = async (
    handler: EventHandler,
    context: EventContext
  ): Promise<void> => {
    try {
      if (!handler.handlers) return;

      if (handler.execution === 'sequential') {
        // Execute handlers one by one
        for (const subHandler of handler.handlers) {
          await executeSingleHandler(subHandler, context);
        }
      } else {
        // Execute handlers in parallel (default)
        const promises = handler.handlers.map(subHandler =>
          executeSingleHandler(subHandler, context)
        );
        await Promise.all(promises);
      }
    } catch (error: any) {
      throw new Error(`Multiple handler execution failed: ${error?.message || 'Unknown error'}`);
    }
  };

  const executeAdvancedHandler = async (
    handler: EventHandler,
    context: EventContext
  ): Promise<void> => {
    try {
      clearErrors();

      if (handler.handler) {
        await executeVueMethod(handler.handler, context);
      }

      if (handler.server) {
        loadingEvents.value.add(context.widgetId);
        try {
          await executeServerMethod(handler, context);
        } finally {
          loadingEvents.value.delete(context.widgetId);
        }
      }
    } catch (error: any) {
      loadingEvents.value.delete(context.widgetId);
      throw new Error(`Advanced handler execution failed: ${error?.message || 'Unknown error'}`);
    }
  };

  const executeVueMethod = async (
    methodName: string,
    context: EventContext
  ): Promise<void> => {
    try {
      // Check if method exists on window (global function)
      if (typeof window[methodName] === 'function') {
        await window[methodName](context);
        return;
      }

      console.warn(`Vue method '${methodName}' not found`);
    } catch (error: any) {
      throw new Error(`Vue method execution failed: ${error?.message || 'Unknown error'}`);
    }
  };

  const executeServerMethod = async (
    handler: EventHandler,
    context: EventContext
  ): Promise<void> => {
    try {
      if (!handler.server) return;

      const [controller, method] = handler.server.replace('@', '').split('@');
      
      let params = handler.params || {};
      if (Object.keys(params).length > 0) {
        const parameterContext = {
          widget: context.widgetData,
          form: context.formData,
          user: {},
          static: {},
          config: {},
          route: {}
        };
        params = await resolveParameters(params, parameterContext);
      }

      const requestData = {
        ...params,
        widgetId: context.widgetId,
        value: context.value,
        formData: context.formData,
        event: {
          type: context.event.type,
          target: {
            name: (context.event.target as any)?.name,
            id: (context.event.target as any)?.id
          }
        }
      };

      const response = await callServer(
        `/${controller.toLowerCase()}/${method.toLowerCase()}`,
        requestData
      );

      await handleServerResponse(response, handler, context);
    } catch (error: any) {
      throw new Error(`Server method execution failed: ${error?.message || 'Unknown error'}`);
    }
  };

  const handleServerResponse = async (
    response: any,
    handler: EventHandler,
    context: EventContext
  ): Promise<void> => {
    try {
      if (!response.success) {
        const errorMessage = response.message || 'Server request failed';
        let errorType: 'inline' | 'toast' | 'dialog' = 'toast';
        
        if (handler.errorHandling === 'inline' || handler.errorHandling === 'dialog') {
          errorType = handler.errorHandling;
        }
        
        showError(errorMessage, errorType, response.field);
        return;
      }

      if (response.message) {
        showError(response.message, 'toast', undefined, 'success');
      }

      if (response.data) {
        const event = new CustomEvent('server-response', {
          detail: {
            widgetId: context.widgetId,
            data: response.data,
            action: response.action || 'update'
          }
        });
        window.dispatchEvent(event);
      }

      if (response.actions && Array.isArray(response.actions)) {
        for (const action of response.actions) {
          await executeAction(action, context);
        }
      }
    } catch (error: any) {
      throw new Error(`Server response handling failed: ${error?.message || 'Unknown error'}`);
    }
  };

  /**
   * Execute follow-up action from server response
   */
  const executeAction = async (
    action: any,
    context: EventContext
  ): Promise<void> => {
    try {
      switch (action.type) {
        case 'redirect':
          if (action.url) {
            window.location.href = action.url;
          }
          break;
        case 'refresh':
          window.location.reload();
          break;
        case 'focus':
          if (action.target) {
            const element = document.getElementById(action.target);
            element?.focus();
          }
          break;
        case 'clear':
          if (action.target) {
            const element = document.getElementById(action.target) as HTMLInputElement;
            if (element) element.value = '';
          }
          break;
        case 'disable':
          if (action.target) {
            const element = document.getElementById(action.target) as HTMLInputElement;
            if (element) element.disabled = true;
          }
          break;
        case 'enable':
          if (action.target) {
            const element = document.getElementById(action.target) as HTMLInputElement;
            if (element) element.disabled = false;
          }
          break;
        default:
          console.warn(`Unknown action type: ${action.type}`);
      }
    } catch (error) {
      console.error(`Error executing action ${action.type}:`, error);
    }
  };

  // Create debounced handler
  const createDebouncedHandler = (
    eventName: string,
    handler: EventHandler,
    originalHandler: (event: Event, value?: any) => void
  ): (event: Event, value?: any) => void => {
    return (event: Event, value?: any) => {
      try {
        const debounceTime = handler.debounce || 0;
        
        if (debounceTime > 0) {
          const existingTimer = debounceTimers.value.get(eventName);
          if (existingTimer) {
            window.clearTimeout(existingTimer);
          }

          const timer = window.setTimeout(() => {
            originalHandler(event, value);
            debounceTimers.value.delete(eventName);
          }, debounceTime);

          debounceTimers.value.set(eventName, timer);
        } else {
          originalHandler(event, value);
        }
      } catch (error) {
        console.error(`Error in debounced handler for ${eventName}:`, error);
      }
    };
  };

  // Create throttled handler
  const createThrottledHandler = (
    eventName: string,
    handler: EventHandler,
    originalHandler: (event: Event, value?: any) => void
  ): (event: Event, value?: any) => void => {
    return (event: Event, value?: any) => {
      try {
        const throttleTime = handler.throttle || 0;
        
        if (throttleTime > 0) {
          const isThrottled = throttleTimers.value.get(eventName);
          
          if (!isThrottled) {
            originalHandler(event, value);
            throttleTimers.value.set(eventName, true);
            
            setTimeout(() => {
              throttleTimers.value.set(eventName, false);
            }, throttleTime);
          }
        } else {
          originalHandler(event, value);
        }
      } catch (error) {
        console.error(`Error in throttled handler for ${eventName}:`, error);
      }
    };
  };

  // Get event handler for component binding
  const getEventHandler = (eventName: string) => {
    const handler = registeredHandlers.value.get(eventName);
    if (!handler) return null;

    let eventHandler: (event: Event, value?: any) => void = (event: Event, value?: any) => {
      executeHandler(eventName, event, value);
    };

    // Apply debouncing if configured
    if (handler.debounce && handler.debounce > 0) {
      eventHandler = createDebouncedHandler(eventName, handler, eventHandler);
    }

    // Apply throttling if configured
    if (handler.throttle && handler.throttle > 0) {
      eventHandler = createThrottledHandler(eventName, handler, eventHandler);
    }

    return eventHandler;
  };

  /**
   * Get all event handlers for component binding
   */
  const getEventHandlers = () => {
    const handlers: Record<string, Function> = {};
    
    registeredHandlers.value.forEach((handler, eventName) => {
      const eventHandler = getEventHandler(eventName);
      if (eventHandler) {
        handlers[eventName] = eventHandler;
      }
    });

    return handlers;
  };

  /**
   * Check if any events are currently loading
   */
  const isLoading = computed(() => loadingEvents.value.size > 0);

  /**
   * Check if specific event is loading
   */
  const isEventLoading = (eventName: string) => {
    return loadingEvents.value.has(eventName);
  };

  const cleanup = () => {
    try {
      debounceTimers.value.forEach(timer => window.clearTimeout(timer));
      debounceTimers.value.clear();
      throttleTimers.value.clear();
      loadingEvents.value.clear();
      registeredHandlers.value.clear();
    } catch (error) {
      console.error('Error during event handling cleanup:', error);
    }
  };

  onMounted(() => {
    registerEvents();
  });

  onUnmounted(() => {
    cleanup();
  });

  return {
    registerEvents,
    executeHandler,
    getEventHandler,
    getEventHandlers,
    cleanup,
    isLoading,
    isEventLoading,
    loadingEvents: computed(() => Array.from(loadingEvents.value)),
    registeredHandlers: computed(() => 
      Object.fromEntries(registeredHandlers.value)
    )
  };
}