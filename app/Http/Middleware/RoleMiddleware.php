<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware(['auth', 'role:admin']) or role:student|instructor
     */
    public function handle(Request $request, Closure $next, string $roles = null)
    {
        $user = $request->user();

        if (! $user || ! $roles) {
            abort(403);
        }

        $allowed = explode('|', $roles);

        if (! $user->hasRole($allowed)) {
            abort(403);
        }

        return $next($request);
    }
}
