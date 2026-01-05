<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Role-based dashboard mappings (works with existing Spatie roles)
     */
    private const ROLE_DASHBOARDS = [
        'super_admin' => '/admin',
        'admin' => '/admin',
        'editor' => '/admin',
        'public' => '/panel/',
    ];

    /**
     * Get appropriate dashboard URL based on user's highest priority role
     * Uses existing Spatie Permission roles
     */
    public function getDashboardUrl(?User $user = null): string
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return '/login';
        }

        // Check roles in priority order using Spatie's hasRole method
        foreach (self::ROLE_DASHBOARDS as $role => $route) {
            if ($user->hasRole($role)) {
                Log::info("User {$user->email} redirected to {$route} based on role: {$role}");
                return $route;
            }
        }

        // Fallback to public panel
        Log::warning("User {$user->email} has no recognized role, redirecting to public panel");
        return '/panel/';
    }

    /**
     * Handle post-login redirect with intended URL support
     */
    public function handlePostLoginRedirect(?User $user = null): string
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return '/login';
        }

        // Check if there's an intended URL in session
        $intended = session()->pull('url.intended');
        
        if ($intended && $this->isUrlAccessibleToUser($intended, $user)) {
            return $intended;
        }

        // Redirect to appropriate dashboard
        return $this->getDashboardUrl($user);
    }

    /**
     * Check if user can access a URL (basic check)
     * This works alongside Filament Shield's more detailed permissions
     */
    public function isUrlAccessibleToUser(string $url, User $user): bool
    {
        // Basic URL pattern checks
        if (str_starts_with($url, '/admin')) {
            return $user->hasAnyRole(['super_admin', 'admin', 'editor']);
        }

        if (str_starts_with($url, '/panel')) {
            return $user->hasRole('public') || $user->hasRole('super_admin');
        }

        // For other URLs, let Filament Shield handle it
        return true;
    }

    /**
     * Get unauthorized redirect URL
     */
    public function getUnauthorizedRedirect(string $attemptedRoute, ?User $user = null): string
    {
        $user = $user ?? Auth::user();

        // Not authenticated - redirect to login
        if (!$user) {
            return '/login';
        }

        // Email not verified - redirect to login with message
        if (!$user->hasVerifiedEmail()) {
            session()->flash('error', 'Please verify your email before accessing this area.');
            return '/login';
        }

        // Authenticated but wrong access - redirect to appropriate dashboard
        $dashboardUrl = $this->getDashboardUrl($user);
        
        // Log unauthorized access attempt
        Log::warning("Unauthorized access attempt", [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->getRoleNames()->toArray(),
            'attempted_route' => $attemptedRoute,
            'redirected_to' => $dashboardUrl,
            'ip' => request()->ip(),
        ]);

        session()->flash('error', 'You do not have permission to access that area.');
        return $dashboardUrl;
    }

    /**
     * Check if user has admin privileges (works with existing roles)
     */
    public function isAdmin(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin', 'editor']);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasRole('super_admin');
    }

    /**
     * Get user's primary role (highest priority)
     */
    public function getPrimaryRole(?User $user = null): ?string
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return null;
        }

        // Return roles in priority order
        foreach (array_keys(self::ROLE_DASHBOARDS) as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return null;
    }

    /**
     * Check if authenticated user should be redirected away from auth pages
     */
    public function shouldRedirectFromAuth(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        return $user && $user->hasVerifiedEmail();
    }
}