<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ScraperAuth
{
    public function handle(Request $request, Closure $next)
    {
        $secret = config('services.scraper.secret');

        if (! $secret || $request->bearerToken() !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
