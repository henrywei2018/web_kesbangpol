<div>
    <div 
        class="cf-turnstile" 
        data-sitekey="{{ $siteKey }}" 
        data-callback="onTurnstileSuccess">
    </div>
    @error('turnstileResponse')
        <span class="text-danger small">{{ $message }}</span>
    @enderror

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        function onTurnstileSuccess(token) {
            @this.set('turnstileResponse', token);
        }
    </script>
</div>
