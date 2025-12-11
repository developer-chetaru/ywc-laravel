<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureFinancialAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route() ? $request->route()->getName() : 'null';
        $routeUri = $request->route() ? $request->route()->uri() : 'null';
        
        Log::info('EnsureFinancialAdmin: Checking access', [
            'route_name' => $routeName,
            'route_uri' => $routeUri,
            'url' => $request->fullUrl(),
            'path' => $request->path(),
        ]);

        if (!Auth::check()) {
            Log::warning('EnsureFinancialAdmin: User not authenticated');
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        Log::info('EnsureFinancialAdmin: User check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'has_role_api' => $user->hasRole('super_admin', 'api'),
            'has_role_default' => $user->hasRole('super_admin'),
        ]);
        
        // Check role with api guard (since User model uses api guard)
        // Also check without guard as fallback
        if (!$user->hasRole('super_admin', 'api') && !$user->hasRole('super_admin')) {
            Log::warning('EnsureFinancialAdmin: Access denied - redirecting to financial dashboard');
            session()->flash('error', 'Access denied. Admin privileges required.');
            return redirect()->route('financial.dashboard');
        }

        Log::info('EnsureFinancialAdmin: Access granted - proceeding');
        // Access granted - proceed
        return $next($request);
    }
}
