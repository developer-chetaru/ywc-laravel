<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the Authorization header is available to Laravel when it gets
 * stripped by the web server (e.g. Nginx, some Apache/PHP-FPM setups).
 * Use this so Bearer token auth works in production.
 */
class EnsureAuthorizationHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        // Already has Authorization – nothing to do
        if ($request->headers->has('Authorization')) {
            return $next($request);
        }

        // Nginx / proxy sometimes passes as X-Authorization or Apache puts in REDIRECT_*
        $auth = $request->header('X-Authorization')
            ?? $request->server('REDIRECT_HTTP_AUTHORIZATION')
            ?? $request->server('HTTP_AUTHORIZATION');

        if (! empty($auth)) {
            $request->headers->set('Authorization', $auth);
        }

        return $next($request);
    }
}
