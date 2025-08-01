<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has Finance role or higher level roles
        if (!$user->hasAnyRole(['Finance', 'Admin', 'superAdmin'])) {
            abort(403, 'Access denied. Finance role required.');
        }
        
        // For non-SuperAdmin users, ensure they have a company
        if (!$user->hasRole('superAdmin') && !$user->company_id) {
            abort(403, 'User must be associated with a company to access finance features.');
        }
        
        return $next($request);
    }
}