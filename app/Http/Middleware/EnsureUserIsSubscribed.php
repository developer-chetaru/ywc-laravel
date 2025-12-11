<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // FIRST: Check if this is a financial-planning route - SKIP ALL subscription checks immediately
        // These routes only require authentication, NOT subscription
        $path = $request->path();
        $uri = $request->getRequestUri();
        $url = $request->fullUrl();
        
        // Check path, URI, and URL for financial-planning routes
        if (str_starts_with($path, 'financial-planning') || 
            str_starts_with($uri, '/financial-planning') ||
            str_contains($url, '/financial-planning')) {
            // This is a financial-planning route - allow without subscription check
            return $next($request);
        }
        
        // Get route name if available (may not be resolved yet)
        $currentRoute = $request->route() ? $request->route()->getName() : null;
        
        // Also check route name if available
        if ($currentRoute && str_starts_with($currentRoute, 'financial.')) {
            return $next($request);
        }
        
        // Continue with subscription check for all other routes
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
      
        // Allow super_admin to bypass subscription (check both guards)
        if ($user->hasRole('super_admin', 'api') || $user->hasRole('super_admin')) {
            return $next($request);
        }

        $active = $user->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->exists();

        if (!$active) {
            $allowedRoutes = [
                'subscription.page',
                'subscription.success',
                'profile',
                'profile.password',
                'purchase.history',
                'subscription.cancel',
            ];

            // Allow all financial routes (should already be handled above, but double-check)
            if ($currentRoute && str_starts_with($currentRoute, 'financial.')) {
                return $next($request);
            }

            // Dashboard → flash popup only
            if ($currentRoute === 'main-dashboard') {
                session()->flash('subscription_required', true);
                return $next($request);
            }

            // Other allowed routes → no popup, just allow access
            if (in_array($currentRoute, $allowedRoutes)) {
                return $next($request);
            }

            // All other restricted pages → flash popup AND redirect to subscription page
            session()->flash('subscription_required', true);
            return redirect()->route('subscription.page');
        }

        return $next($request);
    }
}
