/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Vue composable for handling server response processing with multi-state UI feedback
 * File location: resources/js/components/apex/pro/Widget/PrimeVueBaseWidget/composables/useResponseHandling.ts
 */

import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';

export interface ResponseConfig {
    success?: StateConfig;
    info?: StateConfig;
    warn?: StateConfig;
    error?: StateConfig;
}

export interface StateConfig {
    type: 'server' | 'alert' | 'toast' | 'modal';
    server?: string;
    params?: string[];
    severity?: 'success' | 'info' | 'warn' | 'error' | 'secondary' | 'contrast';
    position?: 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
    life?: number;
    closable?: boolean;
    title?: string;
    buttonText?: string;
    buttonSeverity?: 'success' | 'info' | 'warn' | 'error' | 'secondary' | 'contrast';
    image?: string;
    width?: string;
    modal?: boolean;
}

export interface ServerResponse {
    success: boolean;
    state: 'success' | 'info' | 'warn' | 'error';
    message: string;
    data?: any;
}

export function useResponseHandling() {
    const toast = useToast();
    const modalVisible = ref(false);
    const modalConfig = ref<StateConfig | null>(null);
    const modalMessage = ref('');

    /**
     * Process server response based on configuration
     */
    const processServerResponse = async (
        serverResponse: ServerResponse,
        responseConfig: ResponseConfig,
        originalEvent: Event
    ) => {
        try {
            const state = serverResponse.state || (serverResponse.success ? 'success' : 'error');
            const stateConfig = responseConfig[state];

            if (!stateConfig) {
                console.warn(`No response configuration found for state: ${state}`);
                return;
            }

            const message = serverResponse.message || 'Operation completed';

            switch (stateConfig.type) {
                case 'alert':
                    alert(message);
                    break;

                case 'toast':
                    showToast(message, stateConfig);
                    break;

                case 'modal':
                    showModal(message, stateConfig);
                    break;

                case 'server':
                    await handleServerResponse(message, stateConfig, serverResponse, originalEvent);
                    break;

                default:
                    console.warn(`Unknown response type: ${stateConfig.type}`);
                    alert(message);
            }
        } catch (error) {
            console.error('Error processing server response:', error);
            alert('An error occurred while processing the response');
        }
    };

    /**
     * Show toast notification
     */
    const showToast = (message: string, config: StateConfig) => {
        try {
            toast.add({
                severity: config.severity || 'info',
                summary: config.severity ? config.severity.charAt(0).toUpperCase() + config.severity.slice(1) : 'Info',
                detail: message,
                life: config.life || 3000,
                group: 'apex-widget-toast'
            });
        } catch (error) {
            console.error('Error showing toast:', error);
            alert(message);
        }
    };

    /**
     * Show modal dialog
     */
    const showModal = (message: string, config: StateConfig) => {
        try {
            modalMessage.value = message;
            modalConfig.value = config;
            modalVisible.value = true;
        } catch (error) {
            console.error('Error showing modal:', error);
            alert(message);
        }
    };

    /**
     * Handle server response type
     */
    const handleServerResponse = async (
        message: string,
        config: StateConfig,
        serverResponse: ServerResponse,
        originalEvent: Event
    ) => {
        try {
            if (!config.server) {
                console.error('Server URL not configured for server response type');
                return;
            }

            // Evaluate parameters
            const evaluatedParams: any[] = [];
            if (config.params) {
                for (const param of config.params) {
                    try {
                        if (param.includes('document.getElementById')) {
                            evaluatedParams.push(eval(param));
                        } else if (param.startsWith("'") && param.endsWith("'")) {
                            evaluatedParams.push(param.slice(1, -1));
                        } else {
                            evaluatedParams.push(param);
                        }
                    } catch (evalError) {
                        console.warn(`Could not evaluate parameter "${param}":`, evalError);
                        evaluatedParams.push(param);
                    }
                }
            }

            // Make secondary server request
            const response = await fetch(config.server, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    originalResponse: serverResponse,
                    message: message,
                    params: evaluatedParams,
                    originalEvent: originalEvent.type
                })
            });

            if (response.ok) {
                const result = await response.json();
                console.log('Secondary server response:', result);
            } else {
                console.error('Secondary server request failed:', response.status);
            }
        } catch (error) {
            console.error('Error handling server response:', error);
        }
    };

    /**
     * Close modal
     */
    const closeModal = () => {
        try {
            modalVisible.value = false;
            modalConfig.value = null;
            modalMessage.value = '';
        } catch (error) {
            console.error('Error closing modal:', error);
        }
    };

    /**
     * Decode response configuration from base64 encoded string
     */
    const decodeResponseConfig = (encodedConfig: string): ResponseConfig | null => {
        try {
            const decoded = atob(encodedConfig);
            return JSON.parse(decoded);
        } catch (error) {
            console.error('Error decoding response config:', error);
            return null;
        }
    };

    return {
        processServerResponse,
        showToast,
        showModal,
        closeModal,
        decodeResponseConfig,
        modalVisible,
        modalConfig,
        modalMessage
    };
}