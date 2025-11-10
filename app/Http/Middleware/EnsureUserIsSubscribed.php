<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }
      
        // Allow super_admin to bypass subscription
        if ($user->hasRole('super_admin')) {
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

            $currentRoute = $request->route()->getName();

            // Dashboard → flash popup only
            if ($currentRoute === 'dashboard') {
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


        //return redirect()->route('subscription.page')->with('error', 'You need an active subscription to access this page.');

        return $next($request);
    }
}
