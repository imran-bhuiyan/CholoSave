<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSiteAdmin
{
    /**
     * Ensure the authenticated user has the site-admin role (users.role = 'admin').
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'This area is restricted to site administrators.');
        }

        return $next($request);
    }
}
