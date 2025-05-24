/**
 * APEX Event Bus
 * 
 * Handles widget-to-widget communication through events.
 * Prevents data bleeding between different instances.
 */
class ApexEventBus {
    constructor() {
        this.events = {};
        this.persistentEvents = {};
        this.history = {};
        this.sessionId = this.generateSessionId();
        
        // Load persistent events from localStorage
        this.loadPersistentEvents();
        
        // Set up cross-tab communication
        window.addEventListener('storage', this.handleStorageEvent.bind(this));
        
        // Clean up on page unload
        window.addEventListener('beforeunload', this.cleanup.bind(this));
    }

    /**
     * Generate unique session ID to prevent data bleeding
     */
    generateSessionId() {
        return 'apex_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Subscribe to an event
     */
    subscribe(event, callback, options = {}) {
        const { persistent = false, replay = false, limit = 0, widgetId = null } = options;
        
        if (!this.events[event]) {
            this.events[event] = [];
        }

        // Create subscription with session isolation
        const subscription = {
            callback,
            sessionId: this.sessionId,
            widgetId,
            createdAt: Date.now()
        };

        this.events[event].push(subscription);
        
        // Track if this is a persistent subscription
        if (persistent) {
            this.persistentEvents[event] = true;
        }
        
        // Replay previous events if requested
        if (replay && this.history[event]) {
            const eventsToReplay = limit > 0 
                ? this.history[event].slice(-limit) 
                : this.history[event];
                
            eventsToReplay.forEach(data => {
                try {
                    callback(data);
                } catch (error) {
                    console.error('APEX: Error in event callback replay', error);
                }
            });
        }
        
        // Return unsubscribe function
        return () => {
            this.events[event] = this.events[event].filter(sub => sub !== subscription);
            if (this.events[event].length === 0) {
                delete this.events[event];
            }
        };
    }

    /**
     * Publish an event
     */
    publish(event, data, options = {}) {
        const { persist = false, broadcast = false, throttle = 0, sourceWidget = null } = options;
        
        // Don't publish if throttled
        if (throttle > 0 && this.isThrottled(event, throttle)) {
            return;
        }

        // Add metadata to prevent data bleeding
        const eventData = {
            ...data,
            _meta: {
                sessionId: this.sessionId,
                sourceWidget,
                timestamp: Date.now(),
                eventId: this.generateEventId()
            }
        };
        
        // Store in history
        if (!this.history[event]) {
            this.history[event] = [];
        }
        this.history[event].push(eventData);
        
        // Limit history size to prevent memory leaks
        if (this.history[event].length > 50) {
            this.history[event] = this.history[event].slice(-50);
        }
        
        // Notify all subscribers
        if (this.events[event]) {
            this.events[event].forEach(subscription => {
                // Only deliver to same session to prevent bleeding
                if (subscription.sessionId === this.sessionId) {
                    try {
                        subscription.callback(eventData);
                    } catch (error) {
                        console.error('APEX: Error in event callback', error);
                    }
                }
            });
        }
        
        // Store persistent events
        if (persist || this.persistentEvents[event]) {
            this.storePersistentEvent(event, eventData);
        }
        
        // Broadcast to other tabs if requested
        if (broadcast) {
            localStorage.setItem('apex:event:broadcast', JSON.stringify({
                event,
                data: eventData,
                timestamp: Date.now(),
                sessionId: this.sessionId
            }));
        }
    }

    /**
     * Publish an event targeted at a specific widget
     */
    publishTo(widgetId, event, data, options = {}) {
        // Get the specific widget element
        const widgetElement = document.getElementById(widgetId);
        if (!widgetElement) {
            console.warn(`APEX: Widget with ID ${widgetId} not found`);
            return;
        }
        
        // Create a custom event for the specific widget
        const customEvent = new CustomEvent(`apex:${event}`, {
            detail: {
                ...data,
                _meta: {
                    sessionId: this.sessionId,
                    targetWidget: widgetId,
                    timestamp: Date.now(),
                    eventId: this.generateEventId()
                }
            },
            bubbles: false  // Don't bubble up to prevent other handlers
        });
        
        // Dispatch directly on the widget element
        widgetElement.dispatchEvent(customEvent);
        
        // Also publish to the global event bus if requested
        if (options.global) {
            this.publish(event, data, { ...options, sourceWidget: widgetId });
        }
    }

    /**
     * Subscribe to events only from a specific widget
     */
    subscribeFrom(widgetId, event, callback, options = {}) {
        const widgetElement = document.getElementById(widgetId);
        if (!widgetElement) {
            console.warn(`APEX: Widget with ID ${widgetId} not found`);
            return () => {};
        }
        
        // Create event listener specifically for this widget
        const handler = e => {
            try {
                callback(e.detail);
            } catch (error) {
                console.error('APEX: Error in widget-specific event callback', error);
            }
        };
        
        widgetElement.addEventListener(`apex:${event}`, handler);
        
        // Return unsubscribe function
        return () => {
            widgetElement.removeEventListener(`apex:${event}`, handler);
        };
    }

    /**
     * Generate unique event ID
     */
    generateEventId() {
        return 'evt_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Check if an event is currently throttled
     */
    isThrottled(event, throttleMs) {
        const key = `apex:throttle:${event}`;
        const lastPublish = parseInt(sessionStorage.getItem(key) || 0);
        const now = Date.now();
        
        if (now - lastPublish < throttleMs) {
            return true;
        }
        
        sessionStorage.setItem(key, now.toString());
        return false;
    }

    /**
     * Store persistent event in localStorage
     */
    storePersistentEvent(event, data) {
        const key = `apex:event:${event}`;
        const events = JSON.parse(localStorage.getItem(key) || '[]');
        events.push({
            data,
            timestamp: Date.now(),
            sessionId: this.sessionId
        });
        
        // Keep only last 10 events to avoid localStorage filling up
        if (events.length > 10) {
            events.shift();
        }
        
        localStorage.setItem(key, JSON.stringify(events));
    }

    /**
     * Load persistent events from localStorage
     */
    loadPersistentEvents() {
        // Get all localStorage keys
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('apex:event:')) {
                const event = key.replace('apex:event:', '');
                try {
                    const events = JSON.parse(localStorage.getItem(key) || '[]');
                    this.history[event] = events.map(e => e.data);
                } catch (e) {
                    console.error('APEX: Failed to parse persistent event', e);
                }
            }
        });
    }

    /**
     * Handle storage events from other tabs
     */
    handleStorageEvent(e) {
        if (e.key === 'apex:event:broadcast') {
            try {
                const { event, data, sessionId } = JSON.parse(e.newValue);
                
                // Don't process events from the same session
                if (sessionId === this.sessionId) {
                    return;
                }
                
                if (this.events[event]) {
                    this.events[event].forEach(subscription => {
                        try {
                            subscription.callback(data);
                        } catch (error) {
                            console.error('APEX: Error in broadcast event callback', error);
                        }
                    });
                }
            } catch (error) {
                console.error('APEX: Failed to process broadcast event', error);
            }
        }
    }

    /**
     * Clear event history
     */
    clearHistory(event = null) {
        if (event) {
            delete this.history[event];
            localStorage.removeItem(`apex:event:${event}`);
        } else {
            this.history = {};
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('apex:event:')) {
                    localStorage.removeItem(key);
                }
            });
        }
    }

    /**
     * Get event statistics
     */
    getStats() {
        const totalSubscriptions = Object.values(this.events)
            .reduce((sum, subs) => sum + subs.length, 0);
        
        const totalHistory = Object.values(this.history)
            .reduce((sum, events) => sum + events.length, 0);
        
        return {
            sessionId: this.sessionId,
            activeEvents: Object.keys(this.events).length,
            totalSubscriptions,
            historyEvents: Object.keys(this.history).length,
            totalHistorySize: totalHistory,
            persistentEvents: Object.keys(this.persistentEvents).length
        };
    }

    /**
     * Clean up resources on page unload
     */
    cleanup() {
        // Clear session-specific data
        Object.keys(sessionStorage).forEach(key => {
            if (key.startsWith('apex:throttle:')) {
                sessionStorage.removeItem(key);
            }
        });
        
        // Clear event subscriptions
        this.events = {};
    }
}

// Create singleton instance with session isolation
window.ApexEvents = window.ApexEvents || new ApexEventBus();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.ApexEvents;
}

export default window.ApexEvents;