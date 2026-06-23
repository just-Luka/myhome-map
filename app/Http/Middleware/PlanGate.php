<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlanGate
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        if (! auth()->user()->isPro()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Pro plan required.'], 403)
                : abort(403, 'This feature requires a Pro plan.');
        }

        return $next($request);
    }
}
