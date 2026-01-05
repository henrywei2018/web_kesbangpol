<div x-data="{ 
    timeLeft: {{ $timeLeft }},
    expired: {{ $expired ? 'true' : 'false' }},
    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
}" 
x-init="
    if (!expired && timeLeft > 0) {
        const timer = setInterval(() => {
            if (timeLeft > 0) {
                timeLeft--;
            } else {
                expired = true;
                clearInterval(timer);
                // Dispatch event to parent component
                window.dispatchEvent(new CustomEvent('otp-timer-expired'));
            }
        }, 1000);
    }
"
style="font-size: 14px;">
    <div x-show="!expired && timeLeft > 0" class="alert alert-info">
        <i class="fa fa-clock-o"></i> Kode akan kedaluarsa dalam: 
        <strong x-text="formatTime(timeLeft)"></strong>
    </div>
    <div x-show="expired || timeLeft <= 0" class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> Kode verifikasi telah kedaluarsa. Silakan minta kode baru.
    </div>
</div>

<script>
    // Listen for timer expiry and dispatch to Livewire
    window.addEventListener('otp-timer-expired', function() {
        if (typeof @this !== 'undefined') {
            @this.dispatch('otpTimerExpired');
        }
    });
</script>