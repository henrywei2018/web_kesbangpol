<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class RateLimitMiddleware
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $key = "rate_limit_{$ip}";
        $maxAttempts = 5;
        $decayMinutes = 60;

        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            abort(429, 'Terlalu banyak permintaan. Silakan coba lagi nanti.');
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
