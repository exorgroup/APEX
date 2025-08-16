/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: TypeScript type definitions for PRO widgets providing interfaces for events, state, parameters and error handling
 * File location: resources/js/components/apex/pro/widget/PrimeVueBaseWidget/types/index.ts
 */

import type { Ref, ComputedRef } from 'vue';

// Global declarations for browser APIs
declare global {
  interface Window {
    // User-defined functions for event handling
    [key: string]: any;
  }
}

// Event handling types
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
  priority?: 'low' | 'normal' | 'high';
  timeout?: number;
  retries?: number;
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

// State management types
export interface StateConfig {
  syncToServer?: boolean;
  localState?: boolean;
  conflictResolution?: 'client' | 'server' | 'merge' | 'prompt';
  autoSync?: boolean;
  syncInterval?: number;
  batchUpdates?: boolean;
  persistState?: boolean;
  stateValidation?: {
    required?: string[];
    types?: Record<string, string>;
  };
}

export interface WidgetState {
  widgetId: string;
  value: any;
  valid: boolean;
  dirty: boolean;
  touched: boolean;
  focused: boolean;
  loading: boolean;
  lastUpdated: string;
  version: number;
  syncStatus: 'synchronized' | 'pending' | 'error' | 'conflict';
  [key: string]: any;
}

export interface StateConflict {
  hasConflict: boolean;
  localState: WidgetState;
  serverState: WidgetState;
  conflictFields: string[];
  resolution?: 'client' | 'server' | 'merge' | 'prompt';
}

// Parameter injection types
export interface ParameterConfig {
  contexts?: string[];
  templates?: Record<string, string>;
  validation?: {
    required?: string[];
    types?: Record<string, string>;
  };
  cache?: boolean;
  watchChanges?: boolean;
}

export interface ParameterContext {
  widget?: Record<string, any>;
  form?: Record<string, any>;
  user?: Record<string, any>;
  static?: Record<string, any>;
  config?: Record<string, any>;
  route?: Record<string, any>;
}

export interface CompiledTemplate {
  template: string;
  context: string;
  path: string;
  segments: string[];
  isWildcard: boolean;
  isArray: boolean;
  defaultValue?: any;
}

// Error handling types
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

// Server communication types
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

// Widget configuration types
export interface ProWidgetConfig {
  // Core widget properties
  widgetId: string;
  type: string;
  
  // PRO features
  events?: EventConfig;
  stateConfig?: StateConfig;
  errorConfig?: ErrorConfig;
  parameterConfig?: ParameterConfig;
  
  // Advanced validation
  advancedValidation?: {
    realTimeValidation?: boolean;
    customRules?: ValidationRule[];
    businessRules?: Record<string, any>;
  };
  
  // Server configuration
  serverConfig?: {
    endpoints?: Record<string, string>;
    timeout?: number;
    retries?: number;
  };
  
  // Form and widget context
  formData?: Record<string, any>;
  widgetData?: Record<string, any>;
}

export interface ValidationRule {
  type: 'email' | 'phone' | 'creditCard' | 'pattern' | 'length' | 'server';
  pattern?: string;
  min?: number;
  max?: number;
  message?: string;
  server?: string;
  params?: Record<string, any>;
}

export interface ValidationResult {
  valid: boolean;
  message: string;
  field?: string;
  code: string;
  context?: Record<string, any>;
}

// Composable return types
export interface UseEventHandlingReturn {
  registerEvents: () => void;
  executeHandler: (eventName: string, event: Event, value?: any) => Promise<void>;
  getEventHandler: (eventName: string) => Function | null;
  getEventHandlers: () => Record<string, Function>;
  cleanup: () => void;
  isLoading: ComputedRef<boolean>;
  isEventLoading: (eventName: string) => boolean;
  loadingEvents: ComputedRef<string[]>;
  registeredHandlers: ComputedRef<Record<string, EventHandler>>;
}

