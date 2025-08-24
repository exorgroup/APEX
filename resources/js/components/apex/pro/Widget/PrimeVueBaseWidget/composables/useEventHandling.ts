/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Vue composable for handling PRO widget events with parameter injection and server communication
 * File location: resources/js/components/apex/pro/Widget/PrimeVueBaseWidget/composables/useEventHandling.ts
 */

import { onMounted, onUnmounted, getCurrentInstance } from 'vue';

export interface EventConfig {
    [eventName: string]: any;
}

export interface UseEventHandlingOptions {
    widgetId: string;
    events?: EventConfig;
    isProLicensed: boolean;
}

export function useEventHandling(options: UseEventHandlingOptions) {
    const { widgetId, events, isProLicensed } = options;

    // Common events that we'll listen for
    const commonEvents = [
        'blur', 'focus', 'click', 'dblclick', 'mouseover', 'mouseout',
        'mousedown', 'mouseup', 'keydown', 'keyup', 'keypress',
        'change', 'input'
    ];

    // Debouncing state
    const debounceTimers = new Map<string, number>();

    /**
     * Generic event processor
     */
    const handleGenericEvent = (event: Event) => {
        try {
            const target = event.target as HTMLElement;
            
            // Only process events for THIS widget's input element
            if (!target.id.startsWith(widgetId + '-')) {
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

    /**
     * JavaScript event handler
     */
    const handleJSEvent = (jsCode: string, event: Event) => {
        try {
            console.log('Executing JS event:', jsCode);
            eval(jsCode);
        } catch (error) {
            console.error('Error executing JS event:', error);
        }
    };

    /**
     * Server event handler with debouncing
     */
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
                const eventKey = `${widgetId}-${event.type}`;
                
                // Clear existing timer
                if (debounceTimers.has(eventKey)) {
                    clearTimeout(debounceTimers.get(eventKey));
                }
                
                // Set new timer
                const timerId = window.setTimeout(() => {
                    executeServerCall(commandPart, responsePart, event);
                    debounceTimers.delete(eventKey);
                }, debounceMs);
                
                debounceTimers.set(eventKey, timerId);
                return;
            }
            
            // Execute immediately if no debounce
            await executeServerCall(commandPart, responsePart, event);
        } catch (error) {
            console.error('Error executing server event:', error);
        }
    };

    /**
     * Extract server call execution
     */
    const executeServerCall = async (commandPart: string, responsePart: string, event: Event) => {
        try {
            const match = commandPart.match(/^(.+?)\/(\w+)\((.+)\)$/);
            if (!match) return;
            
            const [, endpoint, handler, fullParamsStr] = match;
            
            // Use regex to find function calls and quoted strings
            const paramRegex = /(?:document\.getElementById\('[^']+'\)\.value|'[^']*'|\w+)/g;
            const paramsList = fullParamsStr.match(paramRegex) || [];
            
            console.log('Parsed parameters:', paramsList);
            
            let evaluatedParams: any[] = [];
            for (const param of paramsList) {
                try {
                    const trimmedParam = param.trim();
                    console.log(`Processing parameter: "${trimmedParam}"`);
                    
                    if (trimmedParam.includes('document.getElementById')) {
                        const evaluatedValue = eval(trimmedParam);
                        evaluatedParams.push(evaluatedValue);
                        console.log(`✓ Evaluated "${trimmedParam}" = "${evaluatedValue}"`);
                    } else if (trimmedParam.startsWith("'") && trimmedParam.endsWith("'")) {
                        const stringValue = trimmedParam.slice(1, -1);
                        evaluatedParams.push(stringValue);
                        console.log(`✓ String literal "${trimmedParam}" = "${stringValue}"`);
                    } else {
                        evaluatedParams.push(trimmedParam);
                        console.log(`✓ Literal value "${trimmedParam}"`);
                    }
                } catch (evalError) {
                    console.error(`✗ Could not evaluate parameter "${param}":`, evalError);
                    evaluatedParams.push(null);
                }
            }
            
            const url = endpoint.startsWith('/') ? endpoint : '/' + endpoint;
            
            console.log('Final evaluated parameters:', evaluatedParams);
            
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
                    widgetId: widgetId
                })
            });
            
            if (response.ok) {
                const result = await response.json();
                console.log('Server response:', result);
                
                // Process response if responseHandler is available
                if (responsePart && window.responseHandler?.processServerResponse) {
                    const responseConfig = window.responseHandler.decodeResponseConfig(responsePart);
                    if (responseConfig) {
                        await window.responseHandler.processServerResponse(result, responseConfig, event);
                    }
                }
                
                // Call handler function if it exists globally
                if (handler && window[handler as keyof Window]) {
                    (window[handler as keyof Window] as Function)(result, event);
                }
            } else {
                console.error('Server request failed:', response.status);
            }
        } catch (error) {
            console.error('Error in server call execution:', error);
        }
    };

    /**
     * Vue event handler - executes Vue methods directly like handleJSEvent
     */
    const handleVueEvent = (vueCommand: string, event: Event) => {
    try {
        console.log('Executing Vue event:', vueCommand);
        
        // Parse method name and parameters string
        const parenIndex = vueCommand.indexOf('(');
        if (parenIndex === -1) {
            console.error('No parameters found in Vue command');
            return;
        }
        
        const method = vueCommand.substring(0, parenIndex);
        const fullParamsStr = vueCommand.substring(parenIndex + 1, vueCommand.lastIndexOf(')'));
        
        console.log('Method:', method);
        console.log('Parameters string:', fullParamsStr);
        
        // Split parameters by comma, but handle function calls properly
        const params: string[] = [];
        let currentParam = '';
        let parenCount = 0;
        let inQuotes = false;
        let quoteChar = '';
        
        for (let i = 0; i < fullParamsStr.length; i++) {
            const char = fullParamsStr[i];
            
            if (!inQuotes && (char === '"' || char === "'")) {
                inQuotes = true;
                quoteChar = char;
                currentParam += char;
            } else if (inQuotes && char === quoteChar) {
                inQuotes = false;
                quoteChar = '';
                currentParam += char;
            } else if (!inQuotes && char === '(') {
                parenCount++;
                currentParam += char;
            } else if (!inQuotes && char === ')') {
                parenCount--;
                currentParam += char;
            } else if (!inQuotes && char === ',' && parenCount === 0) {
                params.push(currentParam.trim());
                currentParam = '';
            } else {
                currentParam += char;
            }
        }
        
        if (currentParam.trim()) {
            params.push(currentParam.trim());
        }
        
        console.log('Split parameters:', params);
        
        // Evaluate each parameter
        const evaluatedParams: any[] = [];
        for (const param of params) {
            try {
                if (param.includes('document.getElementById')) {
                    const value = eval(param);
                    evaluatedParams.push(value);
                    console.log(`Evaluated ${param} = ${value}`);
                } else if ((param.startsWith("'") && param.endsWith("'")) || 
                          (param.startsWith('"') && param.endsWith('"'))) {
                    const stringValue = param.slice(1, -1);
                    evaluatedParams.push(stringValue);
                    console.log(`String literal ${param} = ${stringValue}`);
                } else {
                    evaluatedParams.push(param);
                    console.log(`Literal ${param}`);
                }
            } catch (evalError) {
                console.error(`Failed to evaluate parameter ${param}:`, evalError);
                evaluatedParams.push(param);
            }
        }
        
        console.log('Final evaluated parameters:', evaluatedParams);
        
        // Execute the method
        if (window[method as keyof Window] && typeof window[method as keyof Window] === 'function') {
            console.log(`Executing ${method} with:`, evaluatedParams);
            (window[method as keyof Window] as Function)(...evaluatedParams);
        } else {
            console.warn(`Method ${method} not found on window`);
        }
        
    } catch (error) {
        console.error('Error in Vue event handler:', error);
    }
};

    /**
     * Setup generic event handler
     */
    const setupGenericEventHandler = () => {
        try {
            if (!isProLicensed) return;

            commonEvents.forEach(eventType => {
                document.addEventListener(eventType, handleGenericEvent, true);
            });
        } catch (error) {
            console.error('Error setting up generic event handler:', error);
        }
    };

    /**
     * Cleanup event handlers
     */
    const cleanupEventHandlers = () => {
        try {
            commonEvents.forEach(eventType => {
                document.removeEventListener(eventType, handleGenericEvent, true);
            });
        } catch (error) {
            console.error('Error cleaning up event handlers:', error);
        }
    };

    // Lifecycle management
    onMounted(() => {
        setupGenericEventHandler();
    });

    onUnmounted(() => {
        cleanupEventHandlers();
        
        // Clear any pending debounce timers
        debounceTimers.forEach(timerId => {
            clearTimeout(timerId);
        });
        debounceTimers.clear();
    });

    return {
        handleJSEvent,
        handleServerEvent,
        handleVueEvent,
        setupGenericEventHandler,
        cleanupEventHandlers
    };
}