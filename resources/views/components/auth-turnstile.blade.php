{{-- Auth Turnstile Component (resources/views/components/auth-turnstile.blade.php) --}}
@if(!app()->environment('local'))
<div class="turnstile-container" style="margin: 15px 0; display: flex; justify-content: center;">
    <div id="turnstile-widget-{{ uniqid() }}" class="cf-turnstile-widget"></div>
    @error('turnstileResponse')
        <div style="color: #e74c3c; font-size: 12px; margin-top: 5px; text-align: center;">
            {{ $message }}
        </div>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let turnstileWidget = null;
    const SITE_KEY = '{{ $siteKey }}';
    const widgetId = 'turnstile-widget-{{ uniqid() }}';
    
    function initAuthTurnstile() {
        const container = document.querySelector('#' + widgetId);
        if (!container || !window.turnstile) return;

        try {
            // Clean up existing widget
            if (turnstileWidget) {
                turnstile.remove(turnstileWidget);
                turnstileWidget = null;
            }
            
            // Clear container
            container.innerHTML = '';
            
            // Render new widget
            turnstileWidget = turnstile.render('#' + widgetId, {
                sitekey: SITE_KEY,
                theme: 'light',
                size: 'normal',
                callback: function(token) {
                    // Update Livewire component
                    if (window.Livewire) {
                        const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                        if (wireId) {
                            window.Livewire.find(wireId).set('turnstileResponse', token);
                        }
                    }
                },
                'error-callback': function() {
                    console.error('Turnstile error occurred');
                    // Reset the widget
                    if (turnstileWidget) {
                        turnstile.reset(turnstileWidget);
                    }
                },
                'expired-callback': function() {
                    // Token expired, reset
                    if (window.Livewire) {
                        const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                        if (wireId) {
                            window.Livewire.find(wireId).set('turnstileResponse', '');
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Turnstile initialization error:', error);
        }
    }

    // Initialize when Turnstile is ready
    window.onTurnstileLoad = function() {
        initAuthTurnstile();
    };

    // Handle Livewire events
    document.addEventListener('livewire:navigated', function() {
        setTimeout(initAuthTurnstile, 100);
    });

    // Listen for reset events from Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('reset-turnstile', () => {
            if (turnstileWidget) {
                turnstile.reset(turnstileWidget);
            }
        });
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (turnstileWidget) {
            try {
                turnstile.remove(turnstileWidget);
            } catch (error) {
                console.error('Turnstile cleanup error:', error);
            }
        }
    });
});
</script>

<!-- Load Turnstile API -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad" async defer></script>
@else
<div class="turnstile-container" style="margin: 15px 0; text-align: center;">
    <div style="padding: 20px; background: #f0f0f0; border-radius: 8px; color: #666; font-size: 12px;">
        Turnstile disabled in local environment
    </div>
</div>
@endif