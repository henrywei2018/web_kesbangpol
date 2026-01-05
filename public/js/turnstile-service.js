/**
 * Turnstile Service - Clean, reusable Cloudflare Turnstile implementation
 * Usage: TurnstileService.init(siteKey, containerId, callback)
 */
window.TurnstileService = (function() {
    'use strict';
    
    let instances = new Map();
    let isApiLoaded = false;
    let pendingInits = [];
    
    /**
     * Initialize Turnstile widget
     */
    function init(siteKey, containerId, options = {}) {
        const container = document.querySelector(containerId);
        if (!container || !siteKey) return false;
        
        // Prevent duplicate initialization
        if (instances.has(containerId)) {
            cleanup(containerId);
        }
        
        // Check if widget already exists in DOM
        if (hasExistingWidget(container)) {
            return false;
        }
        
        const config = {
            siteKey: String(siteKey),
            containerId: containerId,
            container: container,
            callback: options.callback || function() {},
            errorCallback: options.errorCallback || function() {},
            expiredCallback: options.expiredCallback || function() {}
        };
        
        if (isApiLoaded && typeof window.turnstile !== 'undefined') {
            return renderWidget(config);
        } else {
            // Queue for later initialization
            pendingInits.push(config);
            loadTurnstileAPI();
            return false;
        }
    }
    
    /**
     * Check if widget already exists in container
     */
    function hasExistingWidget(container) {
        return container.querySelector('iframe') || 
               container.querySelector('.cf-turnstile') || 
               container.querySelector('[data-cf-turnstile]') ||
               (container.innerHTML.trim() !== '' && container.innerHTML.includes('turnstile'));
    }
    
    /**
     * Render the Turnstile widget
     */
    function renderWidget(config) {
        try {
            // Clear container
            config.container.innerHTML = '';
            
            // Render widget
            const widgetId = window.turnstile.render(config.containerId, {
                sitekey: config.siteKey,
                theme: 'light',
                callback: config.callback,
                'error-callback': function(error) {
                    config.container.innerHTML = '<div style="color: #dc2626; font-size: 12px; text-align: center;">Security verification failed</div>';
                    config.errorCallback(error);
                },
                'expired-callback': function() {
                    cleanup(config.containerId);
                    setTimeout(() => renderWidget(config), 1000);
                    config.expiredCallback();
                }
            });
            
            if (widgetId) {
                instances.set(config.containerId, {
                    widgetId: widgetId,
                    config: config
                });
                return true;
            }
            
        } catch (error) {
            config.container.innerHTML = '<div style="color: #dc2626; font-size: 12px; text-align: center;">Security verification unavailable</div>';
            config.errorCallback(error);
        }
        
        return false;
    }
    
    /**
     * Load Turnstile API
     */
    function loadTurnstileAPI() {
        if (document.querySelector('script[src*="challenges.cloudflare.com/turnstile"]')) {
            return; // Already loading/loaded
        }
        
        window.onTurnstileLoad = function() {
            isApiLoaded = true;
            
            // Process pending initializations
            const pending = [...pendingInits];
            pendingInits = [];
            
            pending.forEach(config => {
                renderWidget(config);
            });
        };
        
        const script = document.createElement('script');
        script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
    
    /**
     * Cleanup widget instance
     */
    function cleanup(containerId) {
        const instance = instances.get(containerId);
        if (instance && instance.widgetId && typeof window.turnstile !== 'undefined') {
            try {
                window.turnstile.remove(instance.widgetId);
            } catch (e) {
                // Silent cleanup
            }
        }
        instances.delete(containerId);
    }
    
    /**
     * Reset widget (useful for form resets)
     */
    function reset(containerId) {
        const instance = instances.get(containerId);
        if (instance && instance.widgetId && typeof window.turnstile !== 'undefined') {
            try {
                window.turnstile.reset(instance.widgetId);
                return true;
            } catch (e) {
                // If reset fails, try re-initialization
                cleanup(containerId);
                return init(instance.config.siteKey, containerId, instance.config);
            }
        }
        return false;
    }
    
    /**
     * Get response token
     */
    function getResponse(containerId) {
        const instance = instances.get(containerId);
        if (instance && instance.widgetId && typeof window.turnstile !== 'undefined') {
            try {
                return window.turnstile.getResponse(instance.widgetId);
            } catch (e) {
                return null;
            }
        }
        return null;
    }
    
    /**
     * Handle page cleanup
     */
    function cleanupAll() {
        instances.forEach((instance, containerId) => {
            cleanup(containerId);
        });
    }
    
    // Global cleanup on page unload
    if (typeof window !== 'undefined') {
        window.addEventListener('beforeunload', cleanupAll);
    }
    
    // Public API
    return {
        init: init,
        cleanup: cleanup,
        reset: reset,
        getResponse: getResponse,
        cleanupAll: cleanupAll
    };
})();