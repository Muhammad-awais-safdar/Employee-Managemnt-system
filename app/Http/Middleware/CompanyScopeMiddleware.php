<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Skip middleware if user is not authenticated
        if (!$user) {
            return $next($request);
        }
        
        // SuperAdmin can access everything
        if ($user->hasRole('superAdmin')) {
            return $next($request);
        }
        
        // Ensure user has a company_id
        if (!$user->company_id) {
            abort(403, 'User must be associated with a company.');
        }
        
        // Set company scope for the request
        $request->merge(['company_scope' => $user->company_id]);
        
        return $next($request);
    }
}