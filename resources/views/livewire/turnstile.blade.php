<div>
    <div id="turnstile-container-{{ uniqid() }}" class="cf-turnstile"></div>
    @error('turnstileResponse')
        <span class="text-danger small">{{ $message }}</span>
    @enderror

    {{-- Include Turnstile Service --}}
    <script src="{{ asset('js/turnstile-service.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const SITE_KEY = '{{ $siteKey }}';
            const containerId = '#turnstile-container-{{ uniqid() }}';
            
            // Initialize Turnstile using the service
            TurnstileService.init(SITE_KEY, containerId, {
                callback: function(token) {
                    @this.set('turnstileResponse', token);
                }
            });
        });
    </script>
</div>
