<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $allowedRoles = array_map('trim', explode(',', $role));

        if (!in_array(auth()->user()->role, $allowedRoles, true)) {
            abort(403, 'No tienes permiso para acceder a esta página');
        }

        return $next($request);
    }
}
