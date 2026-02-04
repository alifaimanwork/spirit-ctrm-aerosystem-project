<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()->hasRole(['superadmin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
