<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RouteProtection
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     * Works alongside Filament Shield - handles basic URL guessing protection
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->getPathInfo();
        $user = Auth::user();

        // Skip for API, Livewire, and other system routes
        if ($request->is('api/*') || 
            $request->is('livewire/*') || 
            $request->is('_ignition/*') ||
            $request->is('telescope/*')) {
            return $next($request);
        }

        // Basic URL guessing protection for main areas
        $protectedPatterns = [
            '/admin' => ['super_admin', 'admin', 'editor'],
            '/panel' => ['public', 'super_admin'], // super_admin can access public panel
        ];

        foreach ($protectedPatterns as $pattern => $requiredRoles) {
            if (str_starts_with($route, $pattern)) {
                // Not authenticated
                if (!$user) {
                    session(['url.intended' => $request->url()]);
                    return redirect('/login');
                }

                // Email not verified
                if (!$user->hasVerifiedEmail()) {
                    session()->flash('error', 'Please verify your email to continue.');
                    return redirect('/login');
                }

                // Check role access
                if (!$user->hasAnyRole($requiredRoles)) {
                    $redirectUrl = $this->authService->getUnauthorizedRedirect($route, $user);
                    
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Access denied',
                            'redirect_url' => $redirectUrl
                        ], 403);
                    }

                    return redirect($redirectUrl);
                }
            }
        }

        return $next($request);
    }
}