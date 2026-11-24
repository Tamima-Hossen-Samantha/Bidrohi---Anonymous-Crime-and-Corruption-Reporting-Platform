<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSessionMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!session('admin_authenticated')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 401);
            }
            return redirect('/');
        }

        return $next($request);
    }
}