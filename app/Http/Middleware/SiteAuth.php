<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiteAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('site_auth')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
