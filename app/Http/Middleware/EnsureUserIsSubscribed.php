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

        // FIRST: Check for suspended status - this takes priority over everything
        // Get all subscriptions to check for suspended status
        $suspendedSubscription = $user->subscriptions()
            ->where('status', 'suspended')
            ->latest()
            ->first();

        // If suspended, block ALL access except subscription page and purchase history
        if ($suspendedSubscription) {
            // Allow subscription.page and purchase.history routes
            $allowedRoutes = [
                'subscription.page',
                'purchase.history',
            ];
            
            if (in_array($currentRoute, $allowedRoutes)) {
                session()->flash('account_suspended', true);
                return $next($request);
            }

            // Block everything else - redirect to subscription page with suspended message
            session()->flash('account_suspended', true);
            return redirect()->route('subscription.page')
                ->with('error', 'Your account has been suspended due to failed payments. Please update your payment method to restore access.');
        }

        // Get active subscription (excluding suspended)
        $subscription = $user->subscriptions()
            ->whereIn('status', ['active', 'past_due'])
            ->latest()
            ->first();

        // Check if subscription is active - more lenient check
        $isActive = false;
        if ($subscription && $subscription->status === 'active') {
            // If current_period_end exists, check if it's future
            if ($subscription->current_period_end) {
                $isActive = $subscription->current_period_end->isFuture();
            } elseif ($subscription->end_date) {
                // Fallback to end_date
                $isActive = $subscription->end_date->isFuture();
            } else {
                // If status is active but no period_end, still consider it active (will be set later)
                $isActive = true;
            }
        }

        // Check if in grace period (past_due but grace period not expired)
        $isInGracePeriod = $subscription 
            && $subscription->status === 'past_due'
            && $subscription->isInGracePeriod();

        // If active or in grace period, allow access
        if ($isActive || $isInGracePeriod) {
            // If in grace period, show warning
            if ($isInGracePeriod) {
                session()->flash('payment_warning', [
                    'message' => 'Your payment failed. Please update your payment method to avoid service interruption.',
                    'retry_count' => $subscription->payment_retry_count,
                    'grace_period_end' => $subscription->grace_period_end,
                ]);
            }
            return $next($request);
        }

        // No active subscription
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
}
