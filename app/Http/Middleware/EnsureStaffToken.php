<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffToken
{
    /**
     * Redirect to login if no staff token in session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('tito_access_token')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
