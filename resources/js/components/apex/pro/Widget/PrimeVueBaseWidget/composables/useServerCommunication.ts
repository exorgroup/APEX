/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Server communication composable for PRO widgets providing AJAX calls with CSRF handling, retry logic, timeout management and response processing
 * File location: resources/js/components/apex/pro/Widget/PrimeVueBaseWidget/composables/useServerCommunication.ts
 */

import { ref, computed } from 'vue';

export interface ServerConfig {
  timeout?: number;
  retries?: number;
  baseUrl?: string;
  headers?: Record<string, string>;
}

export interface ServerResponse {
  success: boolean;
  message?: string;
  data?: any;
  field?: string;
  code?: string;
  actions?: any[];
  errors?: Record<string, string>;
}

export interface RequestOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
  timeout?: number;
  retries?: number;
  headers?: Record<string, string>;
  includeCsrf?: boolean;
}

export function useServerCommunication(config: ServerConfig = {}) {
  // Configuration with defaults
  const serverConfig = ref<ServerConfig>({
    timeout: 5000,
    retries: 3,
    baseUrl: '',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    ...config
  });

  // Loading state tracking
  const activeRequests = ref<Set<string>>(new Set());
  const requestCount = ref(0);

  /**
   * Get CSRF token from meta tag or form input
   */
  const getCsrfToken = (): string | null => {
    try {
      // Try meta tag first (Laravel default)
      const metaTag = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
      if (metaTag) {
        return metaTag.content;
      }

      // Try hidden form input
      const hiddenInput = document.querySelector('input[name="_token"]') as HTMLInputElement;
      if (hiddenInput) {
        return hiddenInput.value;
      }

      // Try window global (if set by application)
      if ((window as any).csrfToken) {
        return (window as any).csrfToken;
      }

      console.warn('CSRF token not found');
      return null;
    } catch (error) {
      console.error('Error getting CSRF token:', error);
      return null;
    }
  };

  /**
   * Generate unique request ID for tracking
   */
  const generateRequestId = (): string => {
    try {
      requestCount.value++;
      return `req_${Date.now()}_${requestCount.value}`;
    } catch (error) {
      console.error('Error generating request ID:', error);
      return `req_${Date.now()}`;
    }
  };

  /**
   * Create abort controller with timeout
   */
  const createAbortController = (timeoutMs: number): AbortController => {
    try {
      const controller = new AbortController();
      
      const timeoutId = setTimeout(() => {
        controller.abort();
      }, timeoutMs);

      // Clear timeout if request completes normally
      controller.signal.addEventListener('abort', () => {
        clearTimeout(timeoutId);
      });

      return controller;
    } catch (error) {
      console.error('Error creating abort controller:', error);
      return new AbortController();
    }
  };

  /**
   * Make HTTP request with retry logic
   */
  const makeRequest = async (
    url: string,
    data?: any,
    options: RequestOptions = {}
  ): Promise<ServerResponse> => {
    const requestId = generateRequestId();
    
    try {
      activeRequests.value.add(requestId);

      const requestOptions: RequestOptions = {
        method: 'POST',
        timeout: serverConfig.value.timeout,
        retries: serverConfig.value.retries,
        headers: { ...serverConfig.value.headers },
        includeCsrf: true,
        ...options
      };

      let lastError: Error | null = null;
      
      // Retry loop
      for (let attempt = 0; attempt <= requestOptions.retries!; attempt++) {
        try {
          const response = await executeRequest(url, data, requestOptions);
          return response;
        } catch (error: any) {
          lastError = error as Error;
          
          // Don't retry on certain errors
          if (error instanceof TypeError || error?.name === 'AbortError') {
            throw error;
          }
          
          // Wait before retry (exponential backoff)
          if (attempt < requestOptions.retries!) {
            const delay = Math.pow(2, attempt) * 1000; // 1s, 2s, 4s, etc.
            await new Promise(resolve => setTimeout(resolve, delay));
          }
        }
      }

      throw lastError || new Error('Request failed after retries');
    } finally {
      activeRequests.value.delete(requestId);
    }
  };

  /**
   * Execute single HTTP request
   */
  const executeRequest = async (
    url: string,
    data?: any,
    options: RequestOptions = {}
  ): Promise<ServerResponse> => {
    try {
      const fullUrl = serverConfig.value.baseUrl + url;
      const timeout = options.timeout || serverConfig.value.timeout!;
      const controller = createAbortController(timeout);

      // Prepare headers
      const headers = {
        ...serverConfig.value.headers,
        ...options.headers
      };

      // Add CSRF token if required
      if (options.includeCsrf !== false) {
        const csrfToken = getCsrfToken();
        if (csrfToken) {
          headers['X-CSRF-TOKEN'] = csrfToken;
        }
      }

      // Prepare request configuration
      const fetchConfig: RequestInit = {
        method: options.method || 'POST',
        headers,
        signal: controller.signal
      };

      // Add body for non-GET requests
      if (data && options.method !== 'GET') {
        fetchConfig.body = JSON.stringify(data);
      }

      // Add query parameters for GET requests
      let finalUrl = fullUrl;
      if (data && options.method === 'GET') {
        const params = new URLSearchParams();
        Object.entries(data).forEach(([key, value]) => {
          if (value !== null && value !== undefined) {
            params.append(key, String(value));
          }
        });
        const queryString = params.toString();
        if (queryString) {
          finalUrl += (finalUrl.includes('?') ? '&' : '?') + queryString;
        }
      }

      // Execute request
      const response = await fetch(finalUrl, fetchConfig);

      // Check if response is ok
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      // Parse response
      const responseData = await response.json();

      // Validate response structure
      const serverResponse: ServerResponse = {
        success: responseData.success ?? true,
        message: responseData.message,
        data: responseData.data,
        field: responseData.field,
        code: responseData.code,
        actions: responseData.actions,
        errors: responseData.errors
      };

      return serverResponse;
    } catch (error: any) {
      if (error?.name === 'AbortError') {
        throw new Error('Request timeout');
      }
      throw error;
    }
  };

  /**
   * Call server endpoint (main public method)
   */
  const callServer = async (
    endpoint: string,
    data?: any,
    options: RequestOptions = {}
  ): Promise<ServerResponse> => {
    try {
      // Ensure endpoint starts with /
      const url = endpoint.startsWith('/') ? endpoint : `/${endpoint}`;
      
      return await makeRequest(url, data, options);
    } catch (error) {
      console.error(`Server call failed for ${endpoint}:`, error);
      
      // Return standardized error response
      return {
        success: false,
        message: (error as Error)?.message || 'Server communication failed',
        code: 'SERVER_ERROR'
      };
    }
  };

  /**
   * GET request helper
   */
  const get = async (
    endpoint: string,
    params?: any,
    options: Omit<RequestOptions, 'method'> = {}
  ): Promise<ServerResponse> => {
    try {
      return await callServer(endpoint, params, { ...options, method: 'GET' });
    } catch (error: any) {
      console.error(`GET request failed for ${endpoint}:`, error);
      return {
        success: false,
        message: (error as Error)?.message || 'GET request failed',
        code: 'GET_ERROR'
      };
    }
  };

  /**
   * POST request helper
   */
  const post = async (
    endpoint: string,
    data?: any,
    options: Omit<RequestOptions, 'method'> = {}
  ): Promise<ServerResponse> => {
    try {
      return await callServer(endpoint, data, { ...options, method: 'POST' });
    } catch (error: any) {
      console.error(`POST request failed for ${endpoint}:`, error);
      return {
        success: false,
        message: (error as Error)?.message || 'POST request failed',
        code: 'POST_ERROR'
      };
    }
  };

  /**
   * PUT request helper
   */
  const put = async (
    endpoint: string,
    data?: any,
    options: Omit<RequestOptions, 'method'> = {}
  ): Promise<ServerResponse> => {
    try {
      return await callServer(endpoint, data, { ...options, method: 'PUT' });
    } catch (error: any) {
      console.error(`PUT request failed for ${endpoint}:`, error);
      return {
        success: false,
        message: (error as Error)?.message || 'PUT request failed',
        code: 'PUT_ERROR'
      };
    }
  };

  /**
   * DELETE request helper
   */
  const del = async (
    endpoint: string,
    data?: any,
    options: Omit<RequestOptions, 'method'> = {}
  ): Promise<ServerResponse> => {
    try {
      return await callServer(endpoint, data, { ...options, method: 'DELETE' });
    } catch (error: any) {
      console.error(`DELETE request failed for ${endpoint}:`, error);
      return {
        success: false,
        message: (error as Error)?.message || 'DELETE request failed',
        code: 'DELETE_ERROR'
      };
    }
  };

  /**
   * Cancel all active requests
   */
  const cancelAllRequests = (): void => {
    try {
      // Note: Individual request cancellation is handled by AbortController
      // This method is for cleanup
      activeRequests.value.clear();
    } catch (error) {
      console.error('Error canceling requests:', error);
    }
  };

  /**
   * Update server configuration
   */
  const updateConfig = (newConfig: Partial<ServerConfig>): void => {
    try {
      serverConfig.value = {
        ...serverConfig.value,
        ...newConfig,
        headers: {
          ...serverConfig.value.headers,
          ...newConfig.headers
        }
      };
    } catch (error) {
      console.error('Error updating server configuration:', error);
    }
  };

  // Computed properties
  const isLoading = computed(() => activeRequests.value.size > 0);
  const activeRequestCount = computed(() => activeRequests.value.size);

  return {
    // Main methods
    callServer,
    
    // HTTP method helpers
    get,
    post,
    put,
    delete: del,

    // Configuration
    updateConfig,
    getCsrfToken,

    // State management
    cancelAllRequests,

    // Reactive state
    isLoading,
    activeRequestCount,
    
    // Configuration access
    config: computed(() => serverConfig.value)
  };
}