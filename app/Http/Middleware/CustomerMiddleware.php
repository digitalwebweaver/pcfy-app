<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('account.login')->with('info', 'Please sign in to continue.');
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('adminhu.dashboard');
        }

        return $next($request);
    }
}
