<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     * Redirects authenticated users away from auth pages using AuthService
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Only redirect if email is verified
                if ($user && $user->hasVerifiedEmail()) {
                    $redirectUrl = $this->authService->getDashboardUrl($user);
                    
                    // Add a flash message for better UX
                    session()->flash('info', 'You are already logged in.');
                    
                    return redirect($redirectUrl);
                }
            }
        }

        return $next($request);
    }
}