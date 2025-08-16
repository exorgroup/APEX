/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Parameter injection composable for PRO widgets providing dynamic parameter resolution using template syntax like {{widget:id.value}}, {{form:*}}, {{user:property}} with real-time context updates
 * File location: resources/js/components/apex/pro/widget/PrimeVueBaseWidget/composables/useParameterInjection.ts
 */

import { ref, reactive, computed, watch, readonly, type Ref } from 'vue';

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

export function useParameterInjection(config: ParameterConfig = {}) {
  // Configuration with defaults
  const parameterConfig = reactive<ParameterConfig>({
    contexts: ['widget', 'form', 'user', 'static', 'config', 'route'],
    templates: {},
    validation: {
      required: [],
      types: {}
    },
    cache: true,
    watchChanges: true,
    ...config
  });

  // Context data storage
  const contextData = reactive<ParameterContext>({
    widget: {},
    form: {},
    user: {},
    static: {},
    config: {},
    route: {}
  });

  // Compiled templates cache
  const compiledTemplates = ref<Map<string, CompiledTemplate>>(new Map());
  const resolvedValues = ref<Map<string, any>>(new Map());
  const watcherCleanups = ref<Array<() => void>>([]);

  /**
   * Template pattern for parameter injection
   */
  const TEMPLATE_PATTERN = /\{\{([^:]+):([^}]+)\}\}/g;

  /**
   * Available parameter contexts with descriptions
   */
  const availableContexts = {
    widget: 'Values from other widgets in the form',
    form: 'Form data and validation state',
    user: 'User session and profile information',
    static: 'Static literal values defined in configuration',
    config: 'Application configuration and settings',
    route: 'Current route parameters and query strings'
  };

  /**
   * Update context data
   */
  const updateContext = (newContext: Partial<ParameterContext>): void => {
    try {
      Object.keys(newContext).forEach(contextKey => {
        if (contextKey in contextData) {
          const key = contextKey as keyof ParameterContext;
          const currentContext = contextData[key];
          const newContextValue = newContext[key];
          
          if (currentContext && newContextValue) {
            Object.assign(currentContext, newContextValue);
          } else if (newContextValue) {
            // Initialize context if it doesn't exist
            contextData[key] = newContextValue as any;
          }
        }
      });

      // Re-resolve parameters if watching changes
      if (parameterConfig.watchChanges) {
        resolveAllParameters();
      }
    } catch (error) {
      console.error('Error updating parameter context:', error);
    }
  };

  /**
   * Register parameter template
   */
  const registerTemplate = (key: string, template: string): boolean => {
    try {
      if (!isValidTemplate(template)) {
        console.warn(`Invalid parameter template: ${template}`);
        return false;
      }

      const compiled = compileTemplate(template);
      if (compiled) {
        compiledTemplates.value.set(key, compiled);
        
        // Store in config
        if (!parameterConfig.templates) {
          parameterConfig.templates = {};
        }
        parameterConfig.templates[key] = template;

        return true;
      }

      return false;
    } catch (error) {
      console.error('Error registering parameter template:', error);
      return false;
    }
  };

  /**
   * Validate parameter template syntax
   */
  const isValidTemplate = (template: string): boolean => {
    try {
      const pattern = /^\{\{(widget|form|user|static|config|route):[^}]+\}\}$/;
      return pattern.test(template);
    } catch (error) {
      console.error('Error validating template:', error);
      return false;
    }
  };

  /**
   * Compile parameter template for efficient resolution
   */
  const compileTemplate = (template: string): CompiledTemplate | null => {
    try {
      const match = template.match(/^\{\{([^:]+):([^}]+)\}\}$/);
      if (!match) {
        return null;
      }

      const [, context, path] = match;
      const segments = path.split('.');
      
      return {
        template,
        context,
        path,
        segments,
        isWildcard: path === '*',
        isArray: path.includes('[]'),
        defaultValue: undefined
      };
    } catch (error) {
      console.error('Error compiling template:', error);
      return null;
    }
  };

  /**
   * Resolve single parameter template
   */
  const resolveParameter = (template: string, contextOverride?: ParameterContext): any => {
    try {
      // Use cache if enabled
      if (parameterConfig.cache && resolvedValues.value.has(template)) {
        return resolvedValues.value.get(template);
      }

      const compiled = compileTemplate(template);
      if (!compiled) {
        return undefined;
      }

      const context = contextOverride || contextData;
      const contextValue = context[compiled.context as keyof ParameterContext];

      if (!contextValue) {
        return compiled.defaultValue;
      }

      // Handle wildcard (return entire context)
      if (compiled.isWildcard) {
        const result = { ...contextValue };
        if (parameterConfig.cache) {
          resolvedValues.value.set(template, result);
        }
        return result;
      }

      // Resolve nested path
      let result: any = contextValue;
      for (const segment of compiled.segments) {
        if (result && typeof result === 'object') {
          // Handle array notation
          if (segment.includes('[]')) {
            const arrayKey = segment.replace('[]', '');
            result = result[arrayKey];
            if (Array.isArray(result)) {
              // Return the array itself for array notation
              break;
            }
          } else {
            result = result[segment];
          }
        } else {
          result = undefined;
          break;
        }
      }

      // Cache result if enabled
      if (parameterConfig.cache) {
        resolvedValues.value.set(template, result);
      }

      return result !== undefined ? result : compiled.defaultValue;
    } catch (error) {
      console.error('Error resolving parameter:', error);
      return undefined;
    }
  };

  /**
   * Resolve multiple parameters
   */
  const resolveParameters = async (
    parameters: Record<string, string>,
    contextOverride?: ParameterContext
  ): Promise<Record<string, any>> => {
    try {
      const resolved: Record<string, any> = {};

      // Process all parameters
      for (const [key, template] of Object.entries(parameters)) {
        if (typeof template === 'string' && template.startsWith('{{')) {
          resolved[key] = resolveParameter(template, contextOverride);
        } else {
          // Static value
          resolved[key] = template;
        }
      }

      return resolved;
    } catch (error) {
      console.error('Error resolving parameters:', error);
      return {};
    }
  };

  /**
   * Resolve all registered templates
   */
  const resolveAllParameters = (): void => {
    try {
      if (!parameterConfig.templates) {
        return;
      }

      // Clear cache if enabled
      if (parameterConfig.cache) {
        resolvedValues.value.clear();
      }

      // Resolve all registered templates
      for (const [key, template] of Object.entries(parameterConfig.templates)) {
        const resolved = resolveParameter(template);
        resolvedValues.value.set(key, resolved);
      }
    } catch (error) {
      console.error('Error resolving all parameters:', error);
    }
  };

  /**
   * Process template string with parameter injection
   */
  const processTemplateString = (
    templateString: string,
    contextOverride?: ParameterContext
  ): string => {
    try {
      return templateString.replace(TEMPLATE_PATTERN, (match, context, path) => {
        const template = `{{${context}:${path}}}`;
        const resolved = resolveParameter(template, contextOverride);
        
        // Convert resolved value to string
        if (resolved === null || resolved === undefined) {
          return '';
        }
        
        if (typeof resolved === 'object') {
          return JSON.stringify(resolved);
        }
        
        return String(resolved);
      });
    } catch (error) {
      console.error('Error processing template string:', error);
      return templateString;
    }
  };

  /**
   * Validate resolved parameters
   */
  const validateParameters = (resolvedParams: Record<string, any>): boolean => {
    try {
      const validation = parameterConfig.validation;
      if (!validation) return true;

      // Check required parameters
      if (validation.required) {
        for (const required of validation.required) {
          if (!(required in resolvedParams) || resolvedParams[required] === undefined) {
            console.warn(`Required parameter '${required}' is missing or undefined`);
            return false;
          }
        }
      }

      // Check parameter types
      if (validation.types) {
        for (const [param, expectedType] of Object.entries(validation.types)) {
          if (param in resolvedParams) {
            const actualType = typeof resolvedParams[param];
            if (actualType !== expectedType) {
              console.warn(`Parameter '${param}' has invalid type. Expected ${expectedType}, got ${actualType}`);
              return false;
            }
          }
        }
      }

      return true;
    } catch (error) {
      console.error('Error validating parameters:', error);
      return false;
    }
  };

  /**
   * Setup context watchers for auto-resolution
   */
  const setupContextWatchers = (): void => {
    try {
      if (!parameterConfig.watchChanges) {
        return;
      }

      // Clear existing watchers
      cleanupWatchers();

      // Watch each context for changes
      Object.keys(contextData).forEach(contextKey => {
        const key = contextKey as keyof ParameterContext;
        const contextValue = contextData[key];
        
        if (contextValue) {
          const stopWatcher = watch(
            () => contextData[key],
            () => {
              // Clear cache and re-resolve
              if (parameterConfig.cache) {
                resolvedValues.value.clear();
              }
              resolveAllParameters();
            },
            { deep: true }
          );

          watcherCleanups.value.push(stopWatcher);
        }
      });
    } catch (error) {
      console.error('Error setting up context watchers:', error);
    }
  };

  /**
   * Get parameter value by key
   */
  const getParameterValue = (key: string): any => {
    try {
      if (resolvedValues.value.has(key)) {
        return resolvedValues.value.get(key);
      }

      // Try to resolve if template exists
      const template = parameterConfig.templates?.[key];
      if (template) {
        return resolveParameter(template);
      }

      return undefined;
    } catch (error) {
      console.error('Error getting parameter value:', error);
      return undefined;
    }
  };

  /**
   * Set static parameter value
   */
  const setStaticParameter = (key: string, value: any): void => {
    try {
      if (!contextData.static) {
        contextData.static = {};
      }

      if (contextData.static) {
        contextData.static[key] = value;
      }

      // Update cache if template exists
      const template = `{{static:${key}}}`;
      if (parameterConfig.cache) {
        resolvedValues.value.set(template, value);
      }
    } catch (error) {
      console.error('Error setting static parameter:', error);
    }
  };

  /**
   * Get available parameter contexts
   */
  const getAvailableContexts = (): Record<string, string> => {
    try {
      const filtered: Record<string, string> = {};
      
      (parameterConfig.contexts || Object.keys(availableContexts)).forEach(context => {
        if (context in availableContexts) {
          filtered[context] = availableContexts[context as keyof typeof availableContexts];
        }
      });

      return filtered;
    } catch (error) {
      console.error('Error getting available contexts:', error);
      return {};
    }
  };

  /**
   * Clear parameter cache
   */
  const clearCache = (): void => {
    try {
      resolvedValues.value.clear();
    } catch (error) {
      console.error('Error clearing parameter cache:', error);
    }
  };

  /**
   * Update parameter configuration
   */
  const updateConfig = (newConfig: Partial<ParameterConfig>): void => {
    try {
      Object.assign(parameterConfig, newConfig);

      // Restart watchers if watchChanges setting changed
      if ('watchChanges' in newConfig) {
        setupContextWatchers();
      }

      // Clear cache if caching was disabled
      if ('cache' in newConfig && !newConfig.cache) {
        clearCache();
      }
    } catch (error) {
      console.error('Error updating parameter configuration:', error);
    }
  };

  /**
   * Cleanup watchers
   */
  const cleanupWatchers = (): void => {
    try {
      watcherCleanups.value.forEach(cleanup => cleanup());
      watcherCleanups.value = [];
    } catch (error) {
      console.error('Error cleaning up watchers:', error);
    }
  };

  /**
   * Initialize user context from session/auth
   */
  const initializeUserContext = (): void => {
    try {
      // Try to get user data from common sources
      const userData: Record<string, any> = {};

      // From window global (if set by Laravel)
      if ((window as any).user) {
        Object.assign(userData, (window as any).user);
      }

      // From meta tags
      const userMeta = document.querySelector('meta[name="user-data"]') as HTMLMetaElement;
      if (userMeta && userMeta.content) {
        try {
          const metaData = JSON.parse(userMeta.content);
          Object.assign(userData, metaData);
        } catch (e) {
          // Ignore JSON parse errors
        }
      }

      // From local storage (with fallback)
      try {
        const storedUser = localStorage.getItem('user');
        if (storedUser) {
          const parsedUser = JSON.parse(storedUser);
          Object.assign(userData, parsedUser);
        }
      } catch (e) {
        // Ignore storage errors
      }

      if (Object.keys(userData).length > 0) {
        if (!contextData.user) {
          contextData.user = {};
        }
        Object.assign(contextData.user, userData);
      }
    } catch (error) {
      console.error('Error initializing user context:', error);
    }
  };

  /**
   * Initialize route context from current page
   */
  const initializeRouteContext = (): void => {
    try {
      const routeData: Record<string, any> = {};

      // Get route parameters from URL
      const url = new URL(window.location.href);
      
      routeData.path = url.pathname;
      routeData.query = Object.fromEntries(url.searchParams);
      routeData.hash = url.hash;
      routeData.host = url.host;
      routeData.protocol = url.protocol;

      // Try to get route name from meta tag (Laravel route name)
      const routeMeta = document.querySelector('meta[name="route-name"]') as HTMLMetaElement;
      if (routeMeta) {
        routeData.name = routeMeta.content;
      }

      if (!contextData.route) {
        contextData.route = {};
      }
      Object.assign(contextData.route, routeData);
    } catch (error) {
      console.error('Error initializing route context:', error);
    }
  };

  /**
   * Initialize configuration context
   */
  const initializeConfigContext = (): void => {
    try {
      const configData: Record<string, any> = {};

      // From window global (app config)
      if ((window as any).config) {
        Object.assign(configData, (window as any).config);
      }

      // From meta tags
      const configMeta = document.querySelector('meta[name="app-config"]') as HTMLMetaElement;
      if (configMeta && configMeta.content) {
        try {
          const metaData = JSON.parse(configMeta.content);
          Object.assign(configData, metaData);
        } catch (e) {
          // Ignore JSON parse errors
        }
      }

      if (!contextData.config) {
        contextData.config = {};
      }
      Object.assign(contextData.config, configData);
    } catch (error) {
      console.error('Error initializing config context:', error);
    }
  };

  /**
   * Initialize all contexts
   */
  const initialize = (): void => {
    try {
      // Initialize built-in contexts
      initializeUserContext();
      initializeRouteContext();
      initializeConfigContext();

      // Setup watchers
      setupContextWatchers();

      // Resolve initial parameters
      resolveAllParameters();
    } catch (error) {
      console.error('Error initializing parameter injection:', error);
    }
  };

  // Computed properties
  const hasParameters = computed(() => 
    parameterConfig.templates && Object.keys(parameterConfig.templates).length > 0
  );

  const parameterCount = computed(() => 
    parameterConfig.templates ? Object.keys(parameterConfig.templates).length : 0
  );

  const cacheSize = computed(() => resolvedValues.value.size);

  const allResolvedValues = computed(() => 
    Object.fromEntries(resolvedValues.value.entries())
  );

  // Initialize on creation
  initialize();

  return {
    // Core methods
    resolveParameter,
    resolveParameters,
    processTemplateString,
    
    // Template management
    registerTemplate,
    getParameterValue,
    setStaticParameter,
    
    // Context management
    updateContext,
    getAvailableContexts,
    
    // Cache management
    clearCache,
    resolveAllParameters,
    
    // Configuration
    updateConfig,
    validateParameters,
    
    // State
    hasParameters,
    parameterCount,
    cacheSize,
    allResolvedValues,
    
    // Context data (readonly)
    contextData: readonly(contextData),
    config: readonly(parameterConfig),
    
    // Cleanup
    cleanup: cleanupWatchers
  };
}