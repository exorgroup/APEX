/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Vue composable for handling PRO widget events with parameter injection and server communication
 * File location: resources/js/components/apex/pro/Widget/PrimeVueBaseWidget/composables/useEventHandling.ts
 */

import { onMounted, onUnmounted } from 'vue';

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
     * Server event handler
     */
    const handleServerEvent = async (serverCommand: string, event: Event) => {
        try {
            console.log('Executing Server event:', serverCommand);
            
            // Simple regex parsing
            const match = serverCommand.match(/^(.+?)\/(\w+)\((.+)\)$/);
            if (!match) {
                console.error('Invalid server command format:', serverCommand);
                return;
            }
            
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
                
                // Call handler function if it exists globally
                if (handler && window[handler as keyof Window]) {
                    (window[handler as keyof Window] as Function)(result, event);
                }
            } else {
                console.error('Server request failed:', response.status);
            }
        } catch (error) {
            console.error('Error executing server event:', error);
        }
    };

    /**
     * Vue event handler
     */
    const handleVueEvent = (vueCommand: string, event: Event) => {
        try {
            console.log('Executing Vue event:', vueCommand);
            
            // Parse: "methodName(param1, param2)"
            const [method, paramsStr] = vueCommand.split('(');
            const params = paramsStr ? paramsStr.replace(')', '').split(',').map(p => p.trim()) : [];
            
            // For now, just log - this would need to be customized per widget
            console.log('Vue event would emit:', {
                method: method,
                params: params,
                event: event,
                value: (event.target as HTMLInputElement)?.value || '',
                widgetId: widgetId
            });
        } catch (error) {
            console.error('Error executing Vue event:', error);
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
    });

    return {
        handleJSEvent,
        handleServerEvent,
        handleVueEvent,
        setupGenericEventHandler,
        cleanupEventHandlers
    };
}