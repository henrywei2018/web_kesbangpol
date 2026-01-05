{{-- Auth Turnstile Component (resources/views/components/auth-turnstile.blade.php) --}}
@if(!app()->environment('local'))
@php
    $containerId = 'turnstile-widget-' . uniqid();
@endphp

<div class="turnstile-container" style="margin: 15px 0; display: flex; justify-content: center;">
    <div id="{{ $containerId }}" class="cf-turnstile-widget"></div>
    @error('turnstileResponse')
        <div style="color: #e74c3c; font-size: 12px; margin-top: 5px; text-align: center;">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Include Turnstile Service --}}
<script src="{{ asset('js/turnstile-service.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const SITE_KEY = '{{ $siteKey }}';
    const containerId = '#{{ $containerId }}';
    
    // Initialize Turnstile using the service
    TurnstileService.init(SITE_KEY, containerId, {
        callback: function(token) {
            // Update Livewire component
            if (window.Livewire) {
                const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    window.Livewire.find(wireId).set('turnstileResponse', token);
                }
            }
        },
        errorCallback: function() {
            // Reset token on error
            if (window.Livewire) {
                const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    window.Livewire.find(wireId).set('turnstileResponse', '');
                }
            }
        },
        expiredCallback: function() {
            // Clear token on expiry
            if (window.Livewire) {
                const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    window.Livewire.find(wireId).set('turnstileResponse', '');
                }
            }
        }
    });

    // Handle Livewire events
    document.addEventListener('livewire:updated', function() {
        // Clean and re-initialize on updates
        TurnstileService.cleanup(containerId);
        setTimeout(() => {
            TurnstileService.init(SITE_KEY, containerId, {
                callback: function(token) {
                    if (window.Livewire) {
                        const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                        if (wireId) {
                            window.Livewire.find(wireId).set('turnstileResponse', token);
                        }
                    }
                }
            });
        }, 200);
    });

    // Listen for reset events from Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('reset-turnstile', () => {
            TurnstileService.reset(containerId);
        });
    });
});
</script>
@else
<div class="turnstile-container" style="margin: 15px 0; text-align: center;">
    <div style="padding: 20px; background: #f0f0f0; border-radius: 8px; color: #666; font-size: 12px;">
        Turnstile disabled in local environment
    </div>
</div>
@endif