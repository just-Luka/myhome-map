<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CeoAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->isCeo()) {
            abort(403);
        }

        return $next($request);
    }
}