export interface UseStateManagementReturn {
  state: ComputedRef<WidgetState>;
  isLoading: ComputedRef<boolean>;
  isDirty: ComputedRef<boolean>;
  isValid: ComputedRef<boolean>;
  isSynchronized: ComputedRef<boolean>;
  hasConflict: ComputedRef<boolean>;
  hasPendingUpdates: ComputedRef<boolean>;
  updateState: (updates: Partial<WidgetState>, options?: { syncToServer?: boolean; immediate?: boolean }) => Promise<boolean>;
  syncToServer: () => Promise<boolean>;
  syncFromServer: () => Promise<boolean>;
  resetState: () => Promise<void>;
  updateConfig: (newConfig: Partial<StateConfig>) => void;
  resolveStateConflict: (conflict: StateConflict) => Promise<void>;
  conflictState: ComputedRef<StateConflict | null>;
  config: ComputedRef<StateConfig>;
}

export interface UseErrorDisplayReturn {
  showError: (message: string, displayType?: 'inline' | 'toast' | 'dialog', field?: string, severity?: 'info' | 'success' | 'warn' | 'error', code?: string, context?: Record<string, any>) => string;
  dismissError: (errorId: string) => boolean;
  clearErrors: (field?: string) => number;
  processServerError: (serverResponse: any) => string | null;
  updateConfig: (newConfig: Partial<ErrorConfig>) => void;
  hasErrors: ComputedRef<boolean>;
  errorCount: ComputedRef<number>;
  errorMessage: ComputedRef<string>;
  errorType: ComputedRef<string>;
  hasFieldErrors: (field: string) => ComputedRef<boolean>;
  getFieldErrors: (field: string) => ComputedRef<ErrorItem[]>;
  getAllErrors: ComputedRef<ErrorItem[]>;
  getInlineErrors: ComputedRef<ErrorItem[]>;
  config: ComputedRef<ErrorConfig>;
  cleanup: () => void;
}

export interface UseParameterInjectionReturn {
  resolveParameter: (template: string, contextOverride?: ParameterContext) => any;
  resolveParameters: (parameters: Record<string, string>, contextOverride?: ParameterContext) => Promise<Record<string, any>>;
  processTemplateString: (templateString: string, contextOverride?: ParameterContext) => string;
  registerTemplate: (key: string, template: string) => boolean;
  getParameterValue: (key: string) => any;
  setStaticParameter: (key: string, value: any) => void;
  updateContext: (newContext: Partial<ParameterContext>) => void;
  getAvailableContexts: () => Record<string, string>;
  clearCache: () => void;
  resolveAllParameters: () => void;
  updateConfig: (newConfig: Partial<ParameterConfig>) => void;
  validateParameters: (resolvedParams: Record<string, any>) => boolean;
  hasParameters: ComputedRef<boolean>;
  parameterCount: ComputedRef<number>;
  cacheSize: ComputedRef<number>;
  allResolvedValues: ComputedRef<Record<string, any>>;
  contextData: ComputedRef<ParameterContext>;
  config: ComputedRef<ParameterConfig>;
  cleanup: () => void;
}

export interface UseServerCommunicationReturn {
  callServer: (endpoint: string, data?: any, options?: RequestOptions) => Promise<ServerResponse>;
  get: (endpoint: string, params?: any, options?: Omit<RequestOptions, 'method'>) => Promise<ServerResponse>;
  post: (endpoint: string, data?: any, options?: Omit<RequestOptions, 'method'>) => Promise<ServerResponse>;
  put: (endpoint: string, data?: any, options?: Omit<RequestOptions, 'method'>) => Promise<ServerResponse>;
  delete: (endpoint: string, data?: any, options?: Omit<RequestOptions, 'method'>) => Promise<ServerResponse>;
  updateConfig: (newConfig: Partial<ServerConfig>) => void;
  getCsrfToken: () => string | null;
  cancelAllRequests: () => void;
  isLoading: ComputedRef<boolean>;
  activeRequestCount: ComputedRef<number>;
  config: ComputedRef<ServerConfig>;
}

// Export all types for easy importing
export * from './index';