<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('adminhu.login')->with('error', 'Please sign in to continue.');
        }

        $user = auth()->user();
        $adminRoles = ['super_admin', 'admin', 'sales', 'content', 'products'];

        if (!$user->hasAnyRole($adminRoles)) {
            auth()->logout();
            return redirect()->route('adminhu.login')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
