<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait HasTurnstile
{
    public string $turnstileResponse = '';

    /**
     * Validate Turnstile response
     */
    public function validateTurnstile(): bool
    {
        // Skip in local environment
        if (app()->environment('local')) {
            return true;
        }

        if (empty($this->turnstileResponse)) {
            $this->addError('turnstileResponse', 'Please complete the security verification.');
            return false;
        }

        try {
            $response = Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $this->turnstileResponse,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                Log::warning('Turnstile validation failed', [
                    'ip' => request()->ip(),
                    'errors' => $result['error-codes'] ?? [],
                    'user_agent' => request()->userAgent(),
                ]);

                $this->addError('turnstileResponse', 'Security verification failed. Please try again.');
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Turnstile validation error', [
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            $this->addError('turnstileResponse', 'Security verification error. Please try again.');
            return false;
        }
    }

    /**
     * Reset Turnstile widget
     */
    public function resetTurnstile(): void
    {
        $this->turnstileResponse = '';
        $this->dispatch('reset-turnstile');
    }

    /**
     * Get Turnstile site key
     */
    public function getTurnstileSiteKey(): string
    {
        return config('services.turnstile.sitekey', '');
    }
}