<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; " .
               "script-src-elem 'self' 'unsafe-inline' https:; " .
               "style-src 'self' 'unsafe-inline' https:; " .
               "style-src-elem 'self' 'unsafe-inline' https:; " .
               "img-src 'self' data: https: blob:; " .
               "font-src 'self' data: https:; " .
               "connect-src 'self' https: wss: ws:; " .
               "media-src 'self' https:; " .
               "object-src 'none'; " .
               "frame-src 'self' https:;";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}