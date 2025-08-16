/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: State management composable for PRO widgets providing hybrid client/server state synchronization with conflict resolution and real-time updates
 * File location: resources/js/components/apex/pro/widget/PrimeVueBaseWidget/composables/useStateManagement.ts
 */

import { ref, reactive, computed, watch, onMounted, onUnmounted, readonly, type Ref } from 'vue';
import { useServerCommunication } from './useServerCommunication';

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

export function useStateManagement(
  widgetId: string,
  config: StateConfig = {}
) {
  const { callServer } = useServerCommunication();

  // Configuration with defaults
  const stateConfig = reactive<StateConfig>({
    syncToServer: true,
    localState: true,
    conflictResolution: 'server',
    autoSync: true,
    syncInterval: 5000,
    batchUpdates: true,
    persistState: false,
    stateValidation: {
      required: [],
      types: {}
    },
    ...config
  });

  // Widget state
  const state = reactive<WidgetState>({
    widgetId,
    value: null,
    valid: true,
    dirty: false,
    touched: false,
    focused: false,
    loading: false,
    lastUpdated: new Date().toISOString(),
    version: 1,
    syncStatus: 'synchronized'
  });

  const pendingUpdates = ref<Record<string, any>>({});
  const syncTimer = ref<number | null>(null);
  const isInitialized = ref(false);
  const conflictState = ref<StateConflict | null>(null);

  /**
   * Initialize state management
   */
  const initialize = async (): Promise<void> => {
    try {
      // Load persisted state if enabled
      if (stateConfig.persistState) {
        await loadPersistedState();
      }

      // Sync from server if enabled
      if (stateConfig.syncToServer) {
        await syncFromServer();
      }

      // Setup auto-sync timer
      if (stateConfig.autoSync && stateConfig.syncInterval && stateConfig.syncInterval > 0) {
        setupAutoSync();
      }

      isInitialized.value = true;
    } catch (error) {
      console.error('Error initializing state management:', error);
    }
  };

  /**
   * Update widget state
   */
  const updateState = async (
    updates: Partial<WidgetState>,
    options: { syncToServer?: boolean; immediate?: boolean } = {}
  ): Promise<boolean> => {
    try {
      // Validate updates
      if (!validateStateUpdates(updates)) {
        return false;
      }

      // Store previous state for conflict detection
      const previousState = { ...state };

      // Apply updates to local state
      Object.assign(state, updates);
      
      // Update metadata
      state.lastUpdated = new Date().toISOString();
      state.version++;
      state.dirty = true;

      // Handle batching
      if (stateConfig.batchUpdates && !options.immediate) {
        Object.assign(pendingUpdates.value, updates);
        
        // Schedule sync if auto-sync is enabled
        if (stateConfig.autoSync && options.syncToServer !== false) {
          scheduleSync();
        }
      } else {
        // Immediate sync if requested
        if (options.syncToServer !== false && stateConfig.syncToServer) {
          await syncToServer();
        }
      }

      // Persist state if enabled
      if (stateConfig.persistState) {
        await persistState();
      }

      return true;
    } catch (error) {
      console.error('Error updating state:', error);
      return false;
    }
  };

  /**
   * Sync state to server
   */
  const syncToServer = async (): Promise<boolean> => {
    try {
      if (!stateConfig.syncToServer) {
        return false;
      }

      state.loading = true;
      state.syncStatus = 'pending';

      // Prepare sync data
      const syncData = {
        widgetId: state.widgetId,
        state: { ...state },
        version: state.version,
        updates: { ...pendingUpdates.value }
      };

      // Clear pending updates since we're syncing them
      pendingUpdates.value = {};

      // Call server sync endpoint
      const response = await callServer('/widget/sync-state', syncData);

      if (response.success) {
        state.syncStatus = 'synchronized';
        state.dirty = false;

        // Handle server state updates
        if (response.data?.serverState) {
          await handleServerStateUpdate(response.data.serverState);
        }

        return true;
      } else {
        state.syncStatus = 'error';
        console.error('Server sync failed:', response.message);
        return false;
      }
    } catch (error) {
      state.syncStatus = 'error';
      console.error('Error syncing to server:', error);
      return false;
    } finally {
      state.loading = false;
    }
  };

  /**
   * Sync state from server
   */
  const syncFromServer = async (): Promise<boolean> => {
    try {
      if (!stateConfig.syncToServer) {
        return false;
      }

      state.loading = true;

      const response = await callServer('/widget/get-state', {
        widgetId: state.widgetId
      });

      if (response.success && response.data?.state) {
        await handleServerStateUpdate(response.data.state);
        return true;
      }

      return false;
    } catch (error) {
      console.error('Error syncing from server:', error);
      return false;
    } finally {
      state.loading = false;
    }
  };

  /**
   * Handle server state update
   */
  const handleServerStateUpdate = async (serverState: WidgetState): Promise<void> => {
    try {
      // Check for conflicts
      const conflict = detectStateConflict(state, serverState);
      
      if (conflict.hasConflict) {
        conflictState.value = conflict;
        await resolveStateConflict(conflict);
      } else {
        // No conflict, merge server state
        mergeServerState(serverState);
      }
    } catch (error) {
      console.error('Error handling server state update:', error);
    }
  };

  /**
   * Detect state conflicts
   */
  const detectStateConflict = (
    localState: WidgetState,
    serverState: WidgetState
  ): StateConflict => {
    try {
      const conflictFields: string[] = [];

      // Check version conflict
      if (serverState.version > localState.version + 1) {
        conflictFields.push('version');
      }

      // Check value conflicts (if both states are dirty)
      if (localState.dirty && serverState.dirty) {
        if (JSON.stringify(localState.value) !== JSON.stringify(serverState.value)) {
          conflictFields.push('value');
        }
      }

      // Check other field conflicts
      const fieldsToCheck = ['valid', 'touched'];
      for (const field of fieldsToCheck) {
        if (localState[field] !== serverState[field]) {
          conflictFields.push(field);
        }
      }

      return {
        hasConflict: conflictFields.length > 0,
        localState,
        serverState,
        conflictFields,
        resolution: stateConfig.conflictResolution
      };
    } catch (error) {
      console.error('Error detecting state conflict:', error);
      return {
        hasConflict: false,
        localState,
        serverState,
        conflictFields: []
      };
    }
  };

  /**
   * Resolve state conflict
   */
  const resolveStateConflict = async (conflict: StateConflict): Promise<void> => {
    try {
      const resolution = conflict.resolution || stateConfig.conflictResolution;

      switch (resolution) {
        case 'client':
          state.syncStatus = 'synchronized';
          break;
        case 'server':
          mergeServerState(conflict.serverState);
          break;
        case 'merge':
          mergeStates(conflict.localState, conflict.serverState);
          break;
        case 'prompt':
          state.syncStatus = 'conflict';
          window.dispatchEvent(new CustomEvent('state-conflict', {
            detail: { widgetId, conflict }
          }));
          break;
        default:
          mergeServerState(conflict.serverState);
      }

      // Clear conflict state
      conflictState.value = null;
    } catch (error) {
      console.error('Error resolving state conflict:', error);
    }
  };

  /**
   * Merge server state with local state
   */
  const mergeServerState = (serverState: WidgetState): void => {
    try {
      // Preserve local-only properties
      const localOnlyProps = ['focused', 'loading'];
      const preservedProps: Record<string, any> = {};
      
      localOnlyProps.forEach(prop => {
        preservedProps[prop] = state[prop];
      });

      // Merge server state
      Object.assign(state, serverState);

      // Restore local properties
      Object.assign(state, preservedProps);

      // Update sync status
      state.syncStatus = 'synchronized';
      state.lastServerSync = new Date().toISOString();
    } catch (error) {
      console.error('Error merging server state:', error);
    }
  };

  /**
   * Merge two states with conflict resolution
   */
  const mergeStates = (localState: WidgetState, serverState: WidgetState): void => {
    try {
      // Server wins for business data
      const serverWins = ['value', 'valid', 'version'];
      // Client wins for UI state
      const clientWins = ['focused', 'loading', 'touched'];

      serverWins.forEach(prop => {
        if (prop in serverState) {
          state[prop] = serverState[prop];
        }
      });

      clientWins.forEach(prop => {
        if (prop in localState) {
          state[prop] = localState[prop];
        }
      });

      // Use latest timestamp
      state.lastUpdated = localState.lastUpdated > serverState.lastUpdated 
        ? localState.lastUpdated 
        : serverState.lastUpdated;

      state.syncStatus = 'synchronized';
    } catch (error) {
      console.error('Error merging states:', error);
    }
  };

  /**
   * Validate state updates
   */
  const validateStateUpdates = (updates: Partial<WidgetState>): boolean => {
    try {
      const validation = stateConfig.stateValidation;
      if (!validation) return true;

      // Check required fields
      if (validation.required) {
        for (const field of validation.required) {
          if (!(field in updates) && !(field in state)) {
            console.warn(`Required state field '${field}' is missing`);
            return false;
          }
        }
      }

      // Check field types
      if (validation.types) {
        for (const [field, expectedType] of Object.entries(validation.types)) {
          if (field in updates) {
            const actualType = typeof updates[field];
            if (actualType !== expectedType) {
              console.warn(`Invalid type for field '${field}'. Expected ${expectedType}, got ${actualType}`);
              return false;
            }
          }
        }
      }

      return true;
    } catch (error) {
      console.error('Error validating state updates:', error);
      return false;
    }
  };

  const setupAutoSync = (): void => {
    try {
      if (syncTimer.value) {
        window.clearInterval(syncTimer.value);
      }

      if (stateConfig.syncInterval && stateConfig.syncInterval > 0) {
        syncTimer.value = window.setInterval(() => {
          if (state.dirty && Object.keys(pendingUpdates.value).length > 0) {
            syncToServer();
          }
        }, stateConfig.syncInterval);
      }
    } catch (error) {
      console.error('Error setting up auto-sync:', error);
    }
  };

  const scheduleSync = (): void => {
    try {
      if (syncTimer.value) {
        window.clearTimeout(syncTimer.value);
      }

      syncTimer.value = window.setTimeout(() => {
        if (state.dirty) {
          syncToServer();
        }
      }, 500);
    } catch (error) {
      console.error('Error scheduling sync:', error);
    }
  };

  /**
   * Persist state to local storage
   */
  const persistState = async (): Promise<void> => {
    try {
      if (!stateConfig.persistState) return;

      const persistData = {
        state: { ...state },
        timestamp: new Date().toISOString()
      };

      localStorage.setItem(`widget_state_${widgetId}`, JSON.stringify(persistData));
    } catch (error) {
      console.error('Error persisting state:', error);
    }
  };

  /**
   * Load persisted state from local storage
   */
  const loadPersistedState = async (): Promise<void> => {
    try {
      if (!stateConfig.persistState) return;

      const persistedData = localStorage.getItem(`widget_state_${widgetId}`);
      if (persistedData) {
        const { state: persistedState } = JSON.parse(persistedData);
        
        // Merge persisted state (excluding volatile properties)
        const volatileProps = ['loading', 'focused', 'syncStatus'];
        const filteredState = { ...persistedState };
        
        volatileProps.forEach(prop => {
          delete filteredState[prop];
        });

        Object.assign(state, filteredState);
      }
    } catch (error) {
      console.error('Error loading persisted state:', error);
    }
  };

  /**
   * Reset state to initial values
   */
  const resetState = async (): Promise<void> => {
    try {
      // Reset to initial state
      Object.assign(state, {
        value: null,
        valid: true,
        dirty: false,
        touched: false,
        focused: false,
        loading: false,
        lastUpdated: new Date().toISOString(),
        version: 1,
        syncStatus: 'synchronized'
      });

      // Clear pending updates
      pendingUpdates.value = {};

      // Clear persisted state
      if (stateConfig.persistState) {
        localStorage.removeItem(`widget_state_${widgetId}`);
      }

      // Sync to server if enabled
      if (stateConfig.syncToServer) {
        await syncToServer();
      }
    } catch (error) {
      console.error('Error resetting state:', error);
    }
  };

  /**
   * Update configuration
   */
  const updateConfig = (newConfig: Partial<StateConfig>): void => {
    try {
      Object.assign(stateConfig, newConfig);

      // Restart auto-sync if interval changed
      if ('syncInterval' in newConfig || 'autoSync' in newConfig) {
        setupAutoSync();
      }
    } catch (error) {
      console.error('Error updating state configuration:', error);
    }
  };

  const cleanup = (): void => {
    try {
      if (syncTimer.value) {
        window.clearInterval(syncTimer.value);
        window.clearTimeout(syncTimer.value);
        syncTimer.value = null;
      }
    } catch (error) {
      console.error('Error during state management cleanup:', error);
    }
  };

  // Computed properties
  const isLoading = computed(() => state.loading);
  const isDirty = computed(() => state.dirty);
  const isValid = computed(() => state.valid);
  const isSynchronized = computed(() => state.syncStatus === 'synchronized');
  const hasConflict = computed(() => state.syncStatus === 'conflict');
  const hasPendingUpdates = computed(() => Object.keys(pendingUpdates.value).length > 0);

  // Watchers
  watch(() => state.focused, (focused) => {
    if (focused) {
      state.touched = true;
    }
  });

  // Lifecycle hooks
  onMounted(() => {
    initialize();
  });

  onUnmounted(() => {
    cleanup();
  });

  return {
    // State
    state: readonly(state),
    
    // Computed
    isLoading,
    isDirty,
    isValid,
    isSynchronized,
    hasConflict,
    hasPendingUpdates,
    
    // Methods
    updateState,
    syncToServer,
    syncFromServer,
    resetState,
    updateConfig,
    
    // Conflict resolution
    resolveStateConflict,
    conflictState: readonly(conflictState),
    
    // Configuration
    config: readonly(stateConfig)
  };
}